<?php 

namespace App\Services;

use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use App\Models\User;
use App\Events\OrderMatched;
use Illuminate\Support\Facades\DB;
use Exception;


class OrderService
{
    const COMMISSION_RATE=0.015;

    public function createOrder(User $user,string $symbol,string $side,string $price,string $amount):Order
    {
        return DB::transaction(function()use($user,$symbol,$side,$price,$amount)
        {
            $totalCost=bcmul($price, $amount, 2);
            if ($side == 'buy')
            {
                // Lock user for update
                $user = User::lockForUpdate()->findOrFail($user->id);
                
                if (bccomp($user->balance, $totalCost, 2) < 0){
                    throw new Exception ('Insufficient balance');
                }
                $user->decrement('balance',$totalCost);
            }else{
                $asset=Asset::lockForUpdate()->firstOrCreate(
                    ['user_id'=>$user->id,'symbol'=>$symbol],
                    ['amount'=>'0','locked_amount'=>'0']
                );
                if (bccomp($asset->amount, $amount, 8) < 0)
                {
                    throw new Exception ('Insufficient assets');
                }

                $asset->decrement('amount',$amount);
                $asset->increment('locked_amount',$amount);
            }

            $order=Order::create([

                'user_id'=>$user->id,
                'symbol'=>$symbol,
                'side'=>$side,
                'price'=>$price,
                'amount'=>$amount,
                'status'=>Order::STATUS_OPEN,
            ]);

            $this->matchOrder($order);
            return $order->fresh();

            
        });
    }

public function cancelOrder(Order $order):bool
{
    return DB::transaction(function() use ($order)
    {
        // Lock order for update
        $order = Order::lockForUpdate()->findOrFail($order->id);
        
        if ($order->status !== Order::STATUS_OPEN)
        {
            throw new Exception ('Order cannot be Cancelled');
        }
        $totalCost=bcmul($order->price, $order->amount, 2);
        if ($order->side =='buy'){
            $order->user()->lockForUpdate()->first()->increment('balance',$totalCost);

    }else{
        $asset = Asset::lockForUpdate()
        ->where('user_id',$order ->user_id)
        ->where('symbol', $order ->symbol)
        ->firstOrFail();

        $asset->increment('amount', $order->amount);
        $asset->decrement('locked_amount', $order->amount);

    }
    $order->update(['status'=>Order::STATUS_CANCELLED]);

    return true;

    });
}

private function matchOrder(Order $newOrder):void
{
    DB::transaction (function() use ($newOrder)
    {
        // Reload order with lock
        $newOrder = Order::lockForUpdate()->find($newOrder->id);
        
        // Check if order is still open
        if (!$newOrder || $newOrder->status !== Order::STATUS_OPEN) {
            return;
        }
        
        if($newOrder->side ==='buy'){
            $matchingOrder =Order::lockForUpdate()
            ->where('symbol',$newOrder->symbol)
            ->where('side','sell')
            ->where('status',Order::STATUS_OPEN)
            ->where('price','<=',$newOrder->price)
            ->orderBy('price','asc')
            ->orderBy('created_at','asc')
            ->first();


            if($matchingOrder && $matchingOrder->amount == $newOrder->amount){
                $this->executeTrade($newOrder,$matchingOrder);
            }
        }else{
            $matchingOrder =Order::lockForUpdate()
            ->where('symbol',$newOrder->symbol)
            ->where('side','buy')
            ->where('status',Order::STATUS_OPEN)
            ->where('price','>=',$newOrder->price)
            ->where('amount',$newOrder->amount)
            ->orderBy('price','desc')
            ->orderBy('created_at','asc')
            ->first();
            if($matchingOrder){
                $this->executeTrade($matchingOrder,$newOrder);
            }
        }
    });
}

private function executeTrade(Order $buyOrder,Order $sellOrder):void{
    $tradePrice = $sellOrder->price;
    $tradeAmount = $buyOrder->amount;
    $total =bcmul($tradePrice, $tradeAmount, 2);
    $commission = bcmul($total, (string)self::COMMISSION_RATE, 2);
    
    // Lock users
    $buyer = User::lockForUpdate()->findOrFail($buyOrder->user_id);
    $seller = User::lockForUpdate()->findOrFail($sellOrder->user_id);
    
//commission from the buyer
    if (bccomp($buyer->balance, $commission, 2) < 0) {
        throw new Exception('Buyer has insufficient balance for commission');
    }
    $buyer->decrement('balance',$commission);
//asset transfer
    $buyerAsset= Asset::lockForUpdate()->firstOrCreate(
        ['user_id'=>$buyOrder->user_id,'symbol'=>$buyOrder->symbol],
        ['amount'=>'0','locked_amount'=>'0']
    );
    $buyerAsset->increment('amount',$tradeAmount);

//release locked amount from seller
$sellerAsset =Asset::lockForUpdate()
->where('user_id',$sellOrder->user_id)
->where('symbol',$sellOrder->symbol)
->firstOrFail();
$sellerAsset->decrement('locked_amount',$tradeAmount);

$seller->increment('balance',$total);

$buyOrder->update(['status'=>Order::STATUS_FILLED]);
$sellOrder->update(['status'=>Order::STATUS_FILLED]);


$trade =Trade::create([
    'buy_order_id' => $buyOrder->id,
    'sell_order_id' => $sellOrder->id,
    'buyer_id' => $buyOrder->user_id,
    'seller_id' => $sellOrder->user_id,
    'symbol' => $buyOrder->symbol,
    'price' => $tradePrice,
    'amount' => $tradeAmount,
    'total' => $total,
    'commission' => $commission,
]);

broadcast(new OrderMatched($trade,$buyOrder->user_id))->toOthers();
broadcast(new OrderMatched($trade,$sellOrder->user_id))->toOthers();




}






}
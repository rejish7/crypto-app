<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;


use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function index(Request $request)
    {
        $symbol = $request-> query('symbol');

        $query = Order::with('user:id,name');
            // ->where('status', 1); if needed for only to display the ongoing sell/buy

        if ($symbol) {
            $query->where('symbol', $symbol);
        }

        $orders = $query->orderBy('price','desc')
            ->orderBy('created_at','asc')
            ->get();

        return response()->json($orders);
    }

    public function store(CreateOrderRequest $request)
    {
        try{
            $order=$this->orderService->createOrder
            (
                $request->user(),
                $request->symbol,
                $request->side,
                $request->price,
                $request->amount
            );
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('user:id,name'),
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Failed to create order',
                'error'=>$e->getMessage()
            ],400);

        }
    }
    public function cancel(Request $request ,Order $order)
    {
        if ($order->user_id !==$request->user()->id){
            return response()->json([
                'message' => 'Unauthorized'
            ],403);
        }
        try
        {
            $this->orderService->cancelOrder($order);
            return response()->json([
                'message'=>'Order cancelled successfully',
                'order' => $order->fresh(),
            ]);
        }catch(\Exception $e)
        {
            return response()->json([
                'message' => 'Failed to cancel order',
                'error'=>$e ->getMessage()
            ],400);
        }

    }

}

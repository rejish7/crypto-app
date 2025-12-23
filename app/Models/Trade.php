<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'buy_order_id',
        'sell_order_id',
        'buyer_id',
        'seller_id',
        'symbol',
        'price',
        'amount',
        'total',
        'commission',
    ];


    protected function casts(): array
    {
        return
        [
            'price'=> 'decimal:2',
            'amount'=>'decimal:8',
            'total'=>'decimal:2',
            'commission'=>'decimal:2'
        ];
    }


    public function buyOrder()
    {
        return $this->belongsTo(Order::class,'buy_order_id');
    }

    public function sellOrder()
    {
        return $this->belongsTo(Order::class,'sell_order_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class,'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id');
    }
}

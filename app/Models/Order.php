<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_OPEN=1;
    const STATUS_FILLED=2;
    const STATUS_CANCELLED=3;

    protected $fillable=
    [
    'user_id',
    'symbol',
    'side',
    'price',
    'amount',
    'status',
    ];

    protected function casts(): array
    {
        return
        [
            'price'=>'decimal:2',
            'amount'=>'decimal:8',
            'status'=>'integer',
        ];
    }

    protected $appends = ['total', 'status_label'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function buyTrades()
    {
        return $this->hasMany(Trade::class,'buy_order_id');
    }
    public function sellTrades()
    {
        return $this->hasMany(Trade::class,'sell_order_id');
    }

    public function getTotalAttribute(): string
    {
        return bcmul($this->price, $this->amount, 2);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'open',
            self::STATUS_FILLED => 'filled',
            self::STATUS_CANCELLED => 'cancelled',
            default => 'unknown',
        };
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeBySymbol($query, string $symbol)
    {
        return $query->where('symbol', $symbol);
    }
}

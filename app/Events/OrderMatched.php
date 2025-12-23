<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Trade;

class OrderMatched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
    
        public Trade $trade,
        public int $userId
        )
    {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'OrderMatched';
    }

    public function broadcastWith():array
    {
        $this->trade->load(['buyOrder.user', 'sellOrder.user']);
        
        return[
            'trade' => [
                'id' => $this->trade->id,
                'symbol' => $this->trade->symbol,
                'price' => $this->trade->price,
                'amount' => $this->trade->amount,
                'total' => $this->trade->total,
                'commission' => $this->trade->commission,
                'buyer_id' => $this->trade->buyer_id,
                'seller_id' => $this->trade->seller_id,
                'created_at' => $this->trade->created_at->toISOString(),
            ],
            'is_buyer' => $this->userId === $this->trade->buyer_id,
            'is_seller' => $this->userId === $this->trade->seller_id,
        ];
    }
}


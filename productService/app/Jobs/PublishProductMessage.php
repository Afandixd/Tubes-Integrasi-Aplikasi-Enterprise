<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PublishProductMessage implements ShouldQueue
{
    use Queueable;

    public $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function handle(): void
    {
        Log::info('Product Message Sent', [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'price' => $this->product->price,
        ]);
    }
}

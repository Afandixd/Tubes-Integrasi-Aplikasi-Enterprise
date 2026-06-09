<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;
    public $amount;

    public function __construct($orderId, $amount)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
    }

    public function handle(): void
    {
        Log::info("Memproses pembayaran untuk Order ID: {$this->orderId} sebesar {$this->amount}");

        Payment::create([
            'order_id' => $this->orderId,
            'amount' => $this->amount,
            'status' => 'pending'
        ]);

        Log::info("Tagihan pembayaran berhasil dibuat untuk Order ID: {$this->orderId}");
    }
}

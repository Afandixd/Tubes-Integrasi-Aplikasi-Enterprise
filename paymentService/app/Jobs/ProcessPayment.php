<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPayment implements ShouldQueue
{
    use Queueable;

    public $payment_id;
    public $total_pesanan;
    public $status;

    public function __construct($payment_id, $total_pesanan, $status)
    {
        $this->payment_id = $payment_id;
        $this->total_pesanan = $total_pesanan;
        $this->status = $status;
    }

    public function handle(): void
    {
        Log::info('Payment diproses RabbitMQ', [
            'payment_id' => $this->payment_id,
            'total_pesanan' => $this->total_pesanan,
            'status' => $this->status
        ]);
    }
}

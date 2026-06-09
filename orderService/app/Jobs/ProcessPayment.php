<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;
    public $amount;

    // Konstruktor ini yang akan membawa data dari Order ke Payment
    public function __construct($orderId, $amount)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
    }

    public function handle(): void
    {
        // Di orderService, handle() biarkan KOSONG.
        // Karena yang akan mengeksekusi kodenya nanti adalah paymentService.
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProductStock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productId;
    protected $quantity;

    /**
     * Kita terima data ID Produk dan Jumlah yang dipesan
     */
    public function __construct($productId, $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    /**
     * Logika eksekusi saat pesan diterima
     */
   public function handle(): void
{
    // KOSONGKAN SAJA
    // Karena yang akan memproses pengurangan stok adalah productService
}

}
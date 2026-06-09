<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        Log::info("Menerima pesanan untuk Produk ID: {$this->productId}");

        $product = Product::find($this->productId);

        if ($product) {
            $product->stock -= $this->quantity; // Kurangi stok
            $product->save();
            Log::info("Stok berhasil diperbarui. Stok sisa: {$product->stock}");
        } else {
            Log::error("Produk ID {$this->productId} tidak ditemukan!");
        }
    }
}

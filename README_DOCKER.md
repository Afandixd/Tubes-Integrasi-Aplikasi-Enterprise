# 🚀 Panduan Menjalankan Project Microservices (Docker)

Project ini terdiri dari 4 Microservices (User, Product, Order, Payment) yang berjalan di dalam kontainer Docker.

## 🛠 Persiapan Awal

1. Pastikan sudah menginstal **Docker Desktop** di laptop.
2. Pastikan Docker Desktop sudah dalam keadaan menyala (**Running**).
3. Pastikan tidak ada aplikasi lain yang memakai port: **3307, 5672, 8000, 8001, 8002, 8003** (Matikan Laragon/XAMPP/RabbitMQ lokal jika ada).

## 🏃‍♂️ Cara Menjalankan Project

Buka terminal di folder utama ini, lalu jalankan perintah berikut secara berurutan:

### 1. Build & Up Kontainer

```bash
docker-compose up --build -d
```

*(Tunggu sampai semua status kontainer 'Started' atau 'Running' di Docker Desktop).*

### 2. Migrasi Database (Wajib)

Jalankan perintah ini satu per satu untuk membuat tabel di database Docker:

```bash
docker exec -it user-service php artisan migrate
docker exec -it product-service php artisan migrate
docker exec -it order-service php artisan migrate
docker exec -it payment-service php artisan migrate
```

### 3. Isi Data Awal (Dummy Data)

Agar bisa langsung dites di Postman, masukkan data produk dan user pertama:

```bash
# Tambah Produk
docker exec -it product-service php artisan tinker --execute="App\Models\Product::create(['id'=>1, 'name'=>'Americano', 'price'=>10000, 'stock'=>100])"

# Tambah User
docker exec -it user-service php artisan tinker --execute="App\Models\User::create(['id'=>1, 'name'=>'Afandi', 'email'=>'afandi@test.com', 'password'=>'123'])"
```

## 📍 Daftar URL & Port Service

- **User Service:** `http://localhost:8000`
- **Product Service:** `http://localhost:8001`
- **Order Service:** `http://localhost:8002`
- **Payment Service:** `http://localhost:8003`
- **RabbitMQ Dashboard:** `dari dia ` (User: `guest` | Pass: `guest`)
- **MySQL (Eksternal):** `localhost:3307` (User: `root` | Pass: `root`)

## ⚠️ Troubleshooting

- **Error Database Connection:** Tunggu 10-20 detik sampai kontainer `cafe-db` benar-benar siap (Healthy) sebelum menjalankan perintah migrasi.
- **Worker Mati:** Pastikan `product-worker` dan `payment-worker` berstatus hijau di Docker Desktop agar proses asinkron (update stok) berjalan.
- **Permission Denied (Mac/Linux):** Jalankan `chmod -R 777 .` di folder service yang bermasalah.

---

**Dibuat oleh Tim Week 8 - Pengganti UTS**

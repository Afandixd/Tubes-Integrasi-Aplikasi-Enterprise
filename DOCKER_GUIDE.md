# Panduan Arsitektur Microservices Docker

Sistem sekarang sudah terbungkus sepenuhnya menggunakan Docker. Berikut adalah detail teknisnya:

## 📂 Struktur File Baru
- **Root Folder**: `docker-compose.yml` (Mengatur seluruh ekosistem)
- **userService**: `Dockerfile` (Resep kontainer User)
- **productService**: `Dockerfile` (Resep kontainer Product)
- **orderService**: `Dockerfile` (Resep kontainer Order)
- **paymentService**: `Dockerfile` (Resep kontainer Payment)

## 🌐 Alamat Service (Internal Docker)
Semua service berkomunikasi menggunakan nama internal:
- **Database**: `db:3306`
- **RabbitMQ**: `rabbitmq:5672`
- **User Service**: `user-service:8000`
- **Product Service**: `product-service:8001`
- **Order Service**: `order-service:8002`
- **Payment Service**: `payment-service:8003`

## 🛠 Perubahan Konfigurasi (.env)
Semua file `.env` telah disesuaikan:
- `DB_HOST=db`
- `DB_PASSWORD=root`
- `RABBITMQ_HOST=rabbitmq`
- `USER_SERVICE_URL=http://user-service:8000`
- `PRODUCT_SERVICE_URL=http://product-service:8001`

## 🚀 Cara Menjalankan
1. Hentikan semua proses manual (Ctrl+C).
2. Jalankan `docker-compose up --build` di folder utama.
3. Tunggu sampai semua container statusnya "Running".
4. Tes di Postman: `POST http://localhost:8002/api/orders`.

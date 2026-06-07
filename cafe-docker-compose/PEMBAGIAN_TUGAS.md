# Pembagian Tugas UAS: Integrasi Aplikasi Enterprise

**Proyek:** Pengembangan Studi Kasus Sistem Terintegrasi Berbasis Microservices (Backend Only)

**Anggota Tim & Fokus Service:**
1. **Afandi** (Ketua / Koordinator) - `orderService` & `cafe-docker-compose`
2. **Vincent** - `userService`
3. **Salwa** - `productService`
4. **Niken** - `paymentService`

**Terakhir Diperbarui:** 7 Juni 2026, Sesi 3

---

## 📊 Status Pengerjaan Global

| Kriteria UAS | Bobot | Status | Persentase |
|--------------|-------|--------|------------|
| **Docker Deployment** | 20 | ✅ SELESAI | 100% |
| **RESTful API** | 25 | ⚠️ PROSES (1/4 selesai) | 30% |
| **Message Broker (RabbitMQ)** | 25 | ⚠️ PROSES (Producer done) | 40% |
| **GraphQL Implementation** | 20 | 🔄 PROSES (Hasura setup) | 50% |
| **Dokumentasi & Arsitektur** | 15 | ❌ BELUM | 10% |
| **TOTAL ESTIMASI** | **100** | | **~48%** |

---

## 🎯 Ringkasan Tugas per Anggota

### Status Checklist:
- ✅ **SELESAI** (Sudah dikerjakan & dipush ke GitHub)
- 🔄 **SEDANG DIKERJAKAN** (In Progress)
- ⏳ **BELUM DIMULAI** (Upcoming)
- ⚠️ **URGENT** (Harus dikerjakan minggu ini)

---

## 1️⃣ Afandi (Ketua & Order Service)

### ✅ Yang Sudah Selesai:
- ✅ Setup infrastruktur Docker Compose (9 containers)
- ✅ Konfigurasi MySQL multi-database (user_db, product_db, payment_db)
- ✅ Konfigurasi PostgreSQL untuk Order Service (order_db)
- ✅ Setup Hasura GraphQL Engine (port 8080)
- ✅ Setup RabbitMQ (port 5672 & 15672)
- ✅ REST API Order Service (3 endpoint):
  - `POST /api/orders` - Membuat order baru
  - `GET /api/orders` - List semua order
  - `GET /api/orders/{id}` - Detail order
- ✅ RabbitMQ Producer di Order Service (dispatch job ke queue)

### 🔄 Sedang Dikerjakan:
- 🔄 Migrasi Order Service ke PostgreSQL (untuk Hasura compatibility)
- 🔄 Koneksi Hasura ke order_db

### ⚠️ Yang Harus Dikerjakan Minggu Ini:
1. **Hasura Setup (URGENT):**
   - Buka Hasura Console: `http://localhost:8080`
   - Login dengan admin secret: `myadminsecretkey`
   - Tambah database connection ke `order-postgres-db`
   - Track tabel `orders`
   - Track tabel dari service lain (users, products, payments) setelah tim selesai
   - Test query GraphQL di Hasura playground
   - Screenshot hasil query untuk dokumentasi

2. **Koordinasi Tim:**
   - Monitor progress Vincent, Salwa, Niken
   - Review code yang sudah dipush
   - Pastikan semua service bisa dijalankan via docker-compose

3. **Dokumentasi Postman:**
   - Export collection "Order Service" ke JSON
   - Kirim ke Salwa untuk digabung

### 📝 Cara Kerja:
```bash
# Test Order Service API
curl -X POST http://localhost:8002/api/orders \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "product_id": 1, "quantity": 2}'

# Cek Hasura Console
open http://localhost:8080
# Atau buka browser manual ke http://localhost:8080
```

**Estimasi Waktu:** 3-4 jam untuk Hasura setup + koordinasi

---

## 2️⃣ Vincent (User Service)

### ✅ Yang Sudah Selesai:
- ✅ Repo `userService` sudah dibuat di GitHub
- ✅ Docker container `user-service` sudah dikonfigurasi di docker-compose
- ✅ Database `user_db` sudah siap di MySQL

### ⏳ Yang Belum Dimulai (SEMUA URGENT):

#### **FASE 1: Setup Awal (30 menit)**
1. Clone repo userService:
   ```bash
   cd cafe-docker-compose
   git clone https://github.com/Final-Project-IAE/userService.git
   ```
2. Setup `.env`:
   ```bash
   cd userService
   cp .env.example .env
   ```
   Edit `.env`:
   ```
   DB_HOST=cafe-db
   DB_PORT=3306
   DB_DATABASE=user_db
   DB_USERNAME=root
   DB_PASSWORD=root
   ```
3. Generate app key:
   ```bash
   docker exec -it user-service php artisan key:generate
   ```
4. Migrasi database:
   ```bash
   docker exec -it user-service php artisan migrate
   ```

#### **FASE 2: REST API (3-4 jam) - PRIORITAS TINGGI**
1. Buat Controller:
   ```bash
   docker exec -it user-service php artisan make:controller Api/UserController
   ```
2. Implementasi minimal 5 endpoint di `routes/api.php`:
   - `POST /api/users/register` - Registrasi user baru
   - `POST /api/users/login` - Login & generate token
   - `GET /api/users/profile` - Ambil profile user (auth)
   - `PUT /api/users/profile` - Update profile (auth)
   - `GET /api/users` - List users (admin)

3. Install Laravel Sanctum untuk authentication:
   ```bash
   docker exec -it user-service php artisan install:api
   ```

#### **FASE 3: GraphQL Backend Manual (2-3 jam) - KUNCI NILAI PENUH**
1. Install lighthouse-php:
   ```bash
   docker exec -it user-service composer require nuwave/lighthouse
   ```
2. Publish config:
   ```bash
   docker exec -it user-service php artisan vendor:publish --tag=lighthouse-schema
   ```
3. Edit `graphql/schema.graphql`:
   ```graphql
   type User {
     id: ID!
     name: String!
     email: String!
     created_at: DateTime
   }

   type Query {
     users: [User!]! @all
     user(id: ID! @eq): User @find
   }

   type Mutation {
     createUser(name: String!, email: String!, password: String!): User
   }
   ```
4. Test di GraphQL playground: `http://localhost:8000/graphql-playground`

#### **FASE 4: Hasura Track (5 menit)**
1. Buka Hasura Console: `http://localhost:8080`
2. Klik "Data" → "cafe-db" → "Track" tabel `users`
3. Test query di Hasura playground

#### **FASE 5: Dokumentasi Postman (30 menit)**
1. Buat collection "User Service" di Postman
2. Tambahkan semua 5 endpoint REST
3. Tambahkan contoh GraphQL query (lighthouse & Hasura)
4. Export ke JSON
5. Kirim ke Salwa

**Total Estimasi Waktu:** 6-8 jam
**Deadline:** Minggu ini (sebelum progress report minggu 14)

---

## 3️⃣ Salwa (Product Service)

### ✅ Yang Sudah Selesai:
- ✅ Repo `productService` sudah dibuat di GitHub
- ✅ Docker container `product-service` sudah dikonfigurasi
- ✅ Database `product_db` sudah siap di MySQL
- ✅ Worker container `product-worker` sudah dikonfigurasi

### ⏳ Yang Belum Dimulai (SEMUA URGENT):

#### **FASE 1: Setup Awal (30 menit)**
1. Clone repo:
   ```bash
   cd cafe-docker-compose
   git clone https://github.com/Final-Project-IAE/productService.git
   ```
2. Setup `.env`:
   ```
   DB_HOST=cafe-db
   DB_DATABASE=product_db
   DB_USERNAME=root
   DB_PASSWORD=root
   
   RABBITMQ_HOST=cafe-rabbitmq
   RABBITMQ_PORT=5672
   RABBITMQ_USER=guest
   RABBITMQ_PASSWORD=guest
   ```
3. Migrasi:
   ```bash
   docker exec -it product-service php artisan migrate
   ```

#### **FASE 2: REST API (3-4 jam)**
Buat minimal 5 endpoint:
- `GET /api/products` - List semua produk
- `GET /api/products/{id}` - Detail produk
- `POST /api/products` - Tambah produk baru (admin)
- `PUT /api/products/{id}` - Update produk (admin)
- `PUT /api/products/{id}/stock` - Update stok (akan trigger RabbitMQ)

#### **FASE 3: RabbitMQ Consumer (2-3 jam) - PENTING**
1. Install RabbitMQ package:
   ```bash
   docker exec -it product-service composer require vladimir-yuldashev/laravel-queue-rabbitmq
   ```
2. Config `config/queue.php`:
   ```php
   'rabbitmq' => [
       'driver' => 'rabbitmq',
       'queue' => 'product_queue',
       'connection' => PhpAmqpLib\Connection\AMQPLazyConnection::class,
       'hosts' => [
           [
               'host' => env('RABBITMQ_HOST', 'cafe-rabbitmq'),
               'port' => env('RABBITMQ_PORT', 5672),
               'user' => env('RABBITMQ_USER', 'guest'),
               'password' => env('RABBITMQ_PASSWORD', 'guest'),
           ],
       ],
   ],
   ```
3. Buat Job:
   ```bash
   docker exec -it product-service php artisan make:job UpdateProductStock
   ```
4. Implementasi logic di Job untuk update stock ketika ada order masuk

#### **FASE 4: Hasura Track (5 menit)**
Track tabel `products` di Hasura Console

#### **FASE 5: Dokumentasi Postman MASTER (1-2 jam) - TANGGUNG JAWAB KHUSUS**
1. Kumpulkan file JSON dari Afandi, Vincent, Niken
2. Import semua ke Postman
3. Gabung jadi 1 collection: **"Cafe Microservices API"**
4. Atur folder structure:
   ```
   Cafe Microservices API/
   ├── User Service/
   │   ├── Register
   │   ├── Login
   │   └── ...
   ├── Product Service/
   │   ├── List Products
   │   └── ...
   ├── Order Service/
   │   └── ...
   └── Payment Service/
       └── ...
   ```
5. Export final collection
6. Kirim ke Niken untuk dokumentasi

**Total Estimasi Waktu:** 7-10 jam
**Deadline:** Minggu ini

---

## 4️⃣ Niken (Payment Service)

### ✅ Yang Sudah Selesai:
- ✅ Repo `paymentService` sudah dibuat di GitHub
- ✅ Docker container `payment-service` sudah dikonfigurasi
- ✅ Database `payment_db` sudah siap di MySQL
- ✅ Worker container `payment-worker` sudah dikonfigurasi

### ⏳ Yang Belum Dimulai (SEMUA URGENT):

#### **FASE 1: Setup Awal (30 menit)**
1. Clone repo:
   ```bash
   cd cafe-docker-compose
   git clone https://github.com/Final-Project-IAE/paymentService.git
   ```
2. Setup `.env`:
   ```
   DB_HOST=cafe-db
   DB_DATABASE=payment_db
   DB_USERNAME=root
   DB_PASSWORD=root
   
   RABBITMQ_HOST=cafe-rabbitmq
   RABBITMQ_PORT=5672
   RABBITMQ_USER=guest
   RABBITMQ_PASSWORD=guest
   ```
3. Migrasi:
   ```bash
   docker exec -it payment-service php artisan migrate
   ```

#### **FASE 2: REST API (3-4 jam)**
Buat minimal 4 endpoint:
- `POST /api/payments` - Buat pembayaran baru
- `GET /api/payments/{id}` - Detail pembayaran
- `PUT /api/payments/{id}/status` - Update status payment
- `GET /api/payments/order/{order_id}` - Payment by order

#### **FASE 3: RabbitMQ Consumer (2-3 jam) - PENTING**
1. Install RabbitMQ package (sama seperti Salwa)
2. Buat Job:
   ```bash
   docker exec -it payment-service php artisan make:job ProcessPayment
   ```
3. Implementasi logic:
   - Listen queue `payment_queue`
   - Terima data order dari Order Service
   - Buat record payment baru
   - Update status payment
   - (Opsional) Kirim notifikasi balik ke Order Service

#### **FASE 4: Hasura Track (5 menit)**
Track tabel `payments` di Hasura Console

#### **FASE 5: Diagram Arsitektur (2-3 jam) - TANGGUNG JAWAB KHUSUS**
1. Buka `app.diagrams.net` atau `excalidraw.com`
2. Gambar komponen:
   - 4 Microservices (User, Product, Order, Payment)
   - Database (MySQL multi-db + PostgreSQL)
   - RabbitMQ (message broker)
   - Hasura GraphQL Engine
   - Docker Network
3. Tambahkan panah komunikasi:
   - REST API (garis solid)
   - GraphQL (garis putus-putus)
   - RabbitMQ queue (garis dengan arah)
4. Export ke PNG/SVG untuk laporan

#### **FASE 6: Laporan PDF Final (3-4 jam) - TANGGUNG JAWAB KHUSUS**
Buat dokumen Word/Google Docs dengan struktur:

**BAB 1: Deskripsi Project**
- Nama project: Sistem Microservices Cafe
- Tujuan: Simulasi sistem backend terintegrasi
- Teknologi: Laravel, MySQL, PostgreSQL, RabbitMQ, Hasura, Docker
- Anggota tim & pembagian tugas

**BAB 2: Arsitektur Sistem**
- Diagram arsitektur (dari Fase 5)
- Penjelasan setiap komponen
- Penjelasan komunikasi antar service:
  - REST API untuk CRUD
  - GraphQL untuk flexible query (Hasura + Lighthouse)
  - RabbitMQ untuk async processing

**BAB 3: Deskripsi Service**
- User Service (Vincent): Endpoint, fitur, teknologi
- Product Service (Salwa): Endpoint, fitur, teknologi
- Order Service (Afandi): Endpoint, fitur, teknologi
- Payment Service (Niken): Endpoint, fitur, teknologi

**BAB 4: Flow Fitur End-to-End**
Contoh: "Proses Pembelian Produk"
1. User register via `POST /api/users/register`
2. User login via `POST /api/users/login` → dapat token
3. User browse products via `GET /api/products` atau GraphQL query
4. User create order via `POST /api/orders`
   - Order Service dispatch job ke RabbitMQ queue `payment_queue`
5. Payment Worker consume queue → process payment
   - Update payment status di database
6. User check payment status via `GET /api/payments/order/{order_id}`

**BAB 5: Link Dokumentasi**
- GitHub Repos:
  - https://github.com/Final-Project-IAE/cafe-docker-compose
  - https://github.com/Final-Project-IAE/userService
  - https://github.com/Final-Project-IAE/productService
  - https://github.com/Final-Project-IAE/orderService
  - https://github.com/Final-Project-IAE/paymentService
- Postman Collection (upload ke Google Drive, cantumkan link)
- Demo Video (jika ada)

**BAB 6: Screenshot**
- Postman testing (setiap service)
- Hasura Console (GraphQL queries)
- RabbitMQ Management (queue statistics)
- Docker containers (docker ps output)

Export ke **PDF** untuk dikumpulkan

**Total Estimasi Waktu:** 9-12 jam
**Deadline:** Minggu 15 (sebelum presentasi minggu 16)

---

## 📅 Timeline & Milestone

### **Minggu 14 (MINGGU INI - 8-14 Juni 2026):**
- **Target:** Progress Report #1
- **Harus Selesai:**
  - ✅ Semua REST API (4 services)
  - ✅ RabbitMQ Consumer (Product & Payment)
  - ✅ GraphQL Lighthouse (User Service)
  - ✅ Hasura track semua tabel
  - ✅ Postman Collection Master (Salwa)

### **Minggu 15 (15-21 Juni 2026):**
- **Target:** Progress Report #2 + Finalisasi
- **Harus Selesai:**
  - ✅ Diagram Arsitektur (Niken)
  - ✅ Laporan PDF Final (Niken)
  - ✅ Testing integrasi end-to-end (Semua)
  - ✅ Persiapan demo & presentasi

### **Minggu 16 (22-28 Juni 2026):**
- **Target:** Pengujian & Presentasi Final
- **Deliverables:**
  - Demo live sistem
  - Presentasi arsitektur & flow
  - Q&A dengan dosen

---

## 🚨 Critical Path (Jangan Sampai Tertunda!)

### **URGENT - Harus Selesai Hari Ini/Besok:**
1. Vincent: Clone repo + Setup + Migrasi
2. Salwa: Clone repo + Setup + Migrasi
3. Niken: Clone repo + Setup + Migrasi
4. Afandi: Track Order Service di Hasura

### **URGENT - Harus Selesai Minggu Ini:**
1. Vincent: REST API User + GraphQL Lighthouse (KUNCI NILAI 100%)
2. Salwa: REST API Product + RabbitMQ Consumer
3. Niken: REST API Payment + RabbitMQ Consumer
4. Afandi: Koordinasi + Review code + Track semua tabel di Hasura
5. Salwa: Gabung Postman Collection

### **Minggu Depan:**
1. Niken: Diagram + Laporan PDF
2. Semua: Testing integrasi
3. Semua: Latihan presentasi

---

## 💬 Komunikasi Tim

**Channel Komunikasi:**
- WhatsApp Group: Update harian progress
- GitHub Issues: Bug reports & technical questions
- Google Meet: Sync meeting (jika perlu)

**Daily Standup (di WhatsApp):**
Setiap anggota post:
1. Apa yang sudah dikerjakan kemarin?
2. Apa yang akan dikerjakan hari ini?
3. Ada blocker/hambatan?

**Format Update:**
```
[Nama] - [Tanggal]
✅ Done: ...
🔄 Today: ...
⚠️ Blocker: ...
```

---

## 📚 Resources & Referensi

### **Dokumentasi Teknologi:**
- Laravel: https://laravel.com/docs
- Lighthouse GraphQL: https://lighthouse-php.com
- Hasura: https://hasura.io/docs
- RabbitMQ: https://www.rabbitmq.com/getstarted.html
- Docker Compose: https://docs.docker.com/compose

### **Tutorial Video (Jika Perlu):**
- Laravel REST API: Search "Laravel API Tutorial"
- Lighthouse GraphQL: Search "Laravel GraphQL Lighthouse"
- RabbitMQ Laravel: Search "Laravel Queue RabbitMQ"
- Hasura Getting Started: https://hasura.io/learn

---

## ❓ FAQ (Pertanyaan yang Sering Ditanyakan)

**Q: Apakah semua service harus pakai GraphQL backend manual?**
A: TIDAK. Cukup 1 service (Vincent di User Service pakai Lighthouse). Service lain cukup track di Hasura.

**Q: Apakah harus semua pakai RabbitMQ?**
A: Tidak. Cukup Order → Payment (untuk async payment processing) dan Order → Product (untuk stock update).

**Q: Kalau ada error waktu migrasi gimana?**
A: Cek dulu container jalan atau tidak (`docker ps`). Cek `.env` sudah benar atau belum. Tanya di grup WA.

**Q: Hasura tidak bisa connect ke MySQL?**
A: Hasura CE tidak support MySQL native. Makanya Order Service pakai PostgreSQL. Service lain tetap MySQL tapi track manual via Hasura (connect ke cafe-db).

**Q: Deadline kapan?**
A: Minggu 14 & 15 progress report, Minggu 16 presentasi final.

---

**Good luck semua! Kita pasti bisa! 💪🚀**

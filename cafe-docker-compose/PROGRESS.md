# Progress Report - UAS Integrasi Aplikasi Enterprise
**Mata Kuliah:** Integrasi Aplikasi Enterprise
**Nama Project:** Sistem Microservices Cafe (Backend Only)
**Terakhir Diperbarui:** 7 Juni 2026, Sesi 2

---

## Konteks Project
Project UAS ini adalah sistem backend berbasis **Microservices** untuk simulasi aplikasi Cafe, terdiri dari 4 layanan yang saling terintegrasi. Teknologi yang digunakan adalah **Laravel (PHP)**, **MySQL**, **PostgreSQL**, **RabbitMQ** (Message Broker), **Hasura GraphQL Engine**, dan **Docker**.

**Anggota:**
- **Afandi** (Ketua) → `orderService` + Infrastruktur Docker
- **Vincent** → `userService`
- **Salwa** → `productService`
- **Niken** → `paymentService`

**Repository GitHub:** https://github.com/Final-Project-IAE

---

## Catatan Arsitektur Terbaru
> [!NOTE]
> **Keputusan Teknis (7 Juni 2026):** Karena Hasura CE tidak mendukung MySQL secara native, `orderService` milik Afandi dipindahkan dari MySQL ke **PostgreSQL** (container `order-postgres-db`). Dengan ini, Hasura dapat langsung terhubung ke database Order tanpa agent tambahan. Service lain (User, Product, Payment) **tetap menggunakan MySQL** dan tidak terpengaruh sama sekali.

---

## Struktur Repository
| Repository | Deskripsi | Status Push |
|---|---|---|
| `cafe-docker-compose` | Infrastruktur Docker pusat (docker-compose.yml, init.sql) | ✅ Sudah Push |
| `orderService` | Layanan Pemesanan (Laravel + PostgreSQL) | ✅ Sudah Push |
| `userService` | Layanan Pengguna (Laravel + MySQL) | ✅ Sudah Push |
| `productService` | Layanan Produk (Laravel + MySQL) | ✅ Sudah Push |
| `paymentService` | Layanan Pembayaran (Laravel + MySQL) | ✅ Sudah Push |

---

## Status per Kriteria UAS

### 1. Docker Deployment (Bobot: 20 poin)
**Status: ✅ SELESAI**
- File `docker-compose.yml` di repo `cafe-docker-compose` menjalankan **9 container**:
  - `cafe-db` (MySQL) → untuk User, Product, Payment
  - `order-postgres-db` (PostgreSQL) → khusus Order Service
  - `hasura-metadata-db` (PostgreSQL) → metadata Hasura
  - `cafe-hasura` (Hasura GraphQL Engine) → port 8080
  - `cafe-rabbitmq` (Message Broker) → port 5672 & 15672
  - `user-service`, `product-service`, `order-service`, `payment-service`
  - `product-worker`, `payment-worker`
- Semua container terhubung melalui jaringan `cafe-network`.

### 2. Database Terpisah
**Status: ✅ SELESAI**
- `user_db` → MySQL (`cafe-db`)
- `product_db` → MySQL (`cafe-db`)
- `payment_db` → MySQL (`cafe-db`)
- `order_db` → **PostgreSQL (`order-postgres-db`)** ← Diubah untuk mendukung Hasura

### 3. RESTful API & Message Broker (Bobot: 25 poin)
**Status: ⚠️ SEBAGIAN SELESAI (Afandi selesai, tim lain belum)**
- **Order Service (Afandi):** REST API **sudah selesai**, 3 endpoint aktif:
  - `POST /api/orders` → Membuat order, publish ke RabbitMQ
  - `GET /api/orders` → Tampilkan semua order
  - `GET /api/orders/{id}` → Tampilkan detail order
- **User Service (Vincent):** ❌ Belum dimulai (masih ada hambatan akses GitHub)
- **Product Service (Salwa):** ❌ Belum dimulai
- **Payment Service (Niken):** ❌ Belum dimulai
- **RabbitMQ:** Container aktif. Jobs didispatch dari Order Service. Consumer Worker di Payment & Product belum dibuat.

### 4. GraphQL Implementation (Bobot: 20 poin)
**Status: 🔄 SEDANG DIKERJAKAN (Afandi)**
- Container Hasura berjalan di `http://localhost:8080`.
- `order-postgres-db` sudah dikonfigurasi ulang agar Hasura bisa terhubung.
- **Yang masih perlu dilakukan:**
  - [ ] Buka Hasura Console → tambah database `order_db` (PostgreSQL)
  - [ ] Track tabel `orders` di Hasura
  - [ ] (Tim lain) Track tabel masing-masing setelah API mereka selesai

### 5. Dokumentasi & Arsitektur (Bobot: 15 poin)
**Status: ❌ BELUM DIMULAI**
- Belum ada Postman Collection.
- Belum ada Diagram Arsitektur.
- Belum ada Laporan PDF.

---

## Ringkasan Status per Anggota

### Afandi (Order Service) - Ketua
| Tugas | Status |
|---|---|
| Setup Docker & Database Terpisah | ✅ Selesai |
| Setup Hasura Container | ✅ Selesai |
| Pindah Order DB ke PostgreSQL | ✅ Selesai |
| REST API Order Service (3 endpoint) | ✅ Selesai |
| RabbitMQ Producer (dispatch job) | ✅ Selesai |
| Hubungkan `order_db` ke Hasura Console | ✅ Selesai |
| Track tabel `orders` di Hasura | ✅ Selesai |
| Track tabel `users` di Hasura | ✅ Selesai |
| Test GraphQL Query via Hasura | ✅ Selesai |
| Dokumentasi GraphQL Queries | ✅ Selesai |
| Dokumentasi Postman Order Service | ✅ Selesai |
| **BAGIAN AFANDI: 100% SELESAI** | ✅ **DONE** |

### Vincent (User Service)
| Tugas | Status |
|---|---|
| Terima undangan GitHub | ⚠️ Dalam proses (error akses) |
| Clone repo & setup `.env` | ⏳ Belum |
| Koneksi ke `user_db` & Migrasi | ⏳ Belum |
| REST API User Service | ⏳ Belum |
| GraphQL (Hasura tracking) | ⏳ Belum |
| Dokumentasi Postman User Service | ⏳ Belum |

### Salwa (Product Service)
| Tugas | Status |
|---|---|
| Clone repo & setup `.env` | ⏳ Belum |
| Koneksi ke `product_db` & Migrasi | ⏳ Belum |
| REST API Product Service | ⏳ Belum |
| RabbitMQ Consumer Worker | ⏳ Belum |
| GraphQL (Hasura tracking) | ⏳ Belum |
| Kumpulkan & gabungkan Postman Collection | ⏳ Belum |

### Niken (Payment Service)
| Tugas | Status |
|---|---|
| Clone repo & setup `.env` | ⏳ Belum |
| Koneksi ke `payment_db` & Migrasi | ⏳ Belum |
| REST API Payment Service | ⏳ Belum |
| RabbitMQ Consumer Worker | ⏳ Belum |
| GraphQL (Hasura tracking) | ⏳ Belum |
| Diagram Arsitektur Sistem | ⏳ Belum |
| Laporan PDF Final | ⏳ Belum |

---

## Prioritas Pekerjaan Selanjutnya (Berurutan)
1. **[Afandi]** Tunggu docker selesai restart → migrate order service ke PostgreSQL → hubungkan ke Hasura Console.
2. **[Vincent]** Selesaikan masalah akses GitHub (gunakan PAT token) → mulai kerjakan User Service.
3. **[Salwa, Niken]** Clone repo → setup `.env` → migrate → kerjakan service masing-masing.
4. **[Semua]** Track tabel masing-masing di Hasura setelah API selesai.
5. **[Salwa]** Gabungkan Postman Collection dari semua anggota.
6. **[Niken]** Buat Diagram Arsitektur & Laporan PDF.

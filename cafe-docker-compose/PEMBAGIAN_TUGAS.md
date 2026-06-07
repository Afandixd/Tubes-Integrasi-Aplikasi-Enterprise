# Pembagian Tugas UAS: Integrasi Aplikasi Enterprise

**Proyek:** Pengembangan Studi Kasus Sistem Terintegrasi Berbasis Microservices (Backend Only)
**Anggota Tim:**
1. Afandi (Koordinator & Order Service)
2. Vincent (User Service)
3. Salwa (Product Service)
4. Niken (Payment Service)

---

## 1. Afandi (Ketua Kelompok & Order Service)
**Fokus Repository:** `cafe-docker-compose` & `orderService`

**Tugas Utama & Cara Pengerjaan:**
- **Infrastruktur Global (docker-compose):** 
  - **Cara:** Mengedit `docker-compose.yml` menambahkan konfigurasi `init.sql` untuk men-generate 4 database secara otomatis (`user_db`, `product_db`, dll). Menambahkan *image* `hasura/graphql-engine` dan `postgres` (sebagai penampung metadata Hasura).
- **Service Development (Order):**
  - **Cara:** Masuk ke folder `orderService`, ubah file `.env` dengan `DB_DATABASE=order_db` dan `DB_HOST=cafe-db`.
  - Buat migration dan model untuk tabel `orders` (`php artisan make:model Order -m`).
  - Buat REST API di `routes/api.php` dan `OrderController` untuk transaksi pemesanan.
  - Setup tabel `orders` di Hasura UI (`http://localhost:8080`) untuk GraphQL query.

---

## 2. Vincent (User Service)
**Fokus Repository:** `userService`

**Tugas Utama & Cara Pengerjaan:**
- **Service Development (User):** 
  - **Cara Koneksi DB:** Setelah clone repo, copy `.env.example` menjadi `.env`. Ubah nilai di dalamnya menjadi `DB_HOST=cafe-db`, `DB_PORT=3306`, `DB_DATABASE=user_db`, `DB_USERNAME=root`, `DB_PASSWORD=root`.
  - **Cara Migrasi:** Jalankan perintah `docker exec -it user-service php artisan migrate` dari terminal utama untuk membuat tabel.
  - **Pembuatan API:** Buat *Controller* (`php artisan make:controller UserController`) untuk fitur Register, Login, atau Get User Profile. Daftarkan rutenya di `routes/api.php`.
  - **GraphQL:** Pastikan tabel `users` sudah kamu *track* di dalam Dashboard Hasura (port 8080) agar bisa di-query pakai GraphQL.
- **Dokumentasi API:** 
  - **Cara:** Buka Postman, buat *Collection* bernama `User Service`. Masukkan semua URL endpoint yang sudah kamu buat beserta contoh *request body*-nya. Jika sudah, klik kanan pada *Collection*, pilih **Export** (pilih v2.1), lalu kirim file `.json` tersebut ke **Salwa**.

---

## 3. Salwa (Product Service)
**Fokus Repository:** `productService`

**Tugas Utama & Cara Pengerjaan:**
- **Service Development (Product):**
  - **Cara Koneksi DB:** Sama seperti Vincent, atur `.env` dengan `DB_DATABASE=product_db` dan `DB_HOST=cafe-db`.
  - **Pembuatan API:** Buat REST API untuk CRUD Produk (Create, Read, Update, Delete) di `ProductController`.
  - **RabbitMQ (Producer):** Di bagian kode fungsi *Checkout/Buy*, tambahkan kode *publisher* RabbitMQ (menggunakan package `php-amqplib` atau `rabbitmq-laravel`) untuk mengirim pesan antrean ke *Payment Service*.
  - **GraphQL:** Pastikan tabel `products` sudah kamu *track* di Dashboard Hasura agar bisa di-query via GraphQL.
- **Tugas Ekstra (Integrasi Postman):**
  - **Cara:** Kumpulkan file `.json` Postman dari Afandi, Vincent, dan Niken.
  - Di Postman kamu, klik **Import** dan masukkan semua file tersebut.
  - Gabungkan semuanya ke dalam 1 *Master Collection* bernama `Cafe Microservices API` yang berisi folder-folder (User, Product, Order, Payment).
  - Ekspor hasil akhirnya dan berikan link/file JSON-nya ke **Niken**.

---

## 4. Niken (Payment Service)
**Fokus Repository:** `paymentService`

**Tugas Utama & Cara Pengerjaan:**
- **Service Development (Payment):**
  - **Cara Koneksi DB:** Atur `.env` dengan `DB_DATABASE=payment_db` dan `DB_HOST=cafe-db`.
  - **Pembuatan API:** Buat API untuk status pembayaran.
  - **RabbitMQ (Consumer):** Buat worker/job (`php artisan make:job ProcessPayment`) yang terus mendengarkan *(listen)* antrean *(queue)* dari `productService` atau `orderService`. Ubah status pembayaran di database ketika pesan diterima dari RabbitMQ.
  - **GraphQL:** Track tabel `payments` di Hasura.
- **Tugas Ekstra (Dokumentasi Arsitektur & PDF):** 
  - **Cara Arsitektur:** Gunakan alat seperti **Draw.io** (app.diagrams.net) atau **Miro**. Gambarkan diagram kotak-kotak yang menunjukkan bagaimana Docker mengikat `cafe-db`, `rabbitmq`, `hasura`, dan ke-4 service kalian. Beri panah komunikasi antar servicenya.
  - **Cara Laporan PDF:** Buka Microsoft Word/Google Docs. Buat format laporan. Masukkan *Screenshot* Hasura, *Screenshot* Postman dari Salwa, Diagram Arsitektur, dan link-link GitHub kalian. Simpan sebagai PDF untuk diserahkan ke dosen.

---

## ⏳ Timeline & Urutan Pengerjaan (Workflow)

Pengerjaan ini dilakukan secara bertahap. Pastikan setiap anggota mengerti cara mengeksekusi fasenya:

### Fase 1: Persiapan Infrastruktur (Oleh Afandi)
- **Kapan:** Sekarang / Minggu ini.
- **Apa yang dilakukan:** 
  1. Afandi mengedit `docker-compose.yml` untuk setup Hasura dan init script 4 database.
  2. Afandi mengetes `docker-compose up -d` di PC-nya.
  3. Afandi mem-push hasilnya ke repo `cafe-docker-compose`.
- **Yang dilakukan anggota lain:** Menunggu aba-aba "Fase 1 Selesai" dari Afandi.

### Fase 2: Pengerjaan Service (Oleh Semua Anggota Bersamaan)
- **Kapan:** Setelah Fase 1 selesai (Bisa dikerjakan paralel oleh Afandi, Vincent, Salwa, Niken).
- **Langkah Kerja Harian:**
  1. Buka folder `cafe-docker-compose` di terminal, jalankan `git pull origin main` (untuk mengambil update terbaru dari Afandi).
  2. Jalankan `docker-compose up -d` untuk menghidupkan seluruh sistem.
  3. Buka folder service milik kalian sendiri (misal: `cd ../userService`).
  4. Lakukan *coding* membuat API, mengubah `.env`, dan migration.
  5. **Cara test kode:** Gunakan Postman untuk nembak `localhost:8000` (User), `localhost:8001` (Product), dsb.
  6. Jika hari itu sudah selesai *coding*, selalu jalankan: `git add .` -> `git commit -m "update fitur X"` -> `git push -u origin main`.

### Fase 3: Integrasi, Testing, & Laporan (Oleh Salwa & Niken)
- **Kapan:** Menjelang minggu pengumpulan (Minggu 15 / 16).
- **Langkah Kerja:**
  1. Pastikan semua *push* GitHub setiap anggota sudah 100% beres.
  2. **Salwa** menagih ekspor `.json` Postman dari grup, merapikannya, dan menyerahkannya ke Niken.
  3. **Niken** men-screenshot hasil Postman, memastikan Hasura Console di `localhost:8080` terlihat rapi, menggambar arsitektur diagram, lalu menyusun PDF-nya.
  4. Semua anggota melakukan ujicoba presentasi secara offline.

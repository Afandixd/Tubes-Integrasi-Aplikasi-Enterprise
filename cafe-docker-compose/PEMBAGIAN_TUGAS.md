# Pembagian Tugas UAS: Integrasi Aplikasi Enterprise

**Proyek:** Pengembangan Studi Kasus Sistem Terintegrasi Berbasis Microservices (Backend Only)

**Anggota Tim & Fokus Service:**
1. **Afandi** (Ketua / Koordinator) - `orderService` & `cafe-docker-compose`
2. **Vincent** - `userService`
3. **Salwa** - `productService`
4. **Niken** - `paymentService`

---

## Rincian Tugas & Panduan Pengerjaan per Anggota

### 1. Afandi (Ketua & Order Service)
- **Tugas Infrastruktur (SELESAI):** 
  - *Cara Pengerjaan:* Memodifikasi file `docker-compose.yml`, menambahkan `init.sql` untuk men-generate 4 database secara otomatis (`user_db`, `product_db`, dll), dan menyalakan Hasura GraphQL Engine di port 8080.
- **Tugas Service (Order Service):** 
  - *Cara Pengerjaan:* 
    1. Pastikan koneksi DB di `.env` (di dalam `orderService`) mengarah ke `DB_DATABASE=order_db` dan `DB_HOST=cafe-db`.
    2. Jalankan `docker exec -it order-service php artisan migrate` untuk testing tabel.
    3. Buat *Controller* (`OrderController`) untuk menangani logika REST API pemesanan barang.
    4. Sambungkan Hasura (di `http://localhost:8080`) ke MySQL `order_db` agar memiliki fitur GraphQL.

---

### 2. Vincent (User Service)
- **Tugas Utama:** Mengerjakan REST API & GraphQL untuk fitur-fitur yang berkaitan dengan *User* (seperti Login, Register, Profile).
- **Cara Pengerjaan (Langkah Teknis):**
  1. **Koneksi Database:** Masuk ke folder `userService`, *copy* file `.env.example` menjadi `.env`. Ubah nilai di dalamnya menjadi: `DB_HOST=cafe-db`, `DB_PORT=3306`, `DB_DATABASE=user_db`, `DB_USERNAME=root`, `DB_PASSWORD=root`.
  2. **Migrasi:** Buka terminal di luar folder, jalankan `docker exec -it user-service php artisan migrate` untuk mengirim struktur tabel ke database `user_db`.
  3. **Buat REST API:** Buat Controller dengan perintah `php artisan make:controller UserController`. Daftarkan *routing*-nya di file `routes/api.php`.
  4. **Postman:** Buka aplikasi Postman, tes semua endpoint (URL) yang sudah dibuat. Simpan semua tes tersebut dalam satu *Collection* bernama "User Service", klik kanan Export ke format `.json`, lalu kirim file tersebut ke Salwa.
  5. **GraphQL:** Sama seperti Afandi, *track* tabel `users` di dashboard Hasura agar bisa di-query menggunakan GraphQL.

---

### 3. Salwa (Product Service & Dokumentasi API)
- **Tugas Utama:** Mengerjakan REST API Product, GraphQL Product, mengatur *Publisher* RabbitMQ, dan menyusun Master Collection Postman.
- **Cara Pengerjaan (Langkah Teknis):**
  1. **Koneksi Database:** Masuk ke folder `productService`, atur file `.env` dengan `DB_HOST=cafe-db` dan `DB_DATABASE=product_db`.
  2. **Migrasi:** Jalankan `docker exec -it product-service php artisan migrate`.
  3. **Buat REST API:** Buat `ProductController` untuk operasi CRUD (Create, Read, Update, Delete) data produk.
  4. **RabbitMQ (Producer):** Pada saat proses *checkout* produk terjadi, tambahkan kode untuk mem-*publish* (mengirim) pesan antrean ke RabbitMQ (misalnya ke *Payment Service* agar pembayarannya diproses).
  5. **Tugas Postman (Penting!):** Minta file ekspor `.json` Postman dari Vincent, Afandi, dan Niken. Import semuanya ke Postman-mu, satukan ke dalam sebuah koleksi besar bernama **"Cafe Microservices API"**. Export koleksi akhir tersebut dan berikan ke Niken untuk dimasukkan ke laporan.

---

### 4. Niken (Payment Service & Laporan Akhir)
- **Tugas Utama:** Mengerjakan REST API Payment, GraphQL Payment, mengatur *Consumer* RabbitMQ, dan menyusun Dokumen PDF Final & Diagram.
- **Cara Pengerjaan (Langkah Teknis):**
  1. **Koneksi Database:** Masuk ke folder `paymentService`, atur file `.env` dengan `DB_HOST=cafe-db` dan `DB_DATABASE=payment_db`.
  2. **Migrasi:** Jalankan `docker exec -it payment-service php artisan migrate`.
  3. **RabbitMQ (Consumer):** Buat worker/job (`php artisan make:job ProcessPayment`) yang tugasnya *standby* dan mendengarkan antrean dari RabbitMQ. Jika ada pesan masuk dari Salwa/Afandi, ubah status pembayaran di database menjadi "Sukses".
  4. **Diagram Arsitektur:** Buka web seperti `app.diagrams.net` (Draw.io). Gambarkan kotak-kotak arsitektur sistem kita (Docker, MySQL, RabbitMQ, Hasura, dan 4 microservices kalian). Tarik garis panah yang menunjukkan bagaimana mereka saling berkomunikasi.
  5. **Laporan PDF:** Buat dokumen di Word/Google Docs. Masukkan screenshot Postman, screenshot Hasura, diagram arsitektur yang barusan dibuat, dan cantumkan link GitHub kelompok. Ekspor ke format PDF untuk dinilai oleh dosen.

---

## Urutan Pengerjaan Tim (Workflow)

- **Fase 1 (Selesai):** Afandi mensetup infrastruktur Docker.
- **Fase 2 (Sedang Berjalan):** Semua anggota menarik (pull) update Docker, melakukan *setup* koneksi `.env` ke database masing-masing, melakukan migrasi `php artisan migrate`, lalu **mengerjakan service-nya secara bersamaan (paralel)** tanpa perlu saling tunggu.
- **Fase 3 (Akhir):** Setelah semua service selesai dikoding, Salwa merapikan Postman, dan Niken menyatukan laporan PDF untuk dikumpulkan ke dosen.

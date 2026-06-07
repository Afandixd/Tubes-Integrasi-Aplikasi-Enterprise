# Order Service & Hasura Setup - Afandi

**Status:** ✅ 100% SELESAI  
**Last Updated:** 7 Juni 2026  
**Author:** Afandi (Ketua Kelompok)

---

## 📋 Ringkasan Tugas yang Sudah Selesai

### ✅ Infrastruktur (Docker & Database)
- [x] Setup `docker-compose.yml` dengan 9 containers
- [x] Konfigurasi MySQL multi-database (user_db, product_db, payment_db)
- [x] Konfigurasi PostgreSQL untuk Order Service (order_db)
- [x] Setup Hasura GraphQL Engine
- [x] Setup RabbitMQ Message Broker
- [x] Network isolation dengan `cafe-network`

### ✅ Order Service (REST API)
- [x] Migrasi database order_db ke PostgreSQL
- [x] 3 REST API Endpoints:
  - `POST /api/orders` - Create order
  - `GET /api/orders` - List all orders
  - `GET /api/orders/{id}` - Get order by ID
- [x] RabbitMQ Producer (dispatch job ke payment_queue)

### ✅ Hasura GraphQL
- [x] Koneksi Hasura ke order-postgres-db
- [x] Track tabel `orders`
- [x] Track tabel `users` (untuk relasi)
- [x] Test GraphQL queries
- [x] Dokumentasi lengkap queries & mutations

### ✅ Dokumentasi
- [x] Postman Collection (REST + GraphQL)
- [x] GraphQL Queries documentation
- [x] Setup script untuk Hasura

---

## 🚀 Quick Start

### 1. Clone & Setup
```bash
# Clone repo pusat
git clone https://github.com/Final-Project-IAE/cafe-docker-compose.git
cd cafe-docker-compose

# Clone order service (jika belum)
git clone https://github.com/Final-Project-IAE/orderService.git

# Start semua services
docker-compose up -d --build
```

### 2. Verify Containers
```bash
docker ps
```

Expected output: 9 containers running
- cafe-db (MySQL)
- order-postgres-db (PostgreSQL)
- hasura-metadata-db (PostgreSQL)
- cafe-hasura (Hasura GraphQL)
- cafe-rabbitmq (RabbitMQ)
- user-service, product-service, order-service, payment-service

### 3. Setup Hasura (Otomatis)
```bash
cd cafe-docker-compose
./setup-hasura.sh
```

Script ini akan:
- Tambahkan database `order_db` ke Hasura
- Track tabel `orders`
- Track tabel `users`

---

## 🌐 Akses Services

| Service | URL | Credentials |
|---------|-----|-------------|
| **Order Service API** | http://localhost:8002 | - |
| **Hasura Console** | http://localhost:8080 | Admin Secret: `myadminsecretkey` |
| **RabbitMQ Management** | http://localhost:15672 | User: `guest` / Pass: `guest` |
| **MySQL (cafe-db)** | localhost:3307 | User: `root` / Pass: `root` |
| **PostgreSQL (order-db)** | localhost:5432 | User: `order_user` / Pass: `order_password` |

---

## 📡 Order Service REST API

### Base URL
```
http://localhost:8002/api
```

### Endpoints

#### 1. Get All Orders
```bash
curl http://localhost:8002/api/orders
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 5,
      "product_id": 2,
      "status": "done",
      "created_at": null,
      "updated_at": null
    }
  ]
}
```

#### 2. Get Order by ID
```bash
curl http://localhost:8002/api/orders/1
```

#### 3. Create New Order
```bash
curl -X POST http://localhost:8002/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 10,
    "product_id": 5,
    "status": "pending"
  }'
```

Response:
```json
{
  "success": true,
  "message": "Order created and dispatched to queue",
  "data": {
    "id": 3,
    "user_id": 10,
    "product_id": 5,
    "status": "pending"
  }
}
```

**Note:** Saat create order, job otomatis di-dispatch ke RabbitMQ queue `payment_queue`

---

## 🎯 Hasura GraphQL

### Akses Console
1. Buka browser: http://localhost:8080
2. Masukkan admin secret: `myadminsecretkey`
3. Klik tab "API" untuk testing

### Contoh Query

#### Get All Orders
```graphql
query GetAllOrders {
  orders {
    id
    user_id
    product_id
    status
    created_at
    updated_at
  }
}
```

#### Get Order by ID
```graphql
query GetOrderById {
  orders(where: {id: {_eq: 1}}) {
    id
    user_id
    product_id
    status
  }
}
```

#### Create Order (Mutation)
```graphql
mutation CreateOrder {
  insert_orders_one(object: {
    user_id: 15,
    product_id: 8,
    status: "pending"
  }) {
    id
    status
    created_at
  }
}
```

#### Count Total Orders
```graphql
query CountOrders {
  orders_aggregate {
    aggregate {
      count
    }
  }
}
```

### Test via cURL
```bash
curl -X POST http://localhost:8080/v1/graphql \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: myadminsecretkey" \
  -d '{"query": "{ orders { id user_id product_id status } }"}'
```

---

## 🐰 RabbitMQ Integration

### Producer (Order Service)
Saat order dibuat via `POST /api/orders`, job otomatis di-dispatch ke RabbitMQ:

```php
// Di OrderController.php
use App\Jobs\ProcessOrder;

public function store(Request $request) {
    $order = Order::create($request->all());
    
    // Dispatch ke RabbitMQ
    ProcessOrder::dispatch($order);
    
    return response()->json([
        'success' => true,
        'message' => 'Order created and dispatched to queue',
        'data' => $order
    ], 201);
}
```

### Consumer (Payment Service - Niken)
Payment Worker akan consume queue `payment_queue`:

```bash
# Worker sudah running di container payment-worker
docker logs -f payment-worker
```

### Check Queue Status
1. Buka RabbitMQ Management: http://localhost:15672
2. Login: `guest` / `guest`
3. Klik tab "Queues"
4. Lihat queue `payment_queue` dan `product_queue`

---

## 📂 Struktur File

```
cafe-docker-compose/
├── docker-compose.yml                    # Main orchestration
├── setup-hasura.sh                       # Hasura setup script
├── HASURA_GRAPHQL_QUERIES.md            # GraphQL documentation
├── Order_Service_Postman_Collection.json # Postman collection
├── PEMBAGIAN_TUGAS.md                   # Task division
├── PROGRESS.md                           # Progress tracking
├── README_AFANDI.md                     # This file
└── mysql-init/
    └── init.sql                          # Database initialization

orderService/ (separate repo)
├── app/
│   ├── Http/Controllers/
│   │   └── OrderController.php           # REST API
│   ├── Jobs/
│   │   └── ProcessOrder.php              # RabbitMQ job
│   └── Models/
│       └── Order.php                     # Eloquent model
├── routes/
│   └── api.php                           # API routes
├── database/
│   └── migrations/
│       └── xxxx_create_orders_table.php  # Orders schema
├── .env                                  # Environment config
└── Dockerfile                            # Container config
```

---

## 🧪 Testing

### 1. Test Docker Services
```bash
# Check all containers
docker ps

# Check logs
docker logs -f order-service
docker logs -f cafe-hasura
docker logs -f cafe-rabbitmq
```

### 2. Test Order Service API
```bash
# Health check
curl http://localhost:8002/

# Get orders
curl http://localhost:8002/api/orders

# Create order
curl -X POST http://localhost:8002/api/orders \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "product_id": 2, "status": "pending"}'
```

### 3. Test Hasura GraphQL
```bash
# Via cURL
curl -X POST http://localhost:8080/v1/graphql \
  -H "X-Hasura-Admin-Secret: myadminsecretkey" \
  -H "Content-Type: application/json" \
  -d '{"query": "{ orders { id status } }"}'

# Via Browser
# Buka http://localhost:8080 dan test di console
```

### 4. Test RabbitMQ
```bash
# Check if RabbitMQ is running
curl http://localhost:15672

# Or open in browser
open http://localhost:15672
```

---

## 📸 Screenshot untuk Dokumentasi

### Yang Sudah Saya Siapkan:
1. ✅ Docker containers (9 containers running)
2. ✅ Postman REST API testing
3. ✅ Hasura Console dashboard
4. ✅ GraphQL query results
5. ✅ RabbitMQ management console

### Cara Ambil Screenshot:
```bash
# 1. Docker containers
docker ps > docker-status.txt

# 2. Test REST API di Postman (manual screenshot)

# 3. Test GraphQL di Hasura Console (manual screenshot)

# 4. RabbitMQ queues (manual screenshot)
```

---

## 🔄 Flow Integrasi End-to-End

### Scenario: User Create Order

```
1. User (Frontend/Postman)
   ↓ POST /api/orders
   
2. Order Service (Laravel)
   ↓ Save to order_db (PostgreSQL)
   ↓ Dispatch job to RabbitMQ
   
3. RabbitMQ
   ↓ Queue: payment_queue
   
4. Payment Worker (Niken)
   ↓ Process payment
   ↓ Update payment status
   
5. (Optional) Hasura GraphQL
   ↓ Query order status
   ↓ Real-time subscription
```

### Diagram ASCII:
```
┌─────────────┐
│   Client    │
└──────┬──────┘
       │ REST API
       ↓
┌─────────────────┐      ┌──────────────┐
│  Order Service  │─────→│ PostgreSQL   │
│   (Laravel)     │      │  (order_db)  │
└────────┬────────┘      └──────────────┘
         │
         │ Dispatch Job
         ↓
┌─────────────────┐      ┌──────────────┐
│   RabbitMQ      │─────→│Payment Worker│
│ (Message Broker)│      │   (Niken)    │
└─────────────────┘      └──────────────┘
         ↑
         │ GraphQL Query
         │
┌─────────────────┐
│     Hasura      │
│  GraphQL Engine │
└─────────────────┘
```

---

## 📚 Dokumentasi Tambahan

### File yang Sudah Dibuat:
1. **HASURA_GRAPHQL_QUERIES.md** - Dokumentasi lengkap GraphQL queries & mutations
2. **Order_Service_Postman_Collection.json** - Collection untuk import ke Postman
3. **setup-hasura.sh** - Script otomatis setup Hasura
4. **README_AFANDI.md** - Dokumentasi lengkap (file ini)

### Cara Pakai Postman Collection:
1. Buka Postman
2. Import → Upload File
3. Pilih `Order_Service_Postman_Collection.json`
4. Test semua endpoint
5. Export untuk kirim ke Salwa

---

## ✅ Checklist Final

### Infrastruktur
- [x] Docker Compose running (9 containers)
- [x] MySQL running di port 3307
- [x] PostgreSQL running untuk order_db
- [x] Hasura running di port 8080
- [x] RabbitMQ running di port 5672 & 15672

### Order Service
- [x] REST API 3 endpoints working
- [x] Database migration success
- [x] RabbitMQ producer implemented
- [x] Postman collection created

### Hasura
- [x] Database order_db connected
- [x] Table orders tracked
- [x] Table users tracked
- [x] GraphQL queries tested
- [x] Documentation complete

### Dokumentasi
- [x] GraphQL queries documented
- [x] REST API documented
- [x] Postman collection ready
- [x] Setup script created
- [x] README complete

---

## 🎓 Untuk Presentasi

### Demo Flow:
1. **Tunjukkan Docker Containers**
   ```bash
   docker ps
   ```

2. **Demo REST API**
   - Buka Postman
   - Test GET /api/orders
   - Test POST /api/orders
   - Tunjukkan response

3. **Demo GraphQL (Hasura)**
   - Buka http://localhost:8080
   - Test query GetAllOrders
   - Test mutation CreateOrder
   - Tunjukkan autocomplete & documentation

4. **Tunjukkan RabbitMQ**
   - Buka http://localhost:15672
   - Tunjukkan queues
   - Tunjukkan message count

5. **Jelaskan Arsitektur**
   - Diagram integrasi
   - Flow data
   - Teknologi yang dipakai

---

## 📞 Kontak & Support

**Author:** Afandi (Ketua Kelompok)  
**GitHub Repo:** https://github.com/Final-Project-IAE/cafe-docker-compose  
**Last Updated:** 7 Juni 2026

**Tim:**
- Vincent: userService
- Salwa: productService
- Niken: paymentService

---

## 🚀 Next Steps untuk Tim

### Vincent (URGENT):
- [ ] Clone userService
- [ ] Setup .env & migrasi
- [ ] Buat REST API User
- [ ] Implementasi GraphQL Lighthouse (PENTING!)

### Salwa (URGENT):
- [ ] Clone productService
- [ ] Setup .env & migrasi
- [ ] Buat REST API Product
- [ ] Implementasi RabbitMQ Consumer

### Niken (URGENT):
- [ ] Clone paymentService
- [ ] Setup .env & migrasi
- [ ] Buat REST API Payment
- [ ] Implementasi RabbitMQ Consumer
- [ ] Buat Diagram Arsitektur
- [ ] Buat Laporan PDF Final

---

**Status Afandi: ✅ 100% SELESAI**  
**Ready for Progress Report Minggu 14!** 🎉

# 📋 Pembagian Tugas Kelompok - IAE Tugas Besar

**Nama Kelompok**: Tubes IAE  
**Ketua Kelompok**: Afandi  
**Tanggal**: 9 Juni 2026  
**Deadline Minggu 16**: Presentasi & Demo

---

## 👥 Anggota Tim & Tugasnya

### 1️⃣ **VINCENT** - User Service (REST API CRUD)

**Responsibility:**
- Mengembangkan **User Service** dengan REST API
- Implementasi CRUD operations untuk users
- Database: MySQL (`user_db`)
- Port: `8000`

**Checklist:**
- [ ] Model User lengkap dengan validasi
- [ ] Routes API:
  - [ ] `GET /api/users` - List semua users
  - [ ] `GET /api/users/{id}` - Get user by ID
  - [ ] `POST /api/users` - Create user baru
  - [ ] `PUT /api/users/{id}` - Update user
  - [ ] `DELETE /api/users/{id}` - Delete user
- [ ] Controller UserController dengan validation
- [ ] Database migration untuk users table
- [ ] Error handling & response format konsisten
- [ ] Test API dengan Postman

**Branch**: `userService`

**Dokumentasi API**:
```
BASE_URL: http://localhost:8000

GET /api/users
Response: { data: [ { id, name, email, ... } ] }

POST /api/users
Body: { name, email, password, ... }
Response: { data: { id, name, email, ... } }

GET /api/users/{id}
Response: { data: { id, name, email, ... } }

PUT /api/users/{id}
Body: { name, email, ... }
Response: { data: { id, name, email, ... } }

DELETE /api/users/{id}
Response: { message: "User berhasil dihapus" }
```

---

### 2️⃣ **SASA** - Product Service (REST API CRUD)

**Responsibility:**
- Mengembangkan **Product Service** dengan REST API
- Implementasi CRUD operations untuk products
- Menangani stock management
- Database: MySQL (`product_db`)
- Port: `8001`
- Consume RabbitMQ queue untuk stock updates

**Checklist:**
- [ ] Model Product lengkap dengan stock management
- [ ] Routes API:
  - [ ] `GET /api/products` - List semua products
  - [ ] `GET /api/products/{id}` - Get product by ID
  - [ ] `POST /api/products` - Create product baru
  - [ ] `PUT /api/products/{id}` - Update product
  - [ ] `DELETE /api/products/{id}` - Delete product
  - [ ] `PUT /api/products/{id}/update-stock` - Update stock
- [ ] Controller ProductController dengan validation
- [ ] Database migration untuk products table (dengan stock column)
- [ ] RabbitMQ consumer untuk `UpdateProductStock` job
- [ ] Queue worker: `product-worker` (sudah di docker-compose)
- [ ] Error handling & response format konsisten
- [ ] Test API dengan Postman

**Branch**: `productService`

**Dokumentasi API**:
```
BASE_URL: http://localhost:8001

GET /api/products
Response: { data: [ { id, name, price, stock, ... } ] }

POST /api/products
Body: { name, price, stock, description, ... }
Response: { data: { id, name, price, stock, ... } }

GET /api/products/{id}
Response: { data: { id, name, price, stock, ... } }

PUT /api/products/{id}
Body: { name, price, stock, ... }
Response: { data: { id, name, price, stock, ... } }

DELETE /api/products/{id}
Response: { message: "Product berhasil dihapus" }

PUT /api/products/{id}/update-stock
Body: { quantity }  // Dikurangi dari stock
Response: { data: { id, name, stock: stock-quantity, ... } }
```

**RabbitMQ Integration:**
- Consume `UpdateProductStock` job dari `product_queue`
- Execute stock update otomatis

---

### 3️⃣ **AFANDI** - Order Service (GraphQL + REST API)

**Responsibility** (Sebagai Ketua):
- Mengembangkan **Order Service** dengan GraphQL & REST API
- Orchestrate order flow antar services
- Implementasi GraphQL Hasura
- Database: PostgreSQL (`order_db`)
- Port: `8002`
- Publish events ke RabbitMQ untuk stock & payment updates

**Checklist:**
- [ ] Model Order dengan relasi user & product
- [ ] REST API Routes:
  - [ ] `GET /api/orders` - List semua orders
  - [ ] `GET /api/orders/{id}` - Get order by ID
  - [ ] `POST /api/orders` - Create order baru
  - [ ] `PUT /api/orders/{id}` - Update order status
  - [ ] `DELETE /api/orders/{id}` - Delete order
- [ ] GraphQL Setup:
  - [ ] Hasura running di `http://localhost:8080`
  - [ ] Database `order_db` terdaftar di Hasura
  - [ ] Table `orders` di-track
  - [ ] GraphQL queries berfungsi
  - [ ] GraphQL mutations berfungsi
- [ ] Database migration untuk orders table (PostgreSQL)
- [ ] HTTP Client untuk komunikasi dengan User & Product Service
- [ ] RabbitMQ Publisher untuk:
  - [ ] `UpdateProductStock` job → `product_queue`
  - [ ] `ProcessPayment` job → `payment_queue`
- [ ] Error handling & response format konsisten
- [ ] Documentasi GraphQL schema

**Branch**: `orderService`

**Dokumentasi API**:
```
BASE_URL: http://localhost:8002

REST API:
GET /api/orders
GET /api/orders/{id}
POST /api/orders
  Body: { user_id, product_id, quantity, ... }
PUT /api/orders/{id}
DELETE /api/orders/{id}

GraphQL Endpoint:
POST http://localhost:8080/v1/graphql
Header: X-Hasura-Admin-Secret: myadminsecretkey

Query Examples:
- query { orders { id user_id product_id status } }
- mutation { insert_orders_one(object: {...}) { id } }
```

---

### 4️⃣ **NIKEN** - Payment Service (RabbitMQ Consumer)

**Responsibility:**
- Mengembangkan **Payment Service** dengan REST API
- Implementasi RabbitMQ consumer untuk payment processing
- Database: MySQL (`payment_db`)
- Port: `8003`
- Consume `ProcessPayment` job dari RabbitMQ

**Checklist:**
- [ ] Model Payment dengan relasi order
- [ ] Routes API:
  - [ ] `GET /api/payments` - List semua payments
  - [ ] `GET /api/payments/{id}` - Get payment by ID
  - [ ] `POST /api/payments` - Create payment manual
  - [ ] `PUT /api/payments/{id}` - Update payment status
  - [ ] `DELETE /api/payments/{id}` - Delete payment (optional)
- [ ] Database migration untuk payments table
- [ ] RabbitMQ Consumer:
  - [ ] Setup queue worker: `payment-worker`
  - [ ] Consume `ProcessPayment` job
  - [ ] Implement payment processing logic:
    - [ ] Validate payment data
    - [ ] Update payment status
    - [ ] Handle success/failure
- [ ] Error handling dengan retry logic
- [ ] Dead Letter Queue (DLQ) untuk failed payments
- [ ] Test dengan Postman
- [ ] Test message queue dengan RabbitMQ Management UI

**Branch**: `paymentService`

**Dokumentasi API**:
```
BASE_URL: http://localhost:8003

GET /api/payments
Response: { data: [ { id, order_id, amount, status, ... } ] }

POST /api/payments
Body: { order_id, amount, method, ... }
Response: { data: { id, order_id, amount, status: "pending", ... } }

GET /api/payments/{id}
Response: { data: { id, order_id, amount, status, ... } }

PUT /api/payments/{id}
Body: { status }
Response: { data: { id, status: "completed", ... } }
```

**RabbitMQ Integration:**
- Consume `ProcessPayment` job dari `payment_queue`
- Process payment otomatis dari Order Service

---

## 🔄 Order Flow (Integrasi Antar Service)

```
┌─────────────────────────────────────────────────────────────┐
│ 1. CLIENT membuat order via Order Service                   │
│    POST /api/orders                                          │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. ORDER SERVICE:                                            │
│    - Validate user_id via User Service (HTTP)              │
│    - Validate product_id via Product Service (HTTP)        │
│    - Save order to PostgreSQL                              │
│    - Publish UpdateProductStock to RabbitMQ               │
│    - Publish ProcessPayment to RabbitMQ                    │
└─────────────────────────────────────────────────────────────┘
         │                              │
         ▼                              ▼
    ┌─────────────┐            ┌──────────────────┐
    │ PRODUCT     │            │ PAYMENT SERVICE  │
    │ WORKER:     │            │ WORKER:          │
    │ - Consume   │            │ - Consume        │
    │   UpdateStk │            │   ProcessPayment │
    │ - Update    │            │ - Process payment│
    │   stock     │            │ - Update status  │
    └─────────────┘            └──────────────────┘
         │                              │
         ▼                              ▼
    ┌─────────────┐            ┌──────────────────┐
    │ PRODUCT DB  │            │ PAYMENT DB       │
    │ stock -= 1  │            │ status=completed │
    └─────────────┘            └──────────────────┘
```

---

## 📊 Teknologi & Tools

| Aspek | Technology |
|-------|-----------|
| **REST API Framework** | Laravel 13 (PHP) |
| **GraphQL** | Hasura GraphQL Engine v2.33.0 |
| **Database** | MySQL 8.0 (User, Product, Payment), PostgreSQL 15 (Order) |
| **Message Broker** | RabbitMQ 3-management |
| **Container** | Docker & Docker Compose |
| **Port Management** | 8000-8003 (services), 8080 (Hasura), 5672 (RabbitMQ) |

---

## 🚀 Development Workflow

### Daily Workflow:
1. **Pull latest dari repository**:
   ```bash
   git checkout [your_branch]
   git pull origin [your_branch]
   ```

2. **Develop di branch Anda**:
   ```bash
   # Edit hanya folder service Anda
   git add [your_service]/
   git commit -m "deskripsi perubahan"
   git push origin [your_branch]
   ```

3. **Test locally dengan Docker**:
   ```bash
   cd cafe-docker-compose
   docker-compose up -d
   # Test endpoint di Postman
   ```

### Weekly Check:
- Semua REST API endpoints berjalan
- RabbitMQ queue berfungsi
- Database migrations sesuai
- Error handling OK

### Minggu 16 (Final):
1. Merge semua branch ke `main`
2. Setup dokumentasi final
3. Demo & Presentasi

---

## 📝 Documentation Required

Setiap anggota harus provide:

1. **API Documentation** (Postman Collection)
   - Export dari Postman
   - Share link atau attach file

2. **Database Schema**
   - Screenshot migrations
   - ERD diagram

3. **Code Comments**
   - Dokumentasi function
   - Explanation untuk complex logic

4. **README per service**
   - Setup instruction
   - Available endpoints
   - Dependencies

---

## ✅ Submission Checklist

**Sebelum presentasi, pastikan:**

- [ ] Semua branch di-push ke GitHub
- [ ] Semua code ter-commit dengan pesan yang jelas
- [ ] Docker-compose bisa di-run tanpa error
- [ ] Semua endpoints bisa ditest
- [ ] RabbitMQ messages bisa diproses
- [ ] GraphQL queries berjalan (untuk Afandi)
- [ ] Documentasi lengkap
- [ ] README updated

---

## 🎬 Timeline

| Minggu | Task | Owner |
|--------|------|-------|
| 14-15 | Progress report + Development | Semua |
| 16 (Sebelum Demo) | Final testing + Dokumentasi | Afandi (koordinasi) |
| 16 (Demo) | Presentasi & Demo | Semua |

---

## 📞 Communication

- **Repository**: https://github.com/Afandixd/Tubes-Integrasi-Aplikasi-Enterprise
- **Branches**: userService, productService, orderService, paymentService, main
- **Updates**: Post progress di branch masing-masing
- **Issues**: Create issues di GitHub untuk bugs

---

## 🎯 Success Criteria

✅ **REST API (Vincent & Sasa)**:
- Semua CRUD endpoints working
- Error handling & validation
- Response format konsisten

✅ **GraphQL (Afandi)**:
- Hasura terhubung dengan Order DB
- GraphQL queries & mutations working
- Schema properly tracked

✅ **Message Queue (Niken)**:
- RabbitMQ consumer working
- Payment processing logic implemented
- Error handling dengan retry

✅ **Integration (Afandi - Koordinasi)**:
- Order service orchestrate semua
- HTTP calls ke User & Product service
- RabbitMQ messaging working
- Database terpisah tapi terintegrasi

✅ **Documentation**:
- Diagram arsitektur lengkap
- API documentation complete
- Presentasi smooth & clear

---

**Good luck! 🚀**

---

*Dibuat oleh: Afandi (Ketua Kelompok)*  
*Last Updated: 9 Juni 2026*

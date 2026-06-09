# GraphQL Hasura Setup Guide - IAE Tugas Besar

## 📋 Overview
Panduan lengkap untuk setup GraphQL menggunakan Hasura GraphQL Engine dengan Order Service yang menggunakan PostgreSQL.

---

## 🚀 Prasyarat

1. **Docker & Docker Compose** sudah terinstall
2. **Services sudah running**: `docker-compose up -d` dari folder `cafe-docker-compose`
3. **Order Service database sudah migrated**

---

## 🔧 Step 1: Verify Services Running

```bash
# Cek status semua containers
docker ps -a

# Expected output:
# - cafe-hasura (8080)
# - order-postgres-db (5432)
# - order-service (8002)
# - user-service (8000)
# - product-service (8001)
# - payment-service (8003)
```

---

## 🎯 Step 2: Access Hasura Console

1. Buka browser: **http://localhost:8080**
2. Klik **"Launch Console"**
3. Masukkan Admin Secret: `myadminsecretkey`

---

## 📊 Step 3: Track Order Database

### 3.1: Add Data Source (Jika belum ada)

**Via Console UI:**
1. Klik **"Data"** di sidebar
2. Klik **"Create"** → **"Connect Database"**
3. Pilih **PostgreSQL**
4. Isi form:
   - **Database Name**: `order_db`
   - **Database URL**: `postgresql://order_user:order_password@order-postgres-db:5432/order_db`
5. Klik **"Connect Database"**

**Atau via API:**
```bash
curl -X POST http://localhost:8080/v1/metadata \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: myadminsecretkey" \
  -d '{
    "type": "pg_add_source",
    "args": {
      "name": "order_db",
      "configuration": {
        "connection_info": {
          "database_url": "postgresql://order_user:order_password@order-postgres-db:5432/order_db"
        }
      }
    }
  }'
```

### 3.2: Track Tables

**Via Console UI:**
1. Klik **"Data"** → **"order_db"** → **"Schema: public"**
2. Klik **"Track"** untuk tabel `orders`
3. Setiap table yang di-track akan menjadi GraphQL type

**Atau via API:**
```bash
curl -X POST http://localhost:8080/v1/metadata \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: myadminsecretkey" \
  -d '{
    "type": "pg_track_table",
    "args": {
      "schema": "public",
      "name": "orders",
      "source": "order_db"
    }
  }'
```

---

## 🔗 Step 4: Define Relationships (Optional tapi Recommended)

Untuk menghubungkan Order dengan User dan Product dari service lain:

### 4.1: Add Remote Schema (untuk User Service)

1. Klik **"Remote Schemas"** di sidebar
2. Klik **"Add Remote Schema"**
3. Isi:
   - **Schema Name**: `user_service`
   - **GraphQL Server URL**: `http://user-service:8000/graphql`
4. Klik **"Add Remote Schema"**

---

## 📝 Step 5: Test GraphQL Queries

### 5.1: Buka GraphQL Editor

1. Klik **"API"** di sidebar
2. Klik **"Explorer"** tab

### 5.2: Test Query Dasar

**Query 1: Get All Orders**
```graphql
query {
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

**Query 2: Get Order by ID**
```graphql
query {
  orders_by_pk(id: 1) {
    id
    user_id
    product_id
    status
    created_at
  }
}
```

**Query 3: Get Orders with Filter**
```graphql
query {
  orders(where: {status: {_eq: "pending"}}) {
    id
    user_id
    product_id
    status
  }
}
```

### 5.3: Test Mutation (Create Order)

```graphql
mutation {
  insert_orders_one(object: {user_id: 1, product_id: 1, status: "pending"}) {
    id
    user_id
    product_id
    status
    created_at
  }
}
```

### 5.4: Test Mutation (Update Order)

```graphql
mutation {
  update_orders_by_pk(pk_columns: {id: 1}, _set: {status: "completed"}) {
    id
    status
    updated_at
  }
}
```

### 5.5: Test Mutation (Delete Order)

```graphql
mutation {
  delete_orders_by_pk(id: 1) {
    id
    status
  }
}
```

---

## 🔐 Step 6: Authentication & Authorization (Optional)

Untuk production, setup JWT authentication:

1. Di **Hasura Console** → **Settings** → **Env vars**
2. Tambahkan env var untuk JWT:
   ```
   HASURA_GRAPHQL_JWT_SECRET={"type":"HS256","key":"your-secret-key"}
   ```

---

## 📡 Step 7: Access GraphQL dari Client

### 7.1: Via cURL
```bash
curl -X POST http://localhost:8080/v1/graphql \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: myadminsecretkey" \
  -d '{
    "query": "{ orders { id user_id product_id status } }"
  }'
```

### 7.2: Via Postman
1. Create new **POST** request
2. URL: `http://localhost:8080/v1/graphql`
3. Headers:
   - `Content-Type: application/json`
   - `X-Hasura-Admin-Secret: myadminsecretkey`
4. Body (raw JSON):
   ```json
   {
     "query": "{ orders { id user_id product_id status } }"
   }
   ```

### 7.3: Via Laravel (Apollo Client)

Dalam Laravel, gunakan HTTP client untuk query GraphQL:

```php
use Illuminate\Support\Facades\Http;

// Query orders via GraphQL
$response = Http::post('http://localhost:8080/v1/graphql', [
    'query' => '{ orders { id user_id product_id status } }',
], [
    'X-Hasura-Admin-Secret' => 'myadminsecretkey'
]);

$orders = $response->json('data.orders');
```

---

## 🐛 Troubleshooting

### Issue 1: "Database not connected"
**Solusi:**
```bash
# Verify PostgreSQL connection
docker exec order-postgres-db psql -U order_user -d order_db -c "SELECT * FROM orders;"

# Atau dari Hasura console, test connection di Data → order_db
```

### Issue 2: "Table not appearing"
**Solusi:**
1. Run migration terlebih dahulu:
   ```bash
   docker exec order-service php artisan migrate
   ```
2. Reload Hasura console (Ctrl+R)

### Issue 3: "GraphQL Server Error"
**Solusi:**
```bash
# Check Hasura logs
docker logs cafe-hasura

# Restart Hasura
docker restart cafe-hasura
```

---

## ✅ Checklist Implementasi

- [ ] Hasura running di http://localhost:8080
- [ ] Database `order_db` terdaftar di Hasura
- [ ] Tabel `orders` sudah di-track
- [ ] GraphQL query bisa dijalankan via console
- [ ] Test CRUD mutations berhasil
- [ ] Integration dengan Order Service berjalan

---

## 📚 Dokumentasi Tambahan

- **Hasura Docs**: https://hasura.io/docs
- **GraphQL Tutorial**: https://graphql.org/learn/
- **GraphQL Best Practices**: https://graphql.org/learn/best-practices/

---

## 🎬 Next Steps

1. ✅ Setup GraphQL Hasura (sekarang)
2. ⏳ Implementasi GraphQL di Order Service endpoints
3. ⏳ Integrasi GraphQL dengan User & Product Services
4. ⏳ Setup JWT authentication
5. ⏳ Dokumentasi final untuk presentasi

---

**Author**: Afandi (Ketua Kelompok)  
**Date**: 9 Juni 2026  
**Status**: In Progress ⏳

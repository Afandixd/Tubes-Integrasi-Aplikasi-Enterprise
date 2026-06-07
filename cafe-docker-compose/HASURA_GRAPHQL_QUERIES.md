# Hasura GraphQL Queries - Order Service

**Author:** Afandi  
**Last Updated:** 7 Juni 2026  
**Hasura Console:** http://localhost:8080  
**Admin Secret:** `myadminsecretkey`

---

## 🚀 Quick Start

### Akses Hasura Console:
1. Buka browser: `http://localhost:8080`
2. Masukkan admin secret: `myadminsecretkey`
3. Klik tab **"API"** untuk testing query

---

## 📋 Database Sources

| Source Name | Database Type | Connection |
|------------|---------------|------------|
| `order_db` | PostgreSQL | `order-postgres-db:5432` |
| `default` | PostgreSQL | `hasura-metadata-db:5432` (metadata) |

---

## 📊 Tracked Tables

### Order Database (order_db):
- ✅ `orders` - Tabel utama untuk data order
- ✅ `users` - Tabel user (untuk relasi)

---

## 🔍 GraphQL Queries

### 1. Get All Orders
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

**Response:**
```json
{
  "data": {
    "orders": [
      {
        "id": 1,
        "user_id": 5,
        "product_id": 2,
        "status": "done",
        "created_at": null,
        "updated_at": null
      },
      {
        "id": 2,
        "user_id": 20,
        "product_id": 21,
        "status": "test",
        "created_at": null,
        "updated_at": null
      }
    ]
  }
}
```

---

### 2. Get Order by ID
```graphql
query GetOrderById {
  orders(where: {id: {_eq: 1}}) {
    id
    user_id
    product_id
    status
    created_at
    updated_at
  }
}
```

**Response:**
```json
{
  "data": {
    "orders": [
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
}
```

---

### 3. Get Orders by Status
```graphql
query GetOrdersByStatus {
  orders(where: {status: {_eq: "done"}}) {
    id
    user_id
    product_id
    status
    created_at
  }
}
```

---

### 4. Get Orders by User ID
```graphql
query GetOrdersByUser {
  orders(where: {user_id: {_eq: 5}}) {
    id
    user_id
    product_id
    status
    created_at
  }
}
```

---

### 5. Get Orders with Pagination
```graphql
query GetOrdersWithPagination {
  orders(limit: 10, offset: 0, order_by: {id: desc}) {
    id
    user_id
    product_id
    status
    created_at
  }
}
```

---

### 6. Count Total Orders
```graphql
query CountOrders {
  orders_aggregate {
    aggregate {
      count
    }
  }
}
```

**Response:**
```json
{
  "data": {
    "orders_aggregate": {
      "aggregate": {
        "count": 2
      }
    }
  }
}
```

---

## ✏️ GraphQL Mutations

### 1. Insert New Order
```graphql
mutation CreateOrder {
  insert_orders_one(object: {
    user_id: 10,
    product_id: 5,
    status: "pending"
  }) {
    id
    user_id
    product_id
    status
    created_at
  }
}
```

---

### 2. Update Order Status
```graphql
mutation UpdateOrderStatus {
  update_orders(
    where: {id: {_eq: 1}},
    _set: {status: "completed"}
  ) {
    affected_rows
    returning {
      id
      status
      updated_at
    }
  }
}
```

---

### 3. Delete Order
```graphql
mutation DeleteOrder {
  delete_orders(where: {id: {_eq: 3}}) {
    affected_rows
  }
}
```

---

## 🔗 Advanced Queries (Dengan Relasi)

### Get Orders dengan User Info (Jika sudah setup relationship)
```graphql
query GetOrdersWithUserInfo {
  orders {
    id
    product_id
    status
    user {
      id
      name
      email
    }
  }
}
```

**Note:** Relationship harus disetup dulu di Hasura Console:
1. Buka tab "Data"
2. Pilih tabel "orders"
3. Klik "Relationships"
4. Add relationship `user` → `users.id = orders.user_id`

---

## 🧪 Testing via cURL

### Query via cURL:
```bash
curl -X POST http://localhost:8080/v1/graphql \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: myadminsecretkey" \
  -d '{
    "query": "{ orders { id user_id product_id status } }"
  }'
```

### Mutation via cURL:
```bash
curl -X POST http://localhost:8080/v1/graphql \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: myadminsecretkey" \
  -d '{
    "query": "mutation { insert_orders_one(object: {user_id: 15, product_id: 3, status: \"pending\"}) { id status } }"
  }'
```

---

## 📸 Screenshot untuk Dokumentasi

### Yang Perlu di-Screenshot:
1. **Hasura Console Dashboard** (http://localhost:8080)
2. **Tab "Data"** - menunjukkan tracked tables
3. **Tab "API"** - hasil query GetAllOrders
4. **Tab "API"** - hasil mutation CreateOrder
5. **GraphiQL Explorer** - menunjukkan schema autocomplete

---

## 🔐 Security Notes

**PENTING untuk Production:**
- ❌ Jangan gunakan admin secret yang sederhana seperti `myadminsecretkey`
- ✅ Gunakan environment variable untuk admin secret
- ✅ Setup role-based access control (RBAC)
- ✅ Enable HTTPS
- ✅ Setup authentication via JWT/webhook

**Untuk Development (sekarang):**
- ✅ Admin secret sudah aktif
- ✅ Console hanya accessible di localhost
- ✅ Cukup aman untuk testing lokal

---

## 📚 Resources

- **Hasura Docs:** https://hasura.io/docs/latest/index/
- **GraphQL Tutorial:** https://hasura.io/learn/graphql/intro-graphql/introduction/
- **Hasura Queries:** https://hasura.io/docs/latest/queries/postgres/index/
- **Hasura Mutations:** https://hasura.io/docs/latest/mutations/postgres/index/

---

## ✅ Checklist Setup Hasura (SELESAI)

- ✅ Container Hasura running di port 8080
- ✅ Database `order_db` (PostgreSQL) terkoneksi
- ✅ Tabel `orders` sudah di-track
- ✅ Tabel `users` sudah di-track
- ✅ Query GraphQL berhasil ditest
- ✅ Dokumentasi query lengkap
- ✅ Siap untuk demo presentasi

---

**Status:** ✅ SELESAI  
**Next Step:** Export Postman Collection untuk Order Service REST API

#!/bin/bash

# Script untuk setup Hasura GraphQL Engine
# Author: Afandi
# Date: 7 Juni 2026

HASURA_URL="http://localhost:8080"
ADMIN_SECRET="myadminsecretkey"

echo "=========================================="
echo "Setup Hasura GraphQL Engine"
echo "=========================================="
echo ""

# Step 1: Add Order PostgreSQL Database
echo "Step 1: Menambahkan Order Database (PostgreSQL)..."
curl -X POST \
  "${HASURA_URL}/v1/metadata" \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: ${ADMIN_SECRET}" \
  -d '{
    "type": "pg_add_source",
    "args": {
      "name": "order_db",
      "configuration": {
        "connection_info": {
          "database_url": "postgresql://order_user:order_password@order-postgres-db:5432/order_db",
          "pool_settings": {
            "max_connections": 50,
            "idle_timeout": 180
          }
        }
      }
    }
  }'
echo ""
echo "✅ Order Database berhasil ditambahkan!"
echo ""

# Step 2: Track Orders Table
echo "Step 2: Track tabel 'orders'..."
curl -X POST \
  "${HASURA_URL}/v1/metadata" \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: ${ADMIN_SECRET}" \
  -d '{
    "type": "pg_track_table",
    "args": {
      "source": "order_db",
      "table": {
        "schema": "public",
        "name": "orders"
      }
    }
  }'
echo ""
echo "✅ Tabel 'orders' berhasil di-track!"
echo ""

# Step 3: Track Users Table (for foreign key relationship)
echo "Step 3: Track tabel 'users' (untuk relasi)..."
curl -X POST \
  "${HASURA_URL}/v1/metadata" \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: ${ADMIN_SECRET}" \
  -d '{
    "type": "pg_track_table",
    "args": {
      "source": "order_db",
      "table": {
        "schema": "public",
        "name": "users"
      }
    }
  }'
echo ""
echo "✅ Tabel 'users' berhasil di-track!"
echo ""

echo "=========================================="
echo "Setup Hasura Selesai! 🎉"
echo "=========================================="
echo ""
echo "Silakan buka Hasura Console di:"
echo "👉 http://localhost:8080"
echo ""
echo "Login dengan:"
echo "Admin Secret: myadminsecretkey"
echo ""
echo "Coba query GraphQL ini di tab 'API':"
echo ""
echo "query {"
echo "  orders {"
echo "    id"
echo "    user_id"
echo "    product_id"
echo "    quantity"
echo "    status"
echo "    total_price"
echo "    created_at"
echo "  }"
echo "}"
echo ""

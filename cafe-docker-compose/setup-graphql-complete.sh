#!/bin/bash

# ============================================
# Hasura GraphQL Complete Setup Script
# Author: Afandi
# Date: 9 Juni 2026
# ============================================

set -e  # Exit on any error

HASURA_URL="http://localhost:8080"
ADMIN_SECRET="myadminsecretkey"
MAX_RETRIES=30
RETRY_INTERVAL=2

# Color codes untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=========================================="
echo "Hasura GraphQL Complete Setup"
echo "=========================================${NC}\n"

# Step 0: Wait for Hasura to be ready
echo -e "${YELLOW}Step 0: Menunggu Hasura siap...${NC}"
RETRY_COUNT=0
while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    if curl -s "$HASURA_URL" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Hasura siap!${NC}\n"
        break
    fi
    RETRY_COUNT=$((RETRY_COUNT + 1))
    echo "Attempt $RETRY_COUNT/$MAX_RETRIES... waiting ${RETRY_INTERVAL}s"
    sleep $RETRY_INTERVAL
done

if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
    echo -e "${RED}❌ Hasura tidak merespons setelah ${MAX_RETRIES} attempts${NC}"
    exit 1
fi

# Step 1: Add Order Database as Data Source
echo -e "${YELLOW}Step 1: Menambahkan Order PostgreSQL Database...${NC}"

DB_RESPONSE=$(curl -s -X POST \
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
  }')

if echo "$DB_RESPONSE" | grep -q "\"name\":\"order_db\""; then
    echo -e "${GREEN}✅ Database order_db berhasil ditambahkan!${NC}\n"
elif echo "$DB_RESPONSE" | grep -q "already exists"; then
    echo -e "${GREEN}✅ Database order_db sudah ada!${NC}\n"
else
    echo -e "${RED}❌ Error menambahkan database:${NC}"
    echo "$DB_RESPONSE"
    echo ""
fi

# Step 2: Track Orders Table
echo -e "${YELLOW}Step 2: Tracking table 'orders'...${NC}"

TRACK_RESPONSE=$(curl -s -X POST \
  "${HASURA_URL}/v1/metadata" \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: ${ADMIN_SECRET}" \
  -d '{
    "type": "pg_track_table",
    "args": {
      "schema": "public",
      "name": "orders",
      "source": "order_db"
    }
  }')

if echo "$TRACK_RESPONSE" | grep -q "\"table_name\":\"orders\""; then
    echo -e "${GREEN}✅ Table 'orders' berhasil di-track!${NC}\n"
elif echo "$TRACK_RESPONSE" | grep -q "already tracked"; then
    echo -e "${GREEN}✅ Table 'orders' sudah di-track!${NC}\n"
else
    echo -e "${RED}⚠️ Warning saat tracking table:${NC}"
    echo "$TRACK_RESPONSE"
    echo ""
fi

# Step 3: Track Users Table (jika ada)
echo -e "${YELLOW}Step 3: Tracking table 'users' (jika ada)...${NC}"

TRACK_USERS=$(curl -s -X POST \
  "${HASURA_URL}/v1/metadata" \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: ${ADMIN_SECRET}" \
  -d '{
    "type": "pg_track_table",
    "args": {
      "schema": "public",
      "name": "users",
      "source": "order_db"
    }
  }')

if echo "$TRACK_USERS" | grep -q "\"table_name\":\"users\""; then
    echo -e "${GREEN}✅ Table 'users' berhasil di-track!${NC}\n"
elif echo "$TRACK_USERS" | grep -q "already tracked"; then
    echo -e "${GREEN}✅ Table 'users' sudah di-track!${NC}\n"
else
    echo -e "${YELLOW}⚠️ Table 'users' tidak ditemukan (mungkin belum dimigrasikan)${NC}\n"
fi

# Step 4: Test GraphQL Connection
echo -e "${YELLOW}Step 4: Testing GraphQL Connection...${NC}"

TEST_QUERY=$(curl -s -X POST \
  "${HASURA_URL}/v1/graphql" \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: ${ADMIN_SECRET}" \
  -d '{
    "query": "{ __typename }"
  }')

if echo "$TEST_QUERY" | grep -q "Query"; then
    echo -e "${GREEN}✅ GraphQL connection berhasil!${NC}\n"
else
    echo -e "${RED}❌ GraphQL connection failed:${NC}"
    echo "$TEST_QUERY"
    echo ""
fi

# Step 5: Display Available Queries
echo -e "${YELLOW}Step 5: Available GraphQL Queries${NC}"
echo -e "${BLUE}========================================${NC}"

AVAILABLE_QUERIES=$(curl -s -X POST \
  "${HASURA_URL}/v1/graphql" \
  -H "Content-Type: application/json" \
  -H "X-Hasura-Admin-Secret: ${ADMIN_SECRET}" \
  -d '{
    "query": "{ __schema { types { name fields { name } } } }"
  }')

echo "Akses GraphQL Explorer:"
echo -e "${GREEN}http://localhost:8080/console${NC}"
echo ""

# Step 6: Show Sample Queries
echo -e "${BLUE}========================================${NC}"
echo -e "${YELLOW}Sample GraphQL Queries:${NC}\n"

echo "1. Fetch All Orders:"
echo -e "${GREEN}curl -X POST http://localhost:8080/v1/graphql \\
  -H 'Content-Type: application/json' \\
  -H 'X-Hasura-Admin-Secret: myadminsecretkey' \\
  -d '{\"query\": \"{ orders { id user_id product_id status } }\"}'${NC}\n"

echo "2. Create New Order:"
echo -e "${GREEN}curl -X POST http://localhost:8080/v1/graphql \\
  -H 'Content-Type: application/json' \\
  -H 'X-Hasura-Admin-Secret: myadminsecretkey' \\
  -d '{\"query\": \"mutation { insert_orders_one(object: {user_id: 1, product_id: 1, status: \\\"pending\\\"}) { id user_id product_id status } }\"}'${NC}\n"

echo "3. Update Order Status:"
echo -e "${GREEN}curl -X POST http://localhost:8080/v1/graphql \\
  -H 'Content-Type: application/json' \\
  -H 'X-Hasura-Admin-Secret: myadminsecretkey' \\
  -d '{\"query\": \"mutation { update_orders_by_pk(pk_columns: {id: 1}, _set: {status: \\\"completed\\\"}) { id status } }\"}'${NC}\n"

# Final Summary
echo -e "${BLUE}========================================${NC}"
echo -e "${GREEN}✅ Setup Hasura GraphQL selesai!${NC}\n"

echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Buka http://localhost:8080/console"
echo "2. Login dengan admin secret: myadminsecretkey"
echo "3. Test queries menggunakan GraphQL Explorer"
echo "4. Integrate dengan Order Service endpoints"
echo ""

echo -e "${BLUE}========================================${NC}"

#!/bin/bash

# Test DataTable Endpoints (REQ-B-10)
BASE_URL="http://localhost:8000/api"

echo "🔍 Testing DataTable Endpoints (REQ-B-10)..."

# Get auth token
TOKEN=$(curl -s -X POST -H "Content-Type: application/json" \
    -d '{"phone": "08111111111", "password": "Password123"}' \
    "$BASE_URL/auth/login" | grep -o '"token":"[^"]*' | grep -o '[^"]*$')

if [ -z "$TOKEN" ]; then
    echo "❌ Failed to get auth token"
    exit 1
fi

echo "✅ Auth token obtained"

# Test 1: Schema endpoint
echo -e "\n1️⃣ Testing Schema Endpoint"
curl -s "$BASE_URL/datatables/schema?table=users" \
    -H "Authorization: Bearer $TOKEN" | jq '.'

# Test 2: Users table
echo -e "\n2️⃣ Testing Users Table"
curl -s "$BASE_URL/datatables/users?page=1&per_page=5" \
    -H "Authorization: Bearer $TOKEN" | jq '.'

# Test 3: Customers table
echo -e "\n3️⃣ Testing Customers Table"
curl -s "$BASE_URL/datatables/customers?page=1&per_page=5" \
    -H "Authorization: Bearer $TOKEN" | jq '.'

# Test 4: Bills table
echo -e "\n4️⃣ Testing Bills Table"
curl -s "$BASE_URL/datatables/bills?page=1&per_page=5" \
    -H "Authorization: Bearer $TOKEN" | jq '.'

# Test 5: Payments table
echo -e "\n5️⃣ Testing Payments Table"
curl -s "$BASE_URL/datatables/payments?page=1&per_page=5" \
    -H "Authorization: Bearer $TOKEN" | jq '.'

# Test 6: Audit Logs table
echo -e "\n6️⃣ Testing Audit Logs Table"
curl -s "$BASE_URL/datatables/audit_logs?page=1&per_page=5" \
    -H "Authorization: Bearer $TOKEN" | jq '.'

# Test 7: CSV Export
echo -e "\n7️⃣ Testing CSV Export"
curl -s "$BASE_URL/datatables/export/csv?table=users&limit=10" \
    -H "Authorization: Bearer $TOKEN" | head -n 5

# Test 8: JSON Export
echo -e "\n8️⃣ Testing JSON Export"
curl -s "$BASE_URL/datatables/export/json?table=users&limit=10" \
    -H "Authorization: Bearer $TOKEN" | jq '. | length'

echo -e "\n✅ DataTable endpoint tests completed!"

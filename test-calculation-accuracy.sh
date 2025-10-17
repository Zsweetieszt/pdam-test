#!/bin/bash

# Test specific calculation: 2R1 with 30m³ usage and 1/2" meter
# Expected result: Rp 258,500 (as shown in screenshot)

BASE_URL="http://localhost:8000/api"
TOKEN=""

echo "🧮 Testing Calculation Accuracy Against Screenshot"
echo "================================================"
echo "Testing: 2R1 - Rumah Tinggal Menengah"
echo "Meter Size: 1/2\" (wm 1/2\")"
echo "Usage: 30 m³"
echo "Expected Total: Rp 258,500"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to get auth token
login() {
    echo -e "${YELLOW}🔑 Getting authentication token...${NC}"
    
    login_response=$(curl -s -X POST "$BASE_URL/auth/login" \
        -H "Content-Type: application/json" \
        -d '{
            "email": "admin@pdam.com",
            "phone": "081234567890",
            "password": "password123"
        }')
    
    TOKEN=$(echo "$login_response" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
    
    if [ -z "$TOKEN" ]; then
        echo -e "${RED}❌ Failed to get auth token${NC}"
        echo "Login response: $login_response"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Authentication successful${NC}"
}

# Test tariff simulation
test_calculation() {
    echo -e "\n${BLUE}📊 Testing Tariff Simulation API${NC}"
    
    response=$(curl -s -X POST "$BASE_URL/tariff/simulate" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{
            "customer_group_code": "2R1",
            "usage": 30,
            "meter_size": "1/2\""
        }')
    
    echo "API Response:"
    if command -v jq &> /dev/null; then
        echo "$response" | jq '.'
    else
        echo "$response"
    fi
    
    # Extract values for comparison
    success=$(echo "$response" | grep -o '"success":true' || echo "")
    total_amount=$(echo "$response" | grep -o '"total_amount":[0-9]*' | cut -d':' -f2)
    water_charge=$(echo "$response" | grep -o '"water_charge":[0-9]*' | cut -d':' -f2)
    admin_fee=$(echo "$response" | grep -o '"admin_fee":[0-9]*' | cut -d':' -f2)
    
    echo -e "\n${YELLOW}📋 Calculation Breakdown:${NC}"
    
    if [ -n "$success" ]; then
        echo -e "${GREEN}✅ API call successful${NC}"
        
        # Verify each component
        echo "Water Charge: Rp $water_charge (Expected: Rp 251,000)"
        if [ "$water_charge" = "251000" ]; then
            echo -e "${GREEN}✅ Water charge CORRECT${NC}"
        else
            echo -e "${RED}❌ Water charge INCORRECT${NC}"
        fi
        
        echo "Admin Fee: Rp $admin_fee (Expected: Rp 7,500)"
        if [ "$admin_fee" = "7500" ]; then
            echo -e "${GREEN}✅ Admin fee CORRECT${NC}"
        else
            echo -e "${RED}❌ Admin fee INCORRECT${NC}"
        fi
        
        echo "Total Amount: Rp $total_amount (Expected: Rp 258,500)"
        if [ "$total_amount" = "258500" ]; then
            echo -e "${GREEN}✅ TOTAL CALCULATION CORRECT! 🎉${NC}"
        else
            echo -e "${RED}❌ TOTAL CALCULATION INCORRECT${NC}"
            echo "Expected: Rp 258,500"
            echo "Got: Rp $total_amount"
        fi
        
        # Extract block details if available
        echo -e "\n${BLUE}🔢 Block Breakdown:${NC}"
        if command -v jq &> /dev/null; then
            echo "$response" | jq '.data.blocks[]? | "\\(.name): \\(.usage) m³ × Rp \\(.rate) = Rp \\(.amount)"' -r
        fi
        
    else
        echo -e "${RED}❌ API call failed${NC}"
        echo "Response: $response"
    fi
}

# Verify block calculation manually
verify_manual_calculation() {
    echo -e "\n${BLUE}🔍 Manual Verification (Based on Screenshot):${NC}"
    echo "Expected breakdown for 2R1 with 30 m³:"
    echo "Blok I  : 10 m³ × Rp 7,100  = Rp  71,000"
    echo "Blok II : 10 m³ × Rp 8,500  = Rp  85,000"
    echo "Blok III: 10 m³ × Rp 9,500  = Rp  95,000"
    echo "Blok IV :  0 m³ × Rp     0  = Rp      0"
    echo "                               ─────────"
    echo "Uang Air                       = Rp 251,000"
    echo "Biaya Administrasi             = Rp   7,500"
    echo "                               ─────────"
    echo "Total Tagihan                  = Rp 258,500"
}

# Test edge cases
test_edge_cases() {
    echo -e "\n${BLUE}🧪 Testing Edge Cases:${NC}"
    
    # Test with lower usage (only 1 block)
    echo -e "\n${YELLOW}Testing 5 m³ usage (only Blok I):${NC}"
    response=$(curl -s -X POST "$BASE_URL/tariff/simulate" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{
            "customer_group_code": "2R1",
            "usage": 5,
            "meter_size": "1/2\""
        }')
    
    total_5m3=$(echo "$response" | grep -o '"total_amount":[0-9]*' | cut -d':' -f2)
    water_5m3=$(echo "$response" | grep -o '"water_charge":[0-9]*' | cut -d':' -f2)
    
    echo "Expected: 5 × 7,100 + 7,500 = 43,000"
    echo "Got: $total_5m3"
    
    if [ "$total_5m3" = "43000" ]; then
        echo -e "${GREEN}✅ 5 m³ calculation CORRECT${NC}"
    else
        echo -e "${RED}❌ 5 m³ calculation INCORRECT${NC}"
    fi
    
    # Test with higher usage (all 3 blocks + some)
    echo -e "\n${YELLOW}Testing 25 m³ usage (up to Blok III):${NC}"
    response=$(curl -s -X POST "$BASE_URL/tariff/simulate" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{
            "customer_group_code": "2R1",
            "usage": 25,
            "meter_size": "1/2\""
        }')
    
    total_25m3=$(echo "$response" | grep -o '"total_amount":[0-9]*' | cut -d':' -f2)
    
    # Expected: (10×7100) + (10×8500) + (5×9500) + 7500 = 71000 + 85000 + 47500 + 7500 = 211000
    echo "Expected: (10×7,100) + (10×8,500) + (5×9,500) + 7,500 = 211,000"
    echo "Got: $total_25m3"
    
    if [ "$total_25m3" = "211000" ]; then
        echo -e "${GREEN}✅ 25 m³ calculation CORRECT${NC}"
    else
        echo -e "${RED}❌ 25 m³ calculation INCORRECT${NC}"
    fi
}

# Main execution
main() {
    echo "Checking server connection..."
    if ! curl -s "$BASE_URL/auth/login" > /dev/null; then
        echo -e "${RED}❌ Server not reachable at $BASE_URL${NC}"
        echo "Please make sure Laravel development server is running:"
        echo "php artisan serve --host=0.0.0.0 --port=8000"
        exit 1
    fi
    
    login
    verify_manual_calculation
    test_calculation
    test_edge_cases
    
    echo -e "\n${GREEN}🏁 Calculation Verification Complete!${NC}"
    echo ""
    echo "Summary:"
    echo "- If all tests show ✅, the implementation matches the screenshot exactly"
    echo "- The tariff calculation follows the progressive block structure"
    echo "- Admin fees are correctly applied based on meter size"
}

# Run the test
main

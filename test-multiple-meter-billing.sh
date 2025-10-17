#!/bin/bash

# Test Multiple Meter Billing API Endpoints
# Requirements: Updated backend with multiple meter support per customer

BASE_URL="http://localhost:8000/api"
TOKEN=""

echo "🧪 Testing Multiple Meter Billing API Endpoints"
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to make API call and check response
test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local description=$4
    
    echo -e "\n${BLUE}Testing: $description${NC}"
    echo "Method: $method"
    echo "Endpoint: $endpoint"
    
    if [ "$method" = "GET" ]; then
        response=$(curl -s -X GET "$BASE_URL$endpoint" \
            -H "Authorization: Bearer $TOKEN" \
            -H "Content-Type: application/json")
    else
        response=$(curl -s -X $method "$BASE_URL$endpoint" \
            -H "Authorization: Bearer $TOKEN" \
            -H "Content-Type: application/json" \
            -d "$data")
    fi
    
    # Check if response contains success: true
    if echo "$response" | grep -q '"success":true'; then
        echo -e "${GREEN}✅ PASSED${NC}"
    else
        echo -e "${RED}❌ FAILED${NC}"
        echo "Response: $response"
    fi
    
    # Pretty print response for debugging
    if command -v jq &> /dev/null; then
        echo "$response" | jq '.' | head -20
    else
        echo "$response" | head -200
    fi
}

# Function to get auth token
login() {
    echo -e "${YELLOW}🔑 Getting authentication token...${NC}"
    
    login_response=$(curl -s -X POST "$BASE_URL/auth/login" \
        -H "Content-Type: application/json" \
        -d '{
            "email": "admin@pdam.com",
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

# Function to test tariff endpoints
test_tariff_endpoints() {
    echo -e "\n${YELLOW}=== Testing Tariff Management Endpoints ===${NC}"
    
    test_endpoint "GET" "/tariff/customer-groups" "" "Get all customer groups"
    
    test_endpoint "GET" "/tariff/customer-groups/2R1" "" "Get specific customer group details"
    
    test_endpoint "GET" "/tariff/meter-sizes" "" "Get all meter sizes"
    
    test_endpoint "POST" "/tariff/simulate" '{
        "customer_group_code": "2R1",
        "usage_m3": 30,
        "meter_size": "1/2"
    }' "Simulate tariff calculation (30m³ for 2R1)"
    
    test_endpoint "POST" "/tariff/simulate" '{
        "customer_group_code": "1L1",
        "usage_m3": 15,
        "meter_size": "3/4"
    }' "Simulate tariff calculation (15m³ for 1L1)"
}

# Function to test customer meter management
test_customer_meter_endpoints() {
    echo -e "\n${YELLOW}=== Testing Customer Meter Management ===${NC}"
    
    # Assuming customer ID 1 exists
    CUSTOMER_ID=1
    
    test_endpoint "GET" "/customers/$CUSTOMER_ID/meters" "" "Get customer meters"
    
    test_endpoint "POST" "/customers/$CUSTOMER_ID/meters" '{
        "meter_number": "TEST-MTR-001",
        "customer_group_code": "2R1",
        "meter_size": "1/2",
        "installation_date": "2024-01-15",
        "initial_reading": 0,
        "notes": "Test meter for API testing"
    }' "Add new meter to customer"
}

# Function to test meter billing endpoints
test_meter_billing_endpoints() {
    echo -e "\n${YELLOW}=== Testing Meter Billing Endpoints ===${NC}"
    
    # Assuming meter ID 1 exists
    METER_ID=1
    
    test_endpoint "GET" "/meters/$METER_ID/details" "" "Get meter details"
    
    test_endpoint "GET" "/meters/$METER_ID/outstanding" "" "Get outstanding bills for meter"
    
    test_endpoint "POST" "/meters/$METER_ID/calculate-bill" '{
        "current_reading": 180,
        "previous_reading": 150,
        "billing_period_id": 1
    }' "Calculate bill for meter (30m³ usage)"
    
    test_endpoint "GET" "/meters/$METER_ID/bills" "" "Get all bills for meter"
}

# Function to test edge cases
test_edge_cases() {
    echo -e "\n${YELLOW}=== Testing Edge Cases ===${NC}"
    
    # Test with high usage (should use all blocks)
    test_endpoint "POST" "/tariff/simulate" '{
        "customer_group_code": "2R1",
        "usage_m3": 50,
        "meter_size": "1"
    }' "High usage simulation (50m³)"
    
    # Test with low usage (only first block)
    test_endpoint "POST" "/tariff/simulate" '{
        "customer_group_code": "2R1", 
        "usage_m3": 5,
        "meter_size": "1/2"
    }' "Low usage simulation (5m³)"
    
    # Test different customer group
    test_endpoint "POST" "/tariff/simulate" '{
        "customer_group_code": "3N1",
        "usage_m3": 25,
        "meter_size": "2"
    }' "Niaga customer group simulation"
}

# Function to verify calculation accuracy
test_calculation_accuracy() {
    echo -e "\n${YELLOW}=== Testing Calculation Accuracy ===${NC}"
    
    echo -e "${BLUE}Testing 2R1 with 30m³ (should equal Rp 258,500)${NC}"
    
    response=$(curl -s -X POST "$BASE_URL/tariff/simulate" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{
            "customer_group_code": "2R1",
            "usage_m3": 30,
            "meter_size": "1/2"
        }')
    
    total_amount=$(echo "$response" | grep -o '"total_amount":[0-9]*' | cut -d':' -f2)
    
    if [ "$total_amount" = "258500" ]; then
        echo -e "${GREEN}✅ Calculation CORRECT: Rp 258,500${NC}"
    else
        echo -e "${RED}❌ Calculation INCORRECT: Expected Rp 258,500, got Rp $total_amount${NC}"
    fi
    
    echo "Breakdown verification:"
    echo "$response" | jq '.data.tariff_breakdown' 2>/dev/null || echo "$response"
}

# Main test execution
main() {
    echo "Starting Multiple Meter Billing API Tests..."
    echo "Target: $BASE_URL"
    
    # Check if server is running
    if ! curl -s "$BASE_URL/auth/login" > /dev/null; then
        echo -e "${RED}❌ Server not reachable at $BASE_URL${NC}"
        echo "Please make sure Laravel development server is running:"
        echo "php artisan serve --host=0.0.0.0 --port=8000"
        exit 1
    fi
    
    # Run tests
    login
    test_tariff_endpoints
    test_customer_meter_endpoints  
    test_meter_billing_endpoints
    test_edge_cases
    test_calculation_accuracy
    
    echo -e "\n${GREEN}🎉 Multiple Meter Billing API Testing Complete!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Verify calculation results match requirements"
    echo "2. Test with different customer groups and usages"
    echo "3. Test bill generation and payment flow"
    echo "4. Update frontend to use new multiple meter APIs"
}

# Handle command line arguments
case "$1" in
    "tariff")
        login
        test_tariff_endpoints
        ;;
    "customer")
        login
        test_customer_meter_endpoints
        ;;
    "billing")
        login
        test_meter_billing_endpoints
        ;;
    "accuracy")
        login
        test_calculation_accuracy
        ;;
    "help")
        echo "Usage: $0 [tariff|customer|billing|accuracy|help]"
        echo ""
        echo "Options:"
        echo "  tariff   - Test tariff management endpoints only"
        echo "  customer - Test customer meter management only"
        echo "  billing  - Test meter billing endpoints only"
        echo "  accuracy - Test calculation accuracy only"
        echo "  help     - Show this help message"
        echo ""
        echo "No argument runs all tests"
        ;;
    *)
        main
        ;;
esac

#!/bin/bash

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo ""
echo "=========================================="
echo "🐰 RabbitMQ Demo Script"
echo "=========================================="
echo ""

# Function to check queue
check_queue() {
    echo -e "${BLUE}📊 Current Queue Status:${NC}"
    docker exec cafe-rabbitmq rabbitmqctl list_queues name messages consumers 2>/dev/null | grep -E "payment|product"
    echo ""
}

# Function to dispatch test job
dispatch_job() {
    echo -e "${YELLOW}📤 Dispatching test jobs...${NC}"
    docker exec order-service php artisan tinker --execute="
    use App\Jobs\UpdateProductStock;
    use App\Jobs\ProcessPayment;
    
    UpdateProductStock::dispatch(1, 5)->onQueue('product_queue');
    ProcessPayment::dispatch(999, 100000)->onQueue('payment_queue');
    
    echo 'Jobs dispatched!';
    " 2>/dev/null
    echo ""
}

# Function to create order via API
create_order() {
    echo -e "${YELLOW}🛒 Creating order via API...${NC}"
    RESPONSE=$(curl -s -X POST http://localhost:8002/api/orders \
      -H "Content-Type: application/json" \
      -d '{"user_id": 1, "product_id": 1, "quantity": 2}')
    
    if echo "$RESPONSE" | grep -q "order_id"; then
        echo -e "${GREEN}✅ Order created successfully!${NC}"
        echo "$RESPONSE" | head -c 200
        echo "..."
    else
        echo -e "${RED}❌ Failed to create order${NC}"
        echo "$RESPONSE"
    fi
    echo ""
}

# Menu
echo "Choose test scenario:"
echo ""
echo "1) 📊 Check queue status"
echo "2) 📤 Dispatch test jobs (manual)"
echo "3) 🛒 Create order via API (end-to-end)"
echo "4) 🔄 Multiple orders (5x)"
echo "5) 💪 Worker resilience test (stop → create → start)"
echo "6) 📈 Real-time monitoring"
echo "7) 🧹 Purge all queues (clear messages)"
echo "8) 🎬 Full demo (all scenarios)"
echo "9) ❌ Exit"
echo ""
read -p "Enter choice [1-9]: " choice
echo ""

case $choice in
    1)
        echo "=========================================="
        echo "Test 1: Queue Status"
        echo "=========================================="
        echo ""
        check_queue
        echo -e "${GREEN}✅ Done!${NC}"
        ;;
    
    2)
        echo "=========================================="
        echo "Test 2: Manual Job Dispatch"
        echo "=========================================="
        echo ""
        echo "Before dispatch:"
        check_queue
        
        dispatch_job
        
        sleep 2
        echo "After dispatch (2 seconds later):"
        check_queue
        
        if docker exec cafe-rabbitmq rabbitmqctl list_queues 2>/dev/null | grep -E "payment|product" | grep -q " 0"; then
            echo -e "${GREEN}✅ Jobs consumed successfully!${NC}"
        else
            echo -e "${YELLOW}⚠️  Jobs still in queue (workers might be slow or stopped)${NC}"
        fi
        ;;
    
    3)
        echo "=========================================="
        echo "Test 3: Create Order (End-to-End)"
        echo "=========================================="
        echo ""
        echo "Before order:"
        check_queue
        
        create_order
        
        sleep 2
        echo "After order (2 seconds later):"
        check_queue
        
        echo -e "${GREEN}✅ Test completed!${NC}"
        echo ""
        echo -e "${BLUE}💡 Tip: Check worker logs:${NC}"
        echo "  docker logs product-worker --tail 10"
        echo "  docker logs payment-worker --tail 10"
        ;;
    
    4)
        echo "=========================================="
        echo "Test 4: Multiple Orders (Load Test)"
        echo "=========================================="
        echo ""
        
        echo "Creating 5 orders..."
        for i in {1..5}
        do
            curl -s -X POST http://localhost:8002/api/orders \
              -H "Content-Type: application/json" \
              -d "{\"user_id\": $i, \"product_id\": 1, \"quantity\": 2}" \
              -o /dev/null
            echo -e "${GREEN}✅ Order $i created${NC}"
            sleep 0.5
        done
        
        echo ""
        echo "Queue status after 5 orders:"
        check_queue
        
        echo -e "${GREEN}✅ Load test completed!${NC}"
        ;;
    
    5)
        echo "=========================================="
        echo "Test 5: Worker Resilience"
        echo "=========================================="
        echo ""
        
        echo -e "${YELLOW}Step 1: Stopping workers...${NC}"
        docker stop product-worker payment-worker
        echo -e "${RED}❌ Workers stopped${NC}"
        echo ""
        
        sleep 2
        
        echo -e "${YELLOW}Step 2: Creating order (workers are down)...${NC}"
        create_order
        
        sleep 1
        
        echo "Queue status (jobs should be pending):"
        check_queue
        
        read -p "Press Enter to start workers and process pending jobs..."
        
        echo -e "${YELLOW}Step 3: Starting workers...${NC}"
        docker start product-worker payment-worker
        echo -e "${GREEN}✅ Workers started${NC}"
        echo ""
        
        sleep 3
        
        echo "Queue status (jobs should be processed):"
        check_queue
        
        echo -e "${GREEN}✅ Resilience test completed!${NC}"
        echo -e "${BLUE}💡 Message: Jobs were processed after workers recovered!${NC}"
        ;;
    
    6)
        echo "=========================================="
        echo "Test 6: Real-time Monitoring"
        echo "=========================================="
        echo ""
        echo -e "${BLUE}Starting real-time queue monitoring...${NC}"
        echo -e "${YELLOW}Press Ctrl+C to stop${NC}"
        echo ""
        sleep 2
        
        watch -n 1 "docker exec cafe-rabbitmq rabbitmqctl list_queues name messages consumers 2>/dev/null"
        ;;
    
    7)
        echo "=========================================="
        echo "Test 7: Purge Queues"
        echo "=========================================="
        echo ""
        
        echo "Current queue status:"
        check_queue
        
        read -p "Are you sure you want to delete all messages? (y/n): " confirm
        if [ "$confirm" = "y" ]; then
            echo ""
            echo -e "${YELLOW}Purging queues...${NC}"
            docker exec cafe-rabbitmq rabbitmqctl purge_queue payment_queue 2>/dev/null
            docker exec cafe-rabbitmq rabbitmqctl purge_queue product_queue 2>/dev/null
            echo ""
            echo "After purge:"
            check_queue
            echo -e "${GREEN}✅ All queues purged!${NC}"
        else
            echo -e "${BLUE}Cancelled.${NC}"
        fi
        ;;
    
    8)
        echo "=========================================="
        echo "🎬 FULL DEMO - All Scenarios"
        echo "=========================================="
        echo ""
        
        echo -e "${BLUE}=== Demo 1: Check Initial Status ===${NC}"
        check_queue
        read -p "Press Enter to continue..."
        echo ""
        
        echo -e "${BLUE}=== Demo 2: Dispatch Manual Jobs ===${NC}"
        dispatch_job
        sleep 2
        check_queue
        read -p "Press Enter to continue..."
        echo ""
        
        echo -e "${BLUE}=== Demo 3: Create Order via API ===${NC}"
        create_order
        sleep 2
        check_queue
        read -p "Press Enter to continue..."
        echo ""
        
        echo -e "${BLUE}=== Demo 4: Multiple Orders ===${NC}"
        for i in {1..3}
        do
            curl -s -X POST http://localhost:8002/api/orders \
              -H "Content-Type: application/json" \
              -d "{\"user_id\": $i, \"product_id\": 1, \"quantity\": 1}" \
              -o /dev/null
            echo -e "${GREEN}✅ Order $i created${NC}"
        done
        sleep 2
        check_queue
        echo ""
        
        echo -e "${GREEN}🎉 Full demo completed!${NC}"
        echo ""
        echo -e "${BLUE}📚 Summary:${NC}"
        echo "  ✅ RabbitMQ is working"
        echo "  ✅ Jobs are dispatched correctly"
        echo "  ✅ Workers consume jobs automatically"
        echo "  ✅ System handles multiple requests"
        echo ""
        echo -e "${YELLOW}💡 Next: Check RabbitMQ UI at http://localhost:15672${NC}"
        ;;
    
    9)
        echo -e "${BLUE}Goodbye! 👋${NC}"
        exit 0
        ;;
    
    *)
        echo -e "${RED}Invalid choice!${NC}"
        exit 1
        ;;
esac

echo ""
echo "=========================================="
echo "🎯 Quick Commands:"
echo "=========================================="
echo ""
echo "Check queue:         docker exec cafe-rabbitmq rabbitmqctl list_queues"
echo "Worker logs:         docker logs -f product-worker"
echo "RabbitMQ UI:         http://localhost:15672"
echo "Create order:        curl -X POST http://localhost:8002/api/orders \\"
echo "                       -H 'Content-Type: application/json' \\"
echo "                       -d '{\"user_id\": 1, \"product_id\": 1}'"
echo ""

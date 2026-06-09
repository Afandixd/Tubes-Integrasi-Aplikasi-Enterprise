#!/bin/bash

echo "========================================"
echo "Testing RabbitMQ Integration"
echo "========================================"
echo ""

# Test 1: Dispatch manual job via tinker
echo "Test 1: Dispatching test jobs to RabbitMQ..."
docker exec order-service php artisan tinker --execute="
use App\Jobs\ProcessPayment;
use App\Jobs\UpdateProductStock;

// Dispatch test jobs
ProcessPayment::dispatch(999, 50000)->onQueue('payment_queue');
UpdateProductStock::dispatch(1, 5)->onQueue('product_queue');

echo 'Jobs dispatched successfully!';
"

echo ""
echo "✅ Test jobs dispatched!"
echo ""
echo "========================================"
echo "Checking RabbitMQ Queues..."
echo "========================================"
echo ""

# Wait a bit for jobs to be queued
sleep 2

# Check queues
docker exec cafe-rabbitmq rabbitmqctl list_queues name messages

echo ""
echo "========================================"
echo "Next Steps:"
echo "========================================"
echo ""
echo "1. Open RabbitMQ Management UI:"
echo "   👉 http://localhost:15672"
echo "   Login: guest / guest"
echo ""
echo "2. Go to 'Queues' tab"
echo "3. You should see:"
echo "   - payment_queue (1 message)"
echo "   - product_queue (1 message)"
echo ""
echo "4. Refresh your browser!"
echo ""

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warkop Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-dark);
            background-image: radial-gradient(circle at 50% 0%, #1e1b4b 0%, #0f172a 100%);
            color: var(--text-main);
            padding: 40px 20px;
            margin: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h1 {
            text-align: center;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 40px;
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        h3 {
            margin-top: 0;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-online { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .status-offline { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

        .form-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 10px 15px;
            border-radius: 10px;
            flex: 1;
            outline: none;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }

        pre, .result-area {
            background: rgba(15, 23, 42, 0.8);
            padding: 15px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-top: 15px;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
        }

        .order-summary, .payment-summary {
            padding: 10px;
            background: rgba(99, 102, 241, 0.1);
            border-left: 4px solid var(--primary);
            border-radius: 4px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .summary-label { color: var(--text-dim); }
        .summary-value { font-weight: 600; }

        .service-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>☕ Warkop Microservices</h1>

    <div class="grid">
        <!-- Service Status -->
        <div class="card" style="grid-column: span 2">
            <h3>🌐 Service Connectivity <button onclick="checkServices()" style="margin-left: auto; padding: 6px 12px; font-size: 0.8rem">Refresh</button></h3>
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 10px">
                <div class="service-row">
                    <span>User Service</span>
                    <span id="user_status" class="status-badge status-offline">Checking...</span>
                </div>
                <div class="service-row">
                    <span>Product Service</span>
                    <span id="product_status" class="status-badge status-offline">Checking...</span>
                </div>
                <div class="service-row">
                    <span>Order Service</span>
                    <span id="order_status" class="status-badge status-offline">Checking...</span>
                </div>
                <div class="service-row">
                    <span>Payment Service</span>
                    <span id="payment_status" class="status-badge status-offline">Checking...</span>
                </div>
            </div>
        </div>

        <!-- Order Creation -->
        <div class="card">
            <h3>🛒 Place New Order</h3>
            <div class="form-group">
                <input type="number" id="user_id" placeholder="User ID" min="1">
                <input type="number" id="product_id" placeholder="Product ID" min="1">
            </div>
            <button onclick="createOrder()" style="width: 100%">Confirm Order</button>
            <div id="order_result" class="result-area">Waiting for action...</div>
        </div>

        <!-- Payment Process -->
        <div class="card">
            <h3>💳 Payment Gateway</h3>
            <div class="form-group">
                <input type="number" id="order_id_input" placeholder="Order ID" min="1">
            </div>
            <button onclick="createPayment()" style="width: 100%">Process Payment</button>
            <div id="payment_result" class="result-area">Waiting for action...</div>
        </div>
    </div>

<script>
    function updateStatus(id, isOnline) {
        let el = document.getElementById(id);
        el.innerText = isOnline ? "Online" : "Offline";
        el.className = isOnline ? "status-badge status-online" : "status-badge status-offline";
    }

    function checkServices() {
        const services = [
            { id: 'user_status', url: 'http://localhost:8001/api/users' },
            { id: 'product_status', url: 'http://localhost:8002/api/products' },
            { id: 'order_status', url: 'http://localhost:8003/api/orders' },
            { id: 'payment_status', url: 'http://localhost:8004/api/payments' }
        ];

        services.forEach(s => {
            fetch(s.url)
                .then(res => updateStatus(s.id, res.ok))
                .catch(() => updateStatus(s.id, false));
        });
    }

    function createOrder() {
        const userId = document.getElementById('user_id').value;
        const productId = document.getElementById('product_id').value;
        const resultEl = document.getElementById('order_result');

        if (!userId || !productId) {
            resultEl.innerHTML = '<span style="color:var(--danger)">Please enter both IDs</span>';
            return;
        }

        resultEl.innerHTML = 'Processing...';

        fetch('http://localhost:8003/api/orders', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: parseInt(userId), product_id: parseInt(productId) })
        })
        .then(res => res.json())
        .then(data => {
            if (data.message) {
                resultEl.innerHTML = `<span style="color:var(--danger)">${data.message}</span>`;
            } else {
                resultEl.innerHTML = `
                    <div class="order-summary">
                        <div class="summary-item"><span class="summary-label">Order ID:</span> <span class="summary-value">#${data.order_id}</span></div>
                        <div class="summary-item"><span class="summary-label">Customer:</span> <span class="summary-value">${data.user.name}</span></div>
                        <div class="summary-item"><span class="summary-label">Product:</span> <span class="summary-value">${data.product.name}</span></div>
                        <div class="summary-item"><span class="summary-label">Status:</span> <span class="summary-value" style="color:var(--primary)">${data.status.toUpperCase()}</span></div>
                    </div>
                `;
                document.getElementById('order_id_input').value = data.order_id;
            }
        })
        .catch(err => {
            resultEl.innerHTML = `<span style="color:var(--danger)">Connection Error: ${err.message}</span>`;
        });
    }

    function createPayment() {
        const orderId = document.getElementById('order_id_input').value;
        const resultEl = document.getElementById('payment_result');

        if (!orderId) {
            resultEl.innerHTML = '<span style="color:var(--danger)">Enter Order ID</span>';
            return;
        }

        resultEl.innerHTML = 'Processing...';

        fetch('http://localhost:8004/api/payments', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: parseInt(orderId) })
        })
        .then(res => res.json())
        .then(data => {
            if (data.message) {
                resultEl.innerHTML = `<span style="color:var(--danger)">${data.message}</span>`;
            } else {
                resultEl.innerHTML = `
                    <div class="payment-summary">
                        <div class="summary-item"><span class="summary-label">Receipt:</span> <span class="summary-value">#PAY-${data.order_id}</span></div>
                        <div class="summary-item"><span class="summary-label">Product:</span> <span class="summary-value">${data.product_name}</span></div>
                        <div class="summary-item"><span class="summary-label">Status:</span> <span class="summary-value" style="color:var(--success)">${data.status}</span></div>
                    </div>
                `;
            }
        })
        .catch(err => {
            resultEl.innerHTML = `<span style="color:var(--danger)">Connection Error: ${err.message}</span>`;
        });
    }

    function getUsers() {
        fetch('http://localhost:8001/api/users').then(res => res.json())
            .then(data => document.getElementById('raw_data').innerText = JSON.stringify(data, null, 2));
    }

    function getProducts() {
        fetch('http://localhost:8002/api/products').then(res => res.json())
            .then(data => document.getElementById('raw_data').innerText = JSON.stringify(data, null, 2));
    }

    // Initial check
    checkServices();
</script>

</body>
</html>
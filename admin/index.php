<?php
if (!isset($conn)) {
    require_once '../Database/connection.php';
}

if (!isAdminLoggedIn()) {
    redirect('login.php');
}
$stats = [
    'total_products' => 0,
    'active_suppliers' => 0,
    'pending_orders' => 0,
    'total_customers' => 0
];

// Total produk
$result = $conn->query("SELECT COUNT(*) AS count FROM products");
if ($result) {
    $stats['total_products'] = (int)$result->fetch_assoc()['count'];
}

// Supplier aktif
$result = $conn->query("SELECT COUNT(*) AS count FROM supplier WHERE status = 1");
if ($result) {
    $stats['active_suppliers'] = (int)$result->fetch_assoc()['count'];
}

// Pesanan tertunda
$result = $conn->query("SELECT COUNT(*) AS count FROM orders WHERE order_status = 'pending'");
if ($result) {
    $stats['pending_orders'] = (int)$result->fetch_assoc()['count'];
}

// Total pelanggan
$result = $conn->query("SELECT COUNT(*) AS count FROM customers");
if ($result) {
    $stats['total_customers'] = (int)$result->fetch_assoc()['count'];
}

// Pesanan terbaru (5)
$recent_orders = [];
$result = $conn->query("
    SELECT o.order_id, o.order_cost, o.order_status, o.order_date, c.customer_name 
    FROM orders o 
    JOIN customers c ON o.customer_id = c.customer_id 
    ORDER BY o.order_date DESC 
    LIMIT 5
");
if ($result) {
    $recent_orders = $result->fetch_all(MYSQLI_ASSOC);
}

$header_data = [
    'pending_orders' => $stats['pending_orders'],
    'recent_orders' => array_slice($recent_orders, 0, 5)
];

$product_sales = [];
$result = $conn->query("
    SELECT product_name, SUM(product_quantity) AS total_sales
    FROM order_items
    GROUP BY product_name
    ORDER BY total_sales DESC
    LIMIT 10
");
if ($result) {
    $product_sales = $result->fetch_all(MYSQLI_ASSOC);
}

$order_status_distribution = [];
$result = $conn->query("
    SELECT order_status, COUNT(*) AS total
    FROM orders
    GROUP BY order_status
");
if ($result) {
    $order_status_distribution = $result->fetch_all(MYSQLI_ASSOC);
}


?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEMS Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <?php include 'Layout/sidebar.php'; ?>
    <div id="content">
        <?php include 'Layout/header.php'; ?>

        <div class="container-fluid mt-4">
            <!-- Stats Cards -->
            <div class="row">
                <?php
                $cards = [
                    ['title' => 'Total Customers', 'value' => $stats['total_customers'], 'icon' => 'fa-users', 'color' => 'primary', 'link' => 'listCustomers.php'],
                    ['title' => 'Total Products', 'value' => $stats['total_products'], 'icon' => 'fa-boxes', 'color' => 'success', 'link' => 'listProducts.php'],
                    ['title' => 'Pending Orders', 'value' => $stats['pending_orders'], 'icon' => 'fa-clock', 'color' => 'warning', 'link' => 'listOrder.php'],
                    ['title' => 'Active Suppliers', 'value' => $stats['active_suppliers'], 'icon' => 'fa-truck', 'color' => 'info', 'link' => 'listSupplier.php']
                ];

                foreach ($cards as $card): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <a href="../../admin/Pages/<?= $card['link'] ?>" class="card-link" style="text-decoration: none;">
                            <div class="card border-left-<?= $card['color'] ?> shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-<?= $card['color'] ?> text-uppercase mb-1">
                                                <?= $card['title'] ?>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= htmlspecialchars($card['value']) ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas <?= $card['icon'] ?> fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Charts -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0">Sales by Product</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="salesByProductChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h6 class="m-0">Order Status Distribution</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'Layout/footer.php'; ?>
    </div>
</div>
<!-- AI Chat Widget -->
<div class="ai-chat-container">
    <button id="aiChatToggle" class="btn btn-primary ai-chat-button">
        <i class="fas fa-robot"></i> AI Assistant
    </button>
    
    <div class="ai-chat-window">
        <div class="ai-chat-header">
            <h5>GEMS AI Assistant</h5>
            <button class="btn-close btn-close-white"></button>
        </div>
        <div class="ai-chat-body" id="aiChatMessages">
            <div class="ai-message ai-message-bot">
                <div class="ai-message-content">
                    Hello! I'm your GEMS AI assistant. How can I help you today?
                </div>
            </div>
        </div>
        <div class="ai-chat-footer">
            <input type="text" id="aiChatInput" class="form-control" placeholder="Type your question...">
            <button id="aiChatSend" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/sidebar.js"></script>
<script>
    const salesByProductData = {
        labels: <?= json_encode(array_column($product_sales, 'product_name')) ?>,
        datasets: [{
            label: 'Total Sales',
            data: <?= json_encode(array_map('intval', array_column($product_sales, 'total_sales'))) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    const orderStatusData = {
        labels: <?= json_encode(array_column($order_status_distribution, 'order_status')) ?>,
        datasets: [{
            label: 'Order Status',
            data: <?= json_encode(array_map('intval', array_column($order_status_distribution, 'total'))) ?>,
            backgroundColor: [
                'rgba(255, 205, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(153, 102, 255, 0.7)'
            ],
            borderColor: [
                'rgba(255, 205, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    };
</script>
<script src="js/script.js"></script>
<script>
    const ctxSales = document.getElementById('salesByProductChart').getContext('2d');
    const salesChart = new Chart(ctxSales, {
        type: 'bar',
        data: salesByProductData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Sales by Product' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    const ctxOrder = document.getElementById('orderStatusChart').getContext('2d');
    const orderChart = new Chart(ctxOrder, {
        type: 'doughnut',
        data: orderStatusData,
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Order Status Distribution' }
            }
        }
    });
    // AI Chat Functionality
$(document).ready(function() {
    const chatWindow = $('.ai-chat-window');
    const chatToggle = $('#aiChatToggle');
    const chatInput = $('#aiChatInput');
    const chatSend = $('#aiChatSend');
    const chatMessages = $('#aiChatMessages');
    
    // Toggle chat window
    chatToggle.click(function() {
        chatWindow.toggleClass('show');
    });
    
    // Close chat window
    $('.ai-chat-header .btn-close').click(function() {
        chatWindow.removeClass('show');
    });
    
    // Send message
    function sendMessage() {
        const message = chatInput.val().trim();
        if (message === '') return;
        
        // Add user message
        addMessage(message, 'user');
        chatInput.val('');
        
        // Show typing indicator
        const typingIndicator = addMessage('Typing...', 'bot', true);
        
        // Simulate AI response (in production, replace with actual API call)
        setTimeout(() => {
            typingIndicator.remove();
            const aiResponse = generateAIResponse(message);
            addMessage(aiResponse, 'bot');
        }, 1000);
    }
    
    // Add message to chat
    function addMessage(text, sender, isTyping = false) {
        const messageClass = sender === 'user' ? 'ai-message-user' : 'ai-message-bot';
        const messageId = isTyping ? 'typing-indicator' : '';
        
        const messageElement = $(`
            <div class="ai-message ${messageClass}" id="${messageId}">
                <div class="ai-message-content">${text}</div>
            </div>
        `);
        
        chatMessages.append(messageElement);
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
        
        return isTyping ? messageElement : null;
    }
    
    // Generate AI response (simplified - replace with actual API call)
    // Replace generateAIResponse function with this for actual API integration
async function generateAIResponse(message) {
    try {
        const response = await fetch('https://api.openai.com/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer YOUR_OPENAI_API_KEY'
            },
            body: JSON.stringify({
                model: "gpt-3.5-turbo",
                messages: [{
                    role: "system",
                    content: "You are a helpful assistant for GEMS Admin Dashboard. " +
                             "Provide concise answers about orders, products, customers and suppliers. " +
                             "Current stats: " +
                             `${<?= $stats['pending_orders'] ?>} pending orders, ` +
                             `${<?= $stats['total_products'] ?>} products, ` +
                             `${<?= $stats['total_customers'] ?>} customers, ` +
                             `${<?= $stats['active_suppliers'] ?>} active suppliers.`
                }, {
                    role: "user",
                    content: message
                }]
            })
        });
        
        const data = await response.json();
        return data.choices[0].message.content;
    } catch (error) {
        console.error("AI Error:", error);
        return "Sorry, I'm having trouble connecting to the AI service. Please try again later.";
    }
}
    
    // Send message on button click
    chatSend.click(sendMessage);
    
    // Send message on Enter key
    chatInput.keypress(function(e) {
        if (e.which === 13) {
            sendMessage();
        }
    });
});
</script>
</body>
</html>

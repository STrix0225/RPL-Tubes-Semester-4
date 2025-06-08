<?php
require_once '../../Database/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid customer ID');
}

$customer_id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    die('Customer not found');
}

// Get total amount spent by customer
$total_stmt = $conn->prepare("SELECT SUM(order_cost) AS total_spent FROM orders WHERE customer_id = ?");
$total_stmt->bind_param("i", $customer_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_spent = $total_result->fetch_assoc()['total_spent'] ?? 0;
?>

<div class="customer-detail-container">
    <div class="customer-header bg-primary text-white p-4 rounded-top">
        <div class="d-flex align-items-center">
            <div class="customer-avatar me-4">
                <?php if (!empty($customer['customer_photo'])): ?>
                    <img src="../img/Customers/<?php echo htmlspecialchars($customer['customer_photo']); ?>" 
                         alt="Customer Photo" class="img-thumbnail rounded-circle border-3 border-white shadow" 
                         style="width: 120px; height: 120px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center shadow" 
                         style="width: 120px; height: 120px;">
                        <i class="fas fa-user fa-4x"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="customer-info">
                <h2 class="mb-1"><?php echo htmlspecialchars($customer['customer_name']); ?></h2>
                <p class="mb-2"><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($customer['customer_email']); ?></p>
                <?php if (!empty($customer['customer_phone'])): ?>
                    <p class="mb-0"><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($customer['customer_phone']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="customer-body p-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Address Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Address:</strong>
                                <p class="mb-0"><?php echo !empty($customer['customer_address']) ? htmlspecialchars($customer['customer_address']) : 'N/A'; ?></p>
                            </li>
                            <li>
                                <strong>City:</strong>
                                <p class="mb-0"><?php echo !empty($customer['customer_city']) ? htmlspecialchars($customer['customer_city']) : 'N/A'; ?></p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Account Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Customer ID:</strong>
                                <p class="mb-0"><?php echo htmlspecialchars($customer['customer_id']); ?></p>
                            </li>
                            <li class="mb-2">
                                <strong>Member Since:</strong>
                                <p class="mb-0"><?php echo date('F j, Y', strtotime($customer['created_at'] ?? 'now')); ?></p>
                            </li>
                            <li>
                                <strong>Total Spent:</strong>
                                <p class="mb-0 text-success fw-bold">$<?php echo number_format($total_spent, 2); ?></p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Recent Activity</h5>
                    <span class="badge bg-primary"><?php 
                        $count_stmt = $conn->prepare("SELECT COUNT(*) AS order_count FROM orders WHERE customer_id = ?");
                        $count_stmt->bind_param("i", $customer_id);
                        $count_stmt->execute();
                        $count_result = $count_stmt->get_result();
                        echo htmlspecialchars($count_result->fetch_assoc()['order_count'] ?? 0); 
                    ?> Orders</span>
                </div>
            </div>
            <div class="card-body">
                <?php
                // Get recent orders for this customer with more details
                $order_stmt = $conn->prepare("SELECT order_id, order_cost, order_date, order_status FROM orders WHERE customer_id = ? ORDER BY order_date DESC LIMIT 5");
                $order_stmt->bind_param("i", $customer_id);
                $order_stmt->execute();
                $orders = $order_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                
                if (!empty($orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                    <td>$<?php echo number_format($order['order_cost'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $order['order_status'] === 'completed' ? 'success' : 
                                                 ($order['order_status'] === 'pending' ? 'warning' : 'secondary'); 
                                        ?>">
                                            <?php echo ucfirst(htmlspecialchars($order['order_status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No recent orders found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .customer-detail-container {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .customer-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    .customer-info h2 {
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 600;
    }
    .list-unstyled strong {
        color: #6c757d;
        font-weight: 500;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    .text-success {
        color: #28a745 !important;
    }
    .fw-bold {
        font-weight: 600 !important;
    }
</style>
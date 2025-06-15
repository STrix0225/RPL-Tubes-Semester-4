<?php
require_once '../../Database/connection.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid customer ID.</div>";
    exit();
}

$customer_id = (int)$_GET['id'];

// Get customer details
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Customer not found.</div>";
    exit();
}

$customer = $result->fetch_assoc();

// Get customer's orders
$order_stmt = $conn->prepare("
    SELECT o.*, COUNT(oi.item_id) as item_count, SUM(oi.product_price * oi.product_quantity) as total_amount
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.customer_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
");
$order_stmt->bind_param("i", $customer_id);
$order_stmt->execute();
$orders_result = $order_stmt->get_result();
$orders = $orders_result->fetch_all(MYSQLI_ASSOC);
?>

<div class="row">
    <div class="col-md-4 text-center">
        <?php if (!empty($customer['customer_photo'])): ?>
            <img src="../../Customer/gems-customer-pages/uploads/<?= htmlspecialchars($customer['customer_photo']) ?>" 
                 alt="Customer Photo" class="img-thumbnail mb-3" style="max-width: 200px;">
        <?php else: ?>
            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                 style="width: 200px; height: 200px; border-radius: 50%; margin: 0 auto 1rem;">
                <i class="fas fa-user fa-4x"></i>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-8">
        <h3 class="mb-4"><?= htmlspecialchars($customer['customer_name']) ?></h3>
        
        <div class="row mb-3">
            <div class="col-sm-6">
                <h5 class="text-muted">Email</h5>
                <p><?= htmlspecialchars($customer['customer_email']) ?></p>
            </div>
            <div class="col-sm-6">
                <h5 class="text-muted">Phone</h5>
                <p><?= htmlspecialchars($customer['customer_phone'] ?? 'N/A') ?></p>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-12">
                <h5 class="text-muted">Address</h5>
                <p>
                    <?= htmlspecialchars($customer['customer_address'] ?? 'N/A') ?><br>
                    <?= htmlspecialchars($customer['customer_city'] ?? '') ?>
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-6">
                <h5 class="text-muted">Registration Date</h5>
                <p><?= date('F j, Y', strtotime($customer['registration_date'] ?? 'now')) ?></p>
            </div>
            <div class="col-sm-6">
                <h5 class="text-muted">Customer ID</h5>
                <p><?= $customer['customer_id'] ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Order History Section -->
<div class="mt-5">
    <h4 class="mb-4">Order History</h4>
    
    <?php if (count($orders) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['order_id'] ?></td>
                            <td><?= date('M j, Y g:i A', strtotime($order['order_date'])) ?></td>
                            <td><?= $order['item_count'] ?></td>
                            <td>$<?= number_format($order['total_amount'] ?? $order['order_cost'], 2) ?></td>
                            <td>
                                <span class="badge 
                                    <?= $order['order_status'] === 'completed' ? 'badge-success' : 
                                       ($order['order_status'] === 'cancelled' ? 'badge-danger' : 'badge-warning') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $order['order_status'])) ?>
                                </span>
                            </td>
                            <td><?= ucfirst($order['payment_method'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">This customer hasn't placed any orders yet.</div>
    <?php endif; ?>
</div>

<style>
    h5.text-muted {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }
    p {
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
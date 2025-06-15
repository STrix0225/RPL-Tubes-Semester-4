<?php
require_once '../../Database/connection.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    echo "<div class='alert alert-danger m-3'>Invalid Order ID</div>";
    exit;
}

$stmt = $conn->prepare("
    SELECT o.*, c.customer_name, c.customer_email
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    WHERE o.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    echo "<div class='alert alert-danger m-3'>Order not found.</div>";
    exit;
}

$order = $order_result->fetch_assoc();

$item_stmt = $conn->prepare("
    SELECT product_name, product_quantity, product_price
    FROM order_items
    WHERE order_id = ?
");
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$items = $item_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Order #<?= htmlspecialchars($order['order_id']) ?> Details</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <h6 class="mb-3 text-muted">Customer Info</h6>
    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></li>
        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></li>
        <li class="list-group-item"><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></li>
        <li class="list-group-item"><strong>City:</strong> <?= htmlspecialchars($order['customer_city']) ?></li>
        <li class="list-group-item"><strong>Address:</strong> <?= htmlspecialchars($order['customer_address']) ?></li>
    </ul>

    <h6 class="mb-3 text-muted">Order Info</h6>
    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Date:</strong> <?= date('M d, Y H:i', strtotime($order['order_date'])) ?></li>
        <li class="list-group-item"><strong>Status:</strong>
            <span class="badge <?= match($order['order_status']) {
                'pending' => 'bg-warning',
                'processing' => 'bg-info',
                'completed' => 'bg-success',
                'cancelled' => 'bg-danger',
                default => 'bg-secondary'
            } ?>">
                <?= ucfirst($order['order_status']) ?>
            </span>
        </li>
        <li class="list-group-item"><strong>Payment:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></li>
        <li class="list-group-item"><strong>Total:</strong> $<?= number_format($order['order_cost'], 2) ?></li>
    </ul>

    <h6 class="mb-3 text-muted">Items</h6>
    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; foreach ($items as $i => $item): 
                $subtotal = $item['product_quantity'] * $item['product_price'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= $item['product_quantity'] ?></td>
                <td>$<?= number_format($item['product_price'], 2) ?></td>
                <td>$<?= number_format($subtotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="fw-bold table-light">
                <td colspan="4" class="text-end">Grand Total</td>
                <td>$<?= number_format($total, 2) ?></td>
            </tr>
        </tbody>
    </table>
</div>

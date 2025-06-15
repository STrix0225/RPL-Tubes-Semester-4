<?php
require_once '../../Database/connection.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if payment_id is provided
if (!isset($_GET['payment_id']) || !is_numeric($_GET['payment_id'])) {
    die('<div class="alert alert-danger">Invalid payment ID</div>');
}

$payment_id = (int)$_GET['payment_id'];

// Get payment details
$payment_query = "SELECT p.*, o.order_cost, o.order_status, o.order_date, 
                  c.customer_name, c.customer_email, c.customer_phone
                  FROM payments p
                  JOIN orders o ON p.order_id = o.order_id
                  JOIN customers c ON o.customer_id = c.customer_id
                  WHERE p.payment_id = ?";
$payment_stmt = $conn->prepare($payment_query);
$payment_stmt->bind_param('i', $payment_id);
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();

if ($payment_result->num_rows === 0) {
    die('<div class="alert alert-danger">Payment not found</div>');
}

$payment = $payment_result->fetch_assoc();

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = ?";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param('i', $payment['order_id']);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$order_items = $items_result->fetch_all(MYSQLI_ASSOC);
?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Payment Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Payment ID:</div>
                    <div class="col-sm-8"><?php echo htmlspecialchars($payment['payment_id']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Transaction ID:</div>
                    <div class="col-sm-8"><?php echo htmlspecialchars($payment['transaction_id']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Order ID:</div>
                    <div class="col-sm-8"><?php echo htmlspecialchars($payment['order_id']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Payment Date:</div>
                    <div class="col-sm-8"><?php echo date('M d, Y H:i', strtotime($payment['payment_date'])); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Order Status:</div>
                    <div class="col-sm-8">
                        <span class="badge 
                            <?php 
                                switch($payment['order_status']) {
                                    case 'completed': echo 'bg-success'; break;
                                    case 'processing': echo 'bg-primary'; break;
                                    case 'pending': echo 'bg-warning'; break;
                                    case 'cancelled': echo 'bg-danger'; break;
                                    default: echo 'bg-secondary';
                                }
                            ?>">
                            <?php echo ucfirst(htmlspecialchars($payment['order_status'])); ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Total Amount:</div>
                    <div class="col-sm-8">$<?php echo number_format($payment['order_cost'], 2); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Name:</div>
                    <div class="col-sm-8"><?php echo htmlspecialchars($payment['customer_name']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Email:</div>
                    <div class="col-sm-8"><?php echo htmlspecialchars($payment['customer_email']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Phone:</div>
                    <div class="col-sm-8"><?php echo htmlspecialchars($payment['customer_phone']); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Order Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td>
                            <img src="../images/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['product_quantity']); ?></td>
                        <td>$<?php echo number_format($item['product_price'] * $item['product_quantity'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="table-secondary">
                        <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                        <td class="fw-bold">$<?php echo number_format($payment['order_cost'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
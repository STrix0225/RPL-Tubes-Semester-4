<?php
require_once '../../Database/connection.php';

if (!isAdminLoggedIn()) {
    http_response_code(403);
    echo "<div class='alert alert-danger'>Access denied. Please login as admin.</div>";
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid order ID.</div>";
    exit();
}

$order_id = (int)$_GET['id'];

// Get order details with customer information
$stmt = $conn->prepare("
    SELECT o.*, c.customer_name, c.customer_email, c.customer_phone, c.customer_address, c.customer_city, c.customer_photo
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id = c.customer_id 
    WHERE o.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Order not found.</div>";
    exit();
}

$order = $result->fetch_assoc();

// Get order items
$stmt2 = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt2->bind_param("i", $order_id);
$stmt2->execute();
$items = $stmt2->get_result();

// Calculate total items
$total_items = 0;
$items_data = [];
if ($items->num_rows > 0) {
    while ($item = $items->fetch_assoc()) {
        $total_items += $item['product_quantity'];
        $items_data[] = $item;
    }
}
?>

<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="orderDetailsModalLabel">
                    <i class="fas fa-shopping-cart me-2"></i>Order Details #<?= $order_id ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <span class="status-badge status-<?= $order['order_status'] ?>">
                                <?= ucfirst($order['order_status']) ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Order Date</div>
                            <div class="info-value"><?= date('F j, Y \a\t H:i', strtotime($order['order_date'])) ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Payment Method</div>
                            <div class="info-value text-capitalize"><?= $order['payment_method'] ?? 'Not specified' ?></div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="mb-3">
                            <div class="info-label">Total Items</div>
                            <div class="info-value"><?= $total_items ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Order Total</div>
                            <div class="info-value h4 text-primary">$<?= number_format($order['order_cost'], 2) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <img src="<?= $order['customer_photo'] ?? 'https://via.placeholder.com/80?text=User' ?>" 
                                 alt="<?= htmlspecialchars($order['customer_name']) ?>" 
                                 class="customer-photo">
                            <div>
                                <h4 class="mb-1"><?= htmlspecialchars($order['customer_name']) ?></h4>
                                <p class="text-muted mb-1">Customer ID: <?= $order['customer_id'] ?></p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="info-label"><i class="fas fa-envelope me-2"></i>Email</div>
                                    <div class="info-value"><?= htmlspecialchars($order['customer_email']) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="info-label"><i class="fas fa-phone me-2"></i>Phone</div>
                                    <div class="info-value"><?= htmlspecialchars($order['customer_phone']) ?></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-0">
                                    <div class="info-label"><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</div>
                                    <div class="info-value">
                                        <?= htmlspecialchars($order['customer_address']) ?><br>
                                        <?= htmlspecialchars($order['customer_city']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="mb-3"><i class="fas fa-box-open me-2"></i>Order Items</h4>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Product</th>
                                <th width="120" class="text-end">Price</th>
                                <th width="100" class="text-center">Quantity</th>
                                <th width="120" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items_data as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($item['product_image']) ?>" 
                                             alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                             class="product-img">
                                        <div>
                                            <div class="font-weight-600"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <small class="text-muted">ID: <?= htmlspecialchars($item['product_id']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">$<?= number_format($item['product_price'], 2) ?></td>
                                <td class="text-center"><?= $item['product_quantity'] ?></td>
                                <td class="text-end">$<?= number_format($item['product_price'] * $item['product_quantity'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-center fw-bold"><?= $total_items ?></td>
                                <td class="text-end fw-bold">$<?= number_format($order['order_cost'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Print Invoice
                </button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-paper-plane me-2"></i> Email Invoice
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize the modal and show it
document.addEventListener('DOMContentLoaded', function() {
    var orderModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    orderModal.show();
    
    // Close modal when clicking outside
    document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            orderModal.hide();
        }
    });
});
</script>

<style>
.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 500;
    text-transform: capitalize;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-processing {
    background-color: #cce5ff;
    color: #004085;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}

.customer-photo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-right: 1.5rem;
}

.product-img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 1rem;
}

.info-label {
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 0.3rem;
}

.info-value {
    font-weight: 500;
    margin-bottom: 1rem;
}

.modal-xl {
    max-width: 1200px;
}
</style>
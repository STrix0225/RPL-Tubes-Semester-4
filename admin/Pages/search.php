<?php
require_once '../../Database/connection.php';

if (!isAdminLoggedIn()) {
    redirect('../login.php');
}

$query = $_GET['q'] ?? '';
$results = [];

if (!empty($query)) {
    // Search orders with customer information
    $stmt = $conn->prepare("
        SELECT o.order_id, o.order_cost, o.order_date, c.customer_name 
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.order_id LIKE ? OR c.customer_name LIKE ?
        ORDER BY o.order_date DESC 
        LIMIT 20
    ");
    $searchTerm = '%' . $query . '%';
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Search products
    $stmt = $conn->prepare("
        SELECT product_id, product_name, product_price 
        FROM products 
        WHERE product_name LIKE ? OR product_description LIKE ? 
        ORDER BY product_name 
        LIMIT 20
    ");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Search customers
    $stmt = $conn->prepare("
        SELECT customer_id, customer_name, customer_email 
        FROM customers 
        WHERE customer_name LIKE ? OR customer_email LIKE ? 
        ORDER BY customer_name 
        LIMIT 20
    ");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $customers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Include your header
include_once '../../admin/Layout/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-search me-2"></i>Search Results for "<?= htmlspecialchars($query) ?>"
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (empty($query)): ?>
                        <div class="alert alert-info">Please enter a search term</div>
                    <?php else: ?>

                    <!-- Products Results -->
                    <?php if (!empty($products)): ?>
                        <h5 class="mt-3"><i class="fas fa-box-open me-2"></i>Products</h5>
                        <div class="list-group mb-4">
                            <?php foreach ($products as $product): ?>
                                <a href="../../admin/Pages/listProducts.php?id=<?= $product['product_id'] ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($product['product_name']) ?></h6>
                                        <small>$<?= number_format($product['product_price'], 2) ?></small>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Orders Results -->
                    <?php if (!empty($orders)): ?>
                        <h5 class="mt-3"><i class="fas fa-shopping-cart me-2"></i>Orders</h5>
                        <div class="list-group mb-4">
                            <?php foreach ($orders as $order): ?>
                                <a href="../../admin/Pages/listOrder.php?id=<?= $order['order_id'] ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Order #<?= $order['order_id'] ?></h6>
                                        <small>$<?= number_format($order['order_cost'], 2) ?></small>
                                    </div>
                                    <p class="mb-1">Customer: <?= htmlspecialchars($order['customer_name']) ?></p>
                                    <small class="text-muted">Date: <?= date('M d, Y H:i', strtotime($order['order_date'])) ?></small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Customers Results -->
                    <?php if (!empty($customers)): ?>
                        <h5 class="mt-3"><i class="fas fa-users me-2"></i>Customers</h5>
                        <div class="list-group mb-4">
                            <?php foreach ($customers as $customer): ?>
                                <a href="../../admin/Pages/listCustomers.php?id=<?= $customer['customer_id'] ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($customer['customer_name']) ?></h6>
                                        <small><?= htmlspecialchars($customer['customer_email']) ?></small>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($orders) && empty($products) && empty($customers)): ?>
                        <div class="alert alert-warning">No results found for "<?= htmlspecialchars($query) ?>"</div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../admin/Layout/footer.php'; ?>

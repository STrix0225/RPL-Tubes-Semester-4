<?php
if (!isset($conn)) {
    require_once './DB/connection.php';
}

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$stats = [
    'total_products' => 0,
    'total_orders' => 0,
    'pending_orders' => 0,
    'total_customers' => 0
];

$result = $conn->query("SELECT COUNT(*) as count FROM products");
if ($result) {
    $stats['total_products'] = (int)$result->fetch_assoc()['count'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM orders");
if ($result) {
    $stats['total_orders'] = (int)$result->fetch_assoc()['count'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'pending'");
if ($result) {
    $stats['pending_orders'] = (int)$result->fetch_assoc()['count'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM customers");
if ($result) {
    $stats['total_customers'] = (int)$result->fetch_assoc()['count'];
}

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
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>GEMS Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="css/style.css" rel="stylesheet" />
</head>
<body>
    <div class="wrapper">
        <?php include 'Layout/sidebar.php'; ?>

        <div id="content">
            <?php include 'Layout/header.php'; ?>

            <div class="container-fluid mt-4">

                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Customers
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $stats['total_customers'] ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Products
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo (int)$stats['total_products']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Orders
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo (int)$stats['total_orders']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Orders
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo (int)$stats['pending_orders']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                                <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_orders)): ?>
                                                <?php foreach($recent_orders as $order): ?>
                                                    <tr>
                                                        <td>#<?= htmlspecialchars($order['order_id']) ?></td>
                                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                                        <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                                                        <td>$<?= number_format($order['order_cost'], 2) ?></td>
                                                        <td>
                                                            <span class="badge <?= 
                                                                match(strtolower($order['order_status'])) {
                                                                    'completed' => 'bg-success',
                                                                    'pending' => 'bg-warning',
                                                                    'cancelled' => 'bg-danger',
                                                                    default => 'bg-info'
                                                                }
                                                            ?>">
                                                                <?= ucfirst(htmlspecialchars($order['order_status'])) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="order_details.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="6" class="text-center">No recent orders found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'Layout/footer.php'; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/sidebar.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
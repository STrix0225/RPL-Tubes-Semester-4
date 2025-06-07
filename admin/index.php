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

<<<<<<< HEAD
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Customers
=======
    $query_total_not_paid = "SELECT COUNT(*) AS total__not_paid FROM orders WHERE order_status = 'not paid'";
    $stmt_total_not_paid = $conn->prepare($query_total_not_paid);
    $stmt_total_not_paid->execute();
    $stmt_total_not_paid->bind_result($total_not_paid);
    $stmt_total_not_paid->store_result();
    $stmt_total_not_paid->fetch();

    $kurs_dollar = 15722;

$query_total_brands = "SELECT COUNT(DISTINCT product_brand) AS total_brands FROM products";
$stmt_total_brands = $conn->prepare($query_total_brands);
$stmt_total_brands->execute();
$stmt_total_brands->bind_result($total_brands);
$stmt_total_brands->store_result();
$stmt_total_brands->fetch();

$query_total_products = "SELECT COUNT(*) AS total_products FROM products";
$stmt_total_products = $conn->prepare($query_total_products);
$stmt_total_products->execute();
$stmt_total_products->bind_result($total_products);
$stmt_total_products->store_result();
$stmt_total_products->fetch();

    function setRupiah($price)
    {
        $result = "Rp".number_format($price, 0, ',', '.');
        return $result;
    }

$query_sold_by_brand = "
    SELECT 
        COALESCE(p.product_brand, 'Unknown') AS product_brand, 
        COUNT(*) AS total_sold 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    JOIN orders o ON oi.order_id = o.order_id
    WHERE o.order_status IN ('delivered', 'shipped', 'paid') 
    GROUP BY p.product_brand
    ORDER BY total_sold DESC
";

$brand_names = [];
$brand_totals = [];

if ($result_sold_by_brand = $conn->query($query_sold_by_brand)) {
    while ($row = $result_sold_by_brand->fetch_assoc()) {
        $brand_names[] = htmlspecialchars($row['product_brand']);
        $brand_totals[] = (int)$row['total_sold'];
    }
    $result_sold_by_brand->free();
} else {
    error_log("Query failed: " . $conn->error);
}

?>

<?php include('layouts/header.php'); ?>
                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div href="orders.php" class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="text-decoration: none;">    Total Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php if (isset($total_orders)) { echo $total_orders; } ?></div>
>>>>>>> 01dd7c12e2b7e62f7c5dfad912fba8e969b8c1ee
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
                        <!-- Total Brands -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Product Brands</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_brands; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Products -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Total Products</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_products; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box-open fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<<<<<<< HEAD
=======
                    <!-- HTML -->
                    <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6  class="m-0 font-weight-bold text-primary">Sold Brand</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-pie" style="width: 100%; min-height: 300px;">
                                    <canvas id="brandPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                .chart-pie {
                    position: relative;
                    margin: 0 auto;
                }
                </style>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                <script>
                    // Data dari PHP
                    const brandLabels = <?php echo json_encode($brand_names); ?> || [];
                    const brandData = <?php echo json_encode($brand_totals); ?> || [];

                    // Warna dinamis
                    const backgroundColors = [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                        '#e74a3b', '#858796', '#5a5c69', '#fd7e14',
                        '#20c997', '#6610f2', '#6f42c1', '#d63384'
                    ];

                    const colors = brandLabels.map((_, i) => backgroundColors[i % backgroundColors.length]);

                    const dataBrand = {
                        labels: brandLabels,
                        datasets: [{
                            data: brandData,
                            backgroundColor: colors,
                            hoverBackgroundColor: colors.map(c => c + 'cc'),
                            hoverBorderColor: "rgba(234, 236, 244, 1)"
                        }],
                    };

                    const config = {
                        type: 'pie',
                        data: dataBrand,
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 20,
                                        padding: 15,
                                    }
                                },
                                tooltip: {
                                    enabled: true
                                }
                            }
                        }
                    };

                    const ctx = document.getElementById('brandPieChart').getContext('2d');
                    new Chart(ctx, config);
                </script>

                    <!-- Content Row -->
>>>>>>> 01dd7c12e2b7e62f7c5dfad912fba8e969b8c1ee
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
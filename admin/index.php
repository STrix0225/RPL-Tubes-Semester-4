<?php
    session_start();
    if (!isset($_SESSION['login_success'])) {
    header('Location: ../login.php');
    exit();
}
    include ('../Database/connection.php');
 
?>

<?php
    $query_total_orders = "SELECT COUNT(*) AS total_orders FROM orders";
    $stmt_total_orders = $conn->prepare($query_total_orders);
    $stmt_total_orders->execute();
    $stmt_total_orders->bind_result($total_orders);
    $stmt_total_orders->store_result();
    $stmt_total_orders->fetch();

    $query_total_payments = "SELECT SUM(o.order_cost) AS total_payments FROM payments p, orders o WHERE p.order_id = o.order_id";
    $stmt_total_payments = $conn->prepare($query_total_payments);
    $stmt_total_payments->execute();
    $stmt_total_payments->bind_result($total_payments);
    $stmt_total_payments->store_result();
    $stmt_total_payments->fetch();

    $query_total_paid = "SELECT COUNT(*) AS total_paid FROM orders WHERE order_status = 'delivered' OR order_status = 'shipped' OR order_status = 'paid'";
    $stmt_total_paid = $conn->prepare($query_total_paid);
    $stmt_total_paid->execute();
    $stmt_total_paid->bind_result($total_paid);
    $stmt_total_paid->store_result();
    $stmt_total_paid->fetch();

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
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Income</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php if (isset($total_payments)) { echo setRupiah(($total_payments * $kurs_dollar)); } ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Paid</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php if (isset($total_paid)) { echo $total_paid; } ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Not Paid</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php if (isset($total_not_paid)) { echo $total_not_paid; } ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments-dollar fa-2x text-gray-300"></i>
                                        </div>
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
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

<?php include('layouts/footer.php'); ?>
<?php
require_once '../../Database/connection.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get data for header notifications
$header_data = [
    'pending_orders' => 0,
    'recent_orders' => []
];

$result = $conn->query("SELECT COUNT(*) AS total_pending FROM orders WHERE order_status = 'pending'");
if ($result) {
    $header_data['pending_orders'] = (int)$result->fetch_assoc()['total_pending'];
}

$result = $conn->query("SELECT order_id, order_cost FROM orders WHERE order_status = 'pending' ORDER BY order_date DESC LIMIT 5");
if ($result) {
    $header_data['recent_orders'] = $result->fetch_all(MYSQLI_ASSOC);
}

// Get weekly summaries with proper execution
$weeklySummaries = [];
$query = "
    SELECT 
        YEAR(order_date) AS year,
        WEEK(order_date, 3) AS week_number,
        MIN(DATE(order_date)) AS start_date,
        MAX(DATE(order_date)) AS end_date,
        COUNT(*) AS total_orders,
        SUM(order_cost) AS total_revenue,
        AVG(order_cost) AS avg_order_value
    FROM orders
    WHERE order_status IN ('completed', 'cancelled')
    GROUP BY YEAR(order_date), WEEK(order_date, 3)
    ORDER BY year DESC, week_number DESC
";
$result = $conn->query($query);
if ($result) {
    $weeklySummaries = $result->fetch_all(MYSQLI_ASSOC);
}

// Get monthly summaries with proper execution
$monthlySummaries = [];
$query = "
    SELECT 
        YEAR(order_date) AS year,
        MONTH(order_date) AS month,
        DATE_FORMAT(order_date, '%M') AS month_name,
        COUNT(*) AS total_orders,
        SUM(order_cost) AS total_revenue,
        AVG(order_cost) AS avg_order_value
    FROM orders
    WHERE order_status IN ('completed', 'cancelled')
    GROUP BY YEAR(order_date), MONTH(order_date)
    ORDER BY year DESC, month DESC
";
$result = $conn->query($query);
if ($result) {
    $monthlySummaries = $result->fetch_all(MYSQLI_ASSOC);
}

// Get yearly summaries
$yearlySummaries = [];
$query = "
    SELECT 
        YEAR(order_date) AS year,
        COUNT(*) AS total_orders,
        SUM(order_cost) AS total_revenue,
        AVG(order_cost) AS avg_order_value
    FROM orders
    WHERE order_status IN ('completed', 'cancelled')
    GROUP BY YEAR(order_date)
    ORDER BY year DESC
";
$result = $conn->query($query);
if ($result) {
    $yearlySummaries = $result->fetch_all(MYSQLI_ASSOC);
}

// Get all orders for detailed view (only completed and cancelled)
$allOrders = [];
$query = "SELECT * FROM orders WHERE order_status IN ('completed', 'cancelled') ORDER BY order_date DESC";
$result = $conn->query($query);
if ($result) {
    $allOrders = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $conn->real_escape_string($_POST['new_status']);
    
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        header("Location: historyOrder.php?success=Order+status+updated+successfully");
        exit();
    } else {
        header("Location: historyOrder.php?error=Failed+to+update+order+status");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order History - GEMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="../css/style.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- Add jsPDF library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <?php include '../Layout/sidebar.php'; ?>

        <div id="content">
            <?php include '../Layout/header.php'; ?>

            <div class="container-fluid mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-primary">
                        <i class="fas fa-history me-2"></i>Order History
                    </h1>
                    <div>
                        <button id="exportCSV" class="btn btn-success me-2">
                            <i class="fas fa-file-csv me-1"></i> Export CSV
                        </button>
                        <button id="exportPDF" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </button>
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button" role="tab">
                            Weekly
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab">
                            Monthly
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="yearly-tab" data-bs-toggle="tab" data-bs-target="#yearly" type="button" role="tab">
                            Yearly
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="detailed-tab" data-bs-toggle="tab" data-bs-target="#detailed" type="button" role="tab">
                            Detailed
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="orderTabsContent">
                    <!-- Weekly Summary Tab -->
                    <div class="tab-pane fade show active" id="weekly" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="weeklyTable" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Week</th>
                                                <th>Period</th>
                                                <th>Total Orders</th>
                                                <th>Total Revenue</th>
                                                <th>Avg. Order Value</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($weeklySummaries)): ?>
                                                <tr><td colspan="6" class="text-center">No weekly data available</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($weeklySummaries as $summary): ?>
                                                <tr>
                                                    <td>Week <?php echo $summary['week_number']; ?>, <?php echo $summary['year']; ?></td>
                                                    <td>
                                                        <?php 
                                                            echo date('M j', strtotime($summary['start_date'])) . ' - ' . 
                                                                 date('M j', strtotime($summary['end_date'])); 
                                                        ?>
                                                    </td>
                                                    <td><?php echo $summary['total_orders']; ?></td>
                                                    <td>$<?php echo number_format($summary['total_revenue'], 2); ?></td>
                                                    <td>$<?php echo number_format($summary['avg_order_value'], 2); ?></td>
                                                    <td>
                                                        <a href="?week=<?php echo $summary['week_number']; ?>&year=<?php echo $summary['year']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-search me-1"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Summary Tab -->
                    <div class="tab-pane fade" id="monthly" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="monthlyTable" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Year</th>
                                                <th>Total Orders</th>
                                                <th>Total Revenue</th>
                                                <th>Avg. Order Value</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($monthlySummaries)): ?>
                                                <tr><td colspan="6" class="text-center">No monthly data available</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($monthlySummaries as $summary): ?>
                                                <tr>
                                                    <td><?php echo $summary['month_name']; ?></td>
                                                    <td><?php echo $summary['year']; ?></td>
                                                    <td><?php echo $summary['total_orders']; ?></td>
                                                    <td>$<?php echo number_format($summary['total_revenue'], 2); ?></td>
                                                    <td>$<?php echo number_format($summary['avg_order_value'], 2); ?></td>
                                                    <td>
                                                        <a href="?month=<?php echo $summary['month']; ?>&year=<?php echo $summary['year']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-search me-1"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Yearly Summary Tab -->
                    <div class="tab-pane fade" id="yearly" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="yearlyTable" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Year</th>
                                                <th>Total Orders</th>
                                                <th>Total Revenue</th>
                                                <th>Avg. Order Value</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($yearlySummaries)): ?>
                                                <tr><td colspan="5" class="text-center">No yearly data available</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($yearlySummaries as $summary): ?>
                                                <tr>
                                                    <td><?php echo $summary['year']; ?></td>
                                                    <td><?php echo $summary['total_orders']; ?></td>
                                                    <td>$<?php echo number_format($summary['total_revenue'], 2); ?></td>
                                                    <td>$<?php echo number_format($summary['avg_order_value'], 2); ?></td>
                                                    <td>
                                                        <a href="?year=<?php echo $summary['year']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-search me-1"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Orders Tab -->
                    <div class="tab-pane fade" id="detailed" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="detailedTable" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Customer ID</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Payment Method</th>
                                                <th>City</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($allOrders as $order): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                                <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                                <td><?php echo htmlspecialchars($order['customer_id']); ?></td>
                                                <td>$<?php echo number_format($order['order_cost'], 2); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php 
                                                            switch($order['order_status']) {
                                                                case 'pending': echo 'bg-warning'; break;
                                                                case 'processing': echo 'bg-info'; break;
                                                                case 'completed': echo 'bg-success'; break;
                                                                case 'cancelled': echo 'bg-danger'; break;
                                                                default: echo 'bg-secondary';
                                                            }
                                                        ?>">
                                                        <?php echo ucfirst(htmlspecialchars($order['order_status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($order['customer_city']); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><button class="dropdown-item btn-view-order" 
                                                                data-id="<?php echo $order['order_id']; ?>" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#orderDetailsModal">
                                                                <i class="fas fa-eye me-2"></i>View Details
                                                            </button></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form method="post" class="px-2 py-1">
                                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                                    <select name="new_status" class="form-select form-select-sm mb-2">
                                                                        <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                        <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                                        <option value="completed" <?php echo $order['order_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                                        <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                                    </select>
                                                                    <button type="submit" name="update_status" class="btn btn-sm btn-primary w-100">
                                                                        <i class="fas fa-save me-1"></i> Update
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include '../Layout/footer.php'; ?>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-3">Loading data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/script.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables with responsive settings
            $('#weeklyTable').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 10
            });
            
            $('#monthlyTable').DataTable({
                responsive: true,
                order: [[1, 'desc'], [0, 'desc']],
                pageLength: 10
            });
            
            $('#yearlyTable').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 10
            });
            
            $('#detailedTable').DataTable({
                responsive: true,
                order: [[1, 'desc']],
                pageLength: 25
            });

            // Order details modal handler
            $('.btn-view-order').on('click', function() {
                const orderId = $(this).data('id');
                $('#orderDetailsContent').html(`
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-3">Loading data...</p>
                    </div>
                `);

                $.ajax({
                    url: 'orderDetails.php',    
                    method: 'GET',
                    data: { id: orderId },
                    success: function(response) {
                        $('#orderDetailsContent').html(response);
                    },
                    error: function() {
                        $('#orderDetailsContent').html('<div class="alert alert-danger m-3">Failed to load order details.</div>');
                    }
                });
            });

            // Export to CSV
            $('#exportCSV').click(function() {
                let csvContent = "data:text/csv;charset=utf-8,";
                const activeTab = $('.tab-pane.active').attr('id');
                
                switch(activeTab) {
                    case 'weekly':
                        csvContent += "Week,Period,Total Orders,Total Revenue,Avg. Order Value\n";
                        $('#weeklyTable tbody tr').each(function() {
                            const cells = $(this).find('td');
                            csvContent += [
                                cells.eq(0).text().trim(),
                                cells.eq(1).text().trim(),
                                cells.eq(2).text().trim(),
                                cells.eq(3).text().trim(),
                                cells.eq(4).text().trim()
                            ].join(',') + "\n";
                        });
                        break;
                        
                    case 'monthly':
                        csvContent += "Month,Year,Total Orders,Total Revenue,Avg. Order Value\n";
                        $('#monthlyTable tbody tr').each(function() {
                            const cells = $(this).find('td');
                            csvContent += [
                                cells.eq(0).text().trim(),
                                cells.eq(1).text().trim(),
                                cells.eq(2).text().trim(),
                                cells.eq(3).text().trim(),
                                cells.eq(4).text().trim()
                            ].join(',') + "\n";
                        });
                        break;
                        
                    case 'yearly':
                        csvContent += "Year,Total Orders,Total Revenue,Avg. Order Value\n";
                        $('#yearlyTable tbody tr').each(function() {
                            const cells = $(this).find('td');
                            csvContent += [
                                cells.eq(0).text().trim(),
                                cells.eq(1).text().trim(),
                                cells.eq(2).text().trim(),
                                cells.eq(3).text().trim()
                            ].join(',') + "\n";
                        });
                        break;
                        
                    case 'detailed':
                        csvContent += "Order ID,Date,Customer ID,Amount,Status,Payment Method,City\n";
                        $('#detailedTable tbody tr').each(function() {
                            const cells = $(this).find('td');
                            csvContent += [
                                cells.eq(0).text().trim(),
                                cells.eq(1).text().trim(),
                                cells.eq(2).text().trim(),
                                cells.eq(3).text().trim(),
                                cells.eq(4).text().trim(),
                                cells.eq(5).text().trim(),
                                cells.eq(6).text().trim()
                            ].join(',') + "\n";
                        });
                        break;
                }
                
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "order_history_" + activeTab + ".csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });

            // Export to PDF
            $('#exportPDF').click(function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                const activeTab = $('.tab-pane.active').attr('id');
                let title = '';
                let headers = [];
                let data = [];
                
                switch(activeTab) {
                    case 'weekly':
                        title = 'Weekly Order Summary';
                        headers = [['Week', 'Period', 'Total Orders', 'Total Revenue', 'Avg. Value']];
                        $('#weeklyTable tbody tr').each(function() {
                            const cells = $(this).find('td');
                            data.push([
                                cells.eq(0).text().trim(),
                                cells.eq(1).text().trim(),
                                cells.eq(2).text().trim(),
                                cells.eq(3).text().trim(),
                                cells.eq(4).text().trim()
                            ]);
                        });
                        break;
                        
                    case 'monthly':
                        title = 'Monthly Order Summary';
                        headers = [['Month', 'Year', 'Total Orders', 'Total Revenue', 'Avg. Value']];
                        $('#monthlyTable tbody tr').each(function() {
                            const cells = $(this).find('td');
                            data.push([
                                cells.eq(0).text().trim(),
                                cells.eq(1).text().trim(),
                                cells.eq(2).text().trim(),
                                cells.eq(3).text().trim(),
                                cells.eq(4).text().trim()
                            ]);
                        });
                        break;
                        
                    case 'yearly':
                        title = 'Yearly Order Summary';
                        headers = [['Year', 'Total Orders', 'Total Revenue', 'Avg. Value']];
                        $('#yearlyTable tbody tr').each(function() {
                            const cells = $(this).find('td');
                            data.push([
                                cells.eq(0).text().trim(),
                                cells.eq(1).text().trim(),
                                cells.eq(2).text().trim(),
                                cells.eq(3).text().trim()
                            ]);
                        });
                        break;
                        
                    case 'detailed':
                        title = 'Detailed Order History';
                        headers = [['Order ID', 'Date', 'Customer ID', 'Amount', 'Status', 'Payment', 'City']];
                        $('#detailedTable tbody tr').each(function() {
                            const cells = $(this).find('td');
                            data.push([
                                cells.eq(0).text().trim(),
                                cells.eq(1).text().trim(),
                                cells.eq(2).text().trim(),
                                cells.eq(3).text().trim(),
                                cells.eq(4).text().trim(),
                                cells.eq(5).text().trim(),
                                cells.eq(6).text().trim()
                            ]);
                        });
                        break;
                }
                
                doc.text(title, 14, 15);
                doc.autoTable({
                    head: headers,
                    body: data,
                    startY: 20,
                    theme: 'grid',
                    headStyles: { fillColor: [41, 128, 185] },
                    styles: { fontSize: 8 }
                });
                
                doc.save('order_history_' + activeTab + '.pdf');
            });
        });
    </script>
</body>
</html>
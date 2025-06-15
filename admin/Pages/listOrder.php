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

$orders = [];
$query = "SELECT * FROM orders WHERE order_status IN ('on_hold', 'pending', 'processing') ORDER BY order_date DESC";
$result = $conn->query($query);
if ($result) {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $conn->real_escape_string($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        header("Location: listOrder.php?success=Order+status+updated+successfully");
        exit();
    } else {
        header("Location: listOrder.php?error=Failed+to+update+order+status");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>List Orders - GEMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="../css/style.css" rel="stylesheet" />
</head>
<body>
    <div class="wrapper">
        <?php include '../Layout/sidebar.php'; ?>

        <div id="content">
            <?php include '../Layout/header.php'; ?>

            <div class="container-fluid mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-primary">
                        <i class="fas fa-shopping-cart me-2"></i>List Orders
                    </h1>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ordersTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Order ID</th>
                                        <th>Customer ID</th>
                                        <th>Order Cost</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th>Order Date</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>   
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $index => $order): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
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
                                        <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_address']); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><button class="dropdown-item btn-view-order" 
                                                        data-id="<?php echo $order['order_id']; ?>" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#orderDetailsModal">
                                                        <i class="fas fa-eye me-2"></i>View Details
                                                    </button>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="post" action="listOrder.php" class="p-2">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                            <div class="mb-2">
                                                                <label class="form-label">Update Status</label>
                                                                <select name="status" class="form-select form-select-sm">
                                                                    <option value="on_hold" <?php echo $order['order_status'] == 'on_hold' ? 'selected' : ''; ?>>On Hold</option>
                                                                    <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                    <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                                    <option value="completed" <?php echo $order['order_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                                    <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                                </select>
                                                            </div>
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

            <?php include '../Layout/footer.php'; ?>
        </div>
    </div>
    <!-- Modal detail order -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3">Memuat data...</p>
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
            $('#ordersTable').DataTable({
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 3 },
                    { responsivePriority: 4, targets: 4 },
                    { responsivePriority: 5, targets: -1 }
                ]
            });

            $('.btn-view-order').on('click', function() {
        const orderId = $(this).data('id');
        $('#orderDetailsContent').html(`
            <div class="text-center p-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-3">Memuat data...</p>
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
                $('#orderDetailsContent').html('<div class="alert alert-danger m-3">Gagal memuat detail pesanan.</div>');
            }
        });
    });

        });
    </script>
</body>
</html>
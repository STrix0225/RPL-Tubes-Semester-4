<?php
require_once '../../Database/connection.php';

if (!isAdminLoggedIn()) {
    redirect('../login.php');
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

// Get all restock orders
$restockOrders = [];
$query = "SELECT os.*, s.nama_PT_supplier 
          FROM order_stock os
          LEFT JOIN supplier s ON os.id_supplier = s.id_supplier
          ORDER BY os.order_date DESC, os.id_stock DESC";
$result = $conn->query($query);
if ($result) {
    $restockOrders = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['id_stock']) && isset($_POST['new_status'])) {
    $id_stock = (int)$_POST['id_stock'];
    $new_status = $conn->real_escape_string($_POST['new_status']);
    
    $stmt = $conn->prepare("UPDATE order_stock SET status_order = ? WHERE id_stock = ?");
    $stmt->bind_param("si", $new_status, $id_stock);
    
    if ($stmt->execute()) {
        // If status is changed to 'completed', update product quantity
        if ($new_status == 'completed') {
            // Get the restock order details
            $stmt = $conn->prepare("SELECT product_id, product_qty FROM order_stock WHERE id_stock = ?");
            $stmt->bind_param("i", $id_stock);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();
            
            if ($order) {
                // Update product quantity
                $update_stmt = $conn->prepare("UPDATE products SET product_qty = product_qty + ? WHERE product_id = ?");
                $update_stmt->bind_param("ii", $order['product_qty'], $order['product_id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
        
        header("Location: historyRestock.php?success=Status+updated+successfully");
        exit();
    } else {
        header("Location: historyRestock.php?error=Failed+to+update+status");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>History Restock - GEMS Admin</title>
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
                        <i class="fas fa-boxes-stacked me-2"></i>History Restock
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

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="restockTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Product</th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th>Color</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($restockOrders as $index => $order): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($order['id_stock']); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                        <td><?php echo htmlspecialchars($order['nama_PT_supplier'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_brand']); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_category']); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_color']); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_qty']); ?></td>
                                        <td>$<?php echo number_format($order['product_ori_price'], 2); ?></td>
                                        <td>$<?php echo number_format($order['total_restock_price'], 2); ?></td>
                                        <td>
                                            <span class="badge 
                                                <?php 
                                                    switch($order['status_order']) {
                                                        case 'pending': echo 'bg-warning'; break;
                                                        case 'processing': echo 'bg-info'; break;
                                                        case 'completed': echo 'bg-success'; break;
                                                        case 'cancelled': echo 'bg-danger'; break;
                                                        default: echo 'bg-secondary';
                                                    }
                                                ?>">
                                                <?php echo htmlspecialchars($order['status_order']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['catatan'] ?? '-'); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="id_stock" value="<?php echo $order['id_stock']; ?>">
                                                            <input type="hidden" name="new_status" value="pending">
                                                            <button type="submit" name="update_status" class="dropdown-item <?php echo $order['status_order'] == 'pending' ? 'active' : ''; ?>">
                                                                Set Pending
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="id_stock" value="<?php echo $order['id_stock']; ?>">
                                                            <input type="hidden" name="new_status" value="processing">
                                                            <button type="submit" name="update_status" class="dropdown-item <?php echo $order['status_order'] == 'processing' ? 'active' : ''; ?>">
                                                                Set Processing
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="id_stock" value="<?php echo $order['id_stock']; ?>">
                                                            <input type="hidden" name="new_status" value="completed">
                                                            <button type="submit" name="update_status" class="dropdown-item <?php echo $order['status_order'] == 'completed' ? 'active' : ''; ?>">
                                                                Set Completed
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="id_stock" value="<?php echo $order['id_stock']; ?>">
                                                            <input type="hidden" name="new_status" value="cancelled">
                                                            <button type="submit" name="update_status" class="dropdown-item <?php echo $order['status_order'] == 'cancelled' ? 'active' : ''; ?>">
                                                                Set Cancelled
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                                <button 
                                                    class="btn btn-sm btn-outline-info rounded-circle view-sales-btn" 
                                                    title="View Supplier Sales"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewSalesModal"
                                                    data-id="<?php echo $order['id_supplier']; ?>">
                                                <i class="fas fa-user-tie"></i>
                                            </button>
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
    <!-- View Sales Modal -->
    <div class="modal fade" id="viewSalesModal" tabindex="-1" aria-labelledby="viewSalesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewSalesModalLabel">Supplier Sales Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="salesDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Loading sales information...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    <script src="../js/formatFIle.js"></script>
</body>
</html>
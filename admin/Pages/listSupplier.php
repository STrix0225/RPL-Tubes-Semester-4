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

// Get all suppliers
$suppliers = [];
$query = "SELECT * FROM supplier ORDER BY id_supplier DESC";
$result = $conn->query($query);
if ($result) {
    $suppliers = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $supplier_id = (int)$_GET['delete'];
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM supplier WHERE id_supplier = ?");
    $stmt->bind_param("i", $supplier_id);
    if ($stmt->execute()) {
        header("Location: listSupplier.php?success=Supplier+deleted+successfully");
        exit();
    } else {
        header("Location: listSupplier.php?error=Failed+to+delete+supplier");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>List Suppliers - GEMS Admin</title>
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
                        <i class="fas fa-truck me-2"></i>List Suppliers
                    </h1>
                    <a href="addSupplier.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Add Supplier
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="suppliersTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <th>Company Name</th>
                                        <th>Contact</th>
                                        <th>Email</th>
                                        <th>Brand</th>
                                        <th>Status</th>
                                        <th>Tools</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($suppliers as $index => $supplier): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($supplier['id_supplier']); ?></td>
                                        <td><?php echo htmlspecialchars($supplier['nama_PT_supplier']); ?></td>
                                        <td><?php echo htmlspecialchars($supplier['contact_PT']); ?></td>
                                        <td><?php echo htmlspecialchars($supplier['email_supplier'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($supplier['product_brand'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if ($supplier['status'] == 1): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-btns">
                                            <a href="editSupplier.php?id=<?php echo $supplier['id_supplier']; ?>" 
                                               class="btn btn-sm btn-outline-primary rounded-circle" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger rounded-circle delete-btn" 
                                                    title="Delete"
                                                    data-id="<?php echo $supplier['id_supplier']; ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            <!-- View Sales Modal -->
                                            <div class="modal fade" id="viewSalesModal" tabindex="-1" aria-labelledby="viewSalesModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Sales Information</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body" id="salesDetailsContent">
                                                            <!-- Sales details will be loaded here via AJAX -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button 
                                                class="btn btn-sm btn-outline-info rounded-circle view-sales-btn" 
                                                title="View Sales"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewSalesModal"
                                                data-id="<?php echo $supplier['id_supplier']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this supplier? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="../js/sidebar.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#suppliersTable').DataTable({
                responsive: true
            });

            // Delete button click handler
            $('.delete-btn').click(function() {
                var supplierId = $(this).data('id');
                $('#confirmDelete').attr('href', 'listSupplier.php?delete=' + supplierId);
                $('#deleteModal').modal('show');
            });
        });
    </script>
    <script src="../js/script.js"></script>
</body>
</html>
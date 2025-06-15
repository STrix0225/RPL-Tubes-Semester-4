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
                                            <a href="#" class="btn btn-sm btn-outline-primary rounded-circle edit-btn" 
                                            title="Edit"
                                            data-id="<?php echo $supplier['id_supplier']; ?>">
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
    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSupplierForm" action="updateSupplier.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_supplier" id="edit_id_supplier">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_nama_PT_supplier" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="edit_nama_PT_supplier" name="nama_PT_supplier" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_product_brand" class="form-label">Product Brand</label>
                                <input type="text" class="form-control" id="edit_product_brand" name="product_brand">
                            </div>
                            <div class="col-12">
                                <label for="edit_alamat_supplier" class="form-label">Address</label>
                                <textarea class="form-control" id="edit_alamat_supplier" name="alamat_supplier" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_contact_PT" class="form-label">Company Contact</label>
                                <input type="text" class="form-control" id="edit_contact_PT" name="contact_PT" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_email_supplier" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email_supplier" name="email_supplier">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_sales_name" class="form-label">Sales Name</label>
                                <input type="text" class="form-control" id="edit_sales_name" name="sales_name">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="edit_contact_person" name="contact_person">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="saveSupplierBtn" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
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

    // Edit button click handler - load data via AJAX
    $(document).on('click', '.edit-btn', function() {
        var supplierId = $(this).data('id');
        
        $.ajax({
            url: 'getSupplierData.php',
            type: 'GET',
            data: { id: supplierId },
            dataType: 'json',
            success: function(response) {
                console.log('AJAX Response:', response); // Debugging line
                if (response.success) {
                    $('#edit_id_supplier').val(response.data.id_supplier);
                    $('#edit_nama_PT_supplier').val(response.data.nama_PT_supplier);
                    $('#edit_alamat_supplier').val(response.data.alamat_supplier);
                    $('#edit_contact_PT').val(response.data.contact_PT);
                    $('#edit_email_supplier').val(response.data.email_supplier);
                    $('#edit_sales_name').val(response.data.sales_name);
                    $('#edit_contact_person').val(response.data.contact_person);
                    $('#edit_product_brand').val(response.data.product_brand);
                    $('#edit_status').val(response.data.status);
                    
                    $('#editSupplierModal').modal('show');
                } else {
                    alert('Error loading supplier data: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Debugging line
                alert('Error loading supplier data. Check console for details.');
            }
        });
    });

    // Add this before the form submission handler
$('#editSupplierForm').on('submit', function(e) {
    // Simple validation example
    if ($('#edit_nama_PT_supplier').val().trim() === '') {
        alert('Company Name is required');
        return false;
    }
    // Add more validations as needed
});

    // Form submission handler
    $('#editSupplierForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted'); // Debugging line
        
        // Show loading state
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log('Update Response:', response); // Debugging line
                try {
                    var result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.success) {
                        // Show success message and reload
                        alert('Supplier updated successfully!');
                        location.reload();
                    } else {
                        alert('Error updating supplier: ' + (result.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error('JSON Parse Error:', e);
                    alert('Error parsing server response. Check console for details.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update Error:', status, error);
                alert('Error updating supplier. Check console for details.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Save Changes');
            }
        });
    });
    
    $('#saveSupplierBtn').click(function() {
    $('#editSupplierForm').submit();
    });
});
    </script>
    <script src="../js/script.js"></script>
</body>
</html>
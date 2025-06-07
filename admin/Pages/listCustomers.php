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

// Get all customers
$customers = [];
$query = "SELECT * FROM customers ORDER BY customer_id DESC";
$result = $conn->query($query);
if ($result) {
    $customers = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $customer_id = (int)$_GET['delete'];
    
    // First get customer photo path
    $result = $conn->query("SELECT customer_photo FROM customers WHERE customer_id = $customer_id");
    if ($result && $result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        $photo_path = $customer['customer_photo'];
        
        // Delete photo file if exists
        if ($photo_path && file_exists('../../' . $photo_path)) {
            unlink('../../' . $photo_path);
        }
    }
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    if ($stmt->execute()) {
        header("Location: listCustomers.php?success=Customer+deleted+successfully");
        exit();
    } else {
        header("Location: listCustomers.php?error=Failed+to+delete+customer");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>List Customers - GEMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/customers.css" rel="stylesheet" />
    

</head>
<body>
    <div class="wrapper">
        <?php include '../Layout/sidebar.php'; ?>

        <div id="content">
            <?php include '../Layout/header.php'; ?>

            <div class="container-fluid mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-primary">
                        <i class="fas fa-users me-2"></i>List Customers
                    </h1>
                    <a href="addCustomer.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Add Customer
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
                            <table id="customersTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Photo</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $index => $customer): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <?php if ($customer['customer_photo']): ?>
                                                <img src="../../admin/img/products/<?php echo htmlspecialchars($customer['customer_photo']); ?>" 
                                                     alt="Customer Photo" 
                                                     class="customer-photo">
                                            <?php else: ?>
                                                <div class="customer-photo bg-secondary text-white d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['customer_email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['customer_phone'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($customer['customer_city'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($customer['customer_address'] ?? 'N/A'); ?></td>
                                        <td class="action-btns">
                                            <a href="editCustomer.php?id=<?php echo $customer['customer_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary rounded-circle" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger rounded-circle delete-btn" 
                                                    title="Delete"
                                                    data-id="<?php echo $customer['customer_id']; ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            <a href="viewCustomer.php?id=<?php echo $customer['customer_id']; ?>" 
                                               class="btn btn-sm btn-outline-info rounded-circle" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
                    Are you sure you want to delete this customer? This action cannot be undone.
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
    <script src="../js/customer.js"></script>
</body>
</html>
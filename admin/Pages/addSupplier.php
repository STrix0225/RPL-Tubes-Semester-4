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

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Required fields
    $nama_pt = $conn->real_escape_string($_POST['nama_PT_supplier']);
    $alamat = $conn->real_escape_string($_POST['alamat_supplier']);
    $contact_pt = $conn->real_escape_string($_POST['contact_PT']);
    
    // Optional fields
    $email = isset($_POST['email_supplier']) ? $conn->real_escape_string($_POST['email_supplier']) : null;
    $sales_name = isset($_POST['sales_name']) ? $conn->real_escape_string($_POST['sales_name']) : null;
    $contact_person = isset($_POST['contact_person']) ? $conn->real_escape_string($_POST['contact_person']) : null;
    $product_brand = isset($_POST['product_brand']) ? $conn->real_escape_string($_POST['product_brand']) : null;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;

    $stmt = $conn->prepare("INSERT INTO supplier 
        (nama_PT_supplier, alamat_supplier, contact_PT, email_supplier, sales_name, 
         contact_person, product_brand, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssssi", 
        $nama_pt, $alamat, $contact_pt, $email, $sales_name,
        $contact_person, $product_brand, $status);

    if ($stmt->execute()) {
        $success = "Supplier added successfully.";
    } else {
        $error = "Database error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Supplier - GEMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="../css/style.css" rel="stylesheet" />
</head>
<body>
    <div class="wrapper">
        <?php include '../Layout/sidebar.php'; ?>

        <div id="content">
            <?php include '../Layout/header.php'; ?>

            <div class="container-fluid mt-4">
                <h1 class="h3 mb-4 text-primary">
                    <i class="fas fa-truck mr-2"></i> Add Supplier
                </h1>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <i class="fas fa-truck me-1"></i> Supplier Information
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_PT_supplier" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Company <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="contact_PT" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email_supplier">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sales Name</label>
                                    <input type="text" class="form-control" name="sales_name">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Person</label>
                                    <input type="text" class="form-control" name="contact_person">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="alamat_supplier" rows="2" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Product Brand</label>
                                <input type="text" class="form-control" name="product_brand">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Supplier
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <?php include '../Layout/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>
<?php
require_once '../../Database/connection.php';

if (!isAdminLoggedIn()) {
    http_response_code(403);
    exit('<div class="alert alert-danger">Unauthorized access</div>');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit('<div class="alert alert-danger">Invalid supplier ID</div>');
}

$id = (int)$_GET['id'];

// Get supplier sales info
$stmt = $conn->prepare("SELECT nama_PT_supplier, sales_name, contact_person FROM supplier WHERE id_supplier = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Supplier sales information not found.</div>";
    exit();
}

$supplier = $result->fetch_assoc();
?>

<div class="supplier-sales-info">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($supplier['nama_PT_supplier']) ?></h5>
            <hr>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-user-tie me-3 text-primary"></i>
                    <div>
                        <h6 class="mb-0">Sales Representative</h6>
                        <p class="mb-0"><?= htmlspecialchars($supplier['sales_name'] ?? 'Not specified') ?></p>
                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-phone-alt me-3 text-primary"></i>
                    <div>
                        <h6 class="mb-0">Contact Person</h6>
                        <p class="mb-0"><?= htmlspecialchars($supplier['contact_person'] ?? 'Not specified') ?></p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        For more detailed inquiries, please contact the supplier directly.
    </div>
</div>
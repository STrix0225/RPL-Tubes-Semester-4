<?php
require_once '../../Database/connection.php';

if (!isAdminLoggedIn()) {
    redirect('../login.php');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit('Invalid ID');
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT nama_PT_supplier, sales_name, contact_person FROM supplier WHERE id_supplier = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Sales data not found.</div>";
    exit();
}

$supplier = $result->fetch_assoc();
?>

<ul class="list-group">
    <li class="list-group-item">
        <i class="fas fa-building me-2"></i>
        <strong>Company Name:</strong> <?= htmlspecialchars($supplier['nama_PT_supplier']) ?>
    </li>
    <li class="list-group-item">
        <i class="fas fa-user-tie me-2"></i>
        <strong>Sales Name:</strong> <?= htmlspecialchars($supplier['sales_name'] ?? 'N/A') ?>
    </li>
    <li class="list-group-item">
        <i class="fas fa-id-card-alt me-2"></i>
        <strong>Contact Person:</strong> <?= htmlspecialchars($supplier['contact_person'] ?? 'N/A') ?>
    </li>
</ul>
<?php
require_once '../../Database/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid supplier ID']);
    exit;
}

$supplier_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM supplier WHERE id_supplier = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Supplier not found']);
    exit;
}

$supplier = $result->fetch_assoc();

echo json_encode([
    'success' => true,
    'data' => $supplier
]);
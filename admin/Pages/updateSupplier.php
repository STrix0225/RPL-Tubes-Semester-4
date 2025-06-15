<?php
require_once '../../Database/connection.php';

if (!isAdminLoggedIn()) {
    redirect('../login.php');
}

header('Content-Type: application/json');

// Check if required fields are present
if (!isset($_POST['id_supplier'])) {
    echo json_encode(['success' => false, 'message' => 'Supplier ID is required']);
    exit;
}

try {
    $supplier_id = (int)$_POST['id_supplier'];
    
    // Prepare data - add validation/sanitization as needed
    $data = [
        'nama_PT_supplier' => $_POST['nama_PT_supplier'],
        'alamat_supplier' => $_POST['alamat_supplier'],
        'contact_PT' => $_POST['contact_PT'],
        'email_supplier' => $_POST['email_supplier'] ?? null,
        'sales_name' => $_POST['sales_name'] ?? null,
        'contact_person' => $_POST['contact_person'] ?? null,
        'product_brand' => $_POST['product_brand'] ?? null,
        'status' => (int)$_POST['status'],
        'id_supplier' => $supplier_id
    ];

    // Update database
    $stmt = $conn->prepare("UPDATE supplier SET 
        nama_PT_supplier = ?,
        alamat_supplier = ?,
        contact_PT = ?,
        email_supplier = ?,
        sales_name = ?,
        contact_person = ?,
        product_brand = ?,
        status = ?,
        updated_at = NOW()
        WHERE id_supplier = ?");

    $stmt->bind_param("sssssssii", 
        $data['nama_PT_supplier'],
        $data['alamat_supplier'],
        $data['contact_PT'],
        $data['email_supplier'],
        $data['sales_name'],
        $data['contact_person'],
        $data['product_brand'],
        $data['status'],
        $data['id_supplier']
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Supplier updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
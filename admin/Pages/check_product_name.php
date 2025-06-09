<?php
require_once '../../Database/connection.php';

if (!isset($_GET['name'])) {
    echo json_encode(['exists' => false]);
    exit;
}

$name = $conn->real_escape_string($_GET['name']);

$query = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE product_name = ?");
$query->bind_param("s", $name);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

echo json_encode(['exists' => $data['total'] > 0]);

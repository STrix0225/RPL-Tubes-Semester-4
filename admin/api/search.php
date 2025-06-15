<?php
require_once '../../Database/connection.php';
header('Content-Type: application/json');

if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
    echo json_encode([]);
    exit;
}

$query = '%' . $_GET['q'] . '%';
$results = [];

/** PRODUCTS **/
$stmt = $conn->prepare("
    SELECT product_id, product_name, product_price 
    FROM products 
    WHERE product_name LIKE ? 
    LIMIT 5
");
$stmt->bind_param("s", $query);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
foreach ($products as $product) {
    $results[] = [
        'category' => 'Products',
        'title' => $product['product_name'],
        'subtitle' => '$' . number_format($product['product_price'], 2),
        'link' => '../../admin/Pages/listProducts.php',
        'description' => ''
    ];
}

/** CUSTOMERS **/
$stmt = $conn->prepare("
    SELECT customer_id, customer_name, customer_email 
    FROM customers 
    WHERE customer_name LIKE ? OR customer_email LIKE ?
    LIMIT 5
");
$stmt->bind_param("ss", $query, $query);
$stmt->execute();
$customers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
foreach ($customers as $customer) {
    $results[] = [
        'category' => 'Customers',
        'title' => $customer['customer_name'],
        'subtitle' => $customer['customer_email'],
        'link' => '../../admin/Pages/listCustomers.php',
        'description' => ''
    ];
}

/** SUPPLIERS **/
$stmt = $conn->prepare("
    SELECT id_supplier, nama_PT_supplier, sales_name 
    FROM supplier 
    WHERE nama_PT_supplier LIKE ? OR sales_name LIKE ? OR contact_person LIKE ?
    LIMIT 5
");
$stmt->bind_param("sss", $query, $query, $query);
$stmt->execute();
$suppliers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
foreach ($suppliers as $supplier) {
    $results[] = [
        'category' => 'Suppliers',
        'title' => $supplier['nama_PT_supplier'],
        'subtitle' => 'Sales: ' . $supplier['sales_name'],
        'link' => '../../admin/Pages/listSupplier.php',
        'description' => ''
    ];
}

/** ORDERS **/
$stmt = $conn->prepare("
    SELECT o.order_id, o.order_cost, c.customer_name 
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    WHERE o.order_id LIKE ? OR c.customer_name LIKE ?
    LIMIT 5
");
$stmt->bind_param("ss", $query, $query);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
foreach ($orders as $order) {
    $results[] = [
        'category' => 'Orders',
        'title' => 'Order #' . $order['order_id'],
        'subtitle' => '$' . number_format($order['order_cost'], 2),
        'link' => '../../admin/Pages/listOrder.php',
        'description' => 'Customer: ' . $order['customer_name']
    ];
}

/** REVIEWS **/
$stmt = $conn->prepare("
    SELECT r.review_id, r.rating, r.review_date, c.customer_name, p.product_name
    FROM reviews r
    JOIN customers c ON r.customer_id = c.customer_id
    JOIN products p ON r.product_id = p.product_id
    WHERE c.customer_name LIKE ? OR p.product_name LIKE ? OR r.review_text LIKE ?
    LIMIT 5
");
$stmt->bind_param("sss", $query, $query, $query);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
foreach ($reviews as $review) {
    $results[] = [
        'category' => 'Reviews',
        'title' => 'Rating: ' . str_repeat('★', (int)$review['rating']),
        'subtitle' => 'Product: ' . $review['product_name'],
        'link' => '../../admin/Pages/listReviews.php',
        'description' => 'By: ' . $review['customer_name'] . ' on ' . date('M d, Y', strtotime($review['review_date']))
    ];
}

echo json_encode($results);

<?php
require_once '../../Database/connection.php';

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? 0;

$response = ['success' => false, 'html' => ''];

switch ($type) {
    case 'product':
        $stmt = $conn->prepare("
            SELECT * FROM products 
            WHERE product_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        
        if ($product) {
            $response['html'] = '
                <div class="row">
                    <div class="col-md-6">
                        <img src="../../admin/product_images/'.htmlspecialchars($product['product_image1']).'" 
                             class="img-fluid rounded mb-3" alt="Product Image">
                    </div>
                    <div class="col-md-6">
                        <h4>'.htmlspecialchars($product['product_name']).'</h4>
                        <p><strong>Brand:</strong> '.htmlspecialchars($product['product_brand']).'</p>
                        <p><strong>Price:</strong> $'.number_format($product['product_price'], 2).'</p>
                        <p><strong>Category:</strong> '.htmlspecialchars($product['product_category']).'</p>
                        <p><strong>Stock:</strong> '.$product['product_qty'].'</p>
                        <p><strong>Description:</strong> '.htmlspecialchars($product['product_description']).'</p>
                    </div>
                </div>
            ';
            $response['success'] = true;
        }
        break;
        
    case 'order':
        $stmt = $conn->prepare("
            SELECT o.*, c.customer_name, c.customer_email, c.customer_phone 
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.order_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        
        if ($order) {
            $response['html'] = '
                <div class="order-details">
                    <h4>Order #'.$order['order_id'].'</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Customer:</strong> '.htmlspecialchars($order['customer_name']).'</p>
                            <p><strong>Email:</strong> '.htmlspecialchars($order['customer_email']).'</p>
                            <p><strong>Phone:</strong> '.htmlspecialchars($order['customer_phone']).'</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Order Date:</strong> '.date('M d, Y H:i', strtotime($order['order_date'])).'</p>
                            <p><strong>Total Cost:</strong> $'.number_format($order['order_cost'], 2).'</p>
                            <p><strong>Status:</strong> '.ucfirst(str_replace('_', ' ', $order['order_status'])).'</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h5>Shipping Address</h5>
                        <p>'.htmlspecialchars($order['customer_address']).'<br>
                        '.htmlspecialchars($order['customer_city']).'</p>
                    </div>
                </div>
            ';
            $response['success'] = true;
        }
        break;
        
    case 'customer':
        $stmt = $conn->prepare("
            SELECT * FROM customers 
            WHERE customer_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();
        
        if ($customer) {
            $photo = !empty($customer['customer_photo']) ? 
                     '<img src="../../admin/customer_photos/'.htmlspecialchars($customer['customer_photo']).'" 
                          class="img-thumbnail mb-3" width="150" alt="Customer Photo">' : 
                     '<div class="text-muted mb-3">No photo available</div>';
            
            $response['html'] = '
                <div class="row">
                    <div class="col-md-4 text-center">
                        '.$photo.'
                    </div>
                    <div class="col-md-8">
                        <h4>'.htmlspecialchars($customer['customer_name']).'</h4>
                        <p><strong>Email:</strong> '.htmlspecialchars($customer['customer_email']).'</p>
                        <p><strong>Phone:</strong> '.htmlspecialchars($customer['customer_phone']).'</p>
                        <p><strong>Address:</strong> '.htmlspecialchars($customer['customer_address']).'</p>
                        <p><strong>City:</strong> '.htmlspecialchars($customer['customer_city']).'</p>
                    </div>
                </div>
            ';
            $response['success'] = true;
        }
        break;
        
    case 'supplier':
        $stmt = $conn->prepare("
            SELECT * FROM supplier 
            WHERE id_supplier = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $supplier = $stmt->get_result()->fetch_assoc();
        
        if ($supplier) {
            $status = $supplier['status'] == 1 ? '<span class="badge bg-success">Active</span>' : 
                                               '<span class="badge bg-danger">Inactive</span>';
            
            $response['html'] = '
                <div class="supplier-details">
                    <h4>'.htmlspecialchars($supplier['nama_PT_supplier']).'</h4>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Company Contact:</strong> '.htmlspecialchars($supplier['contact_PT']).'</p>
                            <p><strong>Email:</strong> '.htmlspecialchars($supplier['email_supplier']).'</p>
                            <p><strong>Sales Representative:</strong> '.htmlspecialchars($supplier['sales_name']).'</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Contact Person:</strong> '.htmlspecialchars($supplier['contact_person']).'</p>
                            <p><strong>Product Brand:</strong> '.htmlspecialchars($supplier['product_brand']).'</p>
                            <p><strong>Status:</strong> '.$status.'</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h5>Address</h5>
                        <p>'.nl2br(htmlspecialchars($supplier['alamat_supplier'])).'</p>
                    </div>
                </div>
            ';
            $response['success'] = true;
        }
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
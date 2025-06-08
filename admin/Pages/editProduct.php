<?php
require_once '../../Database/connection.php';

header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Jika hanya product_id dikirim (fetch data produk)
    if (isset($_POST['product_id']) && count($_POST) === 1) {
        $product_id = (int)$_POST['product_id'];
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if ($product) {
            echo json_encode(['success' => true, 'data' => $product]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
        exit;
    }

    // Update produk dengan image handling
    if (isset($_POST['editProductId'])) {
        $id = (int)$_POST['editProductId'];
        $name = $_POST['editProductName'] ?? '';
        $brand = $_POST['editProductBrand'] ?? '';
        $category = $_POST['editProductCategory'] ?? '';
        $color = $_POST['editProductColor'] ?? '';
        $description = $_POST['editProductDescription'] ?? '';
        $price = $_POST['editProductPrice'] !== '' ? floatval($_POST['editProductPrice']) : 0;
        $discount = $_POST['editProductDiscount'] !== '' ? floatval($_POST['editProductDiscount']) : 0;
        $criteria = $_POST['editProductCriteria'] ?? '';

        // Initialize image variables
        $imageUpdates = [];
        $params = [
            $name, $brand, $category, $color, $description, 
            $price, $discount, $criteria
        ];
        $types = "sssssdss";

        // Handle image uploads
        $upload_dir = '../../Customer/gems-customer-pages/images/';
        $image_fields = ['product_image1', 'product_image2', 'product_image3'];
        
        foreach ($image_fields as $index => $field) {
            if (!empty($_FILES[$field]['name'])) {
                $file_name = basename($_FILES[$field]['name']);
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_file_name = "product_" . $id . "_" . ($index + 1) . "_" . time() . "." . $file_ext;
                $target_path = $upload_dir . $new_file_name;
                
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $target_path)) {
                    $imageUpdates[] = "$field = ?";
                    $params[] = $new_file_name;
                    $types .= "s";
                }
            }
        }

        // Build the query
        $query = "UPDATE products SET
            product_name = ?,
            product_brand = ?,
            product_category = ?,
            product_color = ?,
            product_description = ?,
            product_price = ?,
            product_discount = ?,
            product_criteria = ?";
        
        if (!empty($imageUpdates)) {
            $query .= ", " . implode(", ", $imageUpdates);
        }
        
        $query .= " WHERE product_id = ?";
        $params[] = $id;
        $types .= "i";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . $stmt->error]);
        }
        $stmt->close();
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
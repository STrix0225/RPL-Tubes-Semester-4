<?php
session_start();
// Database connection
include __DIR__ . '/../Database/connection.php';


// Initialize response array
$response = array('success' => false, 'message' => '');

try {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $product_name = $_POST['productName'] ?? '';
        $product_brand = $_POST['productBrand'] ?? '';
        $product_category = $_POST['productCategory'] ?? '';
        $product_description = $_POST['productDescription'] ?? '';
        $product_criteria = ($_POST['favourite'] ?? 'no') === 'yes' ? 'Favorit' : 'Non-Favorit';
        $product_price = str_replace(['.', ','], ['', '.'], $_POST['productPrice'] ?? '0');
        $special_offer = str_replace(['.', ','], ['', '.'], $_POST['specialOffer'] ?? '0');
        $product_color = $_POST['productColor'] ?? '';

        // Validate required fields
        if (empty($product_name) || empty($product_brand) || empty($product_category) || empty($product_price)) {
            throw new Exception('Semua field wajib diisi kecuali gambar dan penawaran spesial');
        }

        // Handle file uploads
        $upload_dir = 'uploads/products/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_fields = ['product_image1', 'product_image2', 'product_image3'];
        $uploaded_files = array();

        foreach ($image_fields as $index => $field) {
            $file_key = 'image' . ($index + 1);
            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
                $file_info = $_FILES[$file_key];
                
                // Generate unique filename
                $file_ext = pathinfo($file_info['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $file_ext;
                $destination = $upload_dir . $filename;
                
                // Check file type
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($file_info['type'], $allowed_types)) {
                    throw new Exception('File harus berupa gambar (JPEG, PNG, GIF)');
                }
                
                // Move uploaded file
                if (move_uploaded_file($file_info['tmp_name'], $destination)) {
                    $uploaded_files[$field] = $filename;
                } else {
                    throw new Exception('Gagal mengupload file ' . ($index + 1));
                }
            } else {
                $uploaded_files[$field] = null;
            }
        }

        // Prepare SQL statement
        $sql = "INSERT INTO products (
            product_name, 
            product_brand, 
            product_category, 
            product_description, 
            product_criteria, 
            product_image1, 
            product_image2, 
            product_image3, 
            product_price, 
            special_offer, 
            product_color
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'ssssssssdds',
            $product_name,
            $product_brand,
            $product_category,
            $product_description,
            $product_criteria,
            $uploaded_files['product_image1'],
            $uploaded_files['product_image2'],
            $uploaded_files['product_image3'],
            $product_price,
            $special_offer,
            $product_color
        );

        // Execute query
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Produk berhasil ditambahkan';
            $response['product_id'] = $stmt->insert_id;
            $_SESSION['success_message'] = 'Produk berhasil ditambahkan';
            header('Location: ../../UI/pages/ui-features/insert-product.php?success=1');
            exit;
        } else {
            throw new Exception('Gagal menambahkan produk: ' . $stmt->error);
        }

        $stmt->close();
    } else {
        throw new Exception('Metode request tidak valid');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    
    // Delete any uploaded files if error occurred
    if (!empty($uploaded_files)) {
        foreach ($uploaded_files as $file) {
            if ($file && file_exists($upload_dir . $file)) {
                unlink($upload_dir . $file);
            }
        }
    }
}

$conn->close();
echo json_encode($response);
?>
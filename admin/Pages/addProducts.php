<?php
require_once '../../Database/connection.php';

if (!isAdminLoggedIn()) {
    redirect('../login.php');
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
    $name = $conn->real_escape_string($_POST['product_name']);
    $brand = $conn->real_escape_string($_POST['product_brand']);
    $category = $conn->real_escape_string($_POST['product_category']);
    $description = $conn->real_escape_string($_POST['product_description']);
    $criteria = $conn->real_escape_string($_POST['product_criteria']);
    $price = floatval($_POST['product_price']);
    $discount = floatval($_POST['product_discount']);
    $color = $conn->real_escape_string($_POST['product_color']);
    $product_sold = 0;

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $upload_dir = '../img/Products/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    function uploadImage($file, $allowed, $upload_dir) {
        $img_name = $file['name'];
        $img_tmp = $file['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));

        if (in_array($img_ext, $allowed)) {
            $new_name = uniqid() . '.' . $img_ext;
            $target_path = $upload_dir . $new_name;

            if (move_uploaded_file($img_tmp, $target_path)) {
                return $new_name;
            }
        }
        return null;
    }

    $img1 = uploadImage($_FILES['product_image1'], $allowed, $upload_dir);
    $img2 = uploadImage($_FILES['product_image2'], $allowed, $upload_dir);
    $img3 = uploadImage($_FILES['product_image3'], $allowed, $upload_dir);

    if ($img1 && $img2 && $img3) {
        $stmt = $conn->prepare("INSERT INTO products 
        (product_name, product_brand, product_category, product_description, product_criteria, 
         product_image1, product_image2, product_image3, product_price, product_discount, 
         product_color, product_sold) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssssssdsi", $name, $brand, $category, $description, $criteria,
            $img1, $img2, $img3, $price, $discount, $color, $product_sold);

        if ($stmt->execute()) {
            $success = "Product added successfully.";
        } else {
            $error = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Image upload failed. Please ensure all 3 images are valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Product - GEMS Admin</title>
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
                <h1 class="h3 mb-4 text-primary">Add Product</h1>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <i class="fas fa-box me-1"></i> Product Information
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="product_name" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Brand</label>
                                    <input type="text" class="form-control" name="product_brand" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Category</label>
                                    <input type="text" class="form-control" name="product_category" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Criteria</label>
                                    <input type="text" class="form-control" name="product_criteria" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="product_description" rows="3" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="number" step="0.01" class="form-control" name="product_price" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Discount</label>
                                    <input type="number" step="0.01" class="form-control" name="product_discount">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" name="product_color">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Image 1</label>
                                    <input class="form-control" type="file" name="product_image1" accept="image/*" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Image 2</label>
                                    <input class="form-control" type="file" name="product_image2" accept="image/*" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Image 3</label>
                                    <input class="form-control" type="file" name="product_image3" accept="image/*" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Add Product
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
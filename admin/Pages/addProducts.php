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
    $criteria = isset($_POST['product_criteria']) ? $conn->real_escape_string($_POST['product_criteria']) : 'Non-Favorite';
    $price = floatval($_POST['product_price']);
    $discount = floatval($_POST['product_discount']);
    $color = $conn->real_escape_string($_POST['product_color']);
    $raw_colors = explode(',', $_POST['product_color']);
    $clean_colors = array_map(function($c) use ($conn) {
        return trim($conn->real_escape_string($c));
    }, $raw_colors);
    $color = implode(', ', $clean_colors);
    $product_sold = 0;
    $product_qty = 0;

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $upload_dir = '../../Customer/gems-customer-pages/images/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    function uploadImage($file, $allowed, $upload_dir, $product_name, $image_number) {
        $img_name = $file['name'];
        $img_tmp = $file['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));

        if (in_array($img_ext, $allowed)) {
            $clean_name = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($product_name));
            $new_name = $clean_name . $image_number . '.' . $img_ext;
            $target_path = $upload_dir . $new_name;

            if (move_uploaded_file($img_tmp, $target_path)) {
                return $new_name;
            }
        }
        return null;
    }

    // Upload masing-masing gambar dengan nomor urut
    $img1 = uploadImage($_FILES['product_image1'], $allowed, $upload_dir, $name, 1);
    $img2 = uploadImage($_FILES['product_image2'], $allowed, $upload_dir, $name, 2);
    $img3 = uploadImage($_FILES['product_image3'], $allowed, $upload_dir, $name, 3);
    $img4 = uploadImage($_FILES['product_image4'], $allowed, $upload_dir, $name, 4);

    if ($img1 && $img2 && $img3 && $img4) {

        $name = $conn->real_escape_string($_POST['product_name']);
        // Cek apakah produk sudah ada
        $check_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE product_name = ?");
        $check_stmt->bind_param("s", $name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $existing_count = $check_result->fetch_assoc()['total'];

        if ($existing_count > 0) {
            $error = "Product name already exists. Please choose a different name.";
        } else {
        $stmt = $conn->prepare("INSERT INTO products 
        (product_name, product_brand, product_category, product_description, product_criteria, 
         product_image1, product_image2, product_image3, product_image4, product_price, product_discount, 
         product_color, product_sold, product_qty) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssssssssdsii", $name, $brand, $category, $description, $criteria,
            $img1, $img2, $img3, $img4, $price, $discount, $color, $product_sold, $product_qty);

        if ($stmt->execute()) {
            $success = "Product added successfully with initial quantity set to 0.";
        } else {
            $error = "Database error: " . $stmt->error;
        }
        $stmt->close();
        }
    } else {
        $error = "Image upload failed. Please ensure all 4 images are valid.";
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
                <h1 class="h3 mb-4 text-primary">
                    <i class="fas fa-box mr-2"></i> Add Products
                </h1>

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
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                                <div id="name-feedback" class="form-text text-danger d-none">Product name already exists.</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Brand</label>
                                    <input type="text" class="form-control" name="product_brand" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" name="product_category" required>
                                        <option value="" disabled selected>Select category</option>
                                        <option value="Handphone">Handphone</option>
                                        <option value="Laptop">Laptop</option>
                                        <option value="Aksesoris">Aksesoris</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Criteria</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="product_criteria" id="criteriaFavourite" value="Favorite">
                                            <label class="form-check-label" for="criteriaFavourite">Favorite</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="product_criteria" id="criteriaNon" value="Non-Favorite" checked>
                                            <label class="form-check-label" for="criteriaNon">Non-Favorite</label>
                                        </div>
                                    </div>
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
                                    <label class="form-label">Discount %</label>
                                    <div class="discount-input-wrapper">
                                        <input type="number" step="0.01" class="form-control" name="product_discount" placeholder="0" min="0" max="100">
                                        <span class="percent-symbol">%</span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" name="product_color" placeholder="e.g., black, white, purple" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Image 1</label>
                                    <input id="product_image1" class="form-control" type="file" name="product_image1" accept="image/*" required onchange="previewImage(this, 'preview1')">
                                    <div class="mt-2 image-preview" id="preview1"></div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-1" 
                                            onclick="clearImage('product_image1', 'preview1')">Clear</button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Image 2</label>
                                    <input id="product_image2" class="form-control" type="file" name="product_image2" accept="image/*" required onchange="previewImage(this, 'preview2')">
                                    <div class="mt-2 image-preview" id="preview2"></div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-1" 
                                            onclick="clearImage('product_image2', 'preview2')">Clear</button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Image 3</label>
                                    <input id="product_image3" class="form-control" type="file" name="product_image3" accept="image/*" required onchange="previewImage(this, 'preview3')">
                                    <div class="mt-2 image-preview" id="preview3"></div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-1" 
                                            onclick="clearImage('product_image3', 'preview3')">Clear</button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Image 4</label>
                                    <input id="product_image4" class="form-control" type="file" name="product_image4" accept="image/*" required onchange="previewImage(this, 'preview4')">
                                    <div class="mt-2 image-preview" id="preview4"></div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-1" 
                                            onclick="clearImage('product_image4', 'preview4')">Clear</button>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/script.js"></script>

</body>
</html>
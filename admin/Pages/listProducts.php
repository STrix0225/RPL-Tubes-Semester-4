<?php
require_once '../../Database/connection.php';
if (!isAdminLoggedIn()) {
    redirect('../login.php');
}

$stats = [
    'total_products' => 0,
    'total_brands' => 0,
    'total_quantity' => 0
];

// Hitung total produk
$result = $conn->query("SELECT COUNT(*) AS total_products FROM products");
if ($result) {
    $stats['total_products'] = (int)$result->fetch_assoc()['total_products'];
}

// Hitung total brand unik
$result = $conn->query("SELECT COUNT(DISTINCT product_brand) AS total_brands FROM products");
if ($result) {
    $stats['total_brands'] = (int)$result->fetch_assoc()['total_brands'];
}

// Hitung total quantity semua produk
$result = $conn->query("SELECT SUM(product_qty) AS total_quantity FROM products");
if ($result) {
    $stats['total_quantity'] = (int)$result->fetch_assoc()['total_quantity'];
}

// Build card data
$cards = [
    ['title' => 'Total Products', 'value' => $stats['total_products'], 'icon' => 'fa-boxes', 'color' => 'primary', 'link' => 'listProducts.php'],
    ['title' => 'Total Brands', 'value' => $stats['total_brands'], 'icon' => 'fa-tags', 'color' => 'success', 'link' => 'listProducts.php'],
    ['title' => 'Total Quantity', 'value' => $stats['total_quantity'], 'icon' => 'fa-layer-group', 'color' => 'info', 'link' => 'listProducts.php']
];

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

// Get all products
$products = [];
$query = "SELECT * FROM products ORDER BY product_id DESC";
$result = $conn->query($query);
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];

    // Get product images to delete them from server
    $stmt = $conn->prepare("SELECT product_image1, product_image2, product_image3, product_image4 FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    // Delete images from server (cek dulu ada file-nya dan bukan empty)
    if ($product) {
        $upload_dir = '../../Customer/gems-customer-pages/images/';
        foreach (['product_image1', 'product_image2', 'product_image3', 'product_image4'] as $imgField) {
            if (!empty($product[$imgField]) && file_exists($upload_dir . $product[$imgField])) {
                unlink($upload_dir . $product[$imgField]);
            }
        }
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        header("Location: listProducts.php?success=Product+deleted+successfully");
        exit();
    } else {
        header("Location: listProducts.php?error=Failed+to+delete+product");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>List Products - GEMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/products.css" rel="stylesheet" />
</head>
<body>
    <div class="wrapper">
        <?php include '../Layout/sidebar.php'; ?>

        <div id="content">
            <?php include '../Layout/header.php'; ?>

            <div class="container-fluid mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-primary">
                        <i class="fas fa-boxes me-2"></i>List Products
                    </h1>
                    <a href="addProducts.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Add Product
                    </a>
                </div>
                <div class="row mb-4">
                    <?php foreach ($cards as $card): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-<?= $card['color']; ?> shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-<?= $card['color']; ?> text-uppercase mb-1">
                                                <?= $card['title']; ?>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $card['value']; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas <?= $card['icon']; ?> fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?= $card['link']; ?>" class="stretched-link"></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="productsTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th>Color</th>
                                        <th>Image</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Tools</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $index => $product): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['product_brand']); ?></td>
                                        <td><?php echo htmlspecialchars($product['product_category']); ?></td>
                                        <td><?php echo htmlspecialchars($product['product_color']); ?></td>
                                        <td>
                                            <img src="../../Customer/gems-customer-pages/images/<?php echo htmlspecialchars($product['product_image1']); ?>" 
                                                 alt="Product Image" class="product-img">
                                        </td>
                                        <td>$<?php echo number_format($product['product_price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($product['product_qty'] ?? 'N/A'); ?></td>
                                        <td class="action-btns">
                                            <button 
                                            class="btn btn-sm btn-outline-primary rounded-circle edit-btn" 
                                            title="Edit"
                                            data-id="<?php echo $product['product_id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                            <button class="btn btn-sm btn-outline-danger rounded-circle delete-btn" 
                                                title="Delete"
                                                data-id="<?php echo $product['product_id']; ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php include '../Layout/footer.php'; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form id="editProductForm" enctype="multipart/form-data">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
              <input type="hidden" id="editProductId" name="editProductId">

              <!-- Product Basic Info -->
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label for="editProductName" class="form-label">Product Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="editProductName" name="editProductName" required>
                </div>

                <div class="col-md-6">
                  <label for="editProductBrand" class="form-label">Brand</label>
                  <input type="text" class="form-control" id="editProductBrand" name="editProductBrand">
                </div>

                <div class="col-md-6">
                  <label for="editProductCategory" class="form-label">Category</label>
                  <input type="text" class="form-control" id="editProductCategory" name="editProductCategory">
                </div>

                <div class="col-md-6">
                  <label for="editProductColor" class="form-label">Color</label>
                  <input type="text" class="form-control" id="editProductColor" name="editProductColor">
                </div>

                <div class="col-12">
                  <label for="editProductDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="editProductDescription" name="editProductDescription" rows="3"></textarea>
                </div>
              </div>

              <!-- Pricing Section -->
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label for="editProductPrice" class="form-label">Price <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="0.01" class="form-control" id="editProductPrice" name="editProductPrice" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="editProductDiscount" class="form-label">Discount</label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="0.01" class="form-control" id="editProductDiscount" name="editProductDiscount">
                  </div>
                </div>

                <!-- Criteria Radio Buttons -->
                <div class="col-md-12">
                  <label class="form-label">Criteria</label>
                  <div class="d-flex gap-4">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="editProductCriteria" id="editCriteriaFavorite" value="Favorite">
                      <label class="form-check-label" for="editCriteriaFavorite">
                        <i class="bi bi-star-fill text-warning"></i> Favorite
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="editProductCriteria" id="editCriteriaNonFavorite" value="Non-Favorite">
                      <label class="form-check-label" for="editCriteriaNonFavorite">
                        <i class="bi bi-star text-secondary"></i> Non-Favorite
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Image Upload Section -->
              <div class="row g-3 mb-3">
                <h6 class="fw-bold">Product Images</h6>

                <!-- Image 1 -->
                <div class="col-md-4">
                  <div class="card h-100">
                    <div class="card-body">
                      <label for="product_image1" class="form-label">Image 1 (Primary)</label>
                      <input type="file" class="form-control mb-2" id="product_image1" name="product_image1" accept="image/*">
                      <div class="text-center">
                        <img id="preview_image1" src="" class="img-fluid rounded border" style="max-height: 150px; display: none;">
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Image 2 -->
                <div class="col-md-4">
                  <div class="card h-100">
                    <div class="card-body">
                      <label for="product_image2" class="form-label">Image 2</label>
                      <input type="file" class="form-control mb-2" id="product_image2" name="product_image2" accept="image/*">
                      <div class="text-center">
                        <img id="preview_image2" src="" class="img-fluid rounded border" style="max-height: 150px; display: none;">
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Image 3 -->
                <div class="col-md-4">
                  <div class="card h-100">
                    <div class="card-body">
                      <label for="product_image3" class="form-label">Image 3</label>
                      <input type="file" class="form-control mb-2" id="product_image3" name="product_image3" accept="image/*">
                      <div class="text-center">
                        <img id="preview_image3" src="" class="img-fluid rounded border" style="max-height: 150px; display: none;">
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Image 4 -->
                <div class="col-md-4">
                  <div class="card h-100">
                    <div class="card-body">
                      <label for="product_image4" class="form-label">Image 4</label>
                      <input type="file" class="form-control mb-2" id="product_image4" name="product_image4" accept="image/*">
                      <div class="text-center">
                        <img id="preview_image4" src="" class="img-fluid rounded border" style="max-height: 150px; display: none;">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Save Changes</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- End Edit Product Modal -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/products.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>
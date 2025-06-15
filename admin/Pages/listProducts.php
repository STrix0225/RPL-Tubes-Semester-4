<?php
require_once '../../Database/connection.php';
if (!isAdminLoggedIn()) {
    redirect('../login.php');
}

$highlight = $_GET['highlight'] ?? '';

// Handle AJAX request for chart data
if (isset($_GET['kategori']) && isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $kategori = $_GET['kategori'] ?? '';
    $where = "";
    $params = [];
    
    if ($kategori) {
        $where = "WHERE product_category = ?";
        $params[] = $kategori;
    }

    $stmt = $conn->prepare("SELECT product_brand, product_category, SUM(product_qty) as total_qty 
                           FROM products $where 
                           GROUP BY product_brand, product_category");
    
    if ($kategori) {
        $stmt->bind_param("s", $kategori);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode($data);
    exit();
}

// Handle product deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];

    // Get product images to delete them from server
    $stmt = $conn->prepare("SELECT product_image1, product_image2, product_image3, product_image4 FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    // Delete images from server
    if ($product) {
        $upload_dir = '../../Customer/gems-customer-pages/images/';
        foreach (['product_image1', 'product_image2', 'product_image3', 'product_image4'] as $imgField) {
            if (!empty($product[$imgField])) {
                $file_path = $upload_dir . $product[$imgField];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
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

// Get statistics for cards
$stats = [
    'total_products' => 0,
    'total_brands' => 0,
    'total_quantity' => 0
];

// Get total products
$result = $conn->query("SELECT COUNT(*) AS total_products FROM products");
if ($result) {
    $stats['total_products'] = (int)$result->fetch_assoc()['total_products'];
}

// Get total unique brands
$result = $conn->query("SELECT COUNT(DISTINCT product_brand) AS total_brands FROM products");
if ($result) {
    $stats['total_brands'] = (int)$result->fetch_assoc()['total_brands'];
}

// Get total quantity of all products
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

// Get header notifications data
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
                        <tr class="<?= ($highlight && stripos($product['product_name'], $highlight) !== false) ? 'table-warning' : '' ?>">
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
                                <button 
                                    class="btn btn-sm btn-outline-danger rounded-circle delete-btn" 
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
                <div class="row">
                  <div class="col-md-6">
                      <div class="card p-3" id="chartCard">
                          <h5>Produk per Brand</h5>
                          <select id="filterKategori" class="form-select mb-3">
                              <option value="">Semua Kategori</option>
                              <?php
                              $result = $conn->query("SELECT DISTINCT product_category FROM products");
                              if ($result) {
                                  while ($row = $result->fetch_assoc()) {
                                      echo '<option value="'.htmlspecialchars($row['product_category']).'">'.htmlspecialchars($row['product_category']).'</option>';
                                  }
                              }
                              ?>
                          </select>
                          <div style="height: 260px;">
                              <canvas id="qtyChart"></canvas>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card p-3" id="brandDetailCard">
                        <h5>Detail Brand</h5>
                        <div class="table-responsive" style="max-height: 314px; overflow-y: auto;">
                            <table class="table table-hover mb-0" id="brandTable">
                                <thead class="sticky-top bg-light" style="z-index: 1;">
                                    <tr>
                                        <th>Brand</th>
                                        <th>Kategori</th>
                                        <th>Total Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
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
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="editProductPrice" name="editProductPrice" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="editProductDiscount" class="form-label">Discount</label>
                  <div class="input-group">
                    <span class="input-group-text">%</span>
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
    <!-- Success Toast Template -->
    <!-- Success Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="success-animation">
              <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
              </svg>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <h6 class="mb-0">Success!</h6>
            <small id="successMessage">Product updated successfully</small>
          </div>
        </div>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/products.js"></script>
    <script src="../js/script.js"></script>
<script>
function updateChartTheme() {
    const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const chartCard = document.getElementById('chartCard');
    const brandDetailCard = document.getElementById('brandDetailCard');
    const brandTable = $('#brandTable');
    
    if (isDarkMode) {
        chartCard.classList.add('bg-dark', 'text-white');
        chartCard.classList.remove('bg-white', 'text-dark');
        brandDetailCard.classList.add('bg-dark', 'text-white');
        brandDetailCard.classList.remove('bg-white', 'text-dark');
        brandTable.addClass('table-dark').removeClass('table-light');

        // Warna teks tabel jadi putih
        $('#brandTable td, #brandTable th').css('color', '#fff');
    } else {
        chartCard.classList.add('bg-white', 'text-dark');
        chartCard.classList.remove('bg-dark', 'text-white');
        brandDetailCard.classList.add('bg-white', 'text-dark');
        brandDetailCard.classList.remove('bg-dark', 'text-white');
        brandTable.addClass('table-light').removeClass('table-dark');

        // Warna teks tabel jadi hitam
        $('#brandTable td, #brandTable th').css('color', '#000');
    }

    // Update warna teks pada chart jika ada
    if (window.chart) {
        const textColor = isDarkMode ? '#fff' : '#000';
        window.chart.options.plugins.legend.labels.color = textColor;
        window.chart.update();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Jalankan saat pertama kali halaman load
    updateChartTheme();
    
    // Pantau perubahan tema
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'data-bs-theme') {
                updateChartTheme();
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true
    });
});

$(document).ready(function () {
    // Plugin teks tengah pada donut chart
    const centerTextPlugin = {
        id: 'centerText',
        afterDraw(chart) {
            const { ctx, chartArea } = chart;
            ctx.save();

            const total = chart.data.datasets[0].data.reduce((a, b) => Number(a) + Number(b), 0);
            const centerX = (chartArea.left + chartArea.right) / 2;
            const centerY = (chartArea.top + chartArea.bottom) / 2;

            const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = isDarkMode ? '#fff' : '#000';

            ctx.font = 'bold 18px Arial';
            ctx.fillText(total.toLocaleString(), centerX, centerY - 5);

            ctx.font = 'normal 11px Arial';
            ctx.fillText('Total Qty', centerX, centerY + 12);
            ctx.restore();
        }
    };

    // Fungsi untuk load chart berdasarkan kategori
    function loadChart(kategori = '') {
        $.get('listProducts.php?kategori=' + encodeURIComponent(kategori) + '&ajax=1', function (data) {
            const labels = data.map(item => item.product_brand);
            const values = data.map(item => item.total_qty);
            const colors = labels.map(() => '#' + Math.floor(Math.random() * 16777215).toString(16));
            const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';

            const ctx = document.getElementById('qtyChart').getContext('2d');
            if (window.chart) window.chart.destroy();
            window.chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: isDarkMode ? '#fff' : '#000'
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });

            // Isi tabel brand di bawah chart
            const tbody = $('#brandTable tbody');
            tbody.empty();
            data.forEach(item => {
                tbody.append(`
                    <tr>
                        <td>${item.product_brand}</td>
                        <td>${item.product_category}</td>
                        <td>${item.total_qty}</td>
                    </tr>
                `);
            });

            // Sesuaikan warna teks tabel dengan tema
            $('#brandTable td, #brandTable th').css('color', isDarkMode ? '#fff' : '#000');
        });
    }

    // Ganti chart saat dropdown kategori berubah
    $('#filterKategori').change(function () {
        loadChart($(this).val());
    });

    // Pertama kali load
    loadChart();
});
</script>
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const highlight = urlParams.get('highlight');
    if (highlight) {
        const rows = document.querySelectorAll('#productsTable tbody tr');
        rows.forEach(row => {
            if (row.innerText.toLowerCase().includes(highlight.toLowerCase())) {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
</script>
</body>
</html>
<?php
require_once '../../Database/connection.php';

if (!isAdminLoggedIn()) {
    redirect('../login.php');
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] == 'getProductStock' && isset($_GET['product_id'])) {
        $product_id = (int)$_GET['product_id'];
        $stock = 0;
        if ($product_id > 0) {
            $result = $conn->query("SELECT product_qty AS stock FROM products WHERE product_id = $product_id");
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stock = $row['stock'];
            }
        }
        echo json_encode(['stock' => $stock]);
        exit;
    }
    
    if ($_GET['action'] == 'getSuppliersByBrand' && isset($_GET['brand'])) {
        $brand = $conn->real_escape_string($_GET['brand']);
        $suppliers = [];
        if (!empty($brand)) {
            // Perbaikan query - menggunakan product_brand yang sesuai dengan struktur tabel
            $result = $conn->query("SELECT id_supplier, nama_PT_supplier 
                                  FROM supplier 
                                  WHERE status = 1 AND product_brand = '$brand'
                                  ORDER BY nama_PT_supplier");
            if ($result) {
                $suppliers = $result->fetch_all(MYSQLI_ASSOC);
            }
        }
        echo json_encode($suppliers);
        exit;
    }
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

// Get all products for dropdown
$products = [];
$result = $conn->query("SELECT product_id, product_name, product_brand, product_category, product_color FROM products ORDER BY product_name");
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input
    if (empty($_POST['id_supplier']) || empty($_POST['product_id'])) {
        $error = "Supplier and Product must be selected!";
    } else {
        // Required fields
        $id_supplier = (int)$_POST['id_supplier'];
        $product_id = (int)$_POST['product_id'];
        $product_qty = (int)$_POST['product_qty'];
        $product_ori_price = (float)$_POST['product_ori_price'];
        
        // Validasi nilai
        if ($product_qty <= 0 || $product_ori_price <= 0) {
            $error = "Quantity and Original Price must be greater than zero!";
        } else {
            // Calculate total price
            $total_restock_price = $product_ori_price * $product_qty;
            
            // Optional fields
            $catatan = isset($_POST['catatan']) ? $conn->real_escape_string($_POST['catatan']) : null;
            
            // Verifikasi supplier ada di database
            $supplier_check = $conn->query("SELECT 1 FROM supplier WHERE id_supplier = $id_supplier");
            if ($supplier_check->num_rows == 0) {
                $error = "Invalid supplier selected!";
            } else {
                // Get product details for the order_stock table
                $product_info = $conn->query("SELECT product_name, product_brand, product_category, product_color 
                                           FROM products WHERE product_id = $product_id")->fetch_assoc();
                
                if (!$product_info) {
                    $error = "Invalid product selected!";
                } else {
                    // Mulai transaksi
                    $conn->begin_transaction();
                    
                    try {
                        // Insert into order_stock table
                        $stmt = $conn->prepare("INSERT INTO order_stock 
                            (id_supplier, product_id, product_name, product_brand, product_category, 
                             product_color, product_qty, product_ori_price, total_restock_price, catatan) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        
                        $stmt->bind_param("iissssiids", 
                            $id_supplier, $product_id, $product_info['product_name'], $product_info['product_brand'],
                            $product_info['product_category'], $product_info['product_color'], $product_qty,
                            $product_ori_price, $total_restock_price, $catatan);
                        
                        if (!$stmt->execute()) {
                            throw new Exception("Database error: " . $stmt->error);
                        }
                        
                        // Update product quantity and price in products table
                        $new_price = $product_ori_price * 1.2; // Add 20% markup
                        $update_stmt = $conn->prepare("UPDATE products 
                                                     SET product_qty = product_qty + ?, 
                                                         product_price = ?
                                                     WHERE product_id = ?");
                        $update_stmt->bind_param("idi", $product_qty, $new_price, $product_id);
                        
                        if (!$update_stmt->execute()) {
                            throw new Exception("Failed to update product inventory: " . $update_stmt->error);
                        }
                        
                        $conn->commit();
                        $success = "Product restocked successfully and inventory updated.";
                    } catch (Exception $e) {
                        $conn->rollback();
                        $error = $e->getMessage();
                    }
                    
                    if (isset($stmt)) $stmt->close();
                    if (isset($update_stmt)) $update_stmt->close();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Restock Product - GEMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="../css/style.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <?php include '../Layout/sidebar.php'; ?>

        <div id="content">
            <?php include '../Layout/header.php'; ?>

            <div class="container-fluid mt-4">
                <h1 class="h3 mb-4 text-primary">
                    <i class="fas fa-boxes me-2"></i> Restock Product
                </h1>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-truck-loading me-1"></i> Product Restock Information
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" id="restockForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Product <span class="text-danger">*</span></label>
                                    <select class="form-select" name="product_id" id="productSelect" required>
                                        <option value="">Select Product</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['product_id']; ?>"
                                                data-brand="<?php echo htmlspecialchars($product['product_brand']); ?>"
                                                data-category="<?php echo htmlspecialchars($product['product_category']); ?>"
                                                data-color="<?php echo htmlspecialchars($product['product_color']); ?>">
                                                <?php echo htmlspecialchars($product['product_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                    <select class="form-select" name="id_supplier" id="supplierSelect" required disabled>
                                        <option value="">Select Product First</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Brand</label>
                                    <input type="text" class="form-control" id="productBrand" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Category</label>
                                    <input type="text" class="form-control" id="productCategory" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" id="productColor" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Current Stock</label>
                                    <input type="text" class="form-control" id="currentStock" readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="product_qty" min="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Original Price (per unit) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="product_ori_price" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Selling Price (20% markup)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" id="sellingPrice" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="catatan" rows="2"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Submit Restock
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php include '../Layout/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/script.js"></script>
    
    <script>
$(document).ready(function() {
    // Update product details when product selection changes
    $('#productSelect').change(function() {
        var productId = $(this).val();
        if (productId) {
            // Get selected option data attributes
            var selectedOption = $(this).find('option:selected');
            var productBrand = selectedOption.data('brand');
            $('#productBrand').val(productBrand);
            $('#productCategory').val(selectedOption.data('category'));
            $('#productColor').val(selectedOption.data('color'));
            
            // Fetch current stock via AJAX
            $.get('restockProduct.php', {
                action: 'getProductStock',
                product_id: productId
            }, function(data) {
                $('#currentStock').val(data.stock);
            }, 'json').fail(function() {
                alert('Error fetching product stock');
            });
            
            // Fetch suppliers that match the product brand
            $.get('restockProduct.php', {
                action: 'getSuppliersByBrand',
                brand: productBrand
            }, function(data) {
                var supplierSelect = $('#supplierSelect');
                supplierSelect.empty();
                
                if (data.length > 0) {
                    supplierSelect.append('<option value="">Select Supplier</option>');
                    $.each(data, function(index, supplier) {
                        supplierSelect.append('<option value="' + supplier.id_supplier + '">' + 
                            supplier.nama_PT_supplier + '</option>');
                    });
                    supplierSelect.prop('disabled', false);
                } else {
                    supplierSelect.append('<option value="">No suppliers available for this brand</option>');
                    supplierSelect.prop('disabled', true);
                }
            }, 'json').fail(function() {
                alert('Error fetching suppliers');
            });
        } else {
            // Clear fields if no product selected
            $('#productBrand').val('');
            $('#productCategory').val('');
            $('#productColor').val('');
            $('#currentStock').val('');
            $('#supplierSelect').empty().append('<option value="">Select Product First</option>')
                               .prop('disabled', true);
        }
    });
    
    // Calculate selling price when original price changes
    $('input[name="product_ori_price"]').on('input', function() {
        var originalPrice = parseFloat($(this).val()) || 0;
        var sellingPrice = originalPrice * 1.2; // 20% markup
        $('#sellingPrice').val(sellingPrice.toFixed(2));
    });
    
    // Form validation before submit
    $('#restockForm').submit(function(e) {
        if ($('#supplierSelect').val() === "" || $('#supplierSelect').prop('disabled')) {
            alert('Please select a valid supplier');
            e.preventDefault();
            return false;
        }
        return true;
    });
});
</script>
</body>
</html>
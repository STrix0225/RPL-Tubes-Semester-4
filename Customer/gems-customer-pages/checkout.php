<?php
session_start();
include('../../Database/connection.php');

// Redirect jika belum login
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['checkout_message'] = 'Untuk melanjutkan checkout, silakan login terlebih dahulu.';
    header("Location: login-customer.php?redirect=checkout.php");
    exit();
}

// Ambil data customer dari database
$customer_id = $_SESSION['customer_id'];
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

// Redirect jika cart kosong
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Hitung total
$subtotal = 0;
$cart_items = [];

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

    // PERBAIKAN: Tambahkan tipe data untuk bind_param
    $types = str_repeat('i', count($product_ids));
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    
    // PERBAIKAN: Gunakan bind_param dengan benar
    $stmt->bind_param($types, ...$product_ids);
    
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $cart_item = $_SESSION['cart'][$product_id];

        $has_discount = !empty($product['product_discount']) && $product['product_discount'] > 0;
        $price = $has_discount ? 
                 $product['product_price'] * (1 - $product['product_discount'] / 100) : 
                 $product['product_price'];
                 
        $total = $price * $cart_item['quantity'];

        // PERBAIKAN: Tambahkan field 'image' yang diperlukan
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['product_name'],
            'image' => $product['product_image1'], // Pastikan field ini ada
            'price' => $product['product_price'],
            'discounted_price' => $price,
            'quantity' => $cart_item['quantity'],
            'total' => $total
        ];

        $subtotal += $total;
    }
}

$shipping = $subtotal > 50 ? 0 : 10;
$total = $subtotal + $shipping;

// PERBAIKAN: Pindahkan kode POST handler ke sini
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment'] ?? 'paypal';
    $order_date = date('Y-m-d H:i:s');

    // PERBAIKAN: Gunakan placeholder yang benar
    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, customer_id, customer_phone, customer_city, customer_address, order_date, payment_method) 
                            VALUES (?, 'pending', ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die("Error in order prepare: " . $conn->error);
    }
    
    // PERBAIKAN: Sesuaikan tipe data dan urutan parameter
    $stmt->bind_param(
        'disssss', 
        $total,
        $customer_id,
        $customer['customer_phone'],
        $customer['customer_city'],
        $customer['customer_address'],
        $order_date,
        $payment_method
    );
    
    $stmt->execute();
    
    if ($stmt->error) {
        die("Error executing order insert: " . $stmt->error);
    }
    
    $order_id = $conn->insert_id;

    // Simpan item pesanan
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, customer_id, order_date) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            die("Error in order_items prepare: " . $conn->error);
        }
        
        $stmt->bind_param(
            'iissdiss', 
            $order_id,
            $item['id'],
            $item['name'],
            $item['image'], // Pastikan field ini ada
            $item['discounted_price'],
            $item['quantity'],
            $customer_id,
            $order_date
        );
        
        $stmt->execute();
        
        if ($stmt->error) {
            die("Error executing order_items insert: " . $stmt->error);
        }
    }

    // Kosongkan cart dan redirect
    unset($_SESSION['cart']);
    header("Location: order-success.php?order_id=$order_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gadget MS - Checkout</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Colo Shop Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="styles/bootstrap4/bootstrap.min.css">
    <link href="plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.carousel.css">
    <link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/animate.css">
    <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="plugins/jquery-ui-1.12.1.custom/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="styles/cart_styles.css">
    <link rel="stylesheet" type="text/css" href="styles/checkout_responsive.css">
    <link rel="shortcut icon" href="../gems-customer-pages/images/Background3.jpg" />
    <style>
        /* Modern Checkout Styles */
        .modern-checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .modern-checkout-header {
            text-align: center;
            margin-bottom: 40px;
            padding-top: 20px;
        }
        
        .modern-checkout-header h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .modern-checkout-header p {
            font-size: 1.2rem;
            color: #7f8c8d;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .modern-checkout-body {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .modern-checkout-column {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
            transition: transform 0.3s ease;
        }
        
        .dark-mode .modern-checkout-column {
            background: #2c3e50;
            color: #ecf0f1;
        }
        
        .modern-checkout-column:hover {
            transform: translateY(-5px);
        }
        
        .modern-section-title {
            font-size: 1.6rem;
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3498db;
            position: relative;
        }
        
        .dark-mode .modern-section-title {
            color: #ecf0f1;
            border-bottom-color: #1abc9c;
        }
        
        .modern-section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background: #3498db;
        }
        
        .dark-mode .modern-section-title::after {
            background: #1abc9c;
        }
        
        .modern-customer-info {
            font-size: 1.15rem;
        }
        
        .dark-mode .modern-customer-info {
            color: #ecf0f1;
        }
        
        .modern-info-row {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .dark-mode .modern-info-row {
            border-bottom-color: #34495e;
        }
        
        .modern-info-label {
            font-weight: 700;
            color: #2c3e50;
            min-width: 120px;
        }
        
        .dark-mode .modern-info-label {
            color: #1abc9c;
        }
        
        .modern-info-value {
            color: #555;
            flex-grow: 1;
        }
        
        .dark-mode .modern-info-value {
            color: #bdc3c7;
        }
        
        .modern-order-items {
            margin-bottom: 25px;
        }
        
        .modern-order-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            font-size: 1.15rem;
        }
        
        .dark-mode .modern-order-item {
            border-bottom-color: #34495e;
            color: #ecf0f1;
        }
        
        .modern-order-total {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .dark-mode .modern-order-total {
            background: #34495e;
            color: #ecf0f1;
        }
        
        .modern-total-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 1.2rem;
        }
        
        .modern-total-row:last-child {
            border-top: 2px solid #eee;
            margin-top: 10px;
            padding-top: 20px;
            font-weight: 700;
            color: #2c3e50;
            font-size: 1.4rem;
        }
        
        .dark-mode .modern-total-row:last-child {
            border-top-color: #34495e;
            color: #ecf0f1;
        }
        
        .modern-payment-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .modern-payment-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .dark-mode .modern-payment-option {
            border-color: #34495e;
            background: #2c3e50;
        }
        
        .modern-payment-option:hover {
            border-color: #3498db;
            background: #f8fdff;
        }
        
        .dark-mode .modern-payment-option:hover {
            border-color: #1abc9c;
            background: #34495e;
        }
        
        .modern-payment-option.selected {
            border-color: #3498db;
            background: #e8f4fe;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.2);
        }
        
        .dark-mode .modern-payment-option.selected {
            border-color: #1abc9c;
            background: #2c3e50;
            box-shadow: 0 5px 15px rgba(26, 188, 156, 0.2);
        }
        
        .modern-payment-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .dark-mode .modern-payment-icon {
            color: #1abc9c;
        }
        
        .modern-payment-label {
            font-size: 1.15rem;
            font-weight: 600;
            color: #2c3e50;
            text-align: center;
        }
        
        .dark-mode .modern-payment-label {
            color: #ecf0f1;
        }
        
        .modern-order-button {
            width: 100%;
            padding: 18px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.3rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 30px;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .dark-mode .modern-order-button {
            background: #1abc9c;
            box-shadow: 0 5px 15px rgba(26, 188, 156, 0.4);
        }
        
        .modern-order-button:hover {
            background: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.6);
        }
        
        .dark-mode .modern-order-button:hover {
            background: #16a085;
            box-shadow: 0 8px 20px rgba(26, 188, 156, 0.6);
        }
        
        .modern-payment-input {
            display: none;
        }
        
        @media (max-width: 768px) {
            .modern-checkout-body {
                flex-direction: column;
            }
            
            .modern-checkout-header h1 {
                font-size: 2rem;
            }
            
            .modern-section-title {
                font-size: 1.4rem;
            }
        }
    </style>
</head>

<body>

    <div class="super_container">

        <!-- Header -->
        <header class="header trans_300">
            <!-- Top Navigation -->
            <div class="top_nav">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="top_nav_left">free shipping around the world orders over $50</div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="top_nav_right">
                                <ul class="top_nav_menu">
                                    <!-- Currency / Language / My Account -->
                                    <li class="account">
                                        <a href="#">
                                            My Account
                                            <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="account_selection">
                                            <?php if (isset($_SESSION['customer_id'])): ?>
                                                <li><a href="../gems-login/logout-customer.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></li>
                                            <?php else: ?>
                                                <li><a href="../gems-login/login-customer.php"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a></li>
                                                <li><a href="../gems-login/register-customer.php"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Navigation -->
            <div class="main_nav_container">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="logo_container">
                                <a href="#">Gadget<span>ms</span></a>
                            </div>
                            <nav class="navbar">
                                <ul class="navbar_menu">
                                    <li><a href="dashboard.php">home</a></li>
                                    <li><a href="shop.php">shop</a></li>
                                    <li><a href="contact.php">contact</a></li>
                                </ul>
                                <ul class="navbar_user">
                                    <li><a href="#"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                                    <li class="checkout">
                                        <a href="cart.php">
                                            <i class="fa fa-shopping-cart" aria-hidden="true" id="dark-mode-cart"></i>
                                            <span id="checkout_items" class="checkout_items"><?= count($_SESSION['cart'] ?? []) ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" id="dark-mode-toggle" title="Toggle Dark Mode">
                                            <i class="fa fa-moon-o" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                </ul>
                                <div class="hamburger_container">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="fs_menu_overlay"></div>

        <!-- Hamburger Menu -->
        <div class="hamburger_menu">
            <div class="hamburger_close"><i class="fa fa-times" aria-hidden="true"></i></div>
            <div class="hamburger_menu_content text-right">
                <ul class="menu_top_nav">
                    <li class="menu_item has-children">
                        <a href="#">
                            My Account
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="menu_selection">
                            <li><a href="../gems-login/login-customer.php"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a></li>
                            <li><a href="../gems-login/register-customer.php"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a></li>
                        </ul>
                    </li>
                    <li class="menu_item"><a href="dashboard.php">home</a></li>
                    <li class="menu_item"><a href="shop.php">shop</a></li>
                    <li class="menu_item"><a href="contact.php">contact</a></li>
                </ul>
            </div>
        </div>
        <!-- Modern Checkout Section -->
        <div class="modern-checkout-container">
            <div class="modern-checkout-header">
                <h1>Secure Checkout</h1>
                <p>Review your order details and complete your purchase</p>
            </div>
            
            <form id="checkout_form" method="POST">
                <div class="modern-checkout-body">
                    <!-- Customer Information Column -->
                    <div class="modern-checkout-column">
                        <h2 class="modern-section-title">Customer Information</h2>
                        
                        <div class="modern-customer-info">
                            <div class="modern-info-row">
                                <div class="modern-info-label">Name:</div>
                                <div class="modern-info-value"><?= htmlspecialchars($customer['customer_name']) ?></div>
                            </div>
                            
                            <div class="modern-info-row">
                                <div class="modern-info-label">Email:</div>
                                <div class="modern-info-value"><?= htmlspecialchars($customer['customer_email']) ?></div>
                            </div>
                            
                            <div class="modern-info-row">
                                <div class="modern-info-label">Phone:</div>
                                <div class="modern-info-value"><?= htmlspecialchars($customer['customer_phone']) ?></div>
                            </div>
                            
                            <div class="modern-info-row">
                                <div class="modern-info-label">Country:</div>
                                <div class="modern-info-value"><?= htmlspecialchars($customer['customer_city']) ?></div>
                            </div>
                            
                            <div class="modern-info-row">
                                <div class="modern-info-label">Address:</div>
                                <div class="modern-info-value"><?= htmlspecialchars($customer['customer_address']) ?></div>
                            </div>
                            
                            <div class="modern-info-row">
                                <div class="modern-info-label">City:</div>
                                <div class="modern-info-value"><?= htmlspecialchars($customer['customer_city']) ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary Column -->
                    <div class="modern-checkout-column">
                        <h2 class="modern-section-title">Order Summary</h2>
                        
                        <div class="modern-order-items">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="modern-order-item">
                                    <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                                    <span>$<?= number_format($item['total'], 2) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="modern-order-total">
                            <div class="modern-total-row">
                                <span>Subtotal:</span>
                                <span>$<?= number_format($subtotal, 2) ?></span>
                            </div>
                            
                            <div class="modern-total-row">
                                <span>Shipping:</span>
                                <span>$<?= number_format($shipping, 2) ?></span>
                            </div>
                            
                            <div class="modern-total-row">
                                <span>Total:</span>
                                <span><strong>$<?= number_format($total, 2) ?></strong></span>
                            </div>
                        </div>
                        
                        <h2 class="modern-section-title">Payment Method</h2>
                        
                        <div class="modern-payment-options">
                            <label class="modern-payment-option" id="paypal-option">
                                <div class="modern-payment-icon">
                                    <i class="fa fa-paypal"></i>
                                </div>
                                <div class="modern-payment-label">PayPal</div>
                                <input type="radio" name="payment" value="paypal" class="modern-payment-input" checked>
                            </label>
                            
                            <label class="modern-payment-option" id="creditcard-option">
                                <div class="modern-payment-icon">
                                    <i class="fa fa-credit-card"></i>
                                </div>
                                <div class="modern-payment-label">Credit Card</div>
                                <input type="radio" name="payment" value="credit_card" class="modern-payment-input">
                            </label>
                            
                            <label class="modern-payment-option" id="bank-option">
                                <div class="modern-payment-icon">
                                    <i class="fa fa-university"></i>
                                </div>
                                <div class="modern-payment-label">Bank Transfer</div>
                                <input type="radio" name="payment" value="bank_transfer" class="modern-payment-input">
                            </label>
                            
                            <label class="modern-payment-option" id="cod-option">
                                <div class="modern-payment-icon">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="modern-payment-label">Cash on Delivery</div>
                                <input type="radio" name="payment" value="cod" class="modern-payment-input">
                            </label>
                        </div>
                        
                        <button type="submit" class="modern-order-button">
                            <i class="fa fa-lock"></i> Place Order Securely
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="footer_nav_container d-flex flex-sm-row flex-column align-items-center justify-content-lg-start justify-content-center text-center">
                            <ul class="footer_nav">
                                <li><a href="#">Blog</a></li>
                                <li><a href="#">FAQs</a></li>
                                <li><a href="contact.html">Contact us</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="footer_social d-flex flex-row align-items-center justify-content-lg-end justify-content-center">
                            <ul>
                                <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-skype" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer_nav_container">
                            <div class="cr">©2025 All Rights Reserverd. by <a href="#">GadgetMs</a> &amp; distributed by <a href="https://themewagon.com">ThemeWagon</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="styles/bootstrap4/popper.js"></script>
    <script src="styles/bootstrap4/bootstrap.min.js"></script>
    <script src="plugins/Isotope/isotope.pkgd.min.js"></script>
    <script src="plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
    <script src="plugins/easing/easing.js"></script>
    <script src="plugins/jquery-ui-1.12.1.custom/jquery-ui.js"></script>
    <script src="js/checkout_custom.js"></script>
    <script>
        // Dark Mode Toggle
        document.getElementById('dark-mode-toggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('dark-mode');

            // Save preference to localStorage
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                this.innerHTML = '<i class="fa fa-sun-o" aria-hidden="true"></i>';
            } else {
                localStorage.setItem('darkMode', 'disabled');
                this.innerHTML = '<i class="fa fa-moon-o" aria-hidden="true"></i>';
            }
        });

        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            document.getElementById('dark-mode-toggle').innerHTML = '<i class="fa fa-sun-o" aria-hidden="true"></i>';
        } 
        // Payment option selection
        const paymentOptions = document.querySelectorAll('.modern-payment-option');
        
        paymentOptions.forEach(option => {
            option.addEventListener('click', () => {
                // Remove selected class from all options
                paymentOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Add selected class to clicked option
                option.classList.add('selected');
                
                // Check the radio input
                const radioInput = option.querySelector('.modern-payment-input');
                radioInput.checked = true;
            });
        });
        
        // Initialize with PayPal selected
        document.getElementById('paypal-option').classList.add('selected');
        
        // Place order button animation
        const orderButton = document.querySelector('.modern-order-button');
        
        orderButton.addEventListener('click', (e) => {
            // Animation effect
            orderButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
            orderButton.style.backgroundColor = '#27ae60';
            
            setTimeout(() => {
                // Allow form to submit normally
            }, 500);
        });
    </script>
</body>

</html>
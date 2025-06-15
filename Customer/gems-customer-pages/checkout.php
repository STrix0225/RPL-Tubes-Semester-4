<?php
include('../../Database/connection.php');

// Handle PayPal callback
if (isset($_GET['paypal_success']) && $_GET['paypal_success'] == '1') {
    // Process PayPal payment success
    $order_id = $_SESSION['last_order_id'] ?? 0;

    if ($order_id > 0) {
        // Update payment status
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'completed' WHERE order_id = ?");
        $stmt->bind_param('i', $order_id);
        $stmt->execute();

        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'paid' WHERE order_id = ?");
        $stmt->bind_param('i', $order_id);
        $stmt->execute();

        // Redirect to success page
        header("Location: order-success.php?order_id=$order_id");
        exit();
    }
}

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
    // Get the selected color from session
    $product_color = $_SESSION['cart'][$item['id']]['selected_color'] ?? null;

    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, customer_id, order_date, product_color) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Error in order_items prepare: " . $conn->error);
    }

    $stmt->bind_param(
        'iissdisss',
        $order_id,
        $item['id'],
        $item['name'],
        $item['image'],
        $item['discounted_price'],
        $item['quantity'],
        $customer_id,
        $order_date,
        $product_color
    );

    $stmt->execute();

    if ($stmt->error) {
        die("Error executing order_items insert: " . $stmt->error);
    }

    // Update product quantity in products table
    $update_stmt = $conn->prepare("UPDATE products SET product_qty = product_qty - ?, product_sold = product_sold + ? WHERE product_id = ?");
    $update_stmt->bind_param('iii', $item['quantity'], $item['quantity'], $item['id']);
    $update_stmt->execute();
    
    if ($update_stmt->error) {
        die("Error updating product quantity: " . $update_stmt->error);
    }
}

$transaction_id = 'TX' . time() . rand(1000, 9999);
$payment_date = date('Y-m-d H:i:s');

$stmt = $conn->prepare("INSERT INTO payments (order_id, customer_id, transaction_id, payment_date) 
                        VALUES (?, ?, ?, ?)");

if ($stmt === false) {
    die("Error in payment prepare: " . $conn->error);
}

$stmt->bind_param(
    'iiss',
    $order_id,
    $customer_id,
    $transaction_id,
    $payment_date
);

$stmt->execute();

if ($stmt->error) {
    die("Error executing payment insert: " . $stmt->error);
}

// Kosongkan cart dan redirect
unset($_SESSION['cart']);
header("Location: order-success.php?order_id=$order_id");
exit();
}

define('PAYPAL_SANDBOX', true); // Mode sandbox
define('PAYPAL_CLIENT_ID', 'ARJ93357XnCWxpNDLV8HL8oaiPd_T4E8aSizk4CWli6BdL3Cx_j8y_R_E0JcX54RU_VBdG-71TZcoKMO');
define('PAYPAL_SECRET', 'EKJZRH4GGYg0CPe8VQmlOLOO_iM_0qdNhlMSGCH0xmgQkFCxnwDlB0XN-KgYDkqFnFm7w3Kv3ZHIBdgK');
define('PAYPAL_MERCHANT_EMAIL', 'gugugaga123@gmail.com');

// URL API berbeda untuk sandbox
$paypalAPI = PAYPAL_SANDBOX ? 
    'https://api.sandbox.paypal.com' : 
    'https://api.paypal.com';

    function verifyPayPalPayment($paymentID) {
    global $paypalAPI;
    
    $url = "$paypalAPI/v2/checkout/orders/$paymentID";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . getPayPalAccessToken()
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

function getPayPalAccessToken() {
    global $paypalAPI;
    
    $url = "$paypalAPI/v1/oauth2/token";
    $data = "grant_type=client_credentials";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Accept-Language: en_US'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $json = json_decode($response, true);
    return $json['access_token'] ?? '';
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
    <!-- PayPal SDK -->
    <!-- Ganti dengan client ID sandbox Anda -->
    <script src="https://www.paypal.com/sdk/js?client-id=ARJ93357XnCWxpNDLV8HL8oaiPd_T4E8aSizk4CWli6BdL3Cx_j8y_R_E0JcX54RU_VBdG-71TZcoKMO&currency=USD"></script>
    <style>
        /* Enhanced PayPal Modal Styles */
        .paypal-container {
            max-width: 500px;
            margin: 0 auto;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }

        .paypal-header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #e1e1e1;
            margin-bottom: 20px;
        }

        .paypal-logo {
            height: 30px;
            margin-bottom: 15px;
        }

        .paypal-title {
            color: #003087;
            font-size: 24px;
            font-weight: 500;
            margin: 10px 0;
        }

        .paypal-content {
            padding: 0 20px;
        }

        .paypal-login-section {
            background: #f5f7fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .paypal-login-title {
            font-size: 18px;
            color: #003087;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .paypal-form-group {
            margin-bottom: 15px;
        }

        .paypal-form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        .paypal-form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .paypal-login-button {
            width: 100%;
            padding: 12px;
            background: #0070ba;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 10px;
        }

        .paypal-login-button:hover {
            background: #005ea6;
        }

        .paypal-divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .paypal-divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            z-index: 1;
            color: #666;
            font-size: 14px;
        }

        .paypal-divider:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e1e1;
            z-index: 0;
        }

        .paypal-guest-title {
            font-size: 18px;
            color: #003087;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .paypal-pay-button {
            width: 100%;
            padding: 12px;
            background: #ffc439;
            color: #111;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 20px;
        }

        .paypal-pay-button:hover {
            background: #f5b72b;
        }

        .paypal-secure-note {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }

        .paypal-secure-note i {
            color: #009cde;
            margin-right: 5px;
        }

        .paypal-footer-links {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            font-size: 12px;
        }

        .paypal-footer-link {
            color: #0070ba;
            margin: 0 10px;
            text-decoration: none;
        }

        .paypal-footer-link:hover {
            text-decoration: underline;
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
							<a href="#">Gadget<span>MS</span></a>
						</div>
						<nav class="navbar">
							<ul class="navbar_menu">
								<li><a href="dashboard.php">home</a></li>
                                <li><a href="shop.php">shop</a></li>
                                <li><a href="contact.php">contact</a></li>
                                <li><a href="order.php" class="active">my orders</a></li>
							</ul>
                                <ul class="navbar_user">
                                    <li class="account">
                                        <a href="profile.php">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                            <i class="fa fa-angle-down" aria-hidden="true"></i>
                                        </a>
                                        <ul class="account_selection">
                                            <?php if (isset($_SESSION['customer_id'])): ?>
                                                <li><a href="login-customer.php"><i class="fa fa-cog" aria-hidden="true"></i> Change Account</a></li>
                                                <li><a href="logout-customer.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                                            <?php else: ?>
                                                <li><a href="login-customer.php"><i class="fa fa-sign-in" aria-hidden="true"></i> Sign In</a></li>
                                                <li><a href="register-customer.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                    <li class="checkout">
										<a href="cart.php">
											<i class="fa fa-shopping-cart" aria-hidden="true" id="dark-mode-cart"></i>
											<span id="checkout_items" class="checkout_items"><?= count($_SESSION['cart']) ?></span>
										</a>
									</li>
									<li>
										<a href="#" id="dark-mode-toggle">
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
                                    <div>
                                        <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                                        <?php if (!empty($_SESSION['cart'][$item['id']]['selected_color'])): ?>
                                            <div class="product-color-info">
                                                <small>Color: <?= htmlspecialchars($_SESSION['cart'][$item['id']]['selected_color']) ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
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

                            <label class="modern-payment-option" id="bank-option">
                                <div class="modern-payment-icon">
                                    <i class="fa fa-university"></i>
                                </div>
                                <div class="modern-payment-label">Bank Transfer</div>
                                <input type="radio" name="payment" value="bank_transfer" class="modern-payment-input">
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

    <style>
        /* PayPal Modal Styles */
        .paypal-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .dark-mode .paypal-header {
            border-bottom-color: #34495e;
        }

        .paypal-logo {
            height: 40px;
            margin-bottom: 15px;
        }

        .paypal-title {
            color: #003087;
            font-size: 1.5rem;
            margin: 10px 0;
        }

        .dark-mode .paypal-title {
            color: #009cde;
        }

        .paypal-content {
            max-width: 400px;
            margin: 0 auto;
        }

        .paypal-email-section {
            margin-bottom: 20px;
        }

        .paypal-email-label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }

        .dark-mode .paypal-email-label {
            color: #ecf0f1;
        }

        .paypal-email-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .dark-mode .paypal-email-input {
            background: #34495e;
            border-color: #2c3e50;
            color: #ecf0f1;
        }

        .paypal-or {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .paypal-or span {
            background: white;
            padding: 0 15px;
            position: relative;
            z-index: 1;
            color: #777;
        }

        .dark-mode .paypal-or span {
            background: #2c3e50;
            color: #bdc3c7;
        }

        .paypal-or::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
            z-index: 0;
        }

        .dark-mode .paypal-or::before {
            background: #34495e;
        }

        .paypal-card-logos {
            text-align: center;
            margin-bottom: 20px;
        }

        .paypal-card-logos img {
            height: 30px;
        }

        .paypal-card-form .form-group {
            margin-bottom: 15px;
        }

        .paypal-card-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .dark-mode .paypal-card-form label {
            color: #ecf0f1;
        }

        .paypal-card-form input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .dark-mode .paypal-card-form input {
            background: #34495e;
            border-color: #2c3e50;
            color: #ecf0f1;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .paypal-footer {
            margin-top: 30px;
            text-align: center;
        }

        .paypal-pay-button {
            background: #0070ba;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .paypal-pay-button:hover {
            background: #005ea6;
        }

        .paypal-secure-note {
            margin-top: 15px;
            font-size: 0.9rem;
            color: #777;
        }

        .dark-mode .paypal-secure-note {
            color: #bdc3c7;
        }

        /* Bank Transfer Modal Styles */
        .bank-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .dark-mode .bank-header {
            border-bottom-color: #34495e;
        }

        .bank-icon {
            font-size: 2.5rem;
            color: #0066cc;
            margin-bottom: 10px;
        }

        .bank-title {
            color: #0066cc;
            font-size: 1.5rem;
            margin: 10px 0;
        }

        .bank-content {
            max-width: 500px;
            margin: 0 auto;
        }

        .bank-instruction {
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.1rem;
        }

        .bank-account-details {
            background: #f5f9ff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #d6e4ff;
        }

        .dark-mode .bank-account-details {
            background: #1a2a4a;
            border-color: #2c3e50;
        }

        .bank-detail-row {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #d6e4ff;
        }

        .dark-mode .bank-detail-row {
            border-bottom-color: #2c3e50;
        }

        .bank-detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .bank-detail-label {
            font-weight: bold;
            min-width: 180px;
            color: #333;
        }

        .dark-mode .bank-detail-label {
            color: #ecf0f1;
        }

        .bank-detail-value {
            color: #0066cc;
            font-weight: bold;
        }

        .bank-instruction-steps {
            margin-bottom: 25px;
        }

        .bank-instruction-steps h4 {
            margin-bottom: 15px;
            color: #333;
        }

        .dark-mode .bank-instruction-steps h4 {
            color: #ecf0f1;
        }

        .bank-instruction-steps ol {
            padding-left: 20px;
        }

        .bank-instruction-steps li {
            margin-bottom: 10px;
            color: #555;
        }

        .dark-mode .bank-instruction-steps li {
            color: #bdc3c7;
        }

        .bank-note {
            font-size: 0.9rem;
            color: #666;
            text-align: center;
            margin-top: 20px;
        }

        .dark-mode .bank-note {
            color: #bdc3c7;
        }
    </style>

    <style>
        /* Modal Styles */
        .modern-payment-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modern-payment-modal-content {
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 30px;
            position: relative;
            animation: modalFadeIn 0.3s;
        }

        .dark-mode .modern-payment-modal-content {
            background: #2c3e50;
        }

        .modern-payment-modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #555;
        }

        .dark-mode .modern-payment-modal-close {
            color: #ecf0f1;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-color-info {
    margin-top: 5px;
    font-size: 0.85rem;
    color: #666;
}

.dark-mode .product-color-info {
    color: #bdc3c7;
}

.product-color-display {
    display: inline-block;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    margin-right: 5px;
    vertical-align: middle;
    border: 1px solid #ddd;
}

.dark-mode .product-color-display {
    border-color: #555;
}
    </style>

    <!-- Payment Method Modal -->
    <div id="paymentModal" class="modern-payment-modal">
        <div class="modern-payment-modal-content">
            <span class="modern-payment-modal-close">&times;</span>

            <!-- PayPal Content -->
            <div id="paypal-details" class="modern-payment-method-details">
                <div class="paypal-container">
                    <div class="paypal-header">
                        <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg" alt="PayPal" class="paypal-logo">
                        <h3 class="paypal-title">Pay with PayPal</h3>
                    </div>

                    <div class="paypal-content">

                        <div class="paypal-guest-section">
                            <h4 class="paypal-guest-title">Pay with debit or credit card</h4>
                            <div id="paypal-button-container"></div>
                            <p class="paypal-secure-note">
                                <i class="fa fa-lock"></i> Your payments are secure with PayPal
                            </p>
                        </div>

                        <div class="paypal-footer-links">
                            <a href="#" class="paypal-footer-link">Privacy</a>
                            <a href="#" class="paypal-footer-link">Legal</a>
                            <a href="#" class="paypal-footer-link">Policy</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Transfer Content -->
            <div id="bank-details" class="modern-payment-method-details" style="display:none;">
                <div class="bank-header">
                    <i class="fa fa-university bank-icon"></i>
                    <h3 class="bank-title">Bank Transfer Payment</h3>
                </div>

                <div class="bank-content">
                    <div class="bank-instruction">
                        <p>Please complete your payment to the following virtual account:</p>
                    </div>

                    <div class="bank-account-details">
                        <div class="bank-detail-row">
                            <span class="bank-detail-label">Bank Name:</span>
                            <span class="bank-detail-value">Bank Central Asia (BCA)</span>
                        </div>
                        <div class="bank-detail-row">
                            <span class="bank-detail-label">Virtual Account Number:</span>
                            <span class="bank-detail-value">8880 1234 5678 9012</span>
                        </div>
                        <div class="bank-detail-row">
                            <span class="bank-detail-label">Account Name:</span>
                            <span class="bank-detail-value">GADGET MS STORE</span>
                        </div>
                        <div class="bank-detail-row">
                            <span class="bank-detail-label">Total Amount:</span>
                            <span class="bank-detail-value">$<?= number_format($total, 2) ?></span>
                        </div>
                        <div class="bank-detail-row">
                            <span class="bank-detail-label">Expiry Time:</span>
                            <span class="bank-detail-value">24 hours from now</span>
                        </div>
                    </div>

                    <div class="bank-instruction-steps">
                        <h4>Payment Instructions:</h4>
                        <ol>
                            <li>Open your mobile banking or ATM application</li>
                            <li>Select "Transfer" or "Virtual Account Payment"</li>
                            <li>Enter the virtual account number above</li>
                            <li>Confirm the amount and complete the payment</li>
                            <li>Your order will be processed automatically after payment</li>
                        </ol>
                    </div>

                    <div class="bank-note">
                        <p><strong>Note:</strong> Please complete your payment within 24 hours. Your order will be cancelled if payment is not received.</p>
                    </div>
                </div>
            </div>
        </div>
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

    // Payment Method Handling
    const paymentOptions = document.querySelectorAll('.modern-payment-option');
    const paymentModal = document.getElementById('paymentModal');
    const modalClose = document.querySelector('.modern-payment-modal-close');
    let paypalButtonsInitialized = false;

    // Initialize with PayPal selected
    document.getElementById('paypal-option').classList.add('selected');

    // Initialize PayPal buttons once when page loads
    function initPayPalButtons() {
        if (paypalButtonsInitialized) {
            return; // Already initialized
        }

        // Clear existing buttons if any
        document.getElementById('paypal-button-container').innerHTML = '';

        // Render the PayPal button
        paypal.Buttons({
            style: {
                shape: 'rect',
                color: 'gold',
                layout: 'vertical',
                label: 'paypal'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= $total ?>',
                            currency_code: 'USD'
                        },
                        payee: {
                            email_address: 'gugugaga123@gmail.com'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction completed by ' + details.payer.name.given_name + '!');
                    document.getElementById('checkout_form').submit();
                });
            },
            onError: function(err) {
                console.log(err);
                alert('An error occurred during the transaction. Please try again.');
            }
        }).render('#paypal-button-container');
        
        paypalButtonsInitialized = true;
    }

    // Initialize PayPal buttons when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initPayPalButtons();
    });

    // Payment option selection
    paymentOptions.forEach(option => {
        option.addEventListener('click', (e) => {
            // Remove selected class from all options
            paymentOptions.forEach(opt => opt.classList.remove('selected'));

            // Add selected class to clicked option
            option.classList.add('selected');

            // Check the radio input
            const radioInput = option.querySelector('.modern-payment-input');
            radioInput.checked = true;

            // Show modal when payment option is clicked (excluding radio input clicks)
            if (!e.target.classList.contains('modern-payment-input')) {
                const paymentMethod = option.id.replace('-option', '');
                showPaymentDetails(paymentMethod);
                paymentModal.style.display = 'flex';
            }
        });
    });

    // Close modal when X is clicked
    modalClose.addEventListener('click', () => {
        paymentModal.style.display = 'none';
    });

    // Close modal when clicking outside content
    window.addEventListener('click', (e) => {
        if (e.target === paymentModal) {
            paymentModal.style.display = 'none';
        }
    });

    // Show details for selected payment method
    function showPaymentDetails(method) {
        // Hide all details first
        document.querySelectorAll('.modern-payment-method-details').forEach(detail => {
            detail.style.display = 'none';
        });

        // Show selected method details
        document.getElementById(`${method}-details`).style.display = 'block';
    }

    // Keyboard accessibility
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && paymentModal.style.display === 'flex') {
            paymentModal.style.display = 'none';
        }
    });

    // Place order button - submit form after payment method is selected
    document.querySelector('.modern-order-button').addEventListener('click', function(e) {
        const selectedPayment = document.querySelector('input[name="payment"]:checked').value;

        if (selectedPayment === 'paypal') {
            e.preventDefault();
            showPaymentDetails('paypal');
            paymentModal.style.display = 'flex';
        } else {
            // For other payment methods, submit the form directly
            document.getElementById('checkout_form').submit();
        }
    });
</script>
</body>

</html>
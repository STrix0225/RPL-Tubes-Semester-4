<?php
include('../../Database/connection.php');

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login-customer.php");
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header("Location: order.php");
    exit();
}

$order_id = (int)$_GET['order_id'];
$customer_id = $_SESSION['customer_id'];

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$stmt->bind_param('ii', $order_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: order.php");
    exit();
}

// Get order items
$stmt_items = $conn->prepare("SELECT oi.*, p.product_name, p.product_image1 
                             FROM order_items oi 
                             JOIN products p ON oi.product_id = p.product_id 
                             WHERE oi.order_id = ?");
$stmt_items->bind_param('i', $order_id);
$stmt_items->execute();
$order_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate subtotal
$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += $item['product_price'] * $item['product_quantity'];
}

// Determine status class
$status_class = '';
switch(strtolower($order['order_status'])) {
    case 'pending': $status_class = 'status-pending'; break;
    case 'processing': $status_class = 'status-processing'; break;
    case 'shipped': $status_class = 'status-shipped'; break;
    case 'delivered': $status_class = 'status-delivered'; break;
    case 'cancelled': $status_class = 'status-cancelled'; break;
    default: $status_class = 'status-pending';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Gadget MS - Order Details</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Colo Shop Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="styles/bootstrap4/bootstrap.min.css">
    <link href="plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.carousel.css">
    <link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/animate.css">
    <link rel="stylesheet" type="text/css" href="styles/main_styles.css">
    <link rel="stylesheet" type="text/css" href="styles/responsive.css">
    <link rel="shortcut icon" href="../gems-customer-pages/images/Background3.jpg" />
    <style>
        .order-detail-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .order-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .order-body {
            padding: 20px;
        }
        
        .order-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .order-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 4px;
        }
        
        .order-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-shipped {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-delivered {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .order-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .tracking-steps {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }
        
        .tracking-step {
            text-align: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: #6c757d;
        }
        
        .step-active .step-icon {
            background-color: #6a11cb;
            color: white;
        }
        
        .step-complete .step-icon {
            background-color: #28a745;
            color: white;
        }
        
        .step-title {
            font-size: 12px;
            color: #6c757d;
        }
        
        .step-active .step-title {
            color: #6a11cb;
            font-weight: bold;
        }
        
        .step-complete .step-title {
            color: #28a745;
            font-weight: bold;
        }
        
        .tracking-line {
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e9ecef;
            z-index: 0;
        }
        
        .tracking-progress {
            position: absolute;
            top: 20px;
            left: 0;
            height: 2px;
            background-color: #28a745;
            z-index: 1;
        }
        
        /* Dark mode styles */
        body.dark-mode .order-detail-card {
            border-color: #444;
            background-color: #1e1e1e;
        }
        
        body.dark-mode .order-header {
            background-color: #2d2d2d;
            border-color: #444;
        }
        
        body.dark-mode .order-body {
            background-color: #1e1e1e;
        }
        
        body.dark-mode .order-item {
            border-color: #333;
        }
        
        body.dark-mode .order-summary {
            background-color: #2d2d2d;
        }
        
        body.dark-mode .step-icon {
            background-color: #444;
            color: #e0e0e0;
        }
        
        body.dark-mode .tracking-line {
            background-color: #444;
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
                                            <li><a href="logout-customer.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></li>
                                        <?php else: ?>
                                            <li><a href="login-customer.php"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a></li>
                                            <li><a href="register-customer.php"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a></li>
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
                                <li><a href="order.php" class="active">my orders</a></li>
                            </ul>
                            <ul class="navbar_user">
                                <li><a href="#"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                                <li class="checkout">
                                    <a href="cart.php">
                                        <i class="fa fa-shopping-cart" aria-hidden="true" id="dark-mode-cart"></i>
                                        <span id="checkout_items" class="checkout_items"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span>
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
                        <li><a href="login-customer.php"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a></li>
                        <li><a href="register-customer.php"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a></li>
                    </ul>
                </li>
                <li class="menu_item"><a href="dashboard.php">home</a></li>
                <li class="menu_item"><a href="shop.php">shop</a></li>
                <li class="menu_item"><a href="contact.php">contact</a></li>
                <li class="menu_item"><a href="order.php">my orders</a></li>
            </ul>
        </div>
    </div>

    <!-- Order Detail -->
     <br>
     <br>
     <br>
     <br>
        <br>
     <br>
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Order Details</h2>
                    <a href="order.php" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Orders
                    </a>
                </div>
                
                <div class="order-detail-card">
                    <div class="order-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">Order #<?= $order['order_id'] ?></h4>
                            <small class="text-muted">Placed on <?= date('F j, Y, g:i a', strtotime($order['order_date'])) ?></small>
                        </div>
                        <div>
                            <span class="order-status <?= $status_class ?>"><?= $order['order_status'] ?></span>
                        </div>
                    </div>
                    
                    <div class="order-body">
                        <!-- Order Tracking -->
                        <div class="tracking-steps">
                            <div class="tracking-line"></div>
                            <div class="tracking-progress" style="width: 
                                <?php 
                                    if ($order['order_status'] == 'Pending') echo '0%';
                                    elseif ($order['order_status'] == 'Processing') echo '33%';
                                    elseif ($order['order_status'] == 'Shipped') echo '66%';
                                    elseif ($order['order_status'] == 'Delivered') echo '100%';
                                    elseif ($order['order_status'] == 'Cancelled') echo '0%';
                                    else echo '0%';
                                ?>">
                            </div>
                            
                            <div class="tracking-step <?= $order['order_status'] == 'pending' ? 'step-active' : '' ?> <?= in_array($order['order_status'], ['Processing', 'Shipped', 'Delivered']) ? 'step-complete' : '' ?>">
                                <div class="step-icon">
                                    <i class="fa fa-shopping-cart"></i>
                                </div>
                                <div class="step-title">Order Placed</div>
                            </div>
                            
                            <div class="tracking-step <?= $order['order_status'] == 'processing' ? 'step-active' : '' ?> <?= in_array($order['order_status'], ['Shipped', 'Delivered']) ? 'step-complete' : '' ?>">
                                <div class="step-icon">
                                    <i class="fa fa-cog"></i>
                                </div>
                                <div class="step-title">Processing</div>
                            </div>
                            
                            <div class="tracking-step <?= $order['order_status'] == 'processing' ? 'step-active' : '' ?> <?= $order['order_status'] == '' ? 'step-complete' : '' ?>">
                                <div class="step-icon">
                                    <i class="fa fa-truck"></i>
                                </div>
                                <div class="step-title">Shipped</div>
                            </div>
                            
                            <div class="tracking-step <?= $order['order_status'] == 'completed' ? 'step-active' : '' ?>">
                                <div class="step-icon">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="step-title">Delivered</div>
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <h5 class="mb-4">Order Items</h5>
                        <?php foreach ($order_items as $item): ?>
                            <div class="order-item">
                                <img src="images/<?= $item['product_image1'] ?>" alt="<?= $item['product_name'] ?>" class="order-item-img">
                                <div class="flex-grow-1">
                                    <h6><?= $item['product_name'] ?></h6>
                                    <div class="d-flex justify-content-between">
                                        <span>$<?= number_format($item['product_price'], 2) ?> x <?= $item['product_quantity'] ?></span>
                                        <span>$<?= number_format($item['product_price'] * $item['product_quantity'], 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Order Summary -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>Shipping Information</h5>
                                <div class="order-summary mt-3">
                                    <p class="mb-2"><strong>Name:</strong> <?= $order['customer_name'] ?? 'N/A' ?></p>
                                    <p class="mb-2"><strong>Phone:</strong> <?= $order['customer_phone'] ?? 'N/A' ?></p>
                                    <p class="mb-2"><strong>Address:</strong> <?= $order['customer_address'] ?? 'N/A' ?></p>
                                    <p class="mb-0"><strong>City:</strong> <?= $order['customer_city'] ?? 'N/A' ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Order Summary</h5>
                                <div class="order-summary mt-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span>$<?= number_format($subtotal, 2) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping:</span>
                                        <span>$<?= number_format($order['shipping_cost'] ?? 0, 2) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Payment Method:</span>
                                        <span><?= $order['payment_method'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                        <h6>Total:</h6>
                                        <h6>$<?= number_format($order['order_cost'], 2) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Actions -->
                        <div class="d-flex justify-content-end mt-4">
                            <?php if (strtolower($order['order_status']) == 'pending'): ?>
                                <a href="cancel-order.php?order_id=<?= $order['order_id'] ?>" class="btn btn-outline-danger mr-2">
                                    <i class="fa fa-times mr-1"></i> Cancel Order
                                </a>
                            <?php endif; ?>
                            <a href="shop.php" class="btn btn-primary">
                                <i class="fa fa-shopping-bag mr-1"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefit -->
    <div class="benefit">
        <div class="container">
            <div class="row benefit_row">
                <div class="col-lg-3 benefit_col">
                    <div class="benefit_item d-flex flex-row align-items-center">
                        <div class="benefit_icon"><i class="fa fa-truck" aria-hidden="true"></i></div>
                        <div class="benefit_content">
                            <h6>free shipping</h6>
                            <p>Suffered Alteration in Some Form</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 benefit_col">
                    <div class="benefit_item d-flex flex-row align-items-center">
                        <div class="benefit_icon"><i class="fa fa-money" aria-hidden="true"></i></div>
                        <div class="benefit_content">
                            <h6>cach on delivery</h6>
                            <p>The Internet Tend To Repeat</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 benefit_col">
                    <div class="benefit_item d-flex flex-row align-items-center">
                        <div class="benefit_icon"><i class="fa fa-undo" aria-hidden="true"></i></div>
                        <div class="benefit_content">
                            <h6>45 days return</h6>
                            <p>Making it Look Like Readable</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 benefit_col">
                    <div class="benefit_item d-flex flex-row align-items-center">
                        <div class="benefit_icon"><i class="fa fa-clock-o" aria-hidden="true"></i></div>
                        <div class="benefit_content">
                            <h6>opening all week</h6>
                            <p>8AM - 09PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                            <li><a href="contact.php">Contact us</a></li>
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
<script src="plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
<script src="plugins/easing/easing.js"></script>
<script>
// Dark Mode Toggle
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const darkModeIcon = darkModeToggle.querySelector('i');
    
    // Check for saved user preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        darkModeIcon.classList.remove('fa-moon-o');
        darkModeIcon.classList.add('fa-sun-o');
    }
    
    // Toggle dark mode
    darkModeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        document.body.classList.toggle('dark-mode');
        
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
            darkModeIcon.classList.remove('fa-moon-o');
            darkModeIcon.classList.add('fa-sun-o');
        } else {
            localStorage.setItem('darkMode', 'disabled');
            darkModeIcon.classList.remove('fa-sun-o');
            darkModeIcon.classList.add('fa-moon-o');
        }
    });
});
</script>
</body>
</html>
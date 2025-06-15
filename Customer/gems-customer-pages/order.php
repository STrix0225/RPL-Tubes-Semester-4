<?php
include('../../Database/connection.php');


// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login-customer.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Get customer orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Function to get order items
function getOrderItems($conn, $order_id) {
    $stmt = $conn->prepare("SELECT oi.*, p.product_name, p.product_image1 
                           FROM order_items oi 
                           JOIN products p ON oi.product_id = p.product_id 
                           WHERE oi.order_id = ?");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Gadget MS - My Orders</title>
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
        .order-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .order-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .order-body {
            padding: 15px;
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
        
        .no-orders {
            text-align: center;
            padding: 50px 0;
        }
        
        /* Dark mode styles */
        body.dark-mode .order-card {
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

    <!-- Order History -->
        <br>
     <br>
     <br>
     <br>
        <br>
     <br>
     
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">My Orders</h2>
                
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): 
                        $order_items = getOrderItems($conn, $order['order_id']);
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
                        <div class="order-card">
                            <div class="order-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Order #<?= $order['order_id'] ?></h5>
                                    <small class="text-muted"><?= date('F j, Y, g:i a', strtotime($order['order_date'])) ?></small>
                                </div>
                                <div>
                                    <span class="order-status <?= $status_class ?>"><?= $order['order_status'] ?></span>
                                </div>
                            </div>
                            <div class="order-body">
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
                                
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <div>
                                        <small class="text-muted">Payment Method: <?= $order['payment_method'] ?></small>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted">Shipping: $<?= number_format($order['shipping_cost'] ?? 0, 2) ?></small>
                                        <h5 class="mb-0">Total: $<?= number_format($order['order_cost'], 2) ?></h5>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-3">
                                    <a href="order-detail.php?order_id=<?= $order['order_id'] ?>" class="btn btn-outline-primary btn-sm mr-2">
                                        <i class="fa fa-eye mr-1"></i> View Details
                                    </a>
                                    <?php if (strtolower($order['order_status']) == 'pending'): ?>
                                        <a href="cancel-order.php?order_id=<?= $order['order_id'] ?>" class="btn btn-outline-danger btn-sm">
                                            <i class="fa fa-times mr-1"></i> Cancel Order
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-orders">
                        <img src="images/empty-order.png" alt="No orders" width="200" class="mb-4">
                        <h4>You haven't placed any orders yet</h4>
                        <p class="text-muted mb-4">When you do, their status will appear here</p>
                        <a href="shop.php" class="btn btn-primary">Start Shopping</a>
                    </div>
                <?php endif; ?>
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
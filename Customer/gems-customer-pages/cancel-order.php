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

// Get order details to verify ownership
$query = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param('ii', $order_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: order.php");
    exit();
}

// Check if order is already cancelled
if (strtolower($order['order_status']) == 'cancelled') {
    $_SESSION['error_message'] = "This order has already been cancelled.";
    header("Location: order-detail.php?order_id=" . $order_id);
    exit();
}

// Check if order can be cancelled (only pending orders)
if (strtolower($order['order_status']) != 'pending') {
    $_SESSION['error_message'] = "Only pending orders can be cancelled.";
    header("Location: order-detail.php?order_id=" . $order_id);
    exit();
}

// Process cancellation if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    
    if (empty($reason)) {
        $_SESSION['error_message'] = "Please provide a cancellation reason.";
    } else {
        // If "Other" was selected and there's additional text
        if ($reason === 'Other' && isset($_POST['other_reason']) && !empty(trim($_POST['other_reason']))) {
            $reason = 'Other: ' . trim($_POST['other_reason']);
        }
        
        // Update order status to cancelled
        $update_query = "UPDATE orders SET order_status = 'cancelled', cancellation_reason = ? WHERE order_id = ?";
        $update_stmt = $conn->prepare($update_query);
        
        if (!$update_stmt) {
            die("Error preparing update statement: " . $conn->error);
        }
        
        $update_stmt->bind_param('si', $reason, $order_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Order #" . $order_id . " has been successfully cancelled.";
            header("Location: order-detail.php?order_id=" . $order_id);
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to cancel the order. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Gadget MS - Cancel Order</title>
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
        .cancel-order-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .cancel-order-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .cancel-icon {
            font-size: 50px;
            color: #dc3545;
            margin-bottom: 15px;
        }
        
        .reason-option {
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .reason-option:hover {
            background-color: #f8f9fa;
        }
        
        .reason-option.active {
            border-color: #dc3545;
            background-color: #fff3f3;
        }
        
        /* Dark mode styles */
        body.dark-mode .cancel-order-container {
            background-color: #1e1e1e;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }
        
        body.dark-mode .reason-option {
            border-color: #444;
            background-color: #2d2d2d;
        }
        
        body.dark-mode .reason-option:hover {
            background-color: #333;
        }
        
        body.dark-mode .reason-option.active {
            border-color: #dc3545;
            background-color: #3a1e1e;
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
                                <li><a href="order.php">my orders</a></li>
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

    <!-- Cancel Order Form -->
     <br>
     <br>
     <br>
     <br>
    <div class="container">
        <div class="cancel-order-container">
            <div class="cancel-order-header">
                <div class="cancel-icon">
                    <i class="fa fa-times-circle"></i>
                </div>
                <h2>Cancel Order #<?= $order_id ?></h2>
                <p>Please tell us why you're cancelling this order</p>
            </div>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error_message'] ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="reason">Cancellation Reason</label>
                    <select class="form-control" id="reason" name="reason" required>
                        <option value="" selected disabled>Select a reason...</option>
                        <option value="Found a better price elsewhere">Found a better price elsewhere</option>
                        <option value="Changed my mind">Changed my mind</option>
                        <option value="Ordered by mistake">Ordered by mistake</option>
                        <option value="Shipping takes too long">Shipping takes too long</option>
                        <option value="Other">Other (please specify below)</option>
                    </select>
                </div>
                
                <div class="form-group" id="otherReasonGroup" style="display: none;">
                    <label for="other_reason">Please specify</label>
                    <textarea class="form-control" id="other_reason" name="other_reason" rows="3"></textarea>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fa fa-times mr-2"></i> Confirm Cancellation
                    </button>
                    <a href="order-detail.php?order_id=<?= $order_id ?>" class="btn btn-outline-secondary btn-block mt-2">
                        <i class="fa fa-arrow-left mr-2"></i> Back to Order
                    </a>
                </div>
            </form>
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
    
    // Show/hide other reason textarea
    $('#reason').change(function() {
        if ($(this).val() === 'Other') {
            $('#otherReasonGroup').show();
            $('#other_reason').attr('required', true);
        } else {
            $('#otherReasonGroup').hide();
            $('#other_reason').removeAttr('required');
        }
    });
    
    // Combine reason if "Other" is selected
    $('form').submit(function() {
        if ($('#reason').val() === 'Other') {
            const otherReason = $('#other_reason').val();
            if (otherReason.trim() !== '') {
                $('#reason').val('Other: ' + otherReason);
            }
        }
    });
});
</script>
</body>
</html>
<?php
session_start();
include('../Database/connection.php');

// Redirect jika cart kosong
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Hitung total seperti di cart.php
$subtotal = 0;
$cart_items = [];

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $cart_item = $_SESSION['cart'][$product_id];
        
        $has_discount = !empty($product['product_discount']) && $product['product_discount'] > 0;
        $price = $has_discount ? $product['product_price'] * (1 - $product['product_discount'] / 100) : $product['product_price'];
        $total = $price * $cart_item['quantity'];

        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['product_name'],
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses data checkout di sini
    // Simpan ke database, kosongkan cart, dll.
    
    // Contoh sederhana:
    $_SESSION['order_success'] = true;
    unset($_SESSION['cart']);
    header("Location: order-success.php");
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
                                            <li><a href="../gems-login/login-customer.php"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a></li>
                                            <li><a href="../gems-login/register-customer.php"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a></li>
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

        <!-- Checkout -->
         <br>
         <br>
         <br>
         <br>
          <br>
         <br>
         <br>
         <br>
          <br>
         <br>
         <br>
         <br>
        <div class="checkout_section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="billing checkout_section">
                            <div class="section_title">Billing Details</div>
                            <div class="section_subtitle">Enter your billing information</div>
                            <form id="checkout_form" method="POST" class="checkout_form">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <label for="checkout_first_name">First Name*</label>
                                        <input type="text" id="checkout_first_name" class="checkout_input" required="required">
                                    </div>
                                    <div class="col-xl-6">
                                        <label for="checkout_last_name">Last Name*</label>
                                        <input type="text" id="checkout_last_name" class="checkout_input" required="required">
                                    </div>
                                </div>
                                <div>
                                    <label for="checkout_company">Company Name</label>
                                    <input type="text" id="checkout_company" class="checkout_input">
                                </div>
                                <div>
                                    <label for="checkout_address">Address*</label>
                                    <input type="text" id="checkout_address" class="checkout_input" required="required">
                                </div>
                                <div>
                                    <label for="checkout_address_2">Address Line 2</label>
                                    <input type="text" id="checkout_address_2" class="checkout_input">
                                </div>
                                <div>
                                    <label for="checkout_city">City*</label>
                                    <input type="text" id="checkout_city" class="checkout_input" required="required">
                                </div>
                                <div>
                                    <label for="checkout_state">State/Province*</label>
                                    <input type="text" id="checkout_state" class="checkout_input" required="required">
                                </div>
                                <div>
                                    <label for="checkout_zipcode">Zip/Postal Code*</label>
                                    <input type="text" id="checkout_zipcode" class="checkout_input" required="required">
                                </div>
                                <div>
                                    <label for="checkout_country">Country*</label>
                                    <select id="checkout_country" class="checkout_input" required="required">
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="ID">Indonesia</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="checkout_phone">Phone*</label>
                                    <input type="tel" id="checkout_phone" class="checkout_input" required="required">
                                </div>
                                <div>
                                    <label for="checkout_email">Email*</label>
                                    <input type="email" id="checkout_email" class="checkout_input" required="required">
                                </div>
                                <div class="checkout_extra">
                                    <div>
                                        <input type="checkbox" id="create_account" class="checkout_extra_input">
                                        <label for="create_account">Create an account</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="ship_to_different" class="checkout_extra_input">
                                        <label for="ship_to_different">Ship to different address</label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="order checkout_section">
                            <div class="section_title">Your Order</div>
                            <div class="section_subtitle">Order details</div>
                            <div class="order_list_container">
                                <div class="order_list_bar d-flex flex-row align-items-center justify-content-start">
                                    <div class="order_list_title">Product</div>
                                    <div class="order_list_value ml-auto">Total</div>
                                </div>
                                <ul class="order_list">
                                    <?php foreach ($cart_items as $item): ?>
                                        <li class="d-flex flex-row align-items-center justify-content-start">
                                            <div class="order_list_title"><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></div>
                                            <div class="order_list_value ml-auto">$<?= number_format($item['total'], 2) ?></div>
                                        </li>
                                    <?php endforeach; ?>
                                    <li class="d-flex flex-row align-items-center justify-content-start">
                                        <div class="order_list_title">Subtotal</div>
                                        <div class="order_list_value ml-auto">$<?= number_format($subtotal, 2) ?></div>
                                    </li>
                                    <li class="d-flex flex-row align-items-center justify-content-start">
                                        <div class="order_list_title">Shipping</div>
                                        <div class="order_list_value ml-auto">$<?= number_format($shipping, 2) ?></div>
                                    </li>
                                    <li class="d-flex flex-row align-items-center justify-content-start">
                                        <div class="order_list_title">Total</div>
                                        <div class="order_list_value ml-auto">$<?= number_format($total, 2) ?></div>
                                    </li>
                                </ul>
                            </div>
                            <div class="payment">
                                <div class="payment_options">
                                    <label class="payment_option clearfix">Direct Bank Transfer
                                        <input type="radio" name="payment" checked>
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="payment_option clearfix">Cash on Delivery
                                        <input type="radio" name="payment">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="payment_option clearfix">Paypal
                                        <input type="radio" name="payment">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="order_text">
                                <p>Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our privacy policy.</p>
                            </div>
                            <button type="submit" form="checkout_form" class="order_button">Place Order</button>
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
    </script>
</body>

</html>
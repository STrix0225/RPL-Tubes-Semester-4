<?php
session_start();

include('../../Database/connection.php');

// Ambil order_id dari query string
if (!isset($_GET['order_id'])) {
    header("Location: dashboard.php");
    exit();
}

$order_id = (int)$_GET['order_id'];

// Ambil data pesanan dari database
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// Ambil item pesanan
$stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->bind_param('i', $order_id);
$stmt_items->execute();
$order_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);

// Hitung total dari item jika diperlukan
$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += $item['product_price'] * $item['product_quantity'];
}
$total = $subtotal + ($order['shipping_cost'] ?? 0); // Gunakan shipping_cost dari database
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gadget MS - Order Success</title>
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

        <!-- Order Success -->
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

        <div class="success_section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 text-center">
                        <div class="success_icon mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <h2 class="success_title mb-3">Order Successful!</h2>
                        <div class="confetti mb-4">
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                            <div class="confetti-piece"></div>
                        </div>
                        <p class="success_text lead mb-4">
                            Terima kasih telah berbelanja di GadgetMS. Pesanan Anda telah berhasil diproses.
                        </p>
                        <div class="order_info bg-light p-4 rounded mb-5">
                            <p class="mb-2">Order Number: <strong class="text-primary">#<?= $_GET['order_id'] ?></strong></p>
                            <p class="mb-2">Tanggal Pemesanan: <strong><?= date('d F Y, H:i') ?></strong></p>
                            <p class="mb-0">Total Pembayaran: <strong class="text-success">$<?= number_format($order['order_cost'], 2) ?></strong></p>
                        </div>
                        <div class="success_buttons">
                            <a href="shop.php" class="btn btn-primary btn-lg mr-3">
                                <i class="fa fa-shopping-bag mr-2"></i>Continue Shopping
                            </a>
                            <a href="dashboard.php?orders" class="btn btn-outline-primary btn-lg">
                                <i class="fa fa-list mr-2"></i>View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefit -->
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>


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
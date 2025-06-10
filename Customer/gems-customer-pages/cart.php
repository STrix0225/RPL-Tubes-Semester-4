<?php
session_start();
include('../../Database/connection.php');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle remove item action
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit();
}

// Handle quantity update
if (isset($_POST['update_quantity'])) {
    foreach ($_POST['quantity'] as $id => $quantity) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] = max(1, (int)$quantity);
        }
    }
    header("Location: cart.php");
    exit();
}

// Handle add to cart
if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
    $color = isset($_POST['selected_color']) ? $_POST['selected_color'] : '';

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'color' => $color
        ];
    }

    $_SESSION['message'] = 'Product added to cart successfully!';
    header("Location: cart.php");
    exit();
}

// Calculate totals
$subtotal = 0;
$cart_items = [];

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // In the cart.php file, modify the cart items processing
    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $cart_item = $_SESSION['cart'][$product_id];
        
        $has_discount = !empty($product['product_discount']) && $product['product_discount'] > 0;
        $price = $has_discount ? $product['product_price'] * (1 - $product['product_discount'] / 100) : $product['product_price'];
        $total = $price * $cart_item['quantity'];

        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['product_name'],
            'image' => $product['product_image1'],
            'price' => $product['product_price'],
            'discounted_price' => $price,
            'quantity' => $cart_item['quantity'],
            'total' => $total,
            'has_discount' => $has_discount,
            'discount' => $product['product_discount'],
            'color' => $_SESSION['cart'][$product_id]['color'] ?? ''
        ];

        $subtotal += $total;
    }
}

$shipping = $subtotal > 50 ? 0 : 10;
$total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gadget MS - Shopping Cart</title>
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
    <link rel="stylesheet" type="text/css" href="styles/cart_responsive.css">
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
							</ul>
                                <ul class="navbar_user">
                                    <li class="account">
                                        <a href="#">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                            <i class="fa fa-angle-down" aria-hidden="true"></i>
                                        </a>
                                        <ul class="account_selection">
                                            <?php if (isset($_SESSION['customer_id'])): ?>
                                                <li><a href="register-customer.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</a></li>
                                                <li><a href="change-account.php"><i class="fa fa-cog" aria-hidden="true"></i> Change Account</a></li>
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

        <!-- Cart -->
         <br>
         <br>
         <br>
         <br>
        <div class="cart_section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 offset-lg-1">
                        <div class="cart_container">
                            <div class="cart_title">Shopping Cart</div>
                            <form action="cart.php" method="post">
                                <div class="cart_items">
                                    <ul class="cart_list">
                                        <?php if (!empty($cart_items)): ?>
                                            <?php foreach ($cart_items as $item): ?>
                                                <li class="cart_item clearfix">
                                                    <div class="cart_item_image">
                                                        <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                                    </div>
                                                    <div class="cart_item_info d-flex flex-md-row flex-column justify-content-between">
                                                        <div class="cart_item_name cart_info_col">
                                                            <div class="cart_item_title">Name</div>
                                                            <div class="cart_item_text"><?= htmlspecialchars($item['name']) ?></div>
                                                        </div>
                                                        <div class="cart_item_price cart_info_col">
                                                            <div class="cart_item_title">Price</div>
                                                            <div class="cart_item_text">
                                                                <?php if ($item['has_discount']): ?>
                                                                    <span class="original_price">$<?= number_format($item['price'], 2) ?></span>
                                                                    <span>$<?= number_format($item['discounted_price'], 2) ?></span>
                                                                <?php else: ?>
                                                                    $<?= number_format($item['price'], 2) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="cart_item_quantity cart_info_col">
                                                            <div class="cart_item_title">Quantity</div>
                                                            <div class="cart_item_text">
                                                                <input type="number" class="quantity_input" name="quantity[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1">
                                                            </div>
                                                        </div>
                                                        <div class="cart_item_color cart_info_col">
                                                            <div class="cart_item_title">Color</div>
                                                            <div class="cart_item_text">
                                                                <?php 
                                                                $color = $_SESSION['cart'][$item['id']]['color'] ?? '';
                                                                if (!empty($color)): 
                                                                    $hex_color = match(strtolower($color)) {
                                                                        'black' => '#252525',
                                                                        'white' => '#ffffff',
                                                                        'red' => '#e54e5d',
                                                                        'blue' => '#60b3f3',
                                                                        'green' => '#4CAF50',
                                                                        'yellow' => '#FFEB3B',
                                                                        'purple' => '#9C27B0',
                                                                        'grey', 'gray' => '#9E9E9E',
                                                                        default => '#607D8B'
                                                                    };
                                                                ?>
                                                                <span class="color-display" style="display: inline-block; width: 20px; height: 20px; background-color: <?= $hex_color ?>; border-radius: 50%; vertical-align: middle; margin-right: 5px;"></span>
                                                                <?= htmlspecialchars(ucfirst($color)) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="cart_item_total cart_info_col">
                                                            <div class="cart_item_title">Total</div>
                                                            <div class="cart_item_text">$<?= number_format($item['total'], 2) ?></div>
                                                        </div>
                                                        <div class="cart_item_action cart_info_col">
                                                            <div class="cart_item_title">Action</div>
                                                            <div class="cart_item_text">
                                                                <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-danger btn-sm">Remove</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="cart_item clearfix">
                                                <div class="cart_item_info text-center py-5">
                                                    Your cart is empty. <a href="shop.php">Continue shopping</a>
                                                </div>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <?php if (isset($_SESSION['message'])): ?>
                                    <div class="alert alert-success">
                                        <?php echo $_SESSION['message'];
                                        unset($_SESSION['message']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($cart_items)): ?>
                                    <div class="order_total">
                                        <div class="order_total_content text-md-right">
                                            <div class="order_total_title">Subtotal:</div>
                                            <div class="order_total_amount">$<?= number_format($subtotal, 2) ?></div>
                                        </div>
                                        <div class="order_total_content text-md-right">
                                            <div class="order_total_title">Shipping:</div>
                                            <div class="order_total_amount">$<?= number_format($shipping, 2) ?></div>
                                        </div>
                                        <div class="order_total_content text-md-right">
                                            <div class="order_total_title">Total:</div>
                                            <div class="order_total_amount">$<?= number_format($total, 2) ?></div>
                                        </div>
                                    </div>

                                    <div class="cart_buttons">
                                        <button type="submit" name="update_quantity" class="button cart_button_update">Update Cart</button>
                                        <a href="shop.php" class="button cart_button_checkout">Continue Shopping</a>
                                        <a href="checkout.php" class="button cart_button_checkout">Proceed to Checkout</a>
                                    </div>
                                <?php endif; ?>
                            </form>
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

        <!-- Newsletter -->
        <div class="newsletter">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="newsletter_text d-flex flex-column justify-content-center align-items-lg-start align-items-md-center text-center">
                            <h4>Newsletter</h4>
                            <p>Subscribe to our newsletter and get 20% off your first purchase</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form action="post">
                            <div class="newsletter_form d-flex flex-md-row flex-column flex-xs-column align-items-center justify-content-lg-end justify-content-center">
                                <input id="newsletter_email" type="email" placeholder="Your email" required="required" data-error="Valid email is required.">
                                <button id="newsletter_submit" type="submit" class="newsletter_submit_btn trans_300" value="Submit">subscribe</button>
                            </div>
                        </form>
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
    <script src="js/cart_custom.js"></script>
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
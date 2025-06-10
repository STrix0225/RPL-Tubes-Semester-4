<?php
include('../../Database/connection.php');
session_start();

// Inisialisasi variabel default
$products = [];
$per_page = 12;
$page = 1;
$total_pages = 1;
$cart_items = [];
$subtotal = 0;
$error = null;

try {
    // Handle pencarian produk
    if (isset($_POST['search']) && isset($_POST['product_category'])) {
        $category = $_POST['product_category'];
        $query_products = "SELECT * FROM products WHERE product_category = ?";
        $stmt_products = $conn->prepare($query_products);
        $stmt_products->bind_param('s', $category);
        $stmt_products->execute();
        $result = $stmt_products->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
    } 
    // Handle pagination
    else {
        // Validasi input halaman
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $start = ($page - 1) * $per_page;

        // Query untuk produk dengan pagination
        $sql = "SELECT * FROM products LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $start, $per_page);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);

        // Hitung total halaman
        $total_result = $conn->query("SELECT COUNT(*) FROM products");
        $total_products = $total_result ? $total_result->fetch_row()[0] : 0;
        $total_pages = ceil($total_products / $per_page);
    }

    // Inisialisasi keranjang belanja
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Handle penghapusan item dari keranjang
    if (isset($_GET['remove'])) {
        $remove_id = (int)$_GET['remove'];
        if (isset($_SESSION['cart'][$remove_id])) {
            unset($_SESSION['cart'][$remove_id]);
            header("Location: cart.php");
            exit();
        }
    }

    // Handle update quantity
    if (isset($_POST['update_quantity'])) {
        foreach ($_POST['quantity'] as $id => $quantity) {
            $id = (int)$id;
            $quantity = max(1, (int)$quantity);
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            }
        }
        header("Location: cart.php");
        exit();
    }

    // Hitung total belanja
    if (!empty($_SESSION['cart'])) {
        $ids = array_column($_SESSION['cart'], 'product_id');
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $types = str_repeat('i', count($ids));
            
            $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
            $stmt->bind_param($types, ...$ids);
            $stmt->execute();
            $result = $stmt->get_result();
            $cart_products = $result->fetch_all(MYSQLI_ASSOC);

            foreach ($cart_products as $product) {
    $product_id = $product['product_id'] ?? null; // Tambahkan null coalescing
    
    // Periksa apakah session cart ada dan merupakan array
    $cart = $_SESSION['cart'] ?? [];
    $cart_item_key = false;
    
    if (!empty($cart) && is_array($cart)) {
        $cart_item_key = array_search($product_id, array_column($cart, 'product_id'));
    }
    
    if ($cart_item_key !== false && isset($cart[$cart_item_key])) {
        $cart_item = $cart[$cart_item_key];
        $quantity = $cart_item['quantity'] ?? 0; // Default quantity 0 jika tidak ada

        // Hitung harga diskon dengan pengecekan yang aman
        $discount = $product['product_discount'] ?? 0;
        $has_discount = ($discount > 0);
        
        $product_price = $product['product_price'] ?? 0;
        $price = $has_discount 
            ? $product_price * (1 - $discount / 100) 
            : $product_price;

        $total = $price * $quantity;

        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['product_name'] ?? '',
            'image' => $product['product_image1'] ?? '',
            'price' => $product_price,
            'discounted_price' => $price,
            'quantity' => $quantity,
            'total' => $total,
            'has_discount' => $has_discount,
            'discount' => $discount
        ];

        $subtotal += $total;
    }
}
        }
    }
} catch (Exception $e) {
    $error = "Terjadi kesalahan: " . $e->getMessage();
}

// Tutup koneksi database
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<title>Gadget MS</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="Colo Shop Template">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="styles/bootstrap4/bootstrap.min.css">
	<link href="plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.carousel.css">
	<link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
	<link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/animate.css">
	<link rel="stylesheet" type="text/css" href="plugins/jquery-ui-1.12.1.custom/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="styles/categories_styles.css">
	<link rel="stylesheet" type="text/css" href="styles/categories_responsive.css">
	<link rel="shortcut icon" href="../gems-customer-pages/images/Background3.jpg" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

		<div class="container product_section_container">
			<div class="row">
				<div class="col product_section">
					<!-- Sidebar -->
					<div class="sidebar">
						<div class="sidebar_section">
							<div class="sidebar_title">
								<h5>Product Category</h5>
							</div>
							<ul class="sidebar_categories">
								<li><a href="#">Laptop</a></li>
								<li><a href="#">Handphone</a></li>
								<li><a href="#">Accessories</a></li>
						</div>

						<!-- Price Range Filtering -->
						<div class="sidebar_section">
							<div class="sidebar_title">
								<h5>Filter by Price</h5>
							</div>
							<p>
								<input type="text" id="amount" readonly style="border:0; font-weight:bold;">
							</p>
							<div id="slider-range"></div>
							<div class="filter_button" onclick="window.location.href='shop.php';">
								<span>Clear filter</span>
							</div>
						</div>
					</div>

					<!-- Main Content -->
					<div class="main_content">
						<!-- Products -->
						<div class="products_iso">
							<div class="row">
								<div class="col">
									<!-- Search Bar -->
										<div class="search-box">
											<input type="text" id="search-input" placeholder="Search products..." aria-label="Search products">
											<button id="search-button"><i class="fa fa-search" aria-hidden="true"></i></button>
										</div>

									<!-- Product Sorting -->
									<div class="product_sorting_container product_sorting_container_top">
										<ul class="product_sorting">
											<li>
												<span class="type_sorting_text">Default Sorting</span>
												<i class="fa fa-angle-down"></i>
												<ul class="sorting_type">
													<li class="type_sorting_btn" data-isotope-option='{ "sortBy": "original-order" }'><span>Default Sorting</span></li>
													<li class="type_sorting_btn" data-isotope-option='{ "sortBy": "price" }'><span>Price</span></li>
													<li class="type_sorting_btn" data-isotope-option='{ "sortBy": "name" }'><span>Product Name</span></li>
												</ul>
											</li>
											<li>
												<span>Show</span>
												<span class="num_sorting_text">12</span>
												<i class="fa fa-angle-down"></i>
												<ul class="sorting_num">
													<li class="num_sorting_btn"><span>12</span></li>
													<li class="num_sorting_btn"><span>24</span></li>
													<li class="num_sorting_btn"><span>28</span></li>
												</ul>
											</li>
										</ul>
										<div class="pages d-flex flex-row align-items-center">
											<!-- Previous Button - Always Visible -->
											<div id="prev_page" class="page_prev mr-2">
												<a href="?page=<?php echo $page > 1 ? $page - 1 : 1; ?>" class="<?php echo $page <= 1 ? 'disabled-link' : ''; ?>">
													<i class="fa fa-long-arrow-left" aria-hidden="true"></i>
												</a>
											</div>
											
											<!-- Current Page -->
											<div class="page_current">
												<span><?php echo $page; ?></span>
												<ul class="page_selection">
													<?php for ($i = 1; $i <= $total_pages; $i++): ?>
														<li><a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
													<?php endfor; ?>
												</ul>
											</div>
											
											<!-- Page Total -->
											<div class="page_total mx-2"><span>of</span> <?php echo $total_pages; ?></div>
											
											<!-- Next Button - Always Visible -->
											<div id="next_page" class="page_next ml-2">
												<a href="?page=<?php echo $page < $total_pages ? $page + 1 : $total_pages; ?>" class="<?php echo $page >= $total_pages ? 'disabled-link' : ''; ?>">
													<i class="fa fa-long-arrow-right" aria-hidden="true"></i>
												</a>
											</div>
										</div>
									</div>

									<!-- Product Grid -->
<div class="product-grid">
    <?php 
    // Konversi hasil query ke array jika perlu
    $products_array = [];
    if ($products instanceof mysqli_result) {
        while ($row = $products->fetch_assoc()) {
            $products_array[] = $row;
        }
    } elseif (is_array($products)) {
        $products_array = $products;
    }
    
    if (!empty($products_array)) : 
        foreach ($products_array as $product) :
            // Calculate discount if exists
            $discount_val = $product['product_discount'] ?? 0;
            $has_discount = ($discount_val > 0);
            
            $product_price = $product['product_price'] ?? 0;
            $discounted_price = $has_discount 
                ? $product_price * (1 - $discount_val / 100) 
                : $product_price;
                
            $discount_amount = $has_discount 
                ? $product_price - $discounted_price 
                : 0;
                
            $is_new = empty($product['product_sold']) || ($product['product_sold'] ?? 0) == 0;
    ?>
        <div class="product-item <?php echo htmlspecialchars(strtolower($product['product_category'] ?? '')); ?>">
            <div class="product discount product_filter">
                <div class="product_image">
                    <img src="images/<?php echo htmlspecialchars($product['product_image1'] ?? ''); ?>"
                        alt="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>">
                </div>
                <div class="favorite favorite_left"></div>

                <?php if ($has_discount): ?>
                    <div class="product_bubble product_bubble_right product_bubble_red d-flex flex-column align-items-center">
                        <span>-$<?php echo number_format($discount_amount, 0); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($is_new): ?>
                    <div class="product_bubble product_bubble_left product_bubble_green d-flex flex-column align-items-center">
                        <span>new</span>
                    </div>
                <?php endif; ?>

                <div class="product_info">
                    <h6 class="product_name">
                        <a href="single.php?id=<?php echo $product['product_id'] ?? ''; ?>">
                            <?php echo htmlspecialchars($product['product_name'] ?? ''); ?>
                        </a>
                    </h6>
                    <div class="product_price">
                        $<?php echo number_format($discounted_price, 2); ?>
                        <?php if ($has_discount): ?>
                            <span>$<?php echo number_format($product_price, 2); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="red_button add_to_cart_button">
                <a href="shop-detail.php?id=<?php echo $product['product_id'] ?? ''; ?>">add to cart</a>
            </div>
        </div>
    <?php 
        endforeach;
    else: 
    ?>
        <p class="text-center py-5">No products found</p>
    <?php endif; ?>
</div>
								</div>
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
						<div class="newsletter_form d-flex flex-md-row flex-column flex-xs-column align-items-center justify-content-lg-end justify-content-center">
							<input id="newsletter_email" type="email" placeholder="Your email" required="required" data-error="Valid email is required.">
							<button id="newsletter_submit" type="submit" class="newsletter_submit_btn trans_300" value="Submit">subscribe</button>
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
	<script src="js/categories_custom.js"></script>
	<script>
		// Tambahkan script ini di bagian bawah file
		$(document).ready(function() {
			// Filter by Category
			$('.sidebar_categories li').click(function(e) {
				e.preventDefault();
				let category = $(this).text().trim();

				$.ajax({
					url: 'filter_products.php',
					type: 'POST',
					data: {
						action: 'filter_category',
						category: category
					},
					success: function(response) {
						$('.product-grid').html(response);
					}
				});
			});

			// Filter by Price Range
			$('#slider-range').slider({
				range: true,
				min: 0,
				max: 1000,
				values: [0, 1000],
				slide: function(event, ui) {
					$("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
				},
				stop: function(event, ui) {
					$.ajax({
						url: 'filter_products.php',
						type: 'POST',
						data: {
							action: 'filter_price',
							min_price: ui.values[0],
							max_price: ui.values[1]
						},
						success: function(response) {
							$('.product-grid').html(response);
						}
					});
				}
			});
		});
	</script>
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

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const searchInput = document.getElementById('search-input');
			const searchButton = document.getElementById('search-button');
			const productItems = document.querySelectorAll('.product-item');
			const productGrid = document.querySelector('.product-grid');

			// Create feedback element
			const feedbackEl = document.createElement('div');
			feedbackEl.className = 'search-feedback';
			productGrid.parentNode.insertBefore(feedbackEl, productGrid);

			function performSearch() {
				const searchTerm = searchInput.value.toLowerCase().trim();
				let visibleCount = 0;

				productItems.forEach(item => {
					const productName = item.querySelector('.product_name').textContent.toLowerCase();
					const productDesc = item.querySelector('.product_desc')?.textContent.toLowerCase() || '';
					const productCategory = item.classList.contains('laptop') ? 'laptop' :
						item.classList.contains('handphone') ? 'handphone' :
						item.classList.contains('accessories') ? 'accessories' : '';

					const isMatch = productName.includes(searchTerm) ||
						productDesc.includes(searchTerm) ||
						productCategory.includes(searchTerm);

					if (searchTerm === '' || isMatch) {
						item.classList.remove('hidden');
						item.classList.add('visible');
						visibleCount++;
					} else {
						item.classList.remove('visible');
						item.classList.add('hidden');
					}
				});

				// Show feedback
				if (searchTerm && visibleCount === 0) {
					feedbackEl.textContent = `No products found for "${searchTerm}"`;
					feedbackEl.style.display = 'block';
				} else if (searchTerm) {
					feedbackEl.textContent = `Showing ${visibleCount} results for "${searchTerm}"`;
					feedbackEl.style.display = 'block';
				} else {
					feedbackEl.style.display = 'none';
				}
			}

			// Event listeners
			searchButton.addEventListener('click', performSearch);
			searchInput.addEventListener('keypress', function(e) {
				if (e.key === 'Enter') performSearch();
			});
			searchInput.addEventListener('input', performSearch);

			// Clear search when filter is cleared
			document.querySelector('.filter_button')?.addEventListener('click', function() {
				searchInput.value = '';
				performSearch();
			});
		});

				document.querySelectorAll('.num_sorting_btn').forEach(btn => {
			btn.addEventListener('click', function() {
				const perPage = this.querySelector('span').textContent;
				window.location.href = `?page=1&per_page=${perPage}`;
			});
		});
	</script>

</body>

</html>
<?php
include('../../Database/connection.php');
session_start();

// Query khusus untuk New Arrivals (5 produk terbaru)
$query_new_arrivals = "SELECT * FROM products ORDER BY product_id DESC LIMIT 5";
$new_arrivals = $conn->query($query_new_arrivals);

// Query khusus untuk New Arrivals (5 produk terbaru)
$query_new_arrivals = "SELECT * FROM products ORDER BY product_id DESC LIMIT 5";
$new_arrivals = $conn->query($query_new_arrivals);

// Fungsi untuk mendapatkan produk berdasarkan kategori
function getProductsByCategory($conn, $category = null) {
    if ($category) {
        $query = "SELECT * FROM products WHERE product_category = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $category);
    } else {
        $query = "SELECT * FROM products";
        $stmt = $conn->prepare($query);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}

// Fungsi untuk mendapatkan produk terlaris
function getBestSellers($conn, $limit = 10) {
    $query = "SELECT * FROM products ORDER BY product_sold DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Ambil data produk
if (isset($_POST['search']) && isset($_POST['product_category'])) {
    $products = getProductsByCategory($conn, $_POST['product_category']);
} else {
    $products = getProductsByCategory($conn);
}

// Ambil data produk terlaris
$best_sellers = getBestSellers($conn);

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
            $_SESSION['cart'][$id] = [
                'product_id' => $id,
                'quantity' => max(1, (int)$quantity)
            ];
        }
    }
    header("Location: cart.php");
    exit();
}


$sql = "SELECT * FROM products 
        WHERE product_discount > 0 
        ORDER BY product_discount DESC 
        LIMIT 10";
$best_sellers = $conn->query($sql);
$query = "
    (SELECT * FROM products WHERE product_category = 'Laptop' ORDER BY product_id DESC LIMIT 2)
    UNION
    (SELECT * FROM products WHERE product_category = 'Handphone' ORDER BY product_id DESC LIMIT 2)
    UNION
    (SELECT * FROM products WHERE product_category = 'Accessories' ORDER BY product_id DESC LIMIT 2)
    UNION
    (SELECT * FROM products ORDER BY product_id DESC LIMIT 8)
    LIMIT 8
";

$new_arrivals = $conn->query($query);

// 2. Kemudian simpan semua produk ke dalam array dan urutkan
$all_products = array();
while($product = $best_sellers->fetch_assoc()) {
    $all_products[] = $product;
}

// Urutkan berdasarkan diskon terbesar
usort($all_products, function($a, $b) {
    return $b['product_discount'] - $a['product_discount'];
});

// Ambil hanya 10 produk teratas
$top_discounted = array_slice($all_products, 0, 10);
?>

<!DOCTYPE html>
<html lang="id">
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
<link rel="stylesheet" type="text/css" href="styles/main_styles.css">
<link rel="stylesheet" type="text/css" href="styles/responsive.css">
<link href="plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
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
	<div class="hamburger_menu">
		<div class="hamburger_close"><i class="fa fa-times" aria-hidden="true"></i></div>
		<div class="hamburger_menu_content text-right">
			<ul class="menu_top_nav">
				<li class="menu_item has-children">
					<a href="#">
						usd
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="menu_selection">
						<li><a href="#">cad</a></li>
						<li><a href="#">aud</a></li>
						<li><a href="#">eur</a></li>
						<li><a href="#">gbp</a></li>
					</ul>
				</li>
				<li class="menu_item has-children">
					<a href="#">
						English
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="menu_selection">
						<li><a href="#">French</a></li>
						<li><a href="#">Italian</a></li>
						<li><a href="#">German</a></li>
						<li><a href="#">Spanish</a></li>
					</ul>
				</li>
				<li class="menu_item has-children">
					<a href="#">
						My Account
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="menu_selection">
						<li><a href="#"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a></li>
						<li><a href="#"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a></li>
					</ul>
				</li>
				<li class="menu_item"><a href="#">home</a></li>
				<li class="menu_item"><a href="#">shop</a></li>
				<li class="menu_item"><a href="#">promotion</a></li>
				<li class="menu_item"><a href="#">pages</a></li>
				<li class="menu_item"><a href="#">blog</a></li>	
				<li class="menu_item"><a href="#">contact</a></li>
			</ul>
		</div>
	</div>

<div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="10000">
    <!-- Indicators -->
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2"></button>
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="3"></button>
    </div>
    
    <!-- Slides -->
    <div class="carousel-inner">
        <!-- Slide 1 (Current Active) -->
        <div class="carousel-item active" style="background-image:url(images/Background5.avif)">
            <div class="container fill_height">
                <div class="row align-items-center fill_height">
                    <div class="col">
                        <div class="main_slider_content">
                            <h6>Spring / Summer Collection 2025</h6>
                            <h1>Get up to 30% Off New Arrivals</h1>
                            <div class="red_button shop_now_button"><a href="shop.php">shop now</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 2 (Tempat untuk konten kedua) -->
        <div class="carousel-item" style="background-image:url(images/background2.jpg)">
            <div class="container fill_height">
                <div class="row align-items-center fill_height">
                    <div class="col">
                        <div class="main_slider_content">
                            <!-- Konten slide 2 disini -->
                            <h6>Autumn Collection 2025</h6>
                            <h1>New Trends for the Cool Season</h1>
                            <div class="red_button shop_now_button"><a href="shop.php">shop now</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 3 (Tempat untuk konten ketiga) -->
        <div class="carousel-item" style="background-image:url(images/background3.jpg)">
            <div class="container fill_height">
                <div class="row align-items-center fill_height">
                    <div class="col">
                        <div class="main_slider_content">
                            <!-- Konten slide 3 disini -->
                            <h6>Winter Specials 2025</h6>
                            <h1>Warm Styles for Cold Days</h1>
                            <div class="red_button shop_now_button"><a href="shop.php">shop now</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 4 (Tempat untuk konten keempat) -->
        <div class="carousel-item" style="background-image:url(images/background4.jpg)">
            <div class="container fill_height">
                <div class="row align-items-center fill_height">
                    <div class="col">
                        <div class="main_slider_content">
                            <!-- Konten slide 4 disini -->
                            <h6>Limited Edition</h6>
                            <h1>Exclusive Designs Just for You</h1>
                            <div class="red_button shop_now_button"><a href="shop.php">shop now</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation Buttons (Hover only) -->
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden"></span>
    </button>
</div>

	<!-- Banner -->
	<div class="banner">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<div class="banner_item align-items-center" style="background-image:url(images/IP1.webp)">
						<div class="banner_category">
							<a href="shop.php">Handphone's</a>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="banner_item align-items-center" style="background-image:url(images/Headphone4.jpg)">
						<div class="banner_category">
							<a href="shop.php">accessories's</a>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="banner_item align-items-center" style="background-image:url(images/background2.jpg)">
						<div class="banner_category">
							<a href="shop.php">Laptop's</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>>

	<!-- New Arrivals -->
<div class="new_arrivals">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <div class="section_title new_arrivals_title">
                    <h2>New Arrivals</h2>
                </div>
            </div>
        </div>

        <!-- Filter Kategori -->
        <div class="row align-items-center">
            <div class="col text-center">
                <div class="new_arrivals_sorting">
                    <ul class="arrivals_grid_sorting clearfix button-group filters-button-group">
                        <li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center active is-checked hover" data-filter="*">All</li>
                        <li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center hover" data-filter=".laptop">Laptop</li>
                        <li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center hover" data-filter=".accessories">Accessories</li>
                        <li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center hover" data-filter=".handphone">Handphone</li>
                    </ul>
                </div>
            </div>
        </div>

<!-- Product Slider (Owl Carousel) -->
        <div class="row">
            <div class="col">
                <div class="product_slider_container">
                    <div class="owl-carousel owl-theme product_slider">
                        <?php while ($product = $new_arrivals->fetch_assoc()): 
                            $has_discount = !empty($product['product_discount']) && $product['product_discount'] > 0;
                            $discounted_price = $has_discount ? $product['product_price'] * (1 - $product['product_discount']/100) : $product['product_price'];
                            $discount_amount = $has_discount ? $product['product_price'] - $discounted_price : 0;
                            $category_class = strtolower(str_replace(' ', '-', $product['product_category']));
                        ?>
                        <div class="owl-item product_slider_item <?php echo htmlspecialchars($category_class); ?>">
                            <div class="product-item">
                                <div class="product discount">
                                    <div class="product_image">
                                        <img src="images/<?php echo htmlspecialchars($product['product_image1']); ?>" 
                                            alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    </div>
                                    <div class="favorite favorite_left"></div>
                                    
                                    <?php if ($has_discount): ?>
                                    <div class="product_bubble product_bubble_right product_bubble_red d-flex flex-column align-items-center">
                                        <span>-$<?php echo number_format($discount_amount, 0); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="product_info">
                                        <h6 class="product_name">
                                            <a href="single.php?id=<?php echo $product['product_id']; ?>">
                                                <?php echo htmlspecialchars($product['product_name']); ?>
                                            </a>
                                        </h6>
                                        <div class="product_price">
                                            $<?php echo number_format($discounted_price, 2); ?>
                                            <?php if ($has_discount): ?>
                                                <span>$<?php echo number_format($product['product_price'], 2); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="red_button add_to_cart_button">
                                    <a href="shop-detail.php?id=<?php echo $product['product_id']; ?>">Add to Cart</a>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Slider Navigation -->
                    <div class="product_slider_nav_left product_slider_nav d-flex align-items-center justify-content-center flex-column">
                        <i class="fa fa-chevron-left" aria-hidden="true"></i>
                    </div>
                    <div class="product_slider_nav_right product_slider_nav d-flex align-items-center justify-content-center flex-column">
                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


	<!-- Flash Sale -->
<div class="deal_ofthe_week">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="deal_ofthe_week_img">
                    <?php
                    // Get top 3 products with highest discounts
                    $deal_products_query = "SELECT * FROM products WHERE product_discount > 0 ORDER BY product_discount DESC LIMIT 3";
                    $deal_products_result = $conn->query($deal_products_query);
                    $deal_products = [];
                    while($row = $deal_products_result->fetch_assoc()) {
                        $deal_products[] = $row;
                    }
                    
                    if(!empty($deal_products)) {
                        echo '<img id="flashSaleImage" src="images/'.htmlspecialchars($deal_products[0]['product_image1']).'" alt="Deal of the Week" data-product-id="'.$deal_products[0]['product_id'].'">';
                    }
                    ?>
                </div>
            </div>
            <div class="col-lg-6 text-right deal_ofthe_week_col">
                <div class="deal_ofthe_week_content d-flex flex-column align-items-center float-right">
                    <div class="section_title">
                        <h2>Flash Sale !!!</h2>
                    </div>
                    <ul class="timer">
                        <li class="d-inline-flex flex-column justify-content-center align-items-center">
                            <div id="hour" class="timer_num">00</div>
                            <div class="timer_unit">Hours</div>
                        </li>
                        <li class="d-inline-flex flex-column justify-content-center align-items-center">
                            <div id="minute" class="timer_num">00</div>
                            <div class="timer_unit">Mins</div>
                        </li>
                        <li class="d-inline-flex flex-column justify-content-center align-items-center">
                            <div id="second" class="timer_num">00</div>
                            <div class="timer_unit">Sec</div>
                        </li>
                    </ul>
                    <div class="red_button deal_ofthe_week_button">
                        <a id="flashSaleLink" href="shop-detail.php?id=<?php echo !empty($deal_products) ? $deal_products[0]['product_id'] : ''; ?>">shop now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

	<!-- Best Sellers -->
	<div class="best_sellers">
		<div class="container">
			<div class="row">
				<div class="col text-center">
					<div class="section_title new_arrivals_title">
						<h2>Best Sellers</h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="product_slider_container">
						<div class="owl-carousel owl-theme product_slider">
							<?php foreach($top_discounted as $best_seller): 
								$has_discount = !empty($best_seller['product_discount']) && $best_seller['product_discount'] > 0;
								$discounted_price = $has_discount ? $best_seller['product_price'] * (1 - $best_seller['product_discount']/100) : $best_seller['product_price'];
								$discount_amount = $has_discount ? $best_seller['product_price'] - $discounted_price : 0;
							?>
							<div class="owl-item product_slider_item">
								<div class="product-item">
									<div class="product discount">
										<div class="product_image">
											<img src="images/<?php echo htmlspecialchars($best_seller['product_image1']); ?>" alt="<?php echo htmlspecialchars($best_seller['product_name']); ?>">
										</div>
										<div class="favorite favorite_left"></div>
										<?php if ($has_discount): ?>
										<div class="product_bubble product_bubble_right product_bubble_red d-flex flex-column align-items-center">
											<span>-$<?php echo number_format($discount_amount, 0); ?></span>
										</div>
										<?php endif; ?>
										<div class="product_info">
											<h6 class="product_name">
												<a href="single.php?id=<?php echo $best_seller['product_id']; ?>">
													<?php echo htmlspecialchars($best_seller['product_name']); ?>
												</a>
											</h6>
											<div class="product_price">
												$<?php echo number_format($discounted_price, 2); ?>
												<?php if ($has_discount): ?>
													<span>$<?php echo number_format($best_seller['product_price'], 2); ?></span>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>

						<!-- Slider Navigation -->
						<div class="product_slider_nav_left product_slider_nav d-flex align-items-center justify-content-center flex-column">
							<i class="fa fa-chevron-left" aria-hidden="true"></i>
						</div>
						<div class="product_slider_nav_right product_slider_nav d-flex align-items-center justify-content-center flex-column">
							<i class="fa fa-chevron-right" aria-hidden="true"></i>
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

	<!-- Blogs -->
	<div class="blogs">
		<div class="container">
			<div class="row">
				<div class="col text-center">
					<div class="section_title">
						<h2>Latest Blogs</h2>
					</div>
				</div>
			</div>
			<div class="row blogs_container">
				<div class="col-lg-4 blog_item_col">
					<div class="blog_item">
						<div class="blog_background" style="background-image:url(images/blog1.jpg)"></div>
						<div class="blog_content d-flex flex-column align-items-center justify-content-center text-center">
							<h4 class="blog_title">Here are the trends I see coming this fall</h4>
							<span class="blog_meta">by admin | dec 01, 2017</span>
							<a class="blog_more" href="#">Read more</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 blog_item_col">
					<div class="blog_item">
						<div class="blog_background" style="background-image:url(images/blog2.jpg)"></div>
						<div class="blog_content d-flex flex-column align-items-center justify-content-center text-center">
							<h4 class="blog_title">Here are the trends I see coming this fall</h4>
							<span class="blog_meta">by admin | dec 01, 2017</span>
							<a class="blog_more" href="#">Read more</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 blog_item_col">
					<div class="blog_item">
						<div class="blog_background" style="background-image:url(images/blog3.jpg)"></div>
						<div class="blog_content d-flex flex-column align-items-center justify-content-center text-center">
							<h4 class="blog_title">Here are the trends I see coming this fall</h4>
							<span class="blog_meta">by admin | dec 01, 2017</span>
							<a class="blog_more" href="#">Read more</a>
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
<script src="plugins/Isotope/isotope.pkgd.min.js"></script>
<script src="plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
<script src="plugins/easing/easing.js"></script>
<script src="js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
	
//Flash Sale Timer
// Products data from PHP
const dealProducts = <?php echo json_encode($deal_products); ?>;
let currentProductIndex = 0;

// Set countdown duration in hours
const countdownDuration = 24; // 24 hours countdown
const countDownDate = new Date();
countDownDate.setHours(countDownDate.getHours() + countdownDuration);

// Update the countdown every 1 second
const countdownTimer = setInterval(function() {
    // Get current time
    const now = new Date().getTime();
    
    // Calculate remaining time
    const distance = countDownDate - now;
    
    // Time calculations for hours, minutes and seconds
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    // Display the result
    document.getElementById("hour").innerHTML = hours.toString().padStart(2, '0');
    document.getElementById("minute").innerHTML = minutes.toString().padStart(2, '0');
    document.getElementById("second").innerHTML = seconds.toString().padStart(2, '0');
    
    // If the countdown is finished, reset it
    if (distance < 0) {
        countDownDate.setHours(countDownDate.getHours() + countdownDuration);
    }
}, 1000);

// Rotate products every 5 seconds
function rotateProduct() {
    if (dealProducts.length > 0) {
        currentProductIndex = (currentProductIndex + 1) % dealProducts.length;
        const product = dealProducts[currentProductIndex];
        
        // Update image and link
        document.getElementById("flashSaleImage").src = "images/" + product.product_image1;
        document.getElementById("flashSaleImage").setAttribute("data-product-id", product.product_id);
        document.getElementById("flashSaleLink").href = "shop-detail.php?id=" + product.product_id;
    }
}

// Start rotation (every 5 seconds)
if (dealProducts.length > 1) {
    setInterval(rotateProduct, 5000);
}

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

// Inisialisasi Isotope
$(document).ready(function() {
    var $grid = $('.product-grid').isotope({
        itemSelector: '.product-item',
        layoutMode: 'fitRows'
    });

    // Filter items on button click
    $('.arrivals_grid_sorting').on('click', 'li', function() {
        var filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });
    });

    // Change active class on buttons
    $('.arrivals_grid_sorting').each(function(i, buttonGroup) {
        var $buttonGroup = $(buttonGroup);
        $buttonGroup.on('click', 'li', function() {
            $buttonGroup.find('.is-checked').removeClass('is-checked');
            $(this).addClass('is-checked');
        });
    });
    
        $(document).ready(function() {
        // Inisialisasi Owl Carousel
        var owl = $('.product_slider').owlCarousel({
            loop: false,
            margin: 20,
            nav: false,
            dots: false,
            responsive: {
                0: { items: 1 },
                576: { items: 2 },
                768: { items: 3 },
                992: { items: 4 }
            }
        });

        // Filter Kategori
        $('.grid_sorting_button').click(function() {
            // Update tombol aktif
            $('.grid_sorting_button').removeClass('active is-checked');
            $(this).addClass('active is-checked');
            
            var filter = $(this).data('filter');
            
            // Nonaktifkan transisi untuk menghindari efek visual yang tidak diinginkan
            $('.product_slider').addClass('no-transition');
            
            // Sembunyikan semua item terlebih dahulu
            $('.owl-item').hide().css('opacity', '0');
            
            // Tampilkan item yang sesuai filter
            if (filter === '*') {
                $('.owl-item').show().css('opacity', '1');
            } else {
                $('.owl-item' + filter).show().css('opacity', '1');
            }
            
            // Hitung ulang dan atur ulang carousel
            setTimeout(function() {
                owl.trigger('destroy.owl.carousel');
                
                // Atur margin 0 saat filtering untuk menghilangkan jarak
                owl = $('.product_slider').owlCarousel({
                    loop: false,
                    margin: 0, // Margin 0 untuk tampilan rapat
                    nav: false,
                    dots: false,
                    responsive: {
                        0: { items: 1 },
                        576: { items: 2 },
                        768: { items: 3 },
                        992: { items: 4 }
                    },
                    onInitialized: function() {
                        $('.product_slider').removeClass('no-transition');
                    }
                });
                
                // Geser semua item ke kiri
                owl.trigger('to.owl.carousel', [0, 0]);
            }, 10);
        });

        // Navigasi Slider
        $('.product_slider_nav_left').click(function() {
            owl.trigger('prev.owl.carousel');
        });
        
        $('.product_slider_nav_right').click(function() {
            owl.trigger('next.owl.carousel');
        });
    });
});
</script>

</body>
</html>
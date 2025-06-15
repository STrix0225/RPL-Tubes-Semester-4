<?php
include('../../Database/connection.php');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calculate cart totals
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
            'image' => $product['product_image1'],
            'price' => $product['product_price'],
            'discounted_price' => $price,
            'quantity' => $cart_item['quantity'],
            'total' => $total,
            'has_discount' => $has_discount,
            'discount' => $product['product_discount']
        ];

        $subtotal += $total;
    }
}

$shipping = $subtotal > 50 ? 0 : 10;
$total = $subtotal + $shipping;

// Get product ID from URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $query = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $colors = !empty($product['product_color']) ? explode(',', $product['product_color']) : ['?']; // Default color jika kosong
    $clean_colors = array_filter(array_map('trim', $colors));

    if (!$product) {
        header("Location: shop.php");
        exit();
    }

    $product_images = [
        $product['product_image1'],
        $product['product_image2'],
        $product['product_image3']
    ];

    // Calculate discount
    $has_discount = !empty($product['product_discount']) && $product['product_discount'] > 0;
    $discounted_price = $has_discount ? $product['product_price'] * (1 - $product['product_discount'] / 100) : $product['product_price'];
    $discount_amount = $has_discount ? $product['product_price'] - $discounted_price : 0;
} else {
    header("Location: shop.php");
    exit();
}

// Handle add to cart action
if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

    // Add or update item in cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'quantity' => $quantity
        ];
    }

    // Recalculate totals after adding to cart
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        // In a real implementation, you would fetch the product price from database
        $subtotal += $discounted_price * $item['quantity'];
    }
    $shipping = $subtotal > 50 ? 0 : 10;
    $total = $subtotal + $shipping;

    // Redirect to cart page
    header("Location: cart.php");
    exit();
}

$product_id = $_GET['id'] ?? 0;
// Get product details from database
$query = "SELECT product_name, product_desc, product_type, specs FROM products WHERE product_id = ?";

// Check if product exists
if (empty($product)) {
    die("Produk tidak ditemukan!");
}

// Decode JSON specs (if used)
$specs = json_decode($product['specs'] ?? '{}', true);

// Get reviews from database
// Ambil data review dari database
// Ambil data review dan balasannya dari database
$reviews_query = $conn->prepare("
    SELECT 
        r.*, 
        c.customer_name, 
        c.customer_email,
        rr.review_text AS admin_reply,
        rr.review_date AS admin_reply_date,
        a.admin_name AS admin_name
    FROM reviews r
    JOIN customers c ON r.customer_id = c.customer_id
    LEFT JOIN reviews rr ON r.review_id = rr.review_reply_id
    LEFT JOIN admins a ON rr.admin_id = a.admin_id
    WHERE r.product_id = ? AND r.customer_id IS NOT NULL
    ORDER BY r.review_date DESC
");
$reviews_query->bind_param('i', $product_id);
$reviews_query->execute();
$reviews = $reviews_query->get_result()->fetch_all(MYSQLI_ASSOC);
// Hitung rata-rata rating
$avg_rating_query = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = ?");
$avg_rating_query->bind_param('i', $product_id);
$avg_rating_query->execute();
$avg_rating = $avg_rating_query->get_result()->fetch_assoc()['avg_rating'];
$avg_rating = round($avg_rating, 1);
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Gadget MS</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="Colo Shop Template">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars($product['product_name']); ?></title>
	<link rel="stylesheet" type="text/css" href="styles/bootstrap4/bootstrap.min.css">
	<link href="plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.carousel.css">
	<link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
	<link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/animate.css">
	<link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
	<link rel="stylesheet" type="text/css" href="plugins/jquery-ui-1.12.1.custom/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="styles/single_styles.css">
	<link rel="stylesheet" type="text/css" href="styles/single_responsive.css">
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

		<div class="container single_product_container">
			<div class="row">
				<div class="col">

					<!-- Breadcrumbs -->

					<div class="breadcrumbs d-flex flex-row align-items-center">
						<ul>
							<li><a href="dashboard.php">Home</a></li>
							<li><a href="shop.php"><i class="fa fa-angle-right" aria-hidden="true"></i>shop</a></li>
							<li class="active"><a href="shop-detail.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Product</a></li>
						</ul>
					</div>

				</div>
			</div>

			<div class="row">

<div class="col-lg-7">
  <div class="single_product_pics">
    <!-- Main Carousel with Auto Slide -->
    <div id="productCarousel" class="carousel slide" data-ride="carousel" data-interval="5000" data-pause="hover" data-wrap="true">  <!-- Progress Bar -->
      <div class="carousel-progress">
        <div class="progress-bar"></div>
      </div>
      
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <?php if (!empty($product_images)): ?>
          <?php foreach ($product_images as $index => $image): ?>
            <?php if (!empty($image)): ?>
              <li data-target="#productCarousel" data-slide-to="<?php echo $index; ?>" 
                  class="<?php echo $index === 0 ? 'active' : ''; ?>"></li>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <li data-target="#productCarousel" data-slide-to="0" class="active"></li>
        <?php endif; ?>
      </ol>
      
      <!-- Slides -->
      <div class="carousel-inner">
        <?php if (!empty($product_images)): ?>
          <?php foreach ($product_images as $index => $image): ?>
            <?php if (!empty($image)): ?>
              <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <img class="d-block w-100" src="images/<?php echo htmlspecialchars($image); ?>" 
                     alt="Product Image <?php echo $index + 1; ?>">
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="carousel-item active">
            <img class="d-block w-100" src="images/default_product.jpg" alt="Default Product">
          </div>
        <?php endif; ?>
      </div>
      
      <!-- Controls -->
      <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>

    <!-- Thumbnail Navigation -->
    <div class="text-center">
      <div class="thumbnail-container d-inline-flex justify-content-center" style="gap: 10px; padding: 5px 0; max-width: 100%; overflow-x: auto;">
        <?php if (!empty($product_images)): ?>
          <?php foreach ($product_images as $index => $image): ?>
            <?php if (!empty($image)): ?>
              <img src="images/<?php echo htmlspecialchars($image); ?>" 
                   alt="Thumbnail <?php echo $index + 1; ?>" 
                   class="img-thumbnail"
                   style="width: 60px; height: 60px; object-fit: cover; cursor: pointer; border: 2px solid <?php echo $index === 0 ? '#007bff' : '#ddd'; ?>"
                   onclick="$('#productCarousel').carousel(<?php echo $index; ?>); 
                            $('.carousel-indicators li').removeClass('active').eq(<?php echo $index; ?>).addClass('active');
                            $(this).css('border-color', '#007bff').siblings().css('border-color', '#ddd');
                            resetProgressBar();">
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <img src="images/default_product.jpg" alt="Default Thumbnail" 
               class="img-thumbnail"
               style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #007bff;">
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>


				<div class="col-lg-5">
							<div class="product_details">
								<div class="original_price">$<?php echo number_format($product['product_price'], 2); ?></div>
								<div class="product_price">$<?php echo number_format($discounted_price, 2); ?></div>
								<?php if ($has_discount): ?>
									<div class="discount-badge">Save $<?php echo number_format($discount_amount, 2); ?> (<?php echo $product['product_discount']; ?>%)</div>
								<?php endif; ?>
								
								<ul class="star_rating">
									<?php
									$full_stars = floor($avg_rating);
									$has_half_star = ($avg_rating - $full_stars) >= 0.5;
									$empty_stars = 5 - $full_stars - ($has_half_star ? 1 : 0);
									?>

									<?php for ($i = 0; $i < $full_stars; $i++): ?>
										<li><i class="fa fa-star" aria-hidden="true"></i></li>
									<?php endfor; ?>

									<?php if ($has_half_star): ?>
										<li><i class="fa fa-star-half-o" aria-hidden="true"></i></li>
									<?php endif; ?>

									<?php for ($i = 0; $i < $empty_stars; $i++): ?>
										<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
									<?php endfor; ?>

									<li><span>(<?= $avg_rating ?: '0.0' ?>/5.0)</span></li>
								</ul>
								
								<div class="product_color">
									<span>Available Colors:</span>
									<div class="color-options-text">
										<?php 
										// Ambil data warna dari database
										$colors = !empty($product['product_color']) ? explode(',', $product['product_color']) : ['Black'];
										$clean_colors = array_map('trim', $colors);
										
										foreach ($clean_colors as $index => $color): 
											$color_slug = strtolower(str_replace(' ', '-', $color));
										?>
											<label class="color-option-text <?php echo $index === 0 ? 'selected' : ''; ?>">
												<input type="radio" name="product_color" value="<?php echo htmlspecialchars($color); ?>" 
													<?php echo $index === 0 ? 'checked' : ''; ?>>
												<span class="color-name"><?php echo htmlspecialchars($color); ?></span>
											</label>
										<?php endforeach; ?>
									</div>
								</div>
								<input type="hidden" name="selected_color" id="selected_color" value="">
												
								<form method="POST" action="cart.php" class="add-to-cart-form">
									<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
									<input type="hidden" name="add_to_cart" value="1">
									<input type="hidden" name="selected_color" id="selected_color" value="<?php echo isset($colors[0]) ? htmlspecialchars(trim($colors[0])) : ''; ?>">
								<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
								<input type="hidden" name="add_to_cart" value="1">
								<input type="hidden" name="selected_color" id="selected_color" value="<?php echo isset($colors[0]) ? htmlspecialchars(trim($colors[0])) : ''; ?>">

								<div class="quantity-container">
									<span class="quantity-label">Quantity:</span>
									<div class="quantity-input-group">
										<button type="button" class="quantity-btn minus">-</button>
										<input type="number" name="quantity" value="1" min="1" class="quantity-input" id="quantity_input">
										<button type="button" class="quantity-btn plus">+</button>
									</div>
								</div>

								<!-- Price Calculation Section -->
								<div class="price-calculation"> Subtotal:
									<span id="subtotal_price"><?php echo number_format($discounted_price, 2); ?></span>
								</div>

								<button type="submit" name="add_to_cart" class="add-to-cart-btn">
									<i class="fa fa-shopping-cart"></i> Add to Cart
								</button>
								<a href="./checkout.php?product_id=<?php echo $product['product_id']; ?>&quantity=1" class="checkout-btn" id="checkout_link">
									<i class="fa fa-credit-card"></i> Buy Now
								</a>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Tabs -->

		<div class="tabs_section_container">

			<div class="container">
				<div class="row">
					<div class="col">
						<div class="tabs_container">
							<ul class="tabs d-flex flex-sm-row flex-column align-items-left align-items-md-center justify-content-center">
								<li class="tab active" data-active-tab="tab_1"><span>Description</span></li>
								<li class="tab" data-active-tab="tab_3"><span>Reviews (<?= count($reviews) ?>)</span></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<!-- Tab Description -->
						<div id="tab_1" class="tab_container active">
							<div class="row">
								<div class="col-lg-5 desc_col">
									<div class="tab_title">
										<h4>Description</h4>
									</div>
									<div class="product_details_title">
										<h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
										</div>

									<div class="product_specs">
										<h3>Spesifikasi :</h3>
										<?php
										// Ambil data spesifikasi dari database
										$specs_text = $product['product_description'] ?? '';
										
										// Ekstrak bagian spesifikasi dari deskripsi produk
										if (preg_match('/Spesifikasi\s*:(.*?)(?:\r\n\r\n|\Z)/is', $specs_text, $matches)) {
											$specs_content = trim($matches[1]);
											
											// Bersihkan dan format spesifikasi
											$specs_content = preg_replace('/Partnumber\s*:.*$/im', '', $specs_content); // Hapus part number
											$specs_content = preg_replace('/\r\n/', "\n", $specs_content); // Normalisasi line breaks
											
											$spec_lines = array_filter(explode("\n", $specs_content), function($line) {
												return !empty(trim($line));
											});

											if (empty($spec_lines)) {
												echo '<p>Specifications not available for this product.</p>';
											} else {
												foreach ($spec_lines as $line) {
													$line = trim($line);
													
													// Tangani format "Key : Value"
													if (preg_match('/^([^:]+)\s*:\s*(.+)$/', $line, $matches)) {
														$key = trim($matches[1]);
														$value = trim($matches[2]);
														
														// Skip jika key terlalu pendek (mungkin noise)
														if (strlen($key) > 2) {
															echo '<p><strong>' . htmlspecialchars($key) . ' :</strong> ' . htmlspecialchars($value) . '</p>';
														}
													} 
													// Tangani baris tanpa titik dua (anggap sebagai teks deskriptif)
													else if (strlen($line) > 10) {
														echo '<p>' . htmlspecialchars($line) . '</p>';
													}
												}
											}
										} else {
											echo '<p>Specifications not available for this product.</p>';
										}
										?>
									</div>
								</div>
							</div>
						</div>

						<div id="tab_3" class="tab_container">
							<div class="row">

								<!-- User Reviews -->
								<div class="col-lg-6 reviews_col">
									<div class="tab_title reviews_title">
										<h4>Reviews (<?= count($reviews) ?>)</h4>
									</div>
									<?php foreach ($reviews as $review): ?>
										<div class="user_review_container d-flex flex-column flex-sm-row mb-4">
											<div class="user">
												<div class="user_pic">
													<?= strtoupper(substr($review['customer_name'], 0, 1)) ?>
												</div>
												<div class="user_rating">
													<ul class="star_rating">
														<?php for ($i = 1; $i <= 5; $i++): ?>
															<li>
																<i class="fa fa-<?= $i <= $review['rating'] ? 'star' : 'star-o' ?>" aria-hidden="true"></i>
															</li>
														<?php endfor; ?>
													</ul>
												</div>
											</div>
											<div class="review">
												<div class="review_date">
													<?= date('d M Y', strtotime($review['review_date'])) ?>
												</div>
												<div class="user_name">
													<?= htmlspecialchars($review['customer_name']) ?>
												</div>
												<p><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>

												<!-- Tampilkan balasan admin jika ada -->
												<?php if (!empty($review['admin_reply'])): ?>
													<div class="admin_reply">
														<div class="reply_header">
															<span>
																Balasan dari <?= htmlspecialchars($review['admin_name']) ?>
																<span class="verified-badge" title="Verified Admin">
																	<i class="fa fa-check-circle"></i> Verified  
																</span>
															</span>
															<span style="font-size: 0.85em; color: #666; margin-left: auto;">  
																<?= date('d M Y', strtotime($review['admin_reply_date'])) ?>
															</span>
														</div>
														<p><?= nl2br(htmlspecialchars($review['admin_reply'])) ?></p>
													</div>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>

								<!-- Add Review -->

								<div class="col-lg-6 add_review_col">

									<div class="add_review">
										<?php if (isset($_SESSION['review_error'])): ?>
											<div class="alert alert-danger"><?= $_SESSION['review_error'] ?></div>
											<?php unset($_SESSION['review_error']); ?>
										<?php endif; ?>

										<?php if (isset($_SESSION['review_success'])): ?>
											<div class="alert alert-success"><?= $_SESSION['review_success'] ?></div>
											<?php unset($_SESSION['review_success']); ?>
										<?php endif; ?>

										<form id="review_form" action="submit-review.php" method="POST">
											<div>
												<h1>Add Review</h1>
												<input type="hidden" name="product_id" value="<?= $product_id ?>">
												<input type="hidden" name="rating" id="rating_value" value="0">
												<textarea id="review_message" class="input_review" name="message" placeholder="Your Review" rows="4" required></textarea>
											</div>
											<div>
												<h1>Your Rating:</h1>
												<ul class="user_star_rating" id="star_rating">
													<li data-rating="1"><i class="fa fa-star-o" aria-hidden="true"></i></li>
													<li data-rating="2"><i class="fa fa-star-o" aria-hidden="true"></i></li>
													<li data-rating="3"><i class="fa fa-star-o" aria-hidden="true"></i></li>
													<li data-rating="4"><i class="fa fa-star-o" aria-hidden="true"></i></li>
													<li data-rating="5"><i class="fa fa-star-o" aria-hidden="true"></i></li>
												</ul>
											</div>
											<div class="text-left text-sm-right">
												<button id="review_submit" type="submit" class="red_button review_submit_btn trans_300" value="Submit">submit</button>
											</div>
										</form>
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
<script src="js/single_custom.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize elements
    const quantityInput = document.getElementById('quantity_input');
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');
    const totalPriceElement = document.getElementById('total_price');
    const checkoutLink = document.getElementById('checkout_link');
    
    // Set initial values
    const unitPrice = <?php echo $discounted_price; ?>;
    const productId = <?php echo $product['product_id']; ?>;
    
    // Function to update quantity (changed to handle single increments)
    document.querySelectorAll('.quantity_input').forEach(input => {
        input.addEventListener('change', function() {
            updateCartTotals();
        });
    });

    function updateCartTotals() {
        let subtotal = 0;
        
        // Hitung ulang subtotal berdasarkan quantity baru
        document.querySelectorAll('.cart_item').forEach(item => {
            const price = parseFloat(item.querySelector('.cart_item_text span:last-child').textContent.replace('$', ''));
            const quantity = parseInt(item.querySelector('.quantity_input').value);
            const itemTotal = price * quantity;
            
            // Update total per item
            item.querySelector('.cart_item_total .cart_item_text').textContent = '$' + itemTotal.toFixed(2);
            subtotal += itemTotal;
        });

        // Hitung shipping
        const shipping = subtotal > 50 ? 0 : 10;
        const total = subtotal + shipping;

        // Update tampilan
        document.querySelector('.order_total_amount:nth-of-type(1)').textContent = '$' + subtotal.toFixed(2);
        document.querySelector('.order_total_amount:nth-of-type(2)').textContent = '$' + shipping.toFixed(2);
        document.querySelector('.order_total_amount:nth-of-type(3)').textContent = '$' + total.toFixed(2);
    }
    
    input.addEventListener('change', function() {
        const formData = new FormData(this.form);
        
        fetch('cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Quantity updated on server");
        });
    });

    // Event listeners with debouncing to prevent multiple rapid clicks
    let isProcessing = false;
    
    minusBtn.addEventListener('click', function() {
        if (!isProcessing) {
            isProcessing = true;
            updateQuantity(-1);
            setTimeout(() => { isProcessing = false; }, 200);
        }
    });
    
    plusBtn.addEventListener('click', function() {
        if (!isProcessing) {
            isProcessing = true;
            updateQuantity(1);
            setTimeout(() => { isProcessing = false; }, 200);
        }
    });
    
    // Input field validation
    quantityInput.addEventListener('change', function() {
        let quantity = parseInt(this.value);
        if (isNaN(quantity)) quantity = 1;
        if (quantity < 1) quantity = 1;
        this.value = quantity;
        updateTotalPrice(quantity);
        updateCheckoutLink(quantity);
    });
    
    // Color selection functionality
    const colorItems = document.querySelectorAll('.product_color ul li');
    const selectedColorInput = document.getElementById('selected_color');
    
    if (colorItems.length > 0) {
        colorItems[0].classList.add('active');
    }
    
    colorItems.forEach(item => {
        item.addEventListener('click', function() {
            const color = this.getAttribute('data-color');
            selectedColorInput.value = color;
            
            colorItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Thumbnail image click handler
    document.querySelectorAll('.single_product_thumbnails li img').forEach(img => {
        img.addEventListener('click', function() {
            // Remove active class from all thumbnails
            document.querySelectorAll('.single_product_thumbnails li').forEach(li => {
                li.classList.remove('active');
            });

            // Add active class to clicked thumbnail
            this.parentElement.classList.add('active');

            // Change main image
            const mainImage = document.querySelector('.single_product_image_background');
            mainImage.style.backgroundImage = `url(${this.dataset.image})`;
        });
    });

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

    // Quantity selector
    document.querySelector('.quantity_selector .plus').addEventListener('click', function() {
        var quantity = document.getElementById('quantity_value');
        quantity.textContent = parseInt(quantity.textContent) + 1;
    });

    document.querySelector('.quantity_selector .minus').addEventListener('click', function() {
        var quantity = document.getElementById('quantity_value');
        if (parseInt(quantity.textContent) > 1) {
            quantity.textContent = parseInt(quantity.textContent) - 1;
        }
    });

    // Quantity selector (alternative version)
    document.querySelector('.quantity_selector .plus').addEventListener('click', function() {
        var quantity = document.getElementById('quantity_value');
        var quantityInput = document.getElementById('quantity_input');
        var newQty = parseInt(quantity.textContent) + 1;
        quantity.textContent = newQty;
        quantityInput.value = newQty;
    });

    document.querySelector('.quantity_selector .minus').addEventListener('click', function() {
        var quantity = document.getElementById('quantity_value');
        var quantityInput = document.getElementById('quantity_input');
        if (parseInt(quantity.textContent) > 1) {
            var newQty = parseInt(quantity.textContent) - 1;
            quantity.textContent = newQty;
            quantityInput.value = newQty;
        }
    });

    // Add to cart functionality
    document.getElementById('add_to_cart').addEventListener('click', function(e) {
        e.preventDefault();

        const productId = <?php echo $product_id; ?>;
        const quantity = parseInt(document.getElementById('quantity_value').textContent);

        // Create form dynamically
        const form = document.createElement('form');
        form.method = 'post';
        form.action = 'cart.php';

        // Add product_id input
        const productIdInput = document.createElement('input');
        productIdInput.type = 'hidden';
        productIdInput.name = 'product_id';
        productIdInput.value = productId;
        form.appendChild(productIdInput);

        // Add quantity input
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = 'quantity';
        quantityInput.value = quantity;
        form.appendChild(quantityInput);

        // Add to cart action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'add_to_cart';
        actionInput.value = '1';
        form.appendChild(actionInput);

        // Submit form
        document.body.appendChild(form);
        form.submit();
    });

    // Star rating functionality
    document.querySelectorAll('#star_rating li').forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            document.getElementById('rating_value').value = rating;

            // Update star display
            document.querySelectorAll('#star_rating li').forEach((li, index) => {
                const icon = li.querySelector('i');
                if (index < rating) {
                    icon.classList.remove('fa-star-o');
                    icon.classList.add('fa-star');
                } else {
                    icon.classList.remove('fa-star');
                    icon.classList.add('fa-star-o');
                }
            });
        });
    });

    // Auto-fill name and email if user is logged in
    <?php if (isset($_SESSION['customer_id'])): ?>
        document.getElementById('review_name').value = '<?= isset($_SESSION['customer_name']) ? addslashes($_SESSION['customer_name']) : "" ?>';
        document.getElementById('review_email').value = '<?= isset($_SESSION['customer_email']) ? addslashes($_SESSION['customer_email']) : "" ?>';
    <?php endif; ?>
});

$(document).ready(function() {
    // Jika URL mengandung hash #tab_3, trigger click pada tab review
    if (window.location.hash === '#tab_3') {
        // Aktifkan tab review
        $('.tab[data-active-tab="tab_3"]').click();

        // Scroll ke bagian review
        $('html, body').animate({
            scrollTop: $('#tab_3').offset().top - 100
        }, 800);
    }

    // Highlight active thumbnail when carousel slides
    $('#productCarousel').on('slid.bs.carousel', function () {
        const activeIndex = $('.carousel-item.active').index();
        $('.thumbnail-container img').css('border-color', '#ddd').eq(activeIndex).css('border-color', '#007bff');
    });
    
    // Pause on hover
    $('#productCarousel').hover(
        function() {
            $(this).carousel('pause');
        },
        function() {
            $(this).carousel('cycle');
        }
    );
});

// Quantity control for input field
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.querySelector('.quantity-input');
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');
    
    // Handle minus button
    minusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });
    
    // Handle plus button
    plusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        quantityInput.value = value + 1;
    });
    
    // Validate input
    quantityInput.addEventListener('change', function() {
        if (this.value < 1) {
            this.value = 1;
        }
    });
});
</script>
<script>
		// Star rating functionality
		document.querySelectorAll('#star_rating li').forEach(star => {
			star.addEventListener('click', function() {
				const rating = parseInt(this.getAttribute('data-rating'));
				document.getElementById('rating_value').value = rating;

				// Update star display
				document.querySelectorAll('#star_rating li').forEach((li, index) => {
					const icon = li.querySelector('i');
					if (index < rating) {
						icon.classList.remove('fa-star-o');
						icon.classList.add('fa-star');
					} else {
						icon.classList.remove('fa-star');
						icon.classList.add('fa-star-o');
					}
				});
			});
		});

		// Auto-fill name and email if user is logged in
		document.addEventListener('DOMContentLoaded', function() {
			<?php if (isset($_SESSION['customer_id'])): ?>
				document.getElementById('review_name').value = '<?= isset($_SESSION['customer_name']) ? addslashes($_SESSION['customer_name']) : "" ?>';
				document.getElementById('review_email').value = '<?= isset($_SESSION['customer_email']) ? addslashes($_SESSION['customer_email']) : "" ?>';
			<?php endif; ?>
		});
		document.addEventListener('DOMContentLoaded', function() {
    // Handle color selection
    document.querySelectorAll('.color-option-text input').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.color-option-text').forEach(option => {
                option.classList.remove('selected');
            });
            this.parentElement.classList.add('selected');
        });
    });
});
function getTextColor($colorName) {
    $lightColors = ['white', 'yellow', 'gold', 'silver', 'pink', 'beige'];
    $lowerColor = strtolower(trim($colorName));
    return in_array($lowerColor, $lightColors) ? '#333' : '#fff';
}
	</script>
	<style>
	.admin_reply {
			margin-top: 15px;
			padding: 15px;
			background: #f9f9f9;
			border-radius: 4px;
			border-left: 3px solid #4CAF50;
		}

		.reply_header {
			font-weight: 600;
			color: #333;
			margin-bottom: 8px;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.reply_header span {
			font-size: 0.85em;
			color: #666;
			font-weight: normal;
		}

		.dark-mode .admin_reply {
			background: #2d2d2d;
			border-left-color: #388E3C;
		}

		.dark-mode .reply_header {
			color: #ddd;
		}

		/* Badge Verified */

		.verified-badge i {
			margin-right: 3px;
		}

		.admin_reply {
			margin-top: 15px;
			padding: 15px;
			background: #f9f9f9;
			border-radius: 4px;
			border-left: 3px solid #4CAF50;
			position: relative;
		}

		.reply_header {
			font-weight: 600;
			color: #333;
			margin-bottom: 8px;
			display: flex;
			align-items: center;
		}

		.dark-mode .admin_reply {
			background: #2d2d2d;
			border-left-color: #388E3C;
		}

		.dark-mode .reply_header {
			color: #ddd;
		}
		.verified-badge {
    transition: all 0.3s ease;
}

.verified-badge:hover {
    transform: scale(1.05);
    color: #0d8aee;
}
.verified-badge {
        display: inline-flex;
        align-items: center;
        margin-left: 5px;
        color: #4285F4;
        font-size: 0.8em;
    }
    
    .verified-badge svg {
        width: 14px;
        height: 14px;
        margin-right: 3px;
        fill: currentColor;
    }
	.color-options-text {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.color-options-text {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.color-option-text {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s ease;
    background: #f5f5f5;
    position: relative;
}

.color-option-text input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.color-option-text.selected {
    background: #4CAF50; /* Warna hijau saat dipilih */
    color: white;
    border-color: #4CAF50;
}

.color-option-text:hover {
    background: #eee;
}

.color-option-text.selected:hover {
    background: #45a049; /* Warna hijau lebih gelap saat hover */
}
	</style>
</body>

</html>
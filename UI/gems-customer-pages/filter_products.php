<?php
include('../Database/connection.php');

if(isset($_POST['action'])) {
    if($_POST['action'] == 'filter_category') {
        $category = $_POST['category'];
        $query = "SELECT * FROM products WHERE product_category = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $category);
    } 
    elseif($_POST['action'] == 'filter_price') {
        $min_price = $_POST['min_price'];
        $max_price = $_POST['max_price'];
        $query = "SELECT * FROM products WHERE product_price BETWEEN ? AND ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('dd', $min_price, $max_price);
    }

    $stmt->execute();
    $products = $stmt->get_result();

    // Generate HTML untuk produk yang difilter
    while($product = $products->fetch_assoc()): 
        // Hitung diskon jika ada
        $has_discount = !empty($product['product_discount']) && $product['product_discount'] > 0;
        $discounted_price = $has_discount ? $product['product_price'] * (1 - $product['product_discount']/100) : $product['product_price'];
    ?>
    <div class="product-item">
        <!-- Struktur produk sama persis dengan yang ada di shop.php -->
        <div class="product discount product_filter">
            <div class="product_image">
                <img src="images/<?php echo $product['product_image1']; ?>" alt="<?php echo $product['product_name']; ?>">
            </div>
            <div class="product_info">
                <h6 class="product_name"><a href="single.php?id=<?php echo $product['product_id']; ?>"><?php echo $product['product_name']; ?></a></h6>
                <div class="product_price">$<?php echo number_format($discounted_price, 2); ?>
                    <?php if($has_discount): ?>
                    <span>$<?php echo number_format($product['product_price'], 2); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="red_button add_to_cart_button"><a href="#">add to cart</a></div>
    </div>
    <?php endwhile;
}
?>
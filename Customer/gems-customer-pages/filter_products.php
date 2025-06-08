<?php
include('../../Database/connection.php');

if(isset($_POST['action'])) {
    $products = [];
    
    if($_POST['action'] == 'filter_category') {
        $category = $_POST['category'];
        $query = "SELECT * FROM products WHERE product_category = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $products = $stmt->get_result();
    } 
    elseif($_POST['action'] == 'filter_price') {
        $min_price = $_POST['min_price'];
        $max_price = $_POST['max_price'];
        $query = "SELECT * FROM products WHERE product_price BETWEEN ? AND ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('dd', $min_price, $max_price);
        $stmt->execute();
        $products = $stmt->get_result();
    }

    // Mulai output dengan container grid yang lengkap
    echo '<div class="products_iso">
            <div class="row">
                <div class="col">
                    <div class="product-grid">';

    if($products->num_rows > 0) {
        while($product = $products->fetch_assoc()): 
            $has_discount = !empty($product['product_discount']) && $product['product_discount'] > 0;
            $discounted_price = $has_discount ? $product['product_price'] * (1 - $product['product_discount']/100) : $product['product_price'];
            $discount_amount = $has_discount ? $product['product_price'] - $discounted_price : 0;
            $is_new = empty($product['product_sold']) || $product['product_sold'] == 0;
?>
    <div class="product-item <?php echo htmlspecialchars(strtolower($product['product_category'])); ?>" 
         data-price="<?php echo $discounted_price; ?>">
        <div class="product discount product_filter">
            <div class="product_image">
                <img src="images/<?php echo $product['product_image1']; ?>" alt="<?php echo $product['product_name']; ?>">
            </div>
            
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
                <h6 class="product_name"><a href="single.php?id=<?php echo $product['product_id']; ?>"><?php echo $product['product_name']; ?></a></h6>
                <div class="product_price">$<?php echo number_format($discounted_price, 2); ?>
                    <?php if($has_discount): ?>
                    <span>$<?php echo number_format($product['product_price'], 2); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="red_button add_to_cart_button"><a href="shop-detail.php?id=<?php echo $product['product_id']; ?>">add to cart</a></div>
    </div>
<?php 
        endwhile;
    } else {
        echo '<div class="col-12 text-center py-5">
                <h4>No products found</h4>
                <p>Try adjusting your filters</p>
              </div>';
    }
    
    // Tutup container grid
    echo '      </div>
            </div>
        </div>
    </div>';
    
    // Script untuk inisialisasi ulang Isotope dan plugins
    // Replace the existing script section in filter_products.php with this:
echo <<<HTML
<script>
$(document).ready(function() {
    // Tunggu hingga DOM selesai diupdate
    setTimeout(function() {
        var \$grid = $('.product-grid');
        
        // Hancurkan instance Isotope lama jika ada
        if (\$grid.data('isotope')) {
            \$grid.isotope('destroy');
        }
        
        // Inisialisasi Isotope baru
        \$grid.isotope({
            itemSelector: '.product-item',
            layoutMode: 'fitRows',
            percentPosition: true,
            getSortData: {
                price: function(itemElem) {
                    return parseFloat($(itemElem).attr('data-price'));
                },
                name: function(itemElem) {
                    return $(itemElem).find('.product_name').text().toLowerCase();
                }
            }
        });
        
        // Layout ulang setelah gambar selesai load
        \$grid.imagesLoaded().progress(function() {
            \$grid.isotope('layout');
        });
        
        // Paksa layout ulang untuk memastikan
        setTimeout(function() {
            \$grid.isotope('layout');
        }, 300);
        
        // Re-init event handlers
        $('.type_sorting_btn').off('click').on('click', function() {
            var sortByValue = JSON.parse($(this).attr('data-isotope-option')).sortBy;
            \$grid.isotope({ sortBy: sortByValue });
        });
        
    }, 100);
});
</script>
echo '<style>
.product-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.product-item {
    width: calc(25% - 20px);
    min-width: 250px;
}
@media (max-width: 1200px) {
    .product-item { width: calc(33.333% - 20px); }
}
@media (max-width: 768px) {
    .product-item { width: calc(50% - 20px); }
}
@media (max-width: 480px) {
    .product-item { width: 100%; }
}
</style>';
HTML;
}

?>
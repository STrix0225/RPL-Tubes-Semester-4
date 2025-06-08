<?php
require_once '../../Database/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid review ID');
}

$review_id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT r.*, c.customer_name, c.customer_photo, c.customer_email, 
                               p.product_name, p.product_image1, p.product_price
                        FROM reviews r
                        JOIN customers c ON r.customer_id = c.customer_id
                        JOIN products p ON r.product_id = p.product_id
                        WHERE r.review_id = ?");
$stmt->bind_param("i", $review_id);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();

if (!$review) {
    die('Review not found');
}

// Format rating stars
$rating_stars = str_repeat('<i class="fas fa-star text-warning"></i>', $review['rating']) . 
                str_repeat('<i class="far fa-star text-warning"></i>', 5 - $review['rating']);
?>

<div class="review-detail-container">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <?php if (!empty($review['customer_photo'])): ?>
                            <img src="../img/Customers/<?php echo htmlspecialchars($review['customer_photo']); ?>" 
                                 alt="Customer Photo" class="customer-img me-3" style="width: 80px; height: 80px;">
                        <?php else: ?>
                            <div class="customer-img bg-secondary text-white d-flex align-items-center justify-content-center me-3" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($review['customer_name']); ?></h5>
                            <p class="mb-1 text-muted"><?php echo htmlspecialchars($review['customer_email']); ?></p>
                            <p class="mb-0">
                                <span class="badge bg-primary">
                                    <?php echo $review['rating']; ?> <i class="fas fa-star"></i> Rating
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Product Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="../img/Products/<?php echo htmlspecialchars($review['product_image1']); ?>" 
                             alt="Product Image" class="product-img me-3" style="width: 80px; height: 80px;">
                        <div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($review['product_name']); ?></h5>
                            <p class="mb-1 text-muted">$<?php echo number_format($review['product_price'], 2); ?></p>
                            <div class="rating"><?php echo $rating_stars; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Review Details</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Review Date:</strong>
                <p><?php echo date('F j, Y, g:i a', strtotime($review['review_date'])); ?></p>
            </div>
            
            <div class="mb-3">
                <strong>Rating:</strong>
                <div class="rating fs-4"><?php echo $rating_stars; ?></div>
            </div>
            
            <div class="mb-3">
                <strong>Review:</strong>
                <p class="p-3 bg-light rounded"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
            </div>
            
            <?php if (!empty($review['admin_reply'])): ?>
                <div class="admin-reply">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Admin Reply:</strong>
                        <small class="text-muted"><?php echo date('F j, Y, g:i a', strtotime($review['reply_date'])); ?></small>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($review['admin_reply'])); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($review['admin_reply'])): ?>
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-reply me-2"></i>Reply to Review</h5>
        </div>
        <div class="card-body">
            <form id="replyForm" method="POST" action="reviewCustomer.php">
                <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                <div class="form-group mb-3">
                    <label for="replyText" class="form-label">Your Reply</label>
                    <textarea class="form-control" id="replyText" name="reply_text" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Reply</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    // Handle form submission via AJAX to prevent page reload
    $(document).ready(function() {
        $('#replyForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: $(this).serialize(),
                success: function(response) {
                    // Reload the page to show the updated review with reply
                    window.location.reload();
                },
                error: function() {
                    alert('Failed to submit reply.');
                }
            });
        });
    });
</script>
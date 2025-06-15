<?php
require_once '../../Database/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<div class="alert alert-danger">Invalid review ID</div>');
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

$review_id = (int)$_GET['id'];

// Get review details with customer, product, and any admin reply
$stmt = $conn->prepare("SELECT 
        r.*, 
        c.customer_name, 
        c.customer_photo, 
        c.customer_email,
        p.product_name, 
        p.product_image1, 
        p.product_price,
        reply.review_text AS admin_reply,
        reply.review_date AS reply_date,
        a.admin_name AS reply_admin_name
    FROM reviews r
    JOIN customers c ON r.customer_id = c.customer_id
    JOIN products p ON r.product_id = p.product_id
    LEFT JOIN reviews reply ON r.review_id = reply.review_reply_id
    LEFT JOIN admins a ON reply.admin_id = a.admin_id
    WHERE r.review_id = ?");
$stmt->bind_param("i", $review_id);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();

if (!$review) {
    die('<div class="alert alert-danger">Review not found</div>');
}

// Format rating stars
$rating_stars = str_repeat('<i class="fas fa-star text-warning"></i>', $review['rating']) . 
                str_repeat('<i class="far fa-star text-warning"></i>', 5 - $review['rating']);

// Format dates
$review_date = date('F j, Y, g:i a', strtotime($review['review_date']));
$reply_date = !empty($review['reply_date']) ? date('F j, Y, g:i a', strtotime($review['reply_date'])) : '';
?>

<div class="review-detail-container">
    <div class="row mb-4">
        <!-- Customer Info Card -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <?php if (!empty($review['customer_photo'])): ?>
                                <img src="../img/Customers/<?php echo htmlspecialchars($review['customer_photo']); ?>" 
                                     alt="Customer" class="rounded-circle" width="80" height="80">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle" 
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1"><?php echo htmlspecialchars($review['customer_name']); ?></h5>
                            <p class="mb-1 text-muted small">
                                <i class="fas fa-envelope me-1"></i>
                                <?php echo htmlspecialchars($review['customer_email']); ?>
                            </p>
                            <div class="d-flex align-items-center">
                                <div class="me-2"><?php echo $rating_stars; ?></div>
                                <span class="badge bg-primary">
                                    <?php echo $review['rating']; ?>/5
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Info Card -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Product Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="../img/Products/<?php echo htmlspecialchars($review['product_image1']); ?>" 
                                 alt="Product" class="img-thumbnail" width="80" height="80">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1"><?php echo htmlspecialchars($review['product_name']); ?></h5>
                            <p class="mb-2">
                                <span class="fw-bold">Price:</span> 
                                $<?php echo number_format($review['product_price'], 2); ?>
                            </p>
                            <a href="../../admin/Pages/listProducts.php" 
                               class="btn btn-sm btn-outline-success">
                                <i class="fas fa-external-link-alt me-1"></i>View Product
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Details Card -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-comment-dots me-2"></i>Review Content</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Review Date:</strong></p>
                    <p class="text-muted"><?php echo $review_date; ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Rating:</strong></p>
                    <div class="rating fs-5"><?php echo $rating_stars; ?></div>
                </div>
            </div>
            
            <div class="mb-3">
                <p class="mb-1"><strong>Review Text:</strong></p>
                <div class="p-3 bg-light rounded border">
                    <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                </div>
            </div>
            
            <?php if (!empty($review['admin_reply'])): ?>
                <div class="admin-reply p-3 bg-light rounded border mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong class="text-primary">
                                <i class="fas fa-user-shield me-1"></i>
                                <?php echo htmlspecialchars($review['reply_admin_name']); ?>
                            </strong>
                            <span class="badge bg-primary ms-2">Admin</span>
                        </div>
                        <small class="text-muted"><?php echo $reply_date; ?></small>
                    </div>
                    <div class="ps-4 mt-2">
                        <?php echo nl2br(htmlspecialchars($review['admin_reply'])); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reply Form (only show if no reply exists) -->
    <?php if (empty($review['admin_reply'])): ?>
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0"><i class="fas fa-reply me-2"></i>Submit Admin Response</h5>
        </div>
        <div class="card-body">
            <form id="replyForm" method="POST" action="riviewCustomer.php">
                <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                
                <div class="mb-3">
                    <label for="replyText" class="form-label">Your Response</label>
                    <textarea class="form-control" id="replyText" name="reply_text" rows="4" 
                              placeholder="Type your response here..." required></textarea>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Submit Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Handle form submission with better feedback
    $('#replyForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        
        // Disable button during submission
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Sending...');
        
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                // Show success message and reload after delay
                $('#reviewModal').find('.modal-body').prepend(
                    '<div class="alert alert-success alert-dismissible fade show">' +
                    'Reply submitted successfully! Page will refresh shortly.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                );
                
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            },
            error: function() {
                alert('Failed to submit reply. Please try again.');
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Submit Reply');
            }
        });
    });
});
</script>
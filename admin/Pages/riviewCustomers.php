<?php
require_once '../../Database/connection.php';

if (!isAdminLoggedIn()) {
    redirect('../login.php');
}

// Get data for header notifications
$header_data = [
    'pending_orders' => 0,
    'recent_orders' => []
];

$result = $conn->query("SELECT COUNT(*) AS total_pending FROM orders WHERE order_status = 'pending'");
if ($result) {
    $header_data['pending_orders'] = (int)$result->fetch_assoc()['total_pending'];
}

$result = $conn->query("SELECT order_id, order_cost FROM orders WHERE order_status = 'pending' ORDER BY order_date DESC LIMIT 5");
if ($result) {
    $header_data['recent_orders'] = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_text'])) {
    $review_id = (int)$_POST['review_id'];
    $reply_text = trim($_POST['reply_text']);
    
    if (!empty($reply_text)) {
        $stmt = $conn->prepare("UPDATE reviews SET admin_reply = ?, reply_date = NOW() WHERE review_id = ?");
        $stmt->bind_param("si", $reply_text, $review_id);
        
        if ($stmt->execute()) {
            header("Location: reviewCustomer.php?success=Reply+submitted+successfully");
            exit();
        } else {
            header("Location: reviewCustomer.php?error=Failed+to+submit+reply");
            exit();
        }
    }
}

// Get all reviews with customer and product info
$reviews = [];
$query = "SELECT r.*, c.customer_name, c.customer_photo, p.product_name, p.product_image1 
          FROM reviews r
          JOIN customers c ON r.customer_id = c.customer_id
          JOIN products p ON r.product_id = p.product_id
          ORDER BY r.review_date DESC";
$result = $conn->query($query);
if ($result) {
    $reviews = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Customer Reviews - GEMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="../css/style.css" rel="stylesheet" />
    <style>
        .review-card {
            border-left: 4px solid #4e73df;
            transition: all 0.3s ease;
        }
        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .customer-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .rating {
            color: #ffc107;
            font-size: 1.1rem;
        }
        .reply-form {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }
        .admin-reply {
            background-color: #e9ecef;
            border-left: 3px solid #4e73df;
            padding: 10px;
            border-radius: 0 5px 5px 0;
            margin-top: 10px;
        }
        .badge-rating {
            background-color: #4e73df;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../Layout/sidebar.php'; ?>

        <div id="content">
            <?php include '../Layout/header.php'; ?>

            <div class="container-fluid mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-primary">
                        <i class="fas fa-comments me-2"></i>Customer Reviews
                    </h1>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reviewsTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Rating</th>
                                        <th>Review</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reviews as $index => $review): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($review['customer_photo'])): ?>
                                                    <img src="../img/Customers/<?php echo htmlspecialchars($review['customer_photo']); ?>" 
                                                         alt="Customer Photo" class="customer-img me-2">
                                                <?php else: ?>
                                                    <div class="customer-img bg-secondary text-white d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <span><?php echo htmlspecialchars($review['customer_name']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../img/Products/<?php echo htmlspecialchars($review['product_image1']); ?>" 
                                                     alt="Product Image" class="product-img me-2">
                                                <span><?php echo htmlspecialchars($review['product_name']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-rating text-white">
                                                <?php echo htmlspecialchars($review['rating']); ?> <i class="fas fa-star"></i>
                                            </span>
                                        </td>
                                        <td><?php echo nl2br(htmlspecialchars(substr($review['review_text'], 0, 50) . (strlen($review['review_text']) > 50 ? '...' : ''))); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($review['review_date'])); ?></td>
                                        <td>
                                            <?php if (!empty($review['admin_reply'])): ?>
                                                <span class="badge bg-success">Replied</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary view-review-btn" 
                                                    data-id="<?php echo $review['review_id']; ?>">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php include '../Layout/footer.php'; ?>
        </div>
    </div>

    <!-- Review Detail Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Review Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="reviewDetails">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/script.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#reviewsTable').DataTable();
            
            // Handle view review button click
            $('.view-review-btn').click(function() {
                const reviewId = $(this).data('id');
                $.ajax({
                    url: 'getReviewDetails.php',
                    type: 'GET',
                    data: { id: reviewId },
                    success: function(response) {
                        $('#reviewDetails').html(response);
                        $('#reviewModal').modal('show');
                    },
                    error: function() {
                        alert('Failed to load review details.');
                    }
                });
            });
        });
    </script>
</body>
</html>
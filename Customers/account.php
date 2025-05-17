<?php
session_start();
include('../Database/connection.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if not logged in
if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit;
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header('location: login.php');
    exit;
}

// Change password functionality
if (isset($_POST['change_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['customer_email'];

    if ($password !== $confirm_password) {
        header('location: account.php?error=Password+did+not+match');
        exit;
    } elseif (strlen($password) < 6) {
        header('location: account.php?error=Password+must+be+at+least+6+characters');
        exit;
    } else {
        // Use password_hash() for secure password storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "UPDATE customers SET customer_password = ? WHERE customer_email = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        
        $stmt->bind_param('ss', $hashed_password, $email);
        
        if ($stmt->execute()) {
            header('location: account.php?success=Password+has+been+updated+successfully');
        } else {
            header('location: account.php?error=Could+not+update+password');
        }
        exit;
    }
}

// Get orders for the customer
// Get orders for the customer
$customer_orders = []; // Initialize empty array

if (isset($_SESSION['logged_in'])) {
    $customer_id = $_SESSION['customer_id'];

    // Cek apakah tabel 'orders' ada
    $result = $conn->query("SHOW TABLES LIKE 'orders'");
    if ($result && $result->num_rows > 0) {

        // Tabel ada, cek kolom relasi customer
        $check_columns = $conn->query("SHOW COLUMNS FROM orders LIKE 'customer_id'");
        if ($check_columns && $check_columns->num_rows > 0) {
            $customer_column = 'customer_id';
        } else {
            $check_columns = $conn->query("SHOW COLUMNS FROM orders LIKE 'user_id'");
            if ($check_columns && $check_columns->num_rows > 0) {
                $customer_column = 'user_id';
            } else {
                $customer_column = null;
            }
        }

        if ($customer_column) {
            $query = "SELECT * FROM orders WHERE $customer_column = ? ORDER BY order_date DESC";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param('i', $customer_id);
                if ($stmt->execute()) {
                    $customer_orders = $stmt->get_result();
                } else {
                    die("Execute failed: " . htmlspecialchars($stmt->error));
                }
            } else {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }
        } else {
            // Kolom relasi ke customer tidak ditemukan
            echo "<div class='alert alert-warning'>Orders table does not have a customer reference column.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Orders table not found in the database.</div>";
    }
}


// Check for total payment in session
if (isset($_SESSION['total'])) {
    $total_bayar = $_SESSION['total'];
}
?>

<?php include('layouts/header.php'); ?>

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Account</h4>
                    <div class="breadcrumb__links">
                        <a href="index.php">Home</a>
                        <span>Account</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Checkout Section Begin -->
<section class="checkout spad">
    <div class="container">
        <div class="checkout__form">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <form id="account-form" method="POST" action="account.php">
                        <?php if (isset($_GET['success'])) { ?>
                            <div class="alert alert-info" role="alert">
                                <?php echo htmlspecialchars($_GET['success']); ?>
                            </div>
                        <?php } ?>
                        <?php if (isset($_GET['error'])) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($_GET['error']); ?>
                            </div>
                        <?php } ?>
                        <h6 class="checkout__title">Change Password</h6>
                        <div class="checkout__input">
                            <p>Password<span>*</span></p>
                            <input type="password" id="account-password" name="password" required>
                        </div>
                        <div class="checkout__input">
                            <p>Confirm Password<span>*</span></p>
                            <input type="password" id="account-confirm-password" name="confirm_password" required>
                        </div>
                        <div class="checkout__input">
                            <input type="submit" class="site-btn" id="change-password-btn" name="change_password" value="CHANGE PASSWORD">
                        </div>
                    </form>
                </div>
                <div class="col-lg-6 col-md-6">
                    <?php if (isset($_GET['message'])) { ?>
                        <div class="alert alert-info" role="alert">
                            <?php echo htmlspecialchars($_GET['message']); ?>
                        </div>
                    <?php } ?>
                    <div class="checkout__order">
                        <h4 class="order__title">Account Info</h4>
                        <div class="row">
                            <div class="col-sm-6 col-md-4">
                                <img src="<?php echo 'img/profile/' . htmlspecialchars($_SESSION['customer_photo']); ?>" alt="Profile Photo" class="rounded-circle img-responsive">
                            </div>
                            <div class="col-sm-6 col-md-8">
                                <h4><?php echo htmlspecialchars($_SESSION['customer_name']); ?></h4>
                                <small><cite title="Address">
                                    <?php echo htmlspecialchars($_SESSION['customer_address'] ?? ''); ?>, 
                                    <?php echo htmlspecialchars($_SESSION['customer_city'] ?? ''); ?> 
                                    <i class="fas fa-map-marker-alt"></i>
                                </cite></small>
                                <p>
                                    <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($_SESSION['customer_email']); ?>
                                    <br>
                                    <i class="fa fa-phone"></i> <?php echo htmlspecialchars($_SESSION['customer_phone'] ?? ''); ?>
                                </p>
                            </div>
                        </div>
                        <h4 class="order__title"></h4>
                        <a href="#orders" class="btn btn-primary">YOUR ORDERS</a>
                        <a href="account.php?logout=1" id="logout-btn" class="btn btn-danger">LOG OUT</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Checkout Section End -->

<!-- Order History Begin -->
<section id="orders" class="shopping-cart spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <?php if (isset($_GET['payment_message'])) { ?>
                        <div class="alert alert-info" role="alert">
                            <?php echo htmlspecialchars($_GET['payment_message']); ?>
                        </div>
                    <?php } ?>
                    <h2>Your Orders History</h2>
                    <span>***</span>
                </div>
                <div class="shopping__cart__table">
                    <?php if (!empty($customer_orders)) { ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Cost</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $customer_orders->fetch_assoc()) { ?>
                                    <tr>
                                        <td class="product__cart__item">
                                            <div class="product__cart__item__text">
                                                <h6><?php echo htmlspecialchars($order['order_id']); ?></h6>
                                            </div>
                                        </td>
                                        <td class="product__cart__item">
                                            <div class="product__cart__item__text">
                                                <?php echo isset($order['order_cost'], $kurs_dollar) 
                                                    ? setRupiah($order['order_cost'] * $kurs_dollar) 
                                                    : 'N/A'; ?>
                                            </div>
                                        </td>
                                        <td class="product__cart__item">
                                            <div class="product__cart__item__text">
                                                <h6><?php echo htmlspecialchars($order['order_status'] ?? 'Unknown'); ?></h6>
                                            </div>
                                        </td>
                                        <td class="product__cart__item">
                                            <div class="product__cart__item__text">
                                                <h5><?php echo htmlspecialchars($order['order_date'] ?? 'N/A'); ?></h5>
                                            </div>
                                        </td>
                                        <td class="cart__price">
                                            <form method="POST" action="order-details.php">
                                                <input type="hidden" name="order_status" value="<?php echo htmlspecialchars($order['order_status'] ?? ''); ?>">
                                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id'] ?? ''); ?>">
                                                <input class="btn btn-success" name="order_details_btn" type="submit" value="Details">
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div class="alert alert-info">No orders found.</div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Order History End -->

<?php include('layouts/footer.php'); ?>
<?php
// Start session and include database connection
include('../../Database/connection.php');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Get customer data from database
$customer_id = $_SESSION['customer_id'];
$query = "SELECT * FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>My Profile - Gadget MS</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Gadget MS Profile">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="styles/bootstrap4/bootstrap.min.css">
    <link href="plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="styles/main_styles.css">
    <link rel="stylesheet" type="text/css" href="styles/responsive.css">
    <link rel="shortcut icon" href="../gems-customer-pages/images/Background3.jpg" />
    <style>
        .profile-section {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 30px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #f5f5f5;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-info {
            margin-top: 20px;
        }

        .info-item {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .info-value {
            color: #495057;
            font-size: 16px;
        }

        .password-toggle {
            cursor: pointer;
            color: #6a11cb;
            font-size: 14px;
        }

        .password-toggle:hover {
            text-decoration: underline;
        }

        .edit-btn {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            margin-top: 20px;
        }
        /* Dark Mode Styles */
body.dark-mode {
    background-color: #121212;
    color: #e0e0e0;
}

body.dark-mode .profile-section {
    background-color: #1e1e1e;
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
}

body.dark-mode .info-label {
    color: #9e9e9e;
}

body.dark-mode .info-value {
    color: #e0e0e0;
}

body.dark-mode .profile-img {
    border-color: #333;
}

body.dark-mode .info-item {
    border-bottom-color: #333;
}
    </style>
</head>

<body>

    <div class="super_container">

        <!-- Header (Same as dashboard.php) -->
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
                                        <a href="profile.php">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                            <i class="fa fa-angle-down" aria-hidden="true"></i>
                                        </a>
                                        <ul class="account_selection">
                                            <?php if (isset($_SESSION['customer_id'])): ?>
                                                <li><a href="profile.php"><i class="fa fa-user" aria-hidden="true"></i> My Profile</a></li>
                                                <li><a href="logout-customer.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                                            <?php else: ?>
                                                <li><a href="login-customer.php"><i class="fa fa-sign-in" aria-hidden="true"></i> Sign In</a></li>
                                                <li><a href="register-customer.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                    <li class="checkout">
                                        <a href="cart.php">
                                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                            <span id="checkout_items" class="checkout_items"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span>
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

        <!-- Profile Content -->
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="container profile-container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="profile-section">
                        <div class="profile-header">
                            <?php if (!empty($customer['customer_photo'])): ?>
                                <img src="<?php echo htmlspecialchars($customer['customer_photo']); ?>" alt="Profile Photo" class="profile-img">
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($customer['customer_name']); ?>&size=150&background=random" alt="Profile Photo" class="profile-img">
                            <?php endif; ?>
                            <h2><?php echo htmlspecialchars($customer['customer_name']); ?></h2>
                            <p>Member since <?php echo date('F Y', strtotime($customer['registration_date'] ?? 'now')); ?></p>
                        </div>

                        <div class="profile-info">
                            <div class="info-item">
                                <div class="info-label"><i class="fa fa-envelope"></i> Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($customer['customer_email']); ?></div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="fa fa-phone"></i> Phone</div>
                                <div class="info-value"><?php echo htmlspecialchars($customer['customer_phone']); ?></div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="fa fa-map-marker"></i> Address</div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($customer['customer_address']); ?>, <?php echo htmlspecialchars($customer['customer_city']); ?>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="fa fa-lock"></i> Password</div>
                                <div class="info-value">
                                    <span id="password-display">••••••••</span>
                                </div>
                            </div>

                            <div class="text-center">
                                <a href="edit-profile.php" class="btn btn-primary edit-btn">
                                    <i class="fa fa-edit"></i> Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer (Same as dashboard.php) -->
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
    <script src="plugins/easing/easing.js"></script>
    <script src="js/custom.js"></script>
    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordDisplay = document.getElementById('password-display');
            const toggleButton = document.getElementById('toggle-password');

            if (passwordDisplay.textContent === '••••••••') {
                passwordDisplay.textContent = '<?php echo htmlspecialchars($customer['customer_password']); ?>';
                toggleButton.textContent = 'Hide Password';
            } else {
                passwordDisplay.textContent = '••••••••';
                toggleButton.textContent = 'Show Password';
            }
        }

        // Dark Mode Toggle
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const darkModeIcon = darkModeToggle.querySelector('i');

    // Check for saved dark mode preference
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
    </script>
</body>

</html>
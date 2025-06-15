<?php
// Start session and include database connection
include('../../Database/connection.php');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$errors = [];
$success = '';

// Get customer data from database
$customer_id = $_SESSION['customer_id'];
$query = "SELECT * FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $name = trim($_POST['customer_name']);
    $email = trim($_POST['customer_email']);
    $phone = trim($_POST['customer_phone']);
    $city = trim($_POST['customer_city']);
    $address = trim($_POST['customer_address']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Basic validation
    if (empty($name)) {
        $errors['customer_name'] = "Name is required";
    }
    
    if (empty($email)) {
        $errors['customer_email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['customer_email'] = "Invalid email format";
    }
    
    // Password change validation
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors['current_password'] = "Current password is required to change password";
        } elseif (!password_verify($current_password, $customer['customer_password'])) {
            $errors['current_password'] = "Current password is incorrect";
        } elseif (strlen($new_password) < 8) {
            $errors['new_password'] = "Password must be at least 8 characters";
        } elseif ($new_password !== $confirm_password) {
            $errors['confirm_password'] = "Passwords do not match";
        }
    }
    
    // Handle photo upload
    $photo = $customer['customer_photo'];
    if (!empty($_FILES['customer_photo']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["customer_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["customer_photo"]["tmp_name"]);
        if ($check === false) {
            $errors['customer_photo'] = "File is not an image";
        } elseif ($_FILES["customer_photo"]["size"] > 500000) {
            $errors['customer_photo'] = "Image is too large (max 500KB)";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['customer_photo'] = "Only JPG, JPEG, PNG & GIF files are allowed";
        } else {
            // Generate unique filename
            $new_filename = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["customer_photo"]["tmp_name"], $target_file)) {
                $photo = "uploads/" . $new_filename;
                
                // Delete old photo if it exists and isn't the default
                if (!empty($customer['customer_photo']) && 
                    !str_contains($customer['customer_photo'], 'ui-avatars.com')) {
                    @unlink("../../" . $customer['customer_photo']);
                }
            } else {
                $errors['customer_photo'] = "Error uploading file";
            }
        }
    }
    
    // Update database if no errors
    if (empty($errors)) {
        // Prepare update query
        $query = "UPDATE customers SET 
                  customer_name = ?, 
                  customer_email = ?, 
                  customer_phone = ?, 
                  customer_city = ?, 
                  customer_address = ?, 
                  customer_photo = ?";
        
        $params = [$name, $email, $phone, $city, $address, $photo];
        $types = "ssssss";
        
        // Add password to update if changed
        if (!empty($new_password)) {
            $query .= ", customer_password = ?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            $types .= "s";
        }
        
        $query .= " WHERE customer_id = ?";
        $params[] = $customer_id;
        $types .= "i";
        
        // Execute update
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            // Refresh customer data
            $stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $customer = $result->fetch_assoc();
        } else {
            $errors['database'] = "Error updating profile: " . $conn->error;
        }
    }
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Profile - Gadget MS</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Gadget MS Edit Profile">
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
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s;
        }
        .profile-img:hover {
            opacity: 0.8;
        }
        .profile-info {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .form-control {
            border-radius: 3px;
            padding: 10px 15px;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 14px;
        }
        .password-toggle {
            cursor: pointer;
            color: #6a11cb;
            font-size: 14px;
        }
        .password-toggle:hover {
            text-decoration: underline;
        }
        .btn-save {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            margin-top: 20px;
        }
        .btn-cancel {
            background: #6c757d;
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            margin-top: 20px;
            margin-right: 10px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
            padding: 10px 15px;
            border-radius: 3px;
            margin-bottom: 20px;
        }
        #photo-upload {
            display: none;
        }
        .photo-preview-container {
            position: relative;
            display: inline-block;
        }
        .photo-change-text {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px;
            text-align: center;
            border-radius: 0 0 50% 50%;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .photo-preview-container:hover .photo-change-text {
            opacity: 1;
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
    
    <!-- Edit Profile Content -->
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
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="edit-profile.php" method="POST" enctype="multipart/form-data">
                        <div class="profile-header">
                            <div class="photo-preview-container">
                                <?php if (!empty($customer['customer_photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($customer['customer_photo']); ?>" 
                                         alt="Profile Photo" 
                                         class="profile-img" 
                                         id="photo-preview"
                                         onclick="document.getElementById('photo-upload').click()">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($customer['customer_name']); ?>&size=150&background=random" 
                                         alt="Profile Photo" 
                                         class="profile-img" 
                                         id="photo-preview"
                                         onclick="document.getElementById('photo-upload').click()">
                                <?php endif; ?>
                                <div class="photo-change-text">Change Photo</div>
                            </div>
                            <input type="file" name="customer_photo" id="photo-upload" accept="image/*">
                            <?php if (isset($errors['customer_photo'])): ?>
                                <div class="invalid-feedback d-block text-center">
                                    <?php echo $errors['customer_photo']; ?>
                                </div>
                            <?php endif; ?>
                            <h2>Edit Profile</h2>
                        </div>
                        
                        <div class="profile-info">
                            <div class="form-group">
                                <label for="customer_name" class="form-label"><i class="fa fa-user"></i> Full Name</label>
                                <input type="text" class="form-control <?php echo isset($errors['customer_name']) ? 'is-invalid' : ''; ?>" 
                                       id="customer_name" name="customer_name" 
                                       value="<?php echo htmlspecialchars($customer['customer_name']); ?>">
                                <?php if (isset($errors['customer_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['customer_name']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_email" class="form-label"><i class="fa fa-envelope"></i> Email</label>
                                <input type="email" class="form-control <?php echo isset($errors['customer_email']) ? 'is-invalid' : ''; ?>" 
                                       id="customer_email" name="customer_email" 
                                       value="<?php echo htmlspecialchars($customer['customer_email']); ?>">
                                <?php if (isset($errors['customer_email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['customer_email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_phone" class="form-label"><i class="fa fa-phone"></i> Phone</label>
                                <input type="text" class="form-control" 
                                       id="customer_phone" name="customer_phone" 
                                       value="<?php echo htmlspecialchars($customer['customer_phone']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_city" class="form-label"><i class="fa fa-building"></i> City</label>
                                <input type="text" class="form-control" 
                                       id="customer_city" name="customer_city" 
                                       value="<?php echo htmlspecialchars($customer['customer_city']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_address" class="form-label"><i class="fa fa-map-marker"></i> Address</label>
                                <textarea class="form-control" id="customer_address" name="customer_address" 
                                          rows="3"><?php echo htmlspecialchars($customer['customer_address']); ?></textarea>
                            </div>
                            
                            <hr>
                            <h5><i class="fa fa-lock"></i> Change Password</h5>
                            <p class="text-muted">Leave blank to keep current password</p>
                            
                            <div class="form-group">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php echo isset($errors['current_password']) ? 'is-invalid' : ''; ?>" 
                                           id="current_password" name="current_password">
                                    <div class="input-group-append">
                                        <span class="input-group-text password-toggle" onclick="togglePassword('current_password')">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <?php if (isset($errors['current_password'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?php echo $errors['current_password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>" 
                                           id="new_password" name="new_password">
                                    <div class="input-group-append">
                                        <span class="input-group-text password-toggle" onclick="togglePassword('new_password')">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <?php if (isset($errors['new_password'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?php echo $errors['new_password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                           id="confirm_password" name="confirm_password">
                                    <div class="input-group-append">
                                        <span class="input-group-text password-toggle" onclick="togglePassword('confirm_password')">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?php echo $errors['confirm_password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="text-center">
                                <a href="profile.php" class="btn btn-secondary btn-cancel">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-save">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
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
    // Photo upload preview
    document.getElementById('photo-upload').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo-preview').src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Password toggle functionality
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Dark Mode Toggle (same as dashboard)
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const darkModeIcon = darkModeToggle.querySelector('i');
        
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            darkModeIcon.classList.remove('fa-moon-o');
            darkModeIcon.classList.add('fa-sun-o');
        }
        
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
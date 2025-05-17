<?php
session_start();
include('../Database/connection.php');


if (isset($_SESSION['logged_in'])) {
    header('location: account.php');
    exit;
}


if (isset($_POST['login_btn'])) {
    $email = $_POST['customer_email'];
    $password = md5($_POST['customer_password']); // Note: MD5 is insecure - consider password_hash()/password_verify()

    $query = "SELECT customer_id, customer_name, customer_email, customer_password, 
                     customer_phone, customer_address, customer_city, customer_photo 
              FROM customers WHERE customer_email = ? AND customer_password = ? LIMIT 1";

    $stmt_login = $conn->prepare($query);
    $stmt_login->bind_param('ss', $email, $password);
    
    if ($stmt_login->execute()) {
        $stmt_login->bind_result($customer_id, $customer_name, $customer_email, $customer_password, 
                                $customer_phone, $customer_address, $customer_city, $customer_photo);
        $stmt_login->store_result();

        if ($stmt_login->num_rows() == 1) {
            $stmt_login->fetch();

            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['customer_name'] = $customer_name;
            $_SESSION['customer_email'] = $customer_email;
            $_SESSION['customer_phone'] = $customer_phone;
            $_SESSION['customer_address'] = $customer_address;
            $_SESSION['customer_city'] = $customer_city;
            $_SESSION['customer_photo'] = $customer_photo;
            $_SESSION['logged_in'] = true;

            header('location: account.php?message=Logged in successfully');
            exit;
        } else {
            header('location: login.php?error=Could not verify your account');
            exit;
        }
    } else {
        header('location: login.php?error=Something went wrong!');
        exit;
    }
}
?>

<?php include('layouts/header.php'); ?>

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Login</h4>
                    <div class="breadcrumb__links">
                        <a href="index.php">Home</a>
                        <span>Login</span>
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
            <form id="login-form" method="POST" action="login.php">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <h6 class="checkout__title">Login</h6>
                        <div class="checkout__input">
                            <p>Email<span>*</span></p>
                            <input type="email" name="customer_email" required>
                        </div>
                        <div class="checkout__input">
                            <p>Password<span>*</span></p>
                            <input type="password" name="customer_password" id="login-password" required>
                            <label>
                                <input type="checkbox" onclick="togglePasswordVisibility()">
                                Tampilkan Password
                            </label>
                        </div>
                        <div class="checkout__input">
                            <input type="submit" class="site-btn" id="login-btn" name="login_btn" value="LOGIN">
                        </div>
                        <div class="checkout__input__checkbox">
                            <label>
                                <a id="register-url" href="register.php">Belum punya akun? Registrasi</a>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- Checkout Section End -->

<script>
function togglePasswordVisibility() {
    const passwordField = document.getElementById('login-password');
    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
}
</script>

<?php include('layouts/footer.php'); ?>
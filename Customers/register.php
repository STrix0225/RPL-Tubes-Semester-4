<?php
session_start();
include('../Database/connection.php');


    if (isset($_SESSION['logged_in'])) {
        header('location: account.php');
        exit;
    }



if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];

    // Image file validation
    if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo']['tmp_name'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($photo);
        
        if (!in_array($file_type, $allowed_types)) {
            header('location: register.php?error=Invalid file type. Only JPG, PNG, and GIF are allowed.');
            exit;
        }
        
        $photo_name = str_replace(' ', '_', $name) . ".jpg";
        move_uploaded_file($photo, "../img/" . $photo_name);
    } else {
        $photo_name = 'default.jpg'; // Default image if none uploaded
    }

    if ($password !== $confirm_password) {
        header('location: register.php?error=Password did not match');
        exit;
    } else if (strlen($password) < 6) {
        header('location: register.php?error=Password must be at least 6 characters');
        exit;
    } else {
        $query_check_customer = "SELECT COUNT(*) FROM customers WHERE customer_email = ?";
        $stmt_check_customer = $conn->prepare($query_check_customer);
        $stmt_check_customer->bind_param('s', $email);
        $stmt_check_customer->execute();
        $stmt_check_customer->bind_result($num_rows);
        $stmt_check_customer->store_result();
        $stmt_check_customer->fetch();

        if ($num_rows !== 0) {
            header('location: register.php?error=Email telah terdaftar!');
            exit;
        } else {
            $query_save_customer = "INSERT INTO customers (customer_name, customer_email, customer_password, customer_phone, customer_address, customer_city, customer_photo) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt_save_customer = $conn->prepare($query_save_customer);
            $hashed_password = md5($password); // Consider using password_hash() instead
            $stmt_save_customer->bind_param('sssssss', $name, $email, $hashed_password, $phone, $address, $city, $photo_name);

            if ($stmt_save_customer->execute()) {
                $customer_id = $stmt_save_customer->insert_id;

                $_SESSION['customer_id'] = $customer_id;
                $_SESSION['customer_email'] = $email;
                $_SESSION['customer_name'] = $name;
                $_SESSION['customer_phone'] = $phone;
                $_SESSION['customer_address'] = $address;
                $_SESSION['customer_city'] = $city;
                $_SESSION['customer_photo'] = $photo_name;
                $_SESSION['logged_in'] = true;

                header('location: account.php?register_success=You registered successfully!');
                exit;
            } else {
                header('location: register.php?error=Could not create an account at the moment');
                exit;
            }
        }
    }
}
?>

<?php include('layouts/header.php'); ?>

<!-- Register Section Begin -->
<section class="checkout spad">
    <div class="container">
        <div class="checkout__form">
            <form id="checkout-form" method="POST" action="register.php" enctype="multipart/form-data">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <h6 class="checkout__title">Registration</h6>
                        <div class="checkout__input">
                            <p>Name<span>*</span></p>
                            <input type="text" id="registered-name" name="name" placeholder="Masukan Nama" required>
                        </div>
                        <div class="checkout__input">
                            <p>Email<span>*</span></p>
                            <input id="registered-email" type="email" name="email" placeholder="Masukan Email" required>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Password<span>*</span></p>
                                    <input id="registered-password" type="password" name="password" placeholder="Masukan Password" required>
                                    <label>
                                        <input type="checkbox" onclick="document.getElementById('registered-password').type = this.checked ? 'text' : 'password'">
                                        Show Password
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Confirm Password<span>*</span></p>
                                    <input id="registered-confirm-password" type="password" name="confirm_password" placeholder="Konfimasi Password" required>
                                </div>
                            </div>
                        </div>
                        <div class="checkout__input">
                            <p>Nomor Telepon<span>*</span></p>
                            <input type="text" name="phone" placeholder="Masukan Nomor Telepon" required>
                        </div>
                        <div class="checkout__input">
                            <p>Kota<span>*</span></p>
                            <input type="text" name="city" placeholder="Masukan Kota" required>
                        </div>
                        <div class="checkout__input">
                            <p>Alamat<span>*</span></p>
                            <input type="text" name="address" placeholder="Masukan Alamat" class="checkout__input__add" required>
                        </div>
                        <div class="checkout__input">
                            <p>Photo</p>
                            <div class="custom-file">
                                <input type="file" id="photo" name="photo" accept="image/*">
                            </div>
                        </div>
                        <div class="checkout__input">
                            <input type="submit" class="site-btn" id="register-btn" name="register" value="REGISTER">
                        </div>
                        <div class="checkout__input__checkbox">
                            <label for="acc">
                                <a id="login-url" href="login.php">Sudah punya akun? Login</a>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- Register Section End -->

<?php include('layouts/footer.php'); ?>
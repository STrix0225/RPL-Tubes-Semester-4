<?php
session_start();
include('../Database/connection.php');

/*

if (isset($_SESSION['logged_in'])) {
    header('location: account.php');
    exit;
}
*/

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];

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
        $photo_name = 'default.jpg';
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
            $hashed_password = md5($password); // Gunakan password_hash() jika memungkinkan
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

<!-- Form Registrasi -->
<h2>Registrasi</h2>
<form method="POST" action="register.php" enctype="multipart/form-data">
    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <label>Nama *</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email *</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password *</label><br>
    <input type="password" id="registered-password" name="password" required><br>
    <input type="checkbox" onclick="document.getElementById('registered-password').type = this.checked ? 'text' : 'password'"> Tampilkan Password<br><br>

    <label>Konfirmasi Password *</label><br>
    <input type="password" name="confirm_password" required><br><br>

    <label>No. Telepon *</label><br>
    <input type="text" name="phone" required><br><br>

    <label>Kota *</label><br>
    <input type="text" name="city" required><br><br>

    <label>Alamat *</label><br>
    <input type="text" name="address" required><br><br>

    <label>Foto</label><br>
    <input type="file" name="photo" accept="image/*"><br><br>

    <input type="submit" name="register" value="REGISTER"><br><br>

    <p><a href="login.php">Sudah punya akun? Login</a></p>
</form>

<?php include('layouts/footer.php'); ?>

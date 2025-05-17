<?php
session_start();
include('../Database/connection.php');

// Redirect jika sudah login
if (isset($_SESSION['logged_in'])) {
    header('Location: account.php');
    exit();
}

// Proses login saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_btn'])) {
    $email = $_POST['customer_email'];
    $password = md5($_POST['customer_password']); // Disarankan ganti dengan password_hash/password_verify

    $query = "SELECT customer_id, customer_name, customer_email, customer_password, 
                     customer_phone, customer_address, customer_city, customer_photo 
              FROM customers WHERE customer_email = ? AND customer_password = ? LIMIT 1";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($customer_id, $customer_name, $customer_email, $customer_password,
                $customer_phone, $customer_address, $customer_city, $customer_photo);
            $stmt->fetch();

            // Simpan data ke sesi
            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['customer_name'] = $customer_name;
            $_SESSION['customer_email'] = $customer_email;
            $_SESSION['customer_phone'] = $customer_phone;
            $_SESSION['customer_address'] = $customer_address;
            $_SESSION['customer_city'] = $customer_city;
            $_SESSION['customer_photo'] = $customer_photo;
            $_SESSION['logged_in'] = true;

            header("Location: account.php");
            exit();
        } else {
            $error = "Email atau password salah.";
        }
    } else {
        $error = "Terjadi kesalahan saat login.";
    }

    $stmt->close();
}
?>

<!-- HTML Form Login -->
<!DOCTYPE html>
<html>
<head>
    <title>Login Customer</title>
</head>
<body>

<h2>Login</h2>

<?php if (isset($error)): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="login.php">
    <label for="customer_email">Email:</label><br>
    <input type="email" id="customer_email" name="customer_email" required><br><br>

    <label for="customer_password">Password:</label><br>
    <input type="password" id="customer_password" name="customer_password" required><br><br>

    <input type="submit" name="login_btn" value="Login">
</form>

<p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>

</body>
</html>
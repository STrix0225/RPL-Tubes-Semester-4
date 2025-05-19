<?php
session_start();
include('../Database/connection.php');



if (isset($_POST['login_btn'])) {
    $email = trim($_POST['customer_email']); 
    $password = md5($_POST['customer_password']); 
    
    if (strcasecmp($email, "a@Login.admin") == 0 ||
        strcasecmp($password, "111") == 0 ) {
        header('location: ../admins/Login.php');
        exit;
    }

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
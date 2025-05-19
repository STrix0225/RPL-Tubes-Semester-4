<?php
session_start();
include('../Database/connection.php');

// Debugging error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect jika belum login
if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('location: login.php');
    exit;
}

// Ganti password
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account</title>
</head>
<body>

<h2>Account Information</h2>
<?php if (isset($_GET['success'])): ?>
    <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php endif; ?>

<p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['customer_name']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['customer_email']); ?></p>
<p><strong>Phone:</strong> <?php echo htmlspecialchars($_SESSION['customer_phone'] ?? ''); ?></p>
<p><strong>Address:</strong> <?php echo htmlspecialchars($_SESSION['customer_address'] ?? ''); ?></p>
<p><strong>City:</strong> <?php echo htmlspecialchars($_SESSION['customer_city'] ?? ''); ?></p>
<p><strong>Photo:</strong><br>
    <img src="img/profile/<?php echo htmlspecialchars($_SESSION['customer_photo']); ?>" alt="Profile Photo" width="100">
</p>

<hr>

<h2>Change Password</h2>
<form method="POST" action="account.php">
    <label for="password">New Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label for="confirm_password">Confirm New Password:</label><br>
    <input type="password" name="confirm_password" required><br><br>

    <input type="submit" name="change_password" value="Change Password">
</form>

<br>
<a href="account.php?logout=1">Logout</a>

</body>
</html>

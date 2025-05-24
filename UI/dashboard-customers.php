<?php
session_start();

if (!isset($_SESSION['login_type']) || $_SESSION['login_type'] !== 'customer') {
    header("Location: ../UI/gems-login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Dashboard customers</h1>
</body>
</html>
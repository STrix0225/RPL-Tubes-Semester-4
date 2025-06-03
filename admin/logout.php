<?php session_start(); ?>

<?php
    if (isset($_GET['logout']) && $_GET['logout'] == 1) {
        if (isset($_SESSION['login_success'])) {
            unset($_SESSION['login_success']);
            unset($_SESSION['admin_email']);
            unset($_SESSION['admin_name']);
            header('location: ../../login.php');
            exit;
        }
    }
?>
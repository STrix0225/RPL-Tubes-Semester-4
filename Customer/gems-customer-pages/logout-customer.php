<?php
// File: logout-customer.php
session_start();
session_destroy();
header("Location: ../gems-customer-pages/dashboard.php");
exit();
?>
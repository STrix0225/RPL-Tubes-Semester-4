<?php
require_once '../Database/connection.php';
$_SESSION = array();
session_destroy();
redirect('login.php');
?>
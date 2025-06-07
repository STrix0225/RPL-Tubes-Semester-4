<?php
require_once './DB/connection.php';
$_SESSION = array();
session_destroy();
redirect('login.php');
?>
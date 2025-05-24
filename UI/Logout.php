<?php
session_start();
session_unset();
session_destroy();
header("Location: ../UI/gems-login/login.php");
exit();

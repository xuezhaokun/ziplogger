<?php
//echo "logout\n";
session_start();
//echo "start\n";
unset($_SESSION['login_user']);
session_destroy();
echo "success";
?>
<?php
session_start();
session_destroy();
header("Location: b_login.php");
exit();
?>


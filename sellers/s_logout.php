<?php
session_start();
session_unset();
session_destroy();
header("Location: S_login.php");
exit;
?>

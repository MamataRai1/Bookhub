<?php
session_start();
echo isset($_SESSION['b_loginid']) ? "User ID: " . $_SESSION['b_loginid'] : "User not logged in.";
?>

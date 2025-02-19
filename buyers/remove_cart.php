<?php
session_start();
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cart_id = $_POST['cart_id'];

    $deleteQuery = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
    $deleteQuery->bind_param("i", $cart_id);
    $deleteQuery->execute();
}

header("Location: cart.php");
?>

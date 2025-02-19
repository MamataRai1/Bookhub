<?php
session_start();
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    $updateQuery = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
    $updateQuery->bind_param("ii", $quantity, $cart_id);
    $updateQuery->execute();
}

header("Location: cart.php");
?>

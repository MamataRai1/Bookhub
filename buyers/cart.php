<?php
session_start();
include '../config.php'; // Database connection

if (!isset($_SESSION['buyer_id'])) {
    echo "<script>alert('Please log in first!'); window.location.href='../buyers/login.php';</script>";
    exit;
}

$buyer_id = $_SESSION['buyer_id']; 
$book_id = $_POST['book_id']; 

// Check if book already in cart
$checkCart = $conn->prepare("SELECT * FROM cart WHERE buyer_id = ? AND book_id = ?");
$checkCart->bind_param("ii", $buyer_id, $book_id);
$checkCart->execute();
$result = $checkCart->get_result();

if ($result->num_rows > 0) {
    $updateCart = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE buyer_id = ? AND book_id = ?");
    $updateCart->bind_param("ii", $buyer_id, $book_id);
    $updateCart->execute();
} else {
    $insertCart = $conn->prepare("INSERT INTO cart (buyer_id, book_id, quantity) VALUES (?, ?, 1)");
    $insertCart->bind_param("ii", $buyer_id, $book_id);
    $insertCart->execute();
}

echo "<script>alert('Book added to cart!'); window.location.href='../buyers/cart.php';</script>";
?>

<?php
session_start();
include '../includes/config.php'; // Database connection

header('Content-Type: application/json');

if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$buyer_id = $_SESSION['buyer_id']; 
$book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;

if ($book_id == 0) {
    echo json_encode(["status" => "error"]);
    exit;
}

// Check if book is already in cart
$checkCart = $conn->prepare("SELECT quantity FROM cart WHERE buyer_id = ? AND book_id = ?");
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

// Get updated cart count
$cartQuery = $conn->prepare("SELECT COUNT(*) AS total FROM cart WHERE buyer_id = ?");
$cartQuery->bind_param("i", $buyer_id);
$cartQuery->execute();
$result = $cartQuery->get_result();
$cartCount = $result->fetch_assoc()['total'];

echo json_encode(["status" => "success", "cart_count" => $cartCount]);
?>

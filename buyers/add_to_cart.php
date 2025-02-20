<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

 
// ... other code ...
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Please login first!"]);
    exit();
}
// ... rest of your code ...
 

$buyer_id = $_SESSION['b_loginid']; // Change to b_loginid
$book_id = $_POST['book_id'];


$sql = "SELECT * FROM cart WHERE buyer_id = ? AND book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $buyer_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If the book is already in cart, update quantity
    $sql = "UPDATE cart SET quantity = quantity + 1 WHERE buyer_id = ? AND book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $buyer_id, $book_id);
} else {
    // Otherwise, insert new cart item
    $sql = "INSERT INTO cart (buyer_id, book_id, quantity) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $buyer_id, $book_id);
}

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Book added to cart!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add book to cart."]);
}
?>

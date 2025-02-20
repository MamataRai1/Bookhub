<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "bookhub";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['book_id']) && isset($_SESSION['buyer_id'])) {
    $book_id = $_POST['book_id'];
    $buyer_id = $_SESSION['buyer_id'];

    // Check if the book already exists in the cart
    $check_query = "SELECT * FROM cart WHERE buyer_id = ? AND book_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $buyer_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If book exists, increase quantity
        $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE buyer_id = ? AND book_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $buyer_id, $book_id);
    } else {
        // Insert new book into cart
        $insert_query = "INSERT INTO cart (buyer_id, book_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $buyer_id, $book_id);
    }

    if ($stmt->execute()) {
        echo "Book added to cart!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>



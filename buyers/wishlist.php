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

    // Check if book already exists in wishlist
    $check_query = "SELECT * FROM wishlist WHERE buyer_id = ? AND book_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $buyer_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Insert book into wishlist
        $insert_query = "INSERT INTO wishlist (buyer_id, book_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $buyer_id, $book_id);

        if ($stmt->execute()) {
            echo "Book added to wishlist!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Book is already in your wishlist!";
    }

    $stmt->close();
}

$conn->close();
?>

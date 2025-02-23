<?php
session_start();
include('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];

    // Get buyer_id from users_id
    $buyer_query = "SELECT buyer_id FROM buyers WHERE user_id = ?";
    $stmt = $conn->prepare($buyer_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $buyer = $result->fetch_assoc();
        $buyer_id = $buyer['buyer_id'];

        // Check if the book already exists in cart
        $check_query = "SELECT * FROM cart WHERE buyer_id = ? AND book_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $buyer_id, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity if book exists
            $update_query = "UPDATE cart SET quantity = quantity + 1, added_at = CURRENT_TIMESTAMP WHERE buyer_id = ? AND book_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ii", $buyer_id, $book_id);
        } else {
            // Insert new item if book doesn't exist
            $insert_query = "INSERT INTO cart (buyer_id, book_id, quantity, added_at) VALUES (?, ?, 1, CURRENT_TIMESTAMP)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ii", $buyer_id, $book_id);
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Book added to cart!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error adding to cart']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Buyer not found']);
    }
}
?>
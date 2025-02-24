<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

if (!$conn) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: a_login.php");
    exit();
}

if (isset($_GET['id'], $_GET['action'])) {
    $book_id = intval($_GET['id']);
    $action = $_GET['action'];

    $status = ($action === 'approve') ? 'approved' : 'rejected';

    // ✅ Fix: Use book_id instead of id
    $update_query = "UPDATE book SET status = ? WHERE book_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $book_id);

    if ($stmt->execute()) {
        header("Location: manage_books.php?message=Book $status successfully");
        exit();
    } else {
        header("Location: manage_books.php?error=Failed to update status");
        exit();
    }
} else {
    header("Location: manage_books.php?error=Invalid request");
    exit();
}
?>

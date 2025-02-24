<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: a_login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'bookhub');

if (isset($_GET['id']) && isset($_GET['status'])) {
    $book_id = $_GET['id'];
    $status = $_GET['status'];
    
    if (!in_array($status, ['approved', 'rejected'])) {
        $_SESSION['error'] = 'Invalid status';
        header("Location: manage_books.php");
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE book SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $book_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Book status updated successfully';
    } else {
        $_SESSION['error'] = 'Error updating book status';
    }
}

header("Location: manage_books.php");
exit();
?>
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: a_login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'bookhub');

if (isset($_GET['id']) && isset($_GET['action'])) {
    $seller_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    // Convert action to status
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    
    // Update the status
    $stmt = $conn->prepare("UPDATE form SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $seller_id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back without creating a loop
header("Location:  dashboard.php");
exit();
?>
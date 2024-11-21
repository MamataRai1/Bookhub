<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' is set and is a valid number
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM customer_orders WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid ID!');</script>";
}

// Close the connection
$conn->close();

// Redirect to the dashboard
header("Location: dashboard.php");
exit; // Ensure no further code executes after the redirect
?>

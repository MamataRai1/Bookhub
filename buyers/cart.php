<?php
session_start(); // Enable session management

// Debugging (Uncomment for troubleshooting)
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

if (!isset($_SESSION['b_loginid'])) {
    die("❌ Error: Please log in to view your cart.");
}

$buyer_id = $_SESSION['b_loginid'];

$conn = new mysqli("localhost", "root", "", "bookhub");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT cart.*, book.title, book.image, book.price 
          FROM cart 
          JOIN book ON cart.book_id = book.book_id 
          WHERE cart.buyer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <style>
        .book-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
        .book-item img {
            float: left;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<?php
if ($result->num_rows > 0) {
    echo "<h2>🛒 Shopping Cart</h2>";
    while ($book = $result->fetch_assoc()) {
        echo "<div class='book-item'>";
        echo "<img src='../assets/img/" . htmlspecialchars($book['image']) . "' width='100' height='150'>";
        echo "<h3>" . htmlspecialchars($book['title']) . "</h3>";
        echo "<p>Price: Rs. " . htmlspecialchars($book['price']) . "</p>";
        echo "<p>Quantity: " . htmlspecialchars($book['quantity']) . "</p>";
        echo "<div style='clear: both;'></div>"; // Clear floats
        echo "</div>";
    }
} else {
    echo "<p>Your cart is empty.</p>";
}
?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
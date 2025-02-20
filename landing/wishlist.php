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

if (!isset($_SESSION['buyer_id'])) {
    die("Please log in to view your wishlist.");
}

$buyer_id = $_SESSION['buyer_id'];

$query = "SELECT wishlist.*, book.title, book.image 
          FROM wishlist 
          JOIN book ON wishlist.book_id = book.book_id 
          WHERE wishlist.buyer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h2>📌 Wishlist</h2>";
    while ($book = $result->fetch_assoc()) {
        echo "<div class='book-item'>";
        echo "<img src='../assets/img/" . htmlspecialchars($book['image']) . "' width='100' height='150'>";
        echo "<h3>" . htmlspecialchars($book['title']) . "</h3>";
        echo "</div>";
    }
} else {
    echo "<p>Your wishlist is empty.</p>";
}

$stmt->close();
$conn->close();
?>

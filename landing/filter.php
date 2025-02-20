<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "bookhub";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    // SQL query to search by title or author
    $query = "SELECT book_id, image FROM book WHERE title LIKE CONCAT('%', ?, '%') OR author LIKE CONCAT('%', ?, '%')";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if books are found
    if ($result->num_rows > 0) {
        echo "<h2>Search Results</h2>";
        echo "<div class='book-list' style='display: flex; flex-wrap: wrap; gap: 20px;'>";

        // Loop through results (only displaying images)
        while ($book = $result->fetch_assoc()) {
            echo "<div class='book-item' style='width: 150px; text-align: center;'>";
            echo "<a href='book_details.php?id=" . htmlspecialchars($book['book_id']) . "'>";
            echo "<img src='../assets/img/" . htmlspecialchars($book['image']) . "' width='150' height='200' alt='Book Image' style='border-radius: 5px;'>";
            echo "</a>";
            echo "</div>";
        }

        echo "</div>"; // Close book list container
    } else {
        echo "<p>No books found.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Please enter a search term.</p>";
}

// Close the database connection
$conn->close();
?>

<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['b_loginid'])) {  
    echo json_encode(["status" => "error", "message" => "Please login first!"]);
    exit();
}

// Debugging: Print all received POST data
file_put_contents("debug_log.txt", print_r($_POST, true));  // Save POST data to a file

// Check if book_id is set and not empty
if (!isset($_POST['book_id']) || empty($_POST['book_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid book ID!"]);
    exit();
}

$buyer_id = $_SESSION['b_loginid']; 
$book_id = intval($_POST['book_id']); // Ensure it's an integer

// Check if the book is already in the cart
$sql = "SELECT * FROM cart WHERE buyer_id = ? AND book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $buyer_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If the book is already in the cart, update quantity
    $sql = "UPDATE cart SET quantity = quantity + 1 WHERE buyer_id = ? AND book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $buyer_id, $book_id);
} else {
    // Otherwise, insert a new cart item
    $sql = "INSERT INTO cart (buyer_id, book_id, quantity) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $buyer_id, $book_id);
}

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Book added to cart!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add book to cart."]);
}

$stmt->close();
$conn->close();
?>
<script>
    $.ajax({
    url: "buyers/add_to_cart.php",
    type: "POST",
    data: { book_id: 1 },  // Replace with dynamic book_id
    dataType: "json", // Ensure JSON response
    success: function(response) {
        console.log(response);
    },
    error: function() {
        console.log("Error adding to cart");
    }
});

</script>
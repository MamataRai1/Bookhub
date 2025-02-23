<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "bookhub";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Failed to place order. Error: " . $stmt->error]);

    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

// Validate GET parameters
if (!isset($_GET['book_id']) || !isset($_GET['total'])) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = intval($_GET['book_id']);
$total_amount = floatval($_GET['total']);
$status = 'Pending';
$shipping_address = "User Address Here"; // Update with actual user address

// Insert order into database
$query = "INSERT INTO orders (buyer_id, order_date, status, total_amount, shipping_address) 
          VALUES (?, NOW(), ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("issd", $user_id, $status, $total_amount, $shipping_address);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    echo json_encode(["success" => true, "order_id" => $order_id]);
} else {
    echo json_encode(["success" => false, "message" => "Order creation failed: " . $stmt->error]);
}


// Close connection
$stmt->close();
$conn->close();
?>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

if (!isset($_GET['order_id'])) {
    die("Order ID is missing");
}

$order_id = intval($_GET['order_id']);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "bookhub";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed");
}

// Fetch order details
$query = "SELECT orders.*, books.title, books.price, sellers.store_name 
          FROM orders 
          JOIN books ON books.book_id = orders.buyer_id
          JOIN sellers ON sellers.seller_id = books.seller_id
          WHERE orders.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Order not found");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo htmlspecialchars($order['title']); ?></title>
    <link rel="stylesheet" href="order.css">
</head>
<body>

    <div class="checkout-container">
        <h2>Checkout</h2>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <p><strong>Book:</strong> <?php echo htmlspecialchars($order['title']); ?></p>
            <p><strong>Shop:</strong> <?php echo htmlspecialchars($order['store_name']); ?></p>
            <p><strong>Price:</strong> Rs. <?php echo htmlspecialchars($order['price']); ?></p>
            <p><strong>Delivery Fee:</strong> Rs. 100</p>
            <p><strong>Total:</strong> Rs. <?php echo $order['price'] + 100; ?></p>
        </div>

        <div class="payment-options">
            <h3>Payment Option</h3>
            <p><input type="radio" name="payment" checked> Khalti Payment</p>
        </div>

        <button onclick="proceedToPayment(<?php echo $order_id; ?>)" class="proceed-btn">Proceed to Pay</button>
    </div>

    <script>
        function proceedToPayment(orderId) {
            window.location.href = "../payment/payment.php?order_id=" + orderId;
        }
    </script>

</body>
</html>

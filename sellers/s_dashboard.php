<?php
session_start();

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$database = "bookhub";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if seller is logged in
if (!isset($_SESSION['seller_id']) || empty($_SESSION['seller_id'])) {
    die("Error: Seller ID not found in session. Please log in again.");
}

$seller_id = $_SESSION['seller_id'];

// 🔹 FIXED QUERY: Using correct column names
$sellerQuery = "SELECT seller_id, seller_name FROM sellers WHERE seller_id = ?";
$stmt = mysqli_prepare($conn, $sellerQuery);
mysqli_stmt_bind_param($stmt, "i", $seller_id);
mysqli_stmt_execute($stmt);
$sellerResult = mysqli_stmt_get_result($stmt);
$seller = mysqli_fetch_assoc($sellerResult);

// If seller not found, display an error
if (!$seller) {
    die("Error: Seller not found. Please check your login credentials.");
}

// Fetch seller products
$productQuery = "SELECT product_id, name, price, stock FROM products WHERE seller_id = ?";
$stmt = mysqli_prepare($conn, $productQuery);
mysqli_stmt_bind_param($stmt, "i", $seller_id);
mysqli_stmt_execute($stmt);
$productResult = mysqli_stmt_get_result($stmt);

// Fetch seller orders
$orderQuery = "SELECT orders.order_id, buyers.buyer_name AS customer_name, orders.status 
               FROM orders 
               JOIN buyers ON orders.buyer_id = buyers.buyer_id 
               WHERE orders.seller_id = ?";
$stmt = mysqli_prepare($conn, $orderQuery);
mysqli_stmt_bind_param($stmt, "i", $seller_id);
mysqli_stmt_execute($stmt);
$orderResult = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($seller['seller_name'] ?? "Seller"); ?>!</h2>
    <a href="logout.php">Logout</a>

    <h3>Your Products</h3>
    <table border="1">
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($productResult)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['stock']); ?></td>
                <td><a href="edit_product.php?id=<?php echo $row['product_id']; ?>">Edit</a></td>
            </tr>
        <?php } ?>
    </table>

    <h3>Your Orders</h3>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Status</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($orderResult)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>

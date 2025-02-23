<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Start session
session_start();

// Debug session
error_log("Session contents: " . print_r($_SESSION, true));

// Check for either session variable
if (!isset($_SESSION['user_id']) && !isset($_SESSION['b_loginid'])) {
    header("Location: b_login.php");
    exit;
}

// Get the user ID from whichever session variable is set
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['b_loginid'];

// Fetch user details
$user_query = "SELECT b.fname, b.lname, b.email, b.phone, b.address, b.buyer_id 
               FROM buyers b 
               WHERE b.user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows === 0) {
    echo "User not found.";
    exit;
}

$user = $user_result->fetch_assoc();
$buyer_id = $user['buyer_id'];

// Query to fetch cart items
$cart_query = "SELECT c.cart_id, c.quantity, c.added_at,
               b.title, b.price, b.image
               FROM cart c
               JOIN book b ON c.book_id = b.book_id
               WHERE c.buyer_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$cart_result = $stmt->get_result();

// Update the order query to match your table structure
$order_query = "SELECT o.order_id, o.order_date, o.total_amount, o.status, o.shipping_address,
                oi.quantity, oi.price as item_price,
                b.title, b.image
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                JOIN book b ON oi.b_id = b.book_id
                WHERE o.buyer_id = ?
                ORDER BY o.order_date DESC";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$order_result = $stmt->get_result();

// Add debug output
echo "<!-- Debug: buyer_id = $buyer_id -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage My Account</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            text-decoration: none;
            list-style: none;
            font-family: "Arial";
        }
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #0000FF;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo h2 {
            color: #fff;
            margin-left: 20px;
        }
        .navbar .menubar ul {
            list-style: none;
            display: flex;
            justify-content: center;
        }
        .navbar .menubar ul li {
            margin: 0 20px;
        }
        .navbar .menubar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .navbar .menubar ul li a:hover {
            color: #007bff;
        }
        .navbar .icons {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar .icons span {
            color: white;
        }
        .navbar .icons a,
        .navbar .icons button {
            margin: 0 10px;
            background: #333;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .navbar .icons .btn-logout {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .navbar .icons .btn-logout:hover {
            background: #c82333;
        }
        /* Body Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            margin-top: 80px; /* To prevent overlap with navbar */
        }
        .sidebar {
            width: 25%;
            background: #fff;
            padding: 20px;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            margin: 5px 0;
            color: #333;
            text-decoration: none;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .sidebar a:hover {
            background: #ececec;
        }
        .content {
            width: 75%;
            padding: 20px;
        }
        .card {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #007bff;
        }
        .card h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 1.5rem;
            text-transform: uppercase;
            font-weight: bold;
        }
        .card p {
            margin: 10px 0;
            color: #555;
            font-size: 1.1rem;
        }
        .btn-edit {
            display: inline-block;
            padding: 5px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 14px;
        }
        .btn-edit:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f9f9f9;
        }
        table td a {
            color: #007bff;
            text-decoration: none;
        }
        table td a:hover {
            text-decoration: underline;
        }

        /* Inline layout for profile and address */
        .inline-table {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .inline-table .card {
            width: 48%;
        }

        .cart-items, .orders {
            max-height: 400px;
            overflow-y: auto;
        }

        .cart-item, .order-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }

        .item-details {
            flex: 1;
        }

        .cart-total {
            margin-top: 15px;
            padding: 15px;
            background: #f8f8f8;
            border-radius: 5px;
            text-align: right;
        }

        .order {
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: 5px;
            overflow: hidden;
        }

        .order-header {
            background: #f8f8f8;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .order-items {
            padding: 15px;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-total {
            background: #f8f8f8;
            padding: 15px;
            text-align: right;
            border-top: 1px solid #eee;
        }

        .item-details h5 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .item-details p {
            margin: 5px 0;
            color: #666;
        }

        .orders {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
        }

        .order-header {
            background: #f8f8f8;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .order-header h4 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .order-header p {
            margin: 5px 0;
            color: #666;
        }

        .order-items {
            padding: 15px;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item .item-details h5 {
            margin: 0 0 8px 0;
            color: #333;
        }

        .order-item .item-details p {
            margin: 4px 0;
            color: #666;
        }

        .status-pending {
            color: #ffa500;
            background: #fff3e0;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .status-shipped {
            color: #2196f3;
            background: #e3f2fd;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .status-delivered {
            color: #4caf50;
            background: #e8f5e9;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .status-cancelled {
            color: #f44336;
            background: #ffebee;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <h2>Bookhub</h2>
        </div>
        <div class="menubar">
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="category">Categories</a></li>
                <li><a href="/BOOKHUB/sellers/s_signup.php">Be a seller</a></li>
                <li><a href="#contact_section">Contact us</a></li>
            </ul>
        </div>
        <div class="icons">
            <span>Hello, <?php echo htmlspecialchars($user['fname']); ?>!</span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <!-- Profile Section -->
    <div class="container">
        <div class="sidebar">
            <h3>Manage My Account</h3>
            <a href="#">My Profile</a>
            <a href="#">Address Book</a>
            <a href="#">My Orders</a>
            <a href="#">My Reviews</a>
            <a href="#">My Wishlist</a>
        </div>
        <div class="content">
            <!-- Personal Profile and Address Book Inline -->
            <div class="inline-table">
                <!-- Personal Profile Section -->
                <div class="card">
                    <h3>Personal Profile</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                    <a href="#" class="btn-edit">Edit Profile</a>
                </div>

                <!-- Address Book Section -->
                <div class="card">
                    <h3>Address Book</h3>
                    <p><strong>Delivery Address:</strong></p>
                    <p><?php echo htmlspecialchars($user['address']); ?></p>
                    <a href="#" class="btn-edit">Edit Address</a>
                </div>
            </div>

            <!-- Current Cart Section -->
            <div class="card">
                <h3>Current Cart Items</h3>
                <?php if ($cart_result && $cart_result->num_rows > 0): ?>
                    <div class="cart-items">
                        <?php 
                        $cart_total = 0;
                        while ($cart_item = $cart_result->fetch_assoc()): 
                            $subtotal = $cart_item['price'] * $cart_item['quantity'];
                            $cart_total += $subtotal;
                        ?>
                            <div class="cart-item">
                                <img src="../assets/images/<?php echo htmlspecialchars($cart_item['image']); ?>" 
                                     alt="Book Cover"
                                     style="width: 60px; height: 90px; object-fit: cover;">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($cart_item['title']); ?></h4>
                                    <p>Quantity: <?php echo $cart_item['quantity']; ?></p>
                                    <p>Price: Rs. <?php echo number_format($cart_item['price'], 2); ?></p>
                                    <p>Subtotal: Rs. <?php echo number_format($subtotal, 2); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <div class="cart-total">
                            <h4>Total: Rs. <?php echo number_format($cart_total, 2); ?></h4>
                            <a href="../cart/cart.php" class="btn-edit">View Cart</a>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>

            <!-- Updated Purchase History Section -->
            <div class="card">
                <h3>Purchase History</h3>
                <?php 
                // Debug output
                if ($order_result) {
                    echo "<!-- Debug: Number of orders found: " . $order_result->num_rows . " -->";
                }
                
                if ($order_result && $order_result->num_rows > 0): ?>
                    <div class="orders">
                        <?php while ($order = $order_result->fetch_assoc()): ?>
                            <div class="order">
                                <div class="order-header">
                                    <h4>Order #<?php echo $order['order_id']; ?></h4>
                                    <p>Date: <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></p>
                                    <p>Status: <span class="status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span></p>
                                    <p>Total Amount: Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
                                    <p>Shipping Address: <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                                </div>
                                <div class="order-items">
                                    <div class="order-item">
                                        <img src="../book/<?php echo htmlspecialchars($order['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($order['title']); ?>"
                                             onerror="this.src='../assets/images/default-book.jpg'"
                                             style="width: 80px; height: 120px; object-fit: cover;">
                                        <div class="item-details">
                                            <h5><?php echo htmlspecialchars($order['title']); ?></h5>
                                            <p>Quantity: <?php echo $order['quantity']; ?></p>
                                            <p>Price: Rs. <?php echo number_format($order['item_price'], 2); ?></p>
                                            <p>Subtotal: Rs. <?php echo number_format($order['item_price'] * $order['quantity'], 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>No purchase history found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

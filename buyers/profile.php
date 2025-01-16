<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Start session
session_start();

if (!isset($_SESSION['b_loginid'])) {
    echo "You need to log in to view your profile.";
    exit;
}

$user_id = $_SESSION['b_loginid'];

// Fetch user details
$user_query = "SELECT fname, lname, email, phone, address FROM buyers WHERE user_id = '$user_id'";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

// Query to fetch recent orders
$order_query = "SELECT order_id, order_date, total_amount, status FROM orders WHERE buyer_id = '$user_id' ORDER BY order_date DESC LIMIT 5";
$order_result = $conn->query($order_query);

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
        }
        .navbar .icons span {
            color: white;
            margin-right: 10px;
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
            margin-top: 10px;
            padding: 8px 15px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
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
            <?php if (isset($_SESSION['b_loginid'])): ?>
                <span>Hello, <?php echo htmlspecialchars($user['fname']); ?>!</span>
                <a href="/BOOKHUB/logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <button><i class="fa-solid fa-user"></i> <a href="/BOOKHUB/buyers/b_login.php">LOG IN</a></button>
                <button><a href="/BOOKHUB/buyers/b_signup.php">SIGN UP</a></button>
            <?php endif; ?>
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
                </div>

                <!-- Address Book Section -->
                <div class="card">
                    <h3>Address Book</h3>
                    <p><strong>Address:</strong></p>
                    <p><?php echo htmlspecialchars($user['address']); ?></p>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="card">
                <h3>Recent Orders</h3>
                <?php if ($order_result->num_rows > 0): ?>
                    <table>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        <?php while ($order = $order_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td>Rs. <?php echo htmlspecialchars($order['total_amount']); ?></td>
                                <td><a href="#" class="btn-edit">Manage</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>No recent orders found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

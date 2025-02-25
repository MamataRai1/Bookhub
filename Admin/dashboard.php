<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: a_login.php");
    exit();
}

session_regenerate_id(true); // Security measure

// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total revenue
$revenueQuery = "SELECT SUM(total_amount) AS totalRevenue FROM orders";
$revenueResult = $conn->query($revenueQuery);
$totalRevenue = ($revenueResult && $revenueResult->num_rows > 0) ? $revenueResult->fetch_assoc()['totalRevenue'] : 0;

// Fetch new users count (last 30 days)
$newUsersQuery = "SELECT COUNT(*) AS newUsers FROM users WHERE created_at >= NOW() - INTERVAL 30 DAY";
$newUsersResult = $conn->query($newUsersQuery);
$newUsers = ($newUsersResult && $newUsersResult->num_rows > 0) ? $newUsersResult->fetch_assoc()['newUsers'] : 0;

// Fetch active users count (logged in last 7 days)
$activeUsersQuery = "SELECT COUNT(*) AS activeUsers FROM users WHERE last_login >= NOW() - INTERVAL 7 DAY";
$activeUsersResult = $conn->query($activeUsersQuery);
$activeUsers = ($activeUsersResult && $activeUsersResult->num_rows > 0) ? $activeUsersResult->fetch_assoc()['activeUsers'] : 0;

// Fetch monthly sales (last 30 days)
// Fetch monthly sales
$monthlySalesQuery = "SELECT SUM(total_amount) AS monthlySales FROM orders WHERE order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$monthlySalesResult = $conn->query($monthlySalesQuery);
$monthlySales = $monthlySalesResult->fetch_assoc()['monthlySales'] ?? 0;

// Fetch order history
$orderQuery = "SELECT order_id, total_amount AS order_amount, order_date FROM orders ORDER BY order_date DESC LIMIT 10";
$orderHistoryResult = $conn->query($orderQuery);


$reviewsQuery = "SELECT users.name AS user_name, reviews.rating, reviews.review_text 
                 FROM reviews 
                 INNER JOIN users ON reviews.buyer_id = users.id 
                 ORDER BY reviews.created_at DESC LIMIT 10";

// Fetch statistics
$users_query = "SELECT COUNT(*) as count FROM form";
$users_result = $conn->query($users_query);
$users_count = $users_result->fetch_assoc()['count'];

$books_query = "SELECT COUNT(*) as count FROM book";
$books_result = $conn->query($books_query);
$books_count = $books_result->fetch_assoc()['count'];

$pending_sellers_query = "SELECT COUNT(*) as count FROM form WHERE status = 'pending'";
$pending_result = $conn->query($pending_sellers_query);
$pending_count = $pending_result->fetch_assoc()['count'];

// Fetch recent seller registrations
$recent_sellers = $conn->query("SELECT * FROM form ORDER BY id DESC LIMIT 5");

// Fetch recent books
$recent_books = $conn->query("SELECT b.*, f.email as seller_email 
                             FROM book b 
                             JOIN form f ON b.seller_id = f.id 
                             ORDER BY b.created_at DESC LIMIT 5");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #0000FF;
            color: white;
            padding: 20px;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background: #f4f4f4;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .nav-link {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            margin: 5px 0;
            border-radius: 5px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-size: 14px;
            margin: 0 2px;
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .section h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <nav>
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="manage_users.php" class="nav-link">
                    <i class="fas fa-users"></i> Manage Sellers
                </a>
                <a href="manage_books.php" class="nav-link">
                    <i class="fas fa-book"></i> Manage Books
                </a>
                <a href="reports.php" class="nav-link">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <div class="main-content">
            <h1>Admin Dashboard</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p><?php echo $users_count; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Books</h3>
                    <p><?php echo $books_count; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Pending Sellers</h3>
                    <p><?php echo $pending_count; ?></p>
                </div>
            </div>

            <div class="section">
                <h2>Recent Seller Registrations</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($seller = $recent_sellers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($seller['email']); ?></td>
                                <td><?php echo htmlspecialchars($seller['status']); ?></td>
                                <td>
                                    <?php if ($seller['status'] == 'pending'): ?>
                                        <button onclick="approveSeller(<?php echo $seller['id']; ?>)" 
                                                class="btn btn-approve">Approve</button>
                                        <button onclick="rejectSeller(<?php echo $seller['id']; ?>)" 
                                                class="btn btn-reject">Reject</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Recent Books</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Seller</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($book = $recent_books->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['seller_email']); ?></td>
                                <td>Rs. <?php echo number_format($book['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($book['status'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

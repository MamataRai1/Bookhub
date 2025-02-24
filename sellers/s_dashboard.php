<?php
session_start();
error_log("Dashboard Session: " . print_r($_SESSION, true));

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$database = "bookhub";

$conn = mysqli_connect($server, $username, $password, $database);

// Get seller details
$seller_id = $_SESSION['seller_id'];
error_log("Seller ID from session: " . $seller_id);

// Fetch seller's books
$books_query = "SELECT * FROM book WHERE seller_id = ?";
$stmt = $conn->prepare($books_query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$books = $stmt->get_result();

// Fetch seller details
$seller_query = "SELECT * FROM sellers WHERE seller_id = ?";
$stmt = $conn->prepare($seller_query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("No seller found with ID: " . $seller_id);
    session_destroy();
    header("Location: s_login.php?error=invalid_session");
    exit;
}

$seller = $result->fetch_assoc();
$seller_data = json_decode($seller['seller_data'], true);

// Fetch total earnings
$earnings_query = "SELECT SUM(oi.price * oi.quantity) as total_earnings 
                  FROM order_items oi 
                  JOIN book b ON oi.b_id = b.book_id 
                  WHERE b.seller_id = ?";
$stmt = $conn->prepare($earnings_query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$earnings = $stmt->get_result()->fetch_assoc();

// Fetch recent orders
$orders_query = "SELECT o.order_id, o.order_date, o.status,
                 oi.quantity, oi.price,
                 b.title
                 FROM orders o
                 JOIN order_items oi ON o.order_id = oi.order_id
                 JOIN book b ON oi.b_id = b.book_id
                 WHERE b.seller_id = ?
                 ORDER BY o.order_date DESC
                 LIMIT 5";
$stmt = $conn->prepare($orders_query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$recent_orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
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

        .sidebar h2 {
            margin-bottom: 30px;
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
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .book-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .book-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background: #0000FF;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-edit {
            background: #28a745;
            color: white;
        }

        .recent-orders {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Seller Dashboard</h2>
            <nav>
                <a href="#" class="nav-link active">Dashboard</a>
                <a href="add_book.php" class="nav-link">Add New Book</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h1>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total Books</h3>
                    <p><?php echo $books->num_rows; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Earnings</h3>
                    <p>Rs. <?php echo number_format($earnings['total_earnings'] ?? 0, 2); ?></p>
                </div>
            </div>

            <div class="books-section">
                <div class="header">
                    <h2>Your Books</h2>
                    <a href="add_book.php" class="btn btn-primary">Add New Book</a>
                </div>

                <div class="books-grid">
                    <?php while ($book = $books->fetch_assoc()): ?>
                        <div class="book-card">
                            <img src="../book/<?php echo htmlspecialchars($book['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($book['title']); ?>"
                                 onerror="this.src='../assets/images/default-book.jpg'">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p>Rs. <?php echo number_format($book['price'], 2); ?></p>
                            <div class="actions">
                                <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" 
                                   class="btn btn-edit">Edit</a>
                                <button onclick="deleteBook(<?php echo $book['book_id']; ?>)" 
                                        class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Book</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['title']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>Rs. <?php echo number_format($order['price'] * $order['quantity'], 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function deleteBook(bookId) {
        if (confirm('Are you sure you want to delete this book?')) {
            fetch('delete_book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'book_id=' + bookId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting book');
                }
            });
        }
    }
    </script>
</body>
</html>

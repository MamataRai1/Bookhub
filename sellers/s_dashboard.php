<?php
session_start();

// Basic session check
if (!isset($_SESSION['seller_id'])) {
    header("Location: s_login.php?error=invalid_session");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Verify seller exists and is approved
$stmt = $conn->prepare("SELECT f.*, s.store_name 
                       FROM form f 
                       LEFT JOIN store s ON f.id = s.store_id 
                       WHERE f.id = ? AND f.status = 'approved'");
$stmt->bind_param("i", $_SESSION['seller_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    session_unset();
    session_destroy();
    header("Location: s_login.php?error=invalid_session");
    exit();
}

$seller = $result->fetch_assoc();

// Get total books count
$books_query = $conn->prepare("SELECT COUNT(*) as total FROM book WHERE seller_id = ?");
$books_query->bind_param("i", $_SESSION['seller_id']);
$books_query->execute();
$total_books = $books_query->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - BookHub</title>
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

        .welcome-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
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
            border-radius: 4px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
        }

        .nav-link i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Seller Dashboard</h2>
            <nav>
                <a href="s_dashboard.php" class="nav-link active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="manage_books.php" class="nav-link">
                    <i class="fas fa-book"></i> Manage Books
                </a>
                <a href="add_book.php" class="nav-link">
                    <i class="fas fa-plus"></i> Add New Book
                </a>
                <a href="store_settings.php" class="nav-link">
                    <i class="fas fa-store"></i> Store Settings
                </a>
                <a href="profile.php" class="nav-link">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <div class="main-content">
            <div class="welcome-section">
                <h1>Welcome, <?php echo htmlspecialchars($seller['fname'] . ' ' . $seller['lname']); ?>!</h1>
                <p>Store: <?php echo htmlspecialchars($seller['store_name'] ?? 'No Store Name Set'); ?></p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Books</h3>
                    <p class="number"><?php echo $total_books; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Store Status</h3>
                    <p class="status"><?php echo ucfirst($seller['status']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Quick Actions</h3>
                    <a href="add_book.php" class="btn">Add New Book</a>
                </div>
            </div>

            <!-- Add more dashboard content here -->
        </div>
    </div>
</body>
</html>

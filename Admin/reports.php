<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

if (!isset($_SESSION['admin_id'])) {
    header("Location: a_login.php");
    exit();
}

// Get total counts
$total_books = $conn->query("SELECT COUNT(*) as count FROM book")->fetch_assoc()['count'];
$total_sellers = $conn->query("SELECT COUNT(*) as count FROM form")->fetch_assoc()['count'];
$total_stores = $conn->query("SELECT COUNT(*) as count FROM store")->fetch_assoc()['count'];

// Get books by status
$books_by_status = $conn->query("SELECT status, COUNT(*) as count FROM book GROUP BY status");

// Get top 5 sellers
$top_sellers = $conn->query("
    SELECT f.fname, f.lname, s.store_name, COUNT(b.book_id) as book_count 
    FROM form f 
    LEFT JOIN store s ON f.id = s.store_id
    LEFT JOIN book b ON f.id = b.seller_id 
    GROUP BY f.id, f.fname, f.lname, s.store_name
    ORDER BY book_count DESC 
    LIMIT 5
");


// Get recent books
$recent_books = $conn->query("
    SELECT b.*, f.fname, f.lname, s.store_name 
    FROM book b 
    LEFT JOIN form f ON b.seller_id = f.id 
    LEFT JOIN store s ON f.id = s.store_id 
    ORDER BY b.created_at DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .nav-link {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            margin: 5px 0;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
        }

        .stats-grid {
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

        .stat-card h3 {
            color: #666;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 24px;
            font-weight: bold;
            color: #0000FF;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .recent-items {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
        }

        .status-pending { color: #ffc107; }
        .status-approved { color: #28a745; }
        .status-rejected { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <nav>
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="manage_users.php" class="nav-link">
                    <i class="fas fa-store"></i> Book Stores
                </a>
                <a href="manage_books.php" class="nav-link">
                    <i class="fas fa-book"></i> Manage Books
                </a>
                <a href="reports.php" class="nav-link active">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <div class="main-content">
            <h1>Reports & Analytics</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Books</h3>
                    <div class="number"><?php echo $total_books; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Sellers</h3>
                    <div class="number"><?php echo $total_sellers; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Stores</h3>
                    <div class="number"><?php echo $total_stores; ?></div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-container">
                    <h2>Books by Status</h2>
                    <canvas id="statusChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h2>Top Sellers</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Store Name</th>
                                <th>Seller</th>
                                <th>Books Listed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($seller = $top_sellers->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($seller['store_name'] ?? 'No Store Name'); ?></td>
                                    <td><?php echo htmlspecialchars($seller['fname'] . ' ' . $seller['lname']); ?></td>
                                    <td><?php echo $seller['book_count']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="recent-items">
                <h2>Recently Listed Books</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Store</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Listed Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($book = $recent_books->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['store_name'] ?? 'No Store Name'); ?></td>
                                <td>₹<?php echo number_format($book['price'], 2); ?></td>
                                <td class="status-<?php echo strtolower($book['status']); ?>">
                                    <?php echo ucfirst($book['status']); ?>
                                </td>
                                <td><?php echo date('d M Y', strtotime($book['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Create the status chart
    const statusData = <?php 
        $data = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        while ($row = $books_by_status->fetch_assoc()) {
            $data[$row['status']] = $row['count'];
        }
        echo json_encode($data);
    ?>;

    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: ['Pending', 'Approved', 'Rejected'],
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: ['#ffc107', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    </script>
</body>
</html>

<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: a_login.php");
    exit();
}

// Fetch all sellers with their store details
$sellers_query = "SELECT f.*, s.store_name 
                 FROM form f 
                 LEFT JOIN store s ON f.id = s.store_id 
                 ORDER BY f.created_at DESC";
$sellers = $conn->query($sellers_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage users - Admin Dashboard</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            margin: 0 2px;
        }

        .btn-approve {
            background: #28a745;
        }

        .btn-reject {
            background: #dc3545;
        }

        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }

        .status-approved {
            color: #28a745;
            font-weight: bold;
        }

        .status-rejected {
            color: #dc3545;
            font-weight: bold;
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
            border-radius: 4px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .search-box {
            padding: 10px;
            width: 300px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .store-name {
            font-size: 1.1em;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        
        .owner-name {
            color: #666;
            font-size: 0.9em;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 8px;
        }
        
        .badge-new {
            background: #e3f2fd;
            color: #0066cc;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <nav>
                <a href="a_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="manage_users.php" class="nav-link active">
                    <i class="fas fa-users"></i> Manage Users
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
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <h1>Book Stores</h1>
            
            <input type="text" id="searchInput" class="search-box" placeholder="Search by store name or owner...">
            
            <table>
                <thead>
                    <tr>
                        <th>Store Details</th>
                        <th>Owner Contact</th>
                        <th>PAN Number</th>
                        <th>Status</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($seller = $sellers->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="shop-details">
                                    <div>
                                        <div class="store-name">
                                            <?php echo htmlspecialchars($seller['store_name'] ?? 'No Store Name'); ?>
                                            <?php if (strtotime($seller['created_at']) > strtotime('-7 days')): ?>
                                                <span class="badge badge-new">New</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="owner-name">
                                            Owner: <?php echo htmlspecialchars($seller['fname'] . ' ' . $seller['lname']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="seller-info">
                                    <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($seller['email']); ?></div>
                                    <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($seller['c_no']); ?></div>
                                    <div><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($seller['address']); ?></div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($seller['pan_no']); ?></td>
                            <td class="status-<?php echo strtolower($seller['status']); ?>">
                                <?php echo ucfirst($seller['status']); ?>
                            </td>
                            <td><?php echo date('d M Y', strtotime($seller['created_at'])); ?></td>
                            <td>
                                <?php if ($seller['status'] == 'pending'): ?>
                                    <a href="process_status.php?id=<?php echo $seller['id']; ?>&action=approve" 
                                       class="btn btn-approve" 
                                       onclick="return confirm('Are you sure you want to approve this seller?')">
                                        Approve
                                    </a>
                                    <a href="process_status.php?id=<?php echo $seller['id']; ?>&action=reject" 
                                       class="btn btn-reject"
                                       onclick="return confirm('Are you sure you want to reject this seller?')">
                                        Reject
                                    </a>
                                <?php endif; ?>
                                <a href="view_store_books.php?id=<?php echo $seller['id']; ?>" 
                                   class="btn btn-view">
                                    View Books
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    // Add confirmation to buttons
    document.querySelectorAll('.btn-approve, .btn-reject').forEach(button => {
        button.addEventListener('click', function(e) {
            const action = this.classList.contains('btn-approve') ? 'approve' : 'reject';
            if (!confirm(`Are you sure you want to ${action} this store?`)) {
                e.preventDefault();
            }
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });
    </script>
</body>
</html>
<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: a_login.php");
    exit();
}

// Fetch all books with seller and store information
$books_query = "SELECT b.*, f.fname, f.lname, s.store_name 
                FROM book b 
                LEFT JOIN form f ON b.seller_id = f.id 
                LEFT JOIN store s ON b.store_id = s.store_id 
                ORDER BY b.created_at DESC";
$books = $conn->query($books_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Admin Dashboard</title>
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
            text-decoration: none;
            margin: 2px;
            display: inline-block;
        }

        .btn-approve { background: #28a745; }
        .btn-reject { background: #dc3545; }

        .status-pending { color: #ffc107; font-weight: bold; }
        .status-approved { color: #28a745; font-weight: bold; }
        .status-rejected { color: #dc3545; font-weight: bold; }

        .nav-link {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            margin: 5px 0;
        }

        .nav-link:hover { background: rgba(255,255,255,0.1); border-radius: 4px; }
        .nav-link.active { background: rgba(255,255,255,0.2); }

        h1 { margin-bottom: 20px; }

        .search-box {
            padding: 10px;
            width: 300px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .book-image {
            width: 50px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
        }

        .price {
            font-weight: bold;
            color: #28a745;
        }

        .store-name { color: #0066cc; font-size: 0.9em; }
        .seller-name { color: #666; font-size: 0.85em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <nav>
                <a href="a_dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
                <a href="manage_users.php" class="nav-link"><i class="fas fa-store"></i> Book Stores</a>
                <a href="manage_books.php" class="nav-link active"><i class="fas fa-book"></i> Manage Books</a>
                <a href="reports.php" class="nav-link"><i class="fas fa-chart-bar"></i> Reports</a>
                <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <h1>Manage Books</h1>
            
            <input type="text" id="searchInput" class="search-box" placeholder="Search by title, author, or seller...">
            
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Book Details</th>
                        <th>Price</th>
                        <th>Seller Details</th>
                        <th>Status</th>
                        <th>Listed Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($book = $books->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php 
                                $image_path = "../assets/img/" . htmlspecialchars($book['image'] ?? 'default-book.jpg');
                                echo "<img src='$image_path' alt='Book Cover' class='book-image'>";
                                ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($book['title']); ?></strong><br>
                                <span class="author">by <?php echo htmlspecialchars($book['author']); ?></span>
                            </td>
                            <td class="price">₹<?php echo number_format($book['price'], 2); ?></td>
                            <td>
                                <div class="store-name">
                                    Store: <?php echo htmlspecialchars($book['store_name'] ?? 'No Store'); ?>
                                </div>
                                <div class="seller-name">
                                    Seller: <?php echo htmlspecialchars($book['fname'] . ' ' . $book['lname']); ?>
                                </div>
                            </td>
                            <td class="status-<?php echo strtolower($book['status'] ?? 'pending'); ?>">
                                <?php echo ucfirst($book['status'] ?? 'pending'); ?>
                            </td>
                            <td><?php echo date('d M Y', strtotime($book['created_at'])); ?></td>
                            <td>
                                <?php if (($book['status'] ?? 'pending') == 'pending'): ?>
                                    <a href="process_book_status.php?id=<?php echo $book['book_id']; ?>&action=approve" class="btn btn-approve">Approve</a>
                                    <a href="process_book_status.php?id=<?php echo $book['book_id']; ?>&action=reject" class="btn btn-reject">Reject</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(searchText) ? '' : 'none';
        });
    });

    document.querySelectorAll('.btn-approve, .btn-reject').forEach(button => {
        button.addEventListener('click', function(e) {
            const action = this.classList.contains('btn-approve') ? 'approve' : 'reject';
            if (!confirm(`Are you sure you want to ${action} this book?`)) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>

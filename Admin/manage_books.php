<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: a_login.php");
    exit();
}

// Fetch all books with seller information
$books_query = "SELECT b.*, f.email as seller_email, f.fname, f.lname 
                FROM book b 
                LEFT JOIN form f ON b.seller_id = f.id 
                ORDER BY b.created_at DESC";
$books = $conn->query($books_query);

// Debug: Print the first book record
if($books->num_rows > 0) {
    $first_book = $books->fetch_assoc();
    error_log("First book data: " . print_r($first_book, true));
    $books->data_seek(0); // Reset pointer to start
}
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

        .nav-link.active {
            background: rgba(255,255,255,0.2);
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
                <a href="manage_users.php" class="nav-link">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="manage_books.php" class="nav-link active">
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
            <h1>Manage Books</h1>
            
            <input type="text" id="searchInput" class="search-box" placeholder="Search by title, author, or seller...">
            
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Seller</th>
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
                                $image_path = "../assets/img/" . ($book['image'] ?? 'default-book.jpg');
                                echo "<img src='$image_path' alt='Book Cover' class='book-image'>";
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td class="price">₹<?php echo number_format($book['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($book['fname'] . ' ' . $book['lname']); ?></td>
                            <td class="status-<?php echo strtolower($book['status'] ?? 'pending'); ?>">
                                <?php echo ucfirst(strtolower($book['status'] ?? 'pending')); ?>
                            </td>
                            <td><?php echo date('d M Y', strtotime($book['created_at'])); ?></td>
                            <td>
                                <?php if (($book['status'] ?? 'pending') == 'pending'): ?>
                                    <form method="GET" action="update_book_status.php" style="display:inline;">
                                        <!-- <input type="hidden" name="id" value="<?php echo $book['id']; ?>"> -->
                                        <input type="hidden" name="status" value="approved">
                                        <input type="submit" value="Approve" class="btn btn-approve" 
                                               onclick="return confirm('Are you sure you want to approve this book?')">
                                    </form>
                                    <form method="GET" action="update_book_status.php" style="display:inline;">
                                        <!-- <input type="hidden" name="id" value="<?php echo $book['id']; ?>"> -->
                                        <input type="hidden" name="status" value="rejected">
                                        <input type="submit" value="Reject" class="btn btn-reject" 
                                               onclick="return confirm('Are you sure you want to reject this book?')">
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
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
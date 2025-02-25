<?php
session_start();

if (!isset($_SESSION['seller_id'])) {
    header("Location: s_login.php?error=invalid_session");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Get all books for this seller (simplified query without orders count for now)
$stmt = $conn->prepare("
    SELECT * FROM book 
    WHERE seller_id = ? 
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $_SESSION['seller_id']);
$stmt->execute();
$books = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Seller Dashboard</title>
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

        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .book-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .book-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .book-details {
            padding: 15px;
        }

        .book-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .book-author {
            color: #666;
            margin-bottom: 10px;
        }

        .book-price {
            font-size: 16px;
            color: #0000FF;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .book-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }

        .book-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-edit { background: #0000FF; color: white; }
        .btn-delete { background: #dc3545; color: white; }

        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .filters select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .add-book-btn {
            padding: 10px 20px;
            background: #0000FF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Seller Dashboard</h2>
            <nav>
                <a href="s_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="manage_books.php" class="nav-link active">
                    <i class="fas fa-book"></i> Manage Books
                </a>
                <a href="manage_orders.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i> Manage Orders
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
            <h1>Manage Books</h1>
            
            <a href="add_book.php" class="add-book-btn">
                <i class="fas fa-plus"></i> Add New Book
            </a>

            <div class="filters">
                <select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>

                <select id="sortBy">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="price-low">Price: Low to High</option>
                </select>
            </div>

            <div class="book-grid">
                <?php if ($books->num_rows > 0): ?>
                    <?php while ($book = $books->fetch_assoc()): ?>
                        <div class="book-card">
                            <img src="../uploads/<?php echo $book['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                 class="book-image">
                            
                            <div class="book-details">
                                <div class="book-title">
                                    <?php echo htmlspecialchars($book['title']); ?>
                                </div>
                                
                                <div class="book-author">
                                    by <?php echo htmlspecialchars($book['author']); ?>
                                </div>
                                
                                <div class="book-price">
                                    ₹<?php echo number_format($book['price'], 2); ?>
                                </div>
                                
                                <div class="book-status status-<?php echo strtolower($book['status']); ?>">
                                    <?php echo ucfirst($book['status']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 20px;">
                        <p>No books found. <a href="add_book.php">Add your first book</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function deleteBook(bookId) {
        if (confirm('Are you sure you want to delete this book?')) {
            window.location.href = 'delete_book.php?id=' + bookId;
        }
    }

    // Filter and sort functionality
    document.getElementById('statusFilter').addEventListener('change', filterBooks);
    document.getElementById('sortBy').addEventListener('change', filterBooks);

    function filterBooks() {
        const status = document.getElementById('statusFilter').value;
        const sort = document.getElementById('sortBy').value;
        const books = document.querySelectorAll('.book-card');

        books.forEach(book => {
            const bookStatus = book.querySelector('.book-status').textContent.toLowerCase();
            
            // Handle status filter
            if (status && !bookStatus.includes(status.toLowerCase())) {
                book.style.display = 'none';
                return;
            }
            
            book.style.display = '';
        });

        // Handle sorting
        const booksArray = Array.from(books);
        booksArray.sort((a, b) => {
            switch(sort) {
                case 'price-high':
                    return parseFloat(b.querySelector('.book-price').textContent.replace('₹', '')) - 
                           parseFloat(a.querySelector('.book-price').textContent.replace('₹', ''));
                case 'price-low':
                    return parseFloat(a.querySelector('.book-price').textContent.replace('₹', '')) - 
                           parseFloat(b.querySelector('.book-price').textContent.replace('₹', ''));
                // Add more sorting options as needed
            }
        });

        const container = document.querySelector('.book-grid');
        booksArray.forEach(book => container.appendChild(book));
    }
    </script>
</body>
</html>

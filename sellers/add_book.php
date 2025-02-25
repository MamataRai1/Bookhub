<?php
session_start();

if (!isset($_SESSION['seller_id'])) {
    header("Location: s_login.php?error=invalid_session");
    exit();
}

// Create uploads directory if it doesn't exist
if (!file_exists('../asets')) {
    mkdir('../assets', 0777, true);
}

$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Get book ID from URL
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verify book belongs to seller
$stmt = $conn->prepare("SELECT * FROM book WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $book_id, $_SESSION['seller_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: manage_books.php?error=invalid_book");
    exit();
}

$book = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    
    // Basic validation
    if (empty($title) || empty($author) || $price <= 0) {
        $error = "Please fill in all required fields.";
    } else {
        $image_name = $book['image']; // Keep existing image by default
        
        // Handle new image upload if provided
        if ($_FILES['image']['error'] === 0) {
            $image = $_FILES['image'];
            $allowed = ['jpg', 'jpeg', 'png'];
            $file_ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            
            if (in_array($file_ext, $allowed)) {
                $image_name = uniqid() . '.' . $file_ext;
                $upload_path = '../assets/img' . $image_name;
                
                if (move_uploaded_file($image['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if ($book['image'] && file_exists('../assets/img' . $book['image'])) {
                        unlink('../assets/img' . $book['image']);
                    }
                } else {
                    $error = "Failed to upload new image.";
                }
            } else {
                $error = "Invalid image format. Please use JPG, JPEG or PNG.";
            }
        }
        
        if (!isset($error)) {
            // Update book in database
            $stmt = $conn->prepare("
                UPDATE book 
                SET title = ?, author = ?, price = ?, 
                    description = ?, image = ?
                WHERE id = ? AND seller_id = ?
            ");
            
            $stmt->bind_param("ssdssis", 
                $title, 
                $author, 
                $price, 
                $description, 
                $image_name,
                $book_id,
                $_SESSION['seller_id']
            );
            
            if ($stmt->execute()) {
                header("Location: manage_books.php?success=updated");
                exit();
            } else {
                $error = "Failed to update book. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Seller Dashboard</title>
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

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-primary {
            background: #0000FF;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .preview-image {
            max-width: 200px;
            margin-top: 10px;
            display: none;
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
            <h1>Edit Book</h1>
            
            <div class="form-container">
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Book Title *</label>
                        <input type="text" id="title" name="title" required 
                               value="<?php echo htmlspecialchars($book['title']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="author">Author *</label>
                        <input type="text" id="author" name="author" required 
                               value="<?php echo htmlspecialchars($book['author']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="price">Price (₹) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required 
                               value="<?php echo $book['price']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($book['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Book Cover Image</label>
                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                        <p class="help-text">Leave empty to keep current image</p>
                        <img id="preview" class="preview-image" src="../assets/img<?php echo $book['image']; ?>" style="display: block;">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Book</button>
                        <a href="manage_books.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function previewImage(input) {
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>

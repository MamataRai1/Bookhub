<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
 
if ($_SESSION['role'] !== 'seller') {
    header("Location: index.php");
    exit;
}
echo "Welcome, " . $_SESSION['name'] . " (Seller)";
 

// Check if the seller is logged in
if (!isset($_SESSION['seller_id'])) {
    header("Location: s_login.php");
    exit();
}

// Fetch seller details
$seller_id = $_SESSION['seller_id'];
$seller_query = $conn->prepare("SELECT fname, lname FROM form WHERE id = ?");
$seller_query->bind_param("i", $seller_id);
$seller_query->execute();
$seller_result = $seller_query->get_result();

if ($seller_result->num_rows > 0) {
    $seller = $seller_result->fetch_assoc();
    $seller_name = htmlspecialchars($seller['fname'] . ' ' . $seller['lname'], ENT_QUOTES, 'UTF-8');
} else {
    echo "Seller not found!";
    exit();
}

// Initialize message variables
$success_message = $error_message = "";

// Handle add product request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = htmlspecialchars($_POST['product_name'], ENT_QUOTES, 'UTF-8');
    $price = (float)$_POST['price'];
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $image_name = "";

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
        }
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $error_message = "Only JPEG, PNG, and GIF files are allowed.";
        } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $error_message = "Failed to upload the image.";
        }
    }

    if (empty($product_name) || empty($price) || empty($description) || empty($image_name)) {
        $error_message = "All fields are required!";
    } elseif (empty($error_message)) {
        $insert_query = $conn->prepare("INSERT INTO products (product_name, price, description, image, seller_id) VALUES (?, ?, ?, ?, ?)");
        $insert_query->bind_param("sdssi", $product_name, $price, $description, $image_name, $seller_id);

        if ($insert_query->execute()) {
            $success_message = "Product added successfully!";
        } else {
            $error_message = "Failed to add product: " . $insert_query->error;
        }
    }
}

// Fetch products for the seller
$product_query = $conn->prepare("SELECT * FROM products WHERE seller_id = ?");
$product_query->bind_param("i", $seller_id);
$product_query->execute();
$product_result = $product_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            background-color:   #FF4500;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .navbar a {
            text-decoration: none;
            color: #333;
            margin-right: 15px;
        }

        .container {
            margin-top: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        img {
            max-width: 100px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" onclick="showTab('add-product')">Add Product</a>
        <div class="profile">
            <span>Hello, <?= $seller_name ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <!-- Success/Error Messages -->
        <?php if (!empty($success_message)): ?>
            <p class="success"><?= $success_message ?></p>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?= $error_message ?></p>
        <?php endif; ?>

        <!-- Add Product -->
        <div id="add-product" class="tab-content active">
            <h2>Add Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <label>Product Name:</label><br>
                <input type="text" name="product_name" required><br>
                <label>Price:</label><br>
                <input type="number" step="0.01" name="price" required><br>
                <label>Description:</label><br>
                <textarea name="description" required></textarea><br>
                <label>Product Image:</label><br>
                <input type="file" name="image" required><br>
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>

        <!-- Products List -->
        <h2>Your Products</h2>
        <table>
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Description</th>
            </tr>
            <?php if ($product_result->num_rows > 0): ?>
                <?php while ($row = $product_result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="uploads/<?= htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') ?>"></td>
                        <td><?= htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No products found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>

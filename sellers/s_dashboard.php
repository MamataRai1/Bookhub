<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bookhub");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming seller_id is stored in session after login
$seller_id = $_SESSION['seller_id'];

// Fetch seller's products
$result = $conn->query("SELECT * FROM products WHERE seller_id = '$seller_id'");

// Handle add/edit/delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $product_name = $_POST['product_name'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        $conn->query("INSERT INTO products (product_name, price, description, seller_id) VALUES ('$product_name', '$price', '$description', '$seller_id')");
        echo "Product added successfully!";
    }

    if (isset($_POST['edit_product'])) {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        $conn->query("UPDATE products SET product_name='$product_name', price='$price', description='$description' WHERE id='$product_id'");
        echo "Product updated successfully!";
    }

    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $conn->query("DELETE FROM products WHERE id='$product_id'");
        echo "Product deleted successfully!";
    }
}
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
            background-color: #FF4500;
            color: white;
        }
        h1 {
            background-color: #FF6347;
            padding: 15px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #FF6347;
        }
        .actions form {
            display: inline;
        }
    </style>
</head>
<body>

    <h1>Seller Dashboard</h1>
    
    <form method="POST">
        <h3>Add Product</h3>
        <input type="text" name="product_name" placeholder="Product Name" required>
        <input type="text" name="price" placeholder="Price" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <button type="submit" name="add_product">Add Product</button>
    </form>

    <h3>My Products</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['product_name'] ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= $row['description'] ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="edit_product">Edit</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="delete_product">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>

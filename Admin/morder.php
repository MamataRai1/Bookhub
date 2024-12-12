<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bookhub");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders from the database
$result = $conn->query("SELECT * FROM orders");

// Handle order actions (Approve/Reject/Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve order
    if (isset($_POST['approve_order'])) {
        $order_id = $_POST['order_id'];
        $status = 'approved'; // Set status as approved
        
        $order_id = $conn->real_escape_string($order_id);
        $conn->query("UPDATE orders SET status = '$status' WHERE id = $order_id");
    }

    // Reject order
    if (isset($_POST['reject_order'])) {
        $order_id = $_POST['order_id'];
        $status = 'rejected'; // Set status as rejected
        
        $order_id = $conn->real_escape_string($order_id);
        $conn->query("UPDATE orders SET status = '$status' WHERE id = $order_id");
    }

    // Delete order
    if (isset($_POST['delete_order'])) {
        $order_id = $_POST['order_id'];
        $conn->query("DELETE FROM orders WHERE id = $order_id");

        echo "Order deleted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
     /* OrangeRed background for better visibility */
            color: white;
        }
        h1 {
            background-color: #FF6347; /* Darker orange background */
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
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
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .actions form {
            display: inline;
        }
        button {
            background-color: #FF6347;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #E03D00;
        }
    </style>
</head>
<body>

    <h1>Manage Orders</h1>
    
    <table>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['user_id'] ?></td>
                <td><?= $row['product_name'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['status'] ?></td>
                <td>
                    <?php if ($row['status'] === 'pending') : ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="approve_order">Approve</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="reject_order">Reject</button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="delete_order">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>

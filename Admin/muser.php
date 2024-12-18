<?php
session_start(); // Ensure session is started at the top

// Database connection
$conn = new mysqli("localhost", "root", "", "bookhub");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Approve or Reject Seller Applications
if (isset($_POST['action']) && isset($_POST['seller_id'])) {
    $seller_id = intval($_POST['seller_id']); // Sanitize input
    $action = $_POST['action'];

    if ($action === 'approve') {
        $status = 'approved';
        $_SESSION['success_msg'] = "Seller approved successfully! Redirecting to seller dashboard...";

        // Fetch seller email (optional)
        $stmt = $conn->prepare("SELECT email FROM form WHERE id = ?");
        $stmt->bind_param("i", $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $seller = $result->fetch_assoc();

        // Update status
        $update_stmt = $conn->prepare("UPDATE form SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $status, $seller_id);
        $update_stmt->execute();

        // Redirect
        header("Location: /bookhub/sellers/seller_dashboard.php");
        exit();

    } elseif ($action === 'reject') {
        $status = 'rejected';
        $_SESSION['error_msg'] = "Seller rejected! Please try again later.";

        $update_stmt = $conn->prepare("UPDATE form SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $status, $seller_id);
        $update_stmt->execute();

        header("Location: /bookhub/sellers/s_dashboard.php");
        exit();
    }
}

 


// Delete Seller
if (isset($_POST['delete_seller']) && isset($_POST['seller_id'])) {
    $seller_id = intval($_POST['seller_id']);

    $stmt = $conn->prepare("DELETE FROM form WHERE id = ?");
    $stmt->bind_param("i", $seller_id);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Seller deleted successfully!";
        header("Location: http://localhost/bookhub/sellers/s_dashboard.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch sellers list (pending and approved)
$result = $conn->query("SELECT id, CONCAT(fname, ' ', lname) AS name, email, status FROM form");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Sellers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            background-color: #FF4500;
            color: white;
            padding: 20px;
            text-align: center;
            margin: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        button {
            background-color: #FF4500;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #E03D00;
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
            text-align: left;
            padding: 12px;
        }
        th {
            background-color: #FF4500;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Manage Sellers</h1>
    <div class="container">
        <h3>Sellers List</h3>
        <?php
        // Display messages
        if (isset($_SESSION['success_msg'])) {
            echo "<p class='success'>{$_SESSION['success_msg']}</p>";
            unset($_SESSION['success_msg']);
        } elseif (isset($_SESSION['error_msg'])) {
            echo "<p class='error'>{$_SESSION['error_msg']}</p>";
            unset($_SESSION['error_msg']);
        }
        ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending') : ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="seller_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="approve">Approve</button>
                                <button type="submit" name="action" value="reject">Reject</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="seller_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete_seller">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

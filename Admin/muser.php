<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bookhub");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add a new user
    if (isset($_POST['add_user'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $status = "pending"; // Default status is pending for admin approval

        $sql = "INSERT INTO users (name, email, password, role, status) VALUES ('$name', '$email', '$password', '$role', '$status')";
        if ($conn->query($sql)) {
            echo "User added successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Approve user
    if (isset($_POST['approve_user'])) {
        $user_id = $_POST['users_id'];
        $query = "UPDATE users SET status = 'approved' WHERE users_id = '$user_id'";
        if ($conn->query($query)) {
            echo "User approved successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Delete user
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['users_id'];
        $conn->query("DELETE FROM users WHERE users_id = $user_id");
        $conn->query("DELETE FROM buyers WHERE user_id = $user_id");
        $conn->query("DELETE FROM sellers WHERE user_id = $user_id");
        echo "User deleted successfully!";
    }
}

// Fetch users for listing
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
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
        form {
            margin-bottom: 20px;
        }
        input, select, button {
            margin: 10px 5px 10px 0;
            padding: 10px;
            font-size: 14px;
        }
        button {
            background-color: #FF4500;
            color: white;
            border: none;
            border-radius: 4px;
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
    </style>
</head>
<body>
    <h1>Manage Users</h1>
    <div class="container">
        <form method="POST">
            <h3>Add User</h3>
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="buyer">Buyer</option>
                <option value="seller">Seller</option>
            </select>
            <button type="submit" name="add_user">Add User</button>
        </form>

        <h3>Users List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['users_id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['role'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending') : ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="users_id" value="<?= $row['users_id'] ?>">
                                <button type="submit" name="approve_user">Approve</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="users_id" value="<?= $row['users_id'] ?>">
                            <button type="submit" name="delete_user">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $gmail = $_POST['mail'];
    $password = $_POST['pass'];

    // Prepare the query to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM form WHERE email = ?");
    $stmt->bind_param("s", $gmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Check status
            if ($user['status'] == 'approved') {
                $_SESSION['seller_id'] = $user['id'];
                header("Location: s_dashboard.php"); // Redirect to seller dashboard
                exit();
            } elseif ($user['status'] == 'pending') {
                echo "<script>alert('Your registration is still pending approval.');</script>";
            } elseif ($user['status'] == 'rejected') {
                echo "<script>alert('Your registration was rejected. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('No user found with this email');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login">
        <h1>Seller Login</h1>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="mail" required>
            <label>Password</label>
            <input type="password" name="pass" required>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="s_signup.php">Sign Up here</a></p>
    </div>
</body>
</html>

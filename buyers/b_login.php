<?php
session_start(); // ✅ Ensure session starts

$conn = new mysqli('localhost', 'root', '', 'bookhub');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginError = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $gmail = trim($_POST['mail']);
    $password = trim($_POST['pass']);

    if (!empty($gmail) && !empty($password)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $gmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {  
                // ✅ Correct session variables
                $_SESSION['b_loginid'] = $user['users_id']; 
                $_SESSION['b_username'] = $user['username']; 

                //  // ... other login code ...
                //    $_SESSION['user_id'] = $user['users_id']; // Change to user_id
                //    $_SESSION['b_username'] = $user['username']; 
// ... rest of your login code ...
                // ✅ Redirect without printing anything
                header("Location: b_dashboard.php");
                exit;
            } else {
                $loginError = "Wrong password!";
            }
        } else {
            $loginError = "Email not found!";
        }
    } else {
        $loginError = "Invalid email or password!";
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
        <h1>Login</h1>
        <?php if (!empty($loginError)) { echo "<p style='color: red;'>$loginError</p>"; } ?>
        
        <form method="POST">
            <label>Email</label>
            <input type="email" name="mail" required>
            <label>Password</label>
            <input type="password" name="pass" required>
            <input type="submit" value="Submit">
        </form>

        <p>Don't have an account? <a href="b_signup.php">Sign Up here</a></p>
    </div>
</body>
</html>

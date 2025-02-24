<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

// Debug POST data
error_log("POST data: " . print_r($_POST, true));
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Debug
    error_log("Login attempt with: $email");

    $stmt = $conn->prepare("SELECT * FROM form WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // For now, use direct comparison
        if ($password == $user['password']) {
            // Set both session variables
            $_SESSION['seller_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            
            error_log("Login successful. Session: " . print_r($_SESSION, true));
            
            // Redirect with exit
            header("Location: s_dashboard.php");
            exit();
        } else {
            $error = "Incorrect password";
            error_log("Password mismatch. Input: $password, Stored: {$user['password']}");
        }
    } else {
        $error = "No user found with this email";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffe6e6;
            border-radius: 4px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            color: #666;
        }

        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            padding: 12px;
            background: #0000FF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background: #0000CC;
        }

        p {
            text-align: center;
            margin-top: 20px;
        }

        a {
            color: #0000FF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login">
        <h1>Seller Login</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Email</label>
            <input type="email" name="email" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <label>Password</label>
            <input type="password" name="password" required>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="s_signup.php">Sign Up here</a></p>
    </div>

    <?php
    // Debug current session
    error_log("Current session at end of page: " . print_r($_SESSION, true));
    ?>
</body>
</html>

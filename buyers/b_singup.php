<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session at the top
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get form data
    $firstname = trim($_POST['fname']);
    $lastname = trim($_POST['lname']);
    $phone = trim($_POST['number']);
    $address = trim($_POST['add']);
    $email = trim($_POST['mail']);
    $password = password_hash($_POST['pass'], PASSWORD_DEFAULT); // Hash password

    // ✅ Step 1: Check if email already exists
    $check_email = $conn->prepare("SELECT users_id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already exists! Please use a different email.');</script>";
    } else {
        // ✅ Step 2: Insert into users table
        $users_query = $conn->prepare("INSERT INTO users (email, password, created_at, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        $users_query->bind_param("ss", $email, $password);

        if ($users_query->execute()) {
            // Fetch the inserted user's ID
            $user_id = $conn->insert_id;

            // ✅ Step 3: Insert into buyers table
            $query = $conn->prepare("INSERT INTO buyers (user_id, fname, lname, phone, address, email, password, wishlist, review_history, ratings, created_at, updated_at) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, '', '', '[]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
            $query->bind_param("issssss", $user_id, $firstname, $lastname, $phone, $address, $email, $password);

            if ($query->execute()) {
                // ✅ Store user ID in session
                $_SESSION['b_loginid'] = $user_id;

                // ✅ Redirect to buyer dashboard
                header("Location: b_dashboard.php");
                exit;
            } else {
                echo "<script>alert('Error inserting into buyers: " . $query->error . "');</script>";
            }
        } else {
            echo "<script>alert('Error inserting into users: " . $users_query->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="signup">
        <h1>Sign Up</h1>
        <form method="POST">
            <label>First Name</label>
            <input type="text" name="fname" required>
            
            <label>Last Name</label>
            <input type="text" name="lname" required>
            
            <label>Email</label>
            <input type="email" name="mail" required>
            
            <label>Password</label>
            <input type="password" name="pass" required>
            
            <label>Phone</label>
            <input type="tel" name="number" required>
            
            <label>Address</label>
            <input type="text" name="add" required>
            
            <input type="submit" value="Submit">
        </form>
        <p>By creating an account, you agree to our <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>.</p>
        <p>Already have an account? <a href="b_login.php">Login Here</a></p>
    </div>
</body>
</html>

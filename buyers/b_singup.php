<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get form data
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $phone = $_POST['number'];
    $address = $_POST['add'];
    $email = $_POST['mail'];
    $password =  $_POST['pass'] ; // Hash password for security
    $wishlist = ""; // Initialize wishlist as empty
    $ratings = json_encode([]); // Empty JSON for ratings
    $review_history = ""; // Initialize view_history as empty

    // Prepare the query
    $query = "INSERT INTO buyers (fname, lname, phone, address, email, password, wishlist, review_history, ratings, created_at, updated_at) 
              VALUES ('$firstname', '$lastname', '$phone', '$address', '$email', '$password', '$wishlist', '$review_history', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";

    // Execute the query
    if ($conn->query($query) === TRUE) {
        echo "<script>alert('Successfully registered');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
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
            <label>phone</label>
            <input type="tel" name="number" required>
            <label>Address</label>
            <input type="text" name="add" required>
            
            <input type="submit" value="Submit">
        </form>
        <p>By creating and using Your account, you agree to our <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>.</p>
        <p>Already have an account? <a href="s_login.php">Login Here</a></p>
    </div>
</body>

</html>

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
    $num = $_POST['number'];
    $address = $_POST['add'];
    $gmail = $_POST['mail'];
    $password = $_POST['pass'];
    $confirm_password = $_POST['confirm_pass'];
    $pan_no = $_POST['pan_no'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database
        $query = "INSERT INTO form (fname, lname, c_no, address, email, password, pan_no, status, created_at, updated_at) 
                  VALUES ('$firstname', '$lastname', '$num', '$address', '$gmail', '$hashed_password', '$pan_no', 'pending', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";

        if ($conn->query($query) === TRUE) {
            echo "<script>alert('Registration successful! Wait for admin approval.');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
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
        <h1> Seller Registration</h1>
        <form method="POST">
            <label>First Name</label>
            <input type="text" name="fname" placeholder="Enter Your First Name" required>
            <label>Last Name</label>
            <input type="text" name="lname" placeholder="Enter Your Last Name" required>
            <label>Contact Number</label>
            <input type="tel" name="number" required>
            <label>Address</label>
            <input type="text" name="add" required>
            <label>Email</label>
            <input type="email" name="mail" required>
            <label>Password</label>
            <input type="password" name="pass" required>
            <label>Confirm Password</label>
            <input type="password" name="confirm_pass" required>
            <label>PAN Number of Shop</label>
            <input type="text" name="pan_no" required>

            <input type="submit" value="Submit">
        </form>
        <p>By creating and using your account, you agree to our <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>.</p>
        <p>Already have an account? <a href="s_login.php">Login Here</a></p>
    </div>
</body>

</html>

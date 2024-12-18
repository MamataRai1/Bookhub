<?php

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['signin'])) {
    $adminname = $_POST['adminname'];
    $adminpassword = $_POST['adminpassword'];

    // Check admin credentials
    $query = "SELECT * FROM `admin` WHERE username = '$adminname'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($adminpassword == $row['password']) { // Match plain password
            session_start();
            $_SESSION['adminloginid'] = $adminname;
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('Admin username not found');</script>";
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
        <h2>ADMIN LOGIN PANEL</h2>

        <form method="POST">
             <label>Admin name</label>
            <input type="text" name="adminname" placeholder="Admin Name">
            <label>password</label>
            <input type="password" name="adminpassword" placeholder="password">
            <input type="submit" name="signin" valud="submit">
        </form><br>
        <a href="#">Forgot password ?</a> 
    </div>

</body>
</html>
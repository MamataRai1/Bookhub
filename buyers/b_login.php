<?php
 $conn = new mysqli('localhost', 'root', '', 'bookhub');

 // Check connection
 if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
 }
 
if ($_SERVER['REQUEST_METHOD'] == "POST") 
{
    $gmail = $_POST['mail'];  
    $password = $_POST['pass']; 

    session_start();
    $_SESSION['b_loginid'] = $username;
    header("Location: b_dashboard.php");
    exit;

    if (!empty($gmail) && !empty($password) && !is_numeric($gmail)) 
    {
      
        $query = "select*from form where email = '$gmail' limit 1";
 

        
        echo "<script type='text/javascript'>alert('wrong username or password');</script>";
         
    } 
    else{
        echo "<script type='text/javascript'>alert('wrong username or password');</script>";
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

        <form method="POST">
             <label>Email</label>
            <input type="email" name="mail" required>
            <label>password</label>
            <input type="password" name="pass" required>
            <input type="submit" name="" valud="submit">
        </form>
        <p>Dont have an account? <a href="b_singup.php">Sign Up here</a> </p>
    </div>

</body>
</html>
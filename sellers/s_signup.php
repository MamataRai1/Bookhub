
<?php
  session_start();

  if($_SERVER['REQUEST_METHOD'] == "POST")
  {
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $num = $_POST['number'];
    $address = $_POST['add'];
    $gmail = $_POST['mail'];
    $password = $_POST['pass'];

    if(!empty($gmail)&& !is_numeric($gmail))
    {
        $query = "insert into form (fname,lname	,c-no,address,email,password) values ('$firstname','$lastname','$num','$address','$gmail','$password')	";
    }

  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>signup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="signup">
        <h1>Sign Up</h1>

        <form method="post">
            <label>First Name</label>
            <input type="text" name="fname" required>
            <label>Last Name</label>
            <input type="text" name="lname" required>
            <label>contact Address</label>
            <input type="tel" name="number" required>
            <label>Address</label>
            <input type="text" name="add" required>
            <label>Email</label>
            <input type="email" name="mail" required>
            <label>password</label>
            <input type="password" name="pass" required>
            <input type="submit" name="" valud="submit">
         </form>
         <p>By creating and using Your account,you agree to our<br>
        <a href="">Terms and Condition</a> and <a href="#"> Policy Privacy</a> </p>
        <p>Already have an account? <a href="s_login.php">Login Here</a></p>
</body>
</html>
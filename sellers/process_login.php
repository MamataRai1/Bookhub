<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: s_login.php?error=empty_fields");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, password, status FROM form WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Check if account is approved
        if ($user['status'] !== 'approved') {
            header("Location: s_login.php?error=account_pending");
            exit();
        }

        if (password_verify($password, $user['password'])) {
            // Start a new session and regenerate ID
            session_regenerate_id(true);
            $_SESSION['seller_id'] = $user['id'];

            // 🔴 Debugging session before redirecting
            echo "<pre>";
            echo "DEBUG: SESSION TEST<br>";
            echo "Session ID: " . session_id() . "<br>";
            echo "Seller ID: " . ($_SESSION['seller_id'] ?? 'NOT SET') . "<br>";
            echo "</pre>";
            exit(); // Stop execution to check output
        }
    }

    header("Location: s_login.php?error=invalid_credentials");
    exit();
}

header("Location: s_login.php");
exit();
?>

<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check if connection is successful
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: s_login.php?error=empty_fields");
        exit();
    }

    // Fetch user data
    $stmt = $conn->prepare("SELECT id, password, status FROM form WHERE email = ?");
    if (!$stmt) {
        die("Query Preparation Failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if account is approved
        if ($user['status'] !== 'approved') {
            header("Location: s_login.php?error=account_pending");
            exit();
        }

        // Verify password (Ensure your stored passwords are hashed using password_hash())
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['seller_id'] = $user['id'];

            header("Location: s_dashboard.php");
            exit();
        }
    }

    // If login fails
    header("Location: s_login.php?error=invalid_credentials");
    exit();
}

header("Location: s_login.php");
exit();
?>

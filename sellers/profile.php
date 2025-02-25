<?php
session_start();

if (!isset($_SESSION['seller_id'])) {
    header("Location: s_login.php?error=invalid_session");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Get seller information
$stmt = $conn->prepare("
    SELECT * FROM users 
    WHERE users_id = ? AND role = 'seller'
");
$stmt->bind_param("i", $_SESSION['seller_id']);
$stmt->execute();
$seller = $stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $error = [];
    
    // Basic validation
    if (empty($name) || empty($email)) {
        $error[] = "Name and email are required.";
    }
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Invalid email format.";
    }
    
    // Password change validation
    if (!empty($current_password)) {
        if (!password_verify($current_password, $seller['password'])) {
            $error[] = "Current password is incorrect.";
        } elseif (empty($new_password) || empty($confirm_password)) {
            $error[] = "New password and confirmation are required.";
        } elseif ($new_password !== $confirm_password) {
            $error[] = "New passwords do not match.";
        }
    }
    
    if (empty($error)) {
        // Update basic info
        $update = $conn->prepare("
            UPDATE users 
            SET name = ?, email = ?, phone = ?
            WHERE users_id = ?
        ");
        $update->bind_param("sssi", $name, $email, $phone, $_SESSION['seller_id']);
        
        if ($update->execute()) {
            // Update password if provided
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $pwd_update = $conn->prepare("UPDATE users SET password = ? WHERE users_id = ?");
                $pwd_update->bind_param("si", $hashed_password, $_SESSION['seller_id']);
                $pwd_update->execute();
            }
            
            $success = "Profile updated successfully!";
            // Refresh seller data
            $stmt->execute();
            $seller = $stmt->get_result()->fetch_assoc();
        } else {
            $error[] = "Failed to update profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Seller Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #0000FF;
            color: white;
            padding: 20px;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background: #f4f4f4;
        }

        .nav-link {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            margin: 5px 0;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
        }

        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-primary {
            background: #0000FF;
            color: white;
        }

        .btn-primary:hover {
            background: #0000CC;
        }

        .error-message {
            color: #dc3545;
            padding: 10px;
            margin-bottom: 20px;
            background: #ffe6e6;
            border-radius: 4px;
        }

        .success-message {
            color: #28a745;
            padding: 10px;
            margin-bottom: 20px;
            background: #e6ffe6;
            border-radius: 4px;
        }

        .store-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Seller Dashboard</h2>
            <nav>
                <a href="s_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="manage_books.php" class="nav-link">
                    <i class="fas fa-book"></i> Manage Books
                </a>
                <a href="manage_orders.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i> Manage Orders
                </a>
                <a href="store_settings.php" class="nav-link">
                    <i class="fas fa-store"></i> Store Settings
                </a>
                <a href="profile.php" class="nav-link active">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <div class="main-content">
            <h1>Profile Settings</h1>
            
            <div class="profile-container">
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <?php foreach ($error as $err): ?>
                            <p><?php echo $err; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo htmlspecialchars($seller['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($seller['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($seller['phone'] ?? ''); ?>">
                    </div>

                    <h3 style="margin: 20px 0;">Change Password</h3>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password">
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>


<?php

session_start();

// if(!isset($_SESSION['role'] || $_SESSION['role']!='admin')){
//   // header('Location: ./login.php');
//   die;
// }
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

 
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="admin.css">
  <style>
   
  </style>
</head>
<body>
  <aside>
    <h1>Admin Panel</h1>
    <!-- <a href="#overview">Overview</a> -->
    <a href="muser.php">Manage Users</a>
    <a href="morder.php">Manage Orders</a>
    <!-- <a href="#manage-products">Manage Products</a> -->
    <!-- <a href="#reports">Reports</a> -->
    <!-- <a href="#settings">Settings</a> -->
  </aside>

  <div class="main-content">
    <div class="navbar">
      <!-- Notification Icon -->
      <div class="notification-icon">
        <i class="fa fa-bell"></i> Notifications
      </div>
      
      <!-- Profile Section -->
      <div class="profile-pic">
        <img src="profile.jpg" alt="Profile" width="40" height="40" style="border-radius: 50%;">
        <div class="profile-dropdown">
          <a href="#profile-settings">Admin</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>

   
    <!-- Header
    <div class="header">
      <h1>Admin Dashboard</h1>
    </div>

    <!-- Overview Section -->
    <section class="cards">
      <div class="card">
        <h3>Total Revenue</h3>
        <p>R.S<?php echo $totalRevenue; ?></p>
      </div>
      <div class="card">
        <h3>New Users</h3>
        <p><?php echo $newUsers; ?></p>
      </div>
      <div class="card">
        <h3>Active Users</h3>
        <p><?php echo $activeUsers; ?></p>
      </div>
      <div class="card">
        <h3>Monthly Sales</h3>
        <p>R.S<?php echo $monthlySales; ?></p>
      </div>
    </section> -->

    <!-- Order History Section -->
    <section class="table-section">
      <h2>Order History</h2>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Amount</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($order = $orderHistoryResult->fetch_assoc()): ?>
          <tr>
            <td>#<?php echo $order['order_id']; ?></td>
            <td>R.S<?php echo $order['order_amount']; ?></td>
            <td><?php echo $order['created_at']; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>

    <!-- Product Reviews Section -->
    <section class="table-section">
      <h2>Product Reviews</h2>
      <table>
        <thead>
          <tr>
            <th>User</th>
            <th>Rating</th>
            <th>Comment</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($review = $reviewsResult->fetch_assoc()): ?>
          <tr>
            <td><?php echo $review['user_name']; ?></td>
            <td><?php echo $review['rating']; ?></td>
            <td><?php echo $review['comment']; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </div>
  </div>

  <script>
    
       const notifications = [
      "New order received",
      "Product review posted",
      "User registered"
    ];
    
    // Display the notifications when the notification icon is clicked
    const notificationIcon = document.querySelector('.notification-icon');
    notificationIcon.addEventListener('click', () => {
      alert(notifications.join('\n')); // Simple notification alert
    });
  </script>
</body>
</html>

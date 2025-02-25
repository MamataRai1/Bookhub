<?php
session_start();

if (!isset($_SESSION['seller_id'])) {
    header("Location: s_login.php?error=invalid_session");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Get orders for this seller with correct column names
$stmt = $conn->prepare("
    SELECT o.order_id, o.order_date, o.status, o.total_amount, o.shipping_address,
           u.name as buyer_name, u.email as buyer_email, u.phone as buyer_phone
    FROM orders o
    JOIN users u ON o.buyer_id = u.users_id
    WHERE o.status != 'Cancelled'
    ORDER BY o.order_date DESC
");

$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Seller Dashboard</title>
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

        .orders-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .order-card {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .order-card:last-child {
            border-bottom: none;
        }

        .book-image {
            width: 100px;
            height: 140px;
            object-fit: cover;
            border-radius: 4px;
        }

        .order-details {
            flex: 1;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .order-id {
            font-weight: bold;
            color: #666;
        }

        .order-date {
            color: #888;
        }

        .book-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .buyer-info {
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d4edda; color: #155724; }

        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .filters select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .payment-info {
            margin-top: 10px;
            padding: 10px;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 14px;
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
                <a href="manage_orders.php" class="nav-link active">
                    <i class="fas fa-shopping-cart"></i> Manage Orders
                </a>
                <a href="store_settings.php" class="nav-link">
                    <i class="fas fa-store"></i> Store Settings
                </a>
                <a href="profile.php" class="nav-link">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <div class="main-content">
            <h1>Manage Orders</h1>

            <div class="filters">
                <select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                </select>

                <select id="dateFilter">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>

            <div class="orders-container">
                <?php if ($orders->num_rows > 0): ?>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <div class="order-card">
                            <div class="order-details">
                                <div class="order-header">
                                    <span class="order-id">Order #<?php echo $order['order_id']; ?></span>
                                    <span class="order-date">
                                        <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?>
                                    </span>
                                </div>

                                <div class="price">
                                    Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?>
                                </div>

                                <div class="buyer-info">
                                    <strong>Buyer Details:</strong><br>
                                    Name: <?php echo htmlspecialchars($order['buyer_name']); ?><br>
                                    Email: <?php echo htmlspecialchars($order['buyer_email']); ?><br>
                                    <?php if (!empty($order['buyer_phone'])): ?>
                                        Phone: <?php echo htmlspecialchars($order['buyer_phone']); ?><br>
                                    <?php endif; ?>
                                    <strong>Shipping Address:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                                </div>

                                <div class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo $order['status']; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-message">
                        <i class="fas fa-box-open" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
                        <p>No orders found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', filterOrders);
    document.getElementById('dateFilter').addEventListener('change', filterOrders);

    function filterOrders() {
        const status = document.getElementById('statusFilter').value;
        const date = document.getElementById('dateFilter').value;
        const orders = document.querySelectorAll('.order-card');

        orders.forEach(order => {
            const orderStatus = order.querySelector('.status-badge').textContent.toLowerCase();
            const orderDate = new Date(order.querySelector('.order-date').textContent);
            let showOrder = true;

            if (status && !orderStatus.includes(status.toLowerCase())) {
                showOrder = false;
            }

            if (showOrder && date) {
                const today = new Date();
                switch(date) {
                    case 'today':
                        showOrder = orderDate.toDateString() === today.toDateString();
                        break;
                    case 'week':
                        const weekAgo = new Date(today - 7 * 24 * 60 * 60 * 1000);
                        showOrder = orderDate >= weekAgo;
                        break;
                    case 'month':
                        showOrder = orderDate.getMonth() === today.getMonth() && 
                                  orderDate.getFullYear() === today.getFullYear();
                        break;
                }
            }

            order.style.display = showOrder ? '' : 'none';
        });
    }
    </script>
</body>
</html>



<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total records and other metrics
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM customer_orders")->fetch_assoc()['total'];
$totalRevenue = $conn->query("SELECT SUM(price) AS revenue FROM customer_orders")->fetch_assoc()['revenue'];
$pendingOrders = $conn->query("SELECT COUNT(*) AS pending FROM customer_orders WHERE status = 'Pending'")->fetch_assoc()['pending'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Admin Dashboard</h1>
        
        <!-- Metrics Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Orders</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalOrders; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Revenue</div>
                    <div class="card-body">
                        <h5 class="card-title">RS.<?php echo $totalRevenue ? number_format($totalRevenue, 2) : '0.00'; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Pending Orders</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $pendingOrders; ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mb-4">
            <a href="add.php" class="btn btn-primary">Add New Record</a>
            <a href="dashboard.php" class="btn btn-secondary">View All Records</a>
        </div>

        <!-- Records Table -->
        <h2 class="mb-4">Customer Orders</h2>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Book Name</th>
                    <th>Price</th>
                    <th>Delivery Place</th>
                    <th>Delivery Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM customer_orders");
                if ($result->num_rows > 0) {
                    // Display records
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>{$row['book_name']}</td>
                            <td>₹{$row['price']}</td>
                            <td>{$row['delivery_place']}</td>
                            <td>{$row['delivery_time']}</td>
                            <td>{$row['status']}</td>
                            <td>
                                <a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    // No records found
                    echo "<tr><td colspan='8' class='text-center'>No records found. Add a new record to get started!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>

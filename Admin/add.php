<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookhub');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['customer_name'];
    $bname = $_POST['book_name'];
    $price = $_POST['price'];
    $place = $_POST['delivery_place'];
    $date = $_POST['delivery_date'];

    // Corrected Insert query
    $stmt = $conn->prepare("INSERT INTO customer_orders (customer_name, book_name, price, delivery_place, delivery_time) VALUES (?, ?, ?, ?, ?)");
    
    // Match parameters with placeholders
    $stmt->bind_param('ssiss', $name, $bname, $price, $place, $date);

    if ($stmt->execute()) {
        echo "<script>alert('Record added successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Record</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Add New Record</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
            <div class="mb-3">
                <label for="book_name" class="form-label">Book Name</label>
                <input type="text" class="form-control" id="book_name" name="book_name" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">price</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            
            <div class="mb-3">
                <label for="delivery_place" class="form-label">delivery_place</label>
                <input type="text" class="form-control" id="delivery_place" name="delivery_place" required>
            </div>
            <div class="mb-3">
                <label for="delivery_date" class="form-label">Delivery Date</label>
                <input type="date" class="form-control" id="delivery_date" name="delivery_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Record</button>
        </form>
    </div>
</body>
</html>

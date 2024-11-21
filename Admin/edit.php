<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookhub');  // Ensure database is 'customer_orders'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect the form data
    $id = $_POST['id'];
    $name = $_POST['customer_name'];
    $bname = $_POST['book_name'];
    $price = $_POST['price'];
    $delivery_place = $_POST['delivery_place'];
    $date = $_POST['delivery_date'];
    $status = $_POST['status'];

    // Update query with corrected table name and variables
    $stmt = $conn->prepare("UPDATE customer_orders SET customer_name = ?, book_name = ?, price = ?, delivery_place = ?, delivery_date = ?, status = ? WHERE id = ?");
    
    // Bind parameters ('s' for string, 'i' for integer, 'd' for date)
    $stmt->bind_param('ssisssi', $name, $bname, $price, $delivery_place, $date, $status, $id);
    
    // Execute the query
    $stmt->execute();
    $stmt->close();

    // Redirect after updating
    header("Location: dashboard.php");
    exit(); // Always use exit after header redirection to prevent further code execution
}

// Fetch record for editing
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM customer_orders WHERE id = $id");
$record = $result->fetch_assoc();
$conn->close();
?>

<!-- HTML form pre-filled with $record data, styled with Bootstrap -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Customer Record</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $record['id']; ?>">

            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" class="form-control" value="<?php echo $record['customer_name']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="book_name" class="form-label">Book Name</label>
                <input type="text" name="book_name" id="book_name" class="form-control" value="<?php echo $record['book_name']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" name="price" id="price" class="form-control" value="<?php echo $record['price']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="delivery_place" class="form-label">Delivery Place</label>
                <input type="text" name="delivery_place" id="delivery_place" class="form-control" value="<?php echo $record['delivery_place']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="delivery_date" class="form-label">Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="<?php echo $record['delivery_date']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" name="status" id="status" class="form-control" value="<?php echo $record['status']; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Record</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

$conn = new mysqli('localhost', 'root', '', 'customer_orders');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

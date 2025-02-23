<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "bookhub";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debug session
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    echo "<script>
        alert('You must be logged in to make a payment.');
        window.location.href = '../buyers/b_login.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Get buyer details from buyers table using user_id
$buyer_query = "SELECT buyer_id, fname, lname, email, phone, address FROM buyers WHERE user_id = ?";
$buyer_stmt = $conn->prepare($buyer_query);
$buyer_stmt->bind_param("i", $user_id);
$buyer_stmt->execute();
$buyer_result = $buyer_stmt->get_result();

if ($buyer_result->num_rows === 0) {
    echo "<script>
        alert('Buyer account not found.');
        window.location.href = '../buyers/b_login.php';
    </script>";
    exit();
}

$buyer = $buyer_result->fetch_assoc();
$buyer_id = $buyer['buyer_id'];
$customer_name = $buyer['fname'] . ' ' . $buyer['lname'];
$customer_email = $buyer['email'];
$customer_phone = $buyer['phone'] ?? 'N/A';
$shipping_address = $buyer['address'] ?? 'Address not provided';

// Get total from URL
$total = isset($_GET['total']) ? $_GET['total'] : 0;

if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    echo "<script>
        alert('Invalid book ID.');
        window.location.href = '../landing/index.php';
    </script>";
    exit();
}

$book_id = intval($_GET['book_id']);

// Fetch book details
$stmt = $conn->prepare("SELECT price, title FROM book WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $book = $result->fetch_assoc();
    $book_price = $book['price'] * 100; // Convert to paisa
    $book_title = $book['title'];
} else {
    echo "<script>
        alert('Book not found.');
        window.location.href = '../landing/index.php';
    </script>";
    exit();
}

// Insert order into database using buyer_id
try {
    $order_query = "INSERT INTO orders (buyer_id, order_date, status, total_amount, shipping_address) 
                    VALUES (?, CURRENT_TIMESTAMP, 'Pending', ?, ?)";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("ids", $buyer_id, $total, $shipping_address);
    
    if (!$order_stmt->execute()) {
        throw new Exception("Error executing order query: " . $conn->error);
    }
    $order_id = $order_stmt->insert_id;
} catch (Exception $e) {
    echo "<script>
        alert('Error creating order: " . addslashes($e->getMessage()) . "');
        window.location.href = '../landing/index.php';
    </script>";
    exit();
}

// Khalti Payment Integration
$khalti_secret_key = "YOUR_SECRET_KEY_HERE"; // Store securely
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/initiate/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode(array(
        "return_url" => "http://localhost/bookhub/payment/payment_success.php?order_id=$order_id",
        "website_url" => "https://localhost/bookhub/",
        "amount" => $total * 100,
        "purchase_order_id" => "Order_" . $order_id,
        "purchase_order_name" => $book_title,
        "customer_info" => array(
            "name" => "Test Bahadur" ,
            "email" => "test@khalti.com" ,
            "phone" => "9800000001"
        )
    )),
    CURLOPT_HTTPHEADER => array(
        'Authorization:  Key 6d6cb9155ad4475484968b379b6b24d0' ,
        'Content-Type: application/json',
    ),
));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo 'Curl Error: ' . curl_error($curl);
} else {
    $response_data = json_decode($response, true);
    if (isset($response_data['payment_url'])) {
        header('Location: ' . $response_data['payment_url']);
        exit;
    } else {
        echo 'Error: Payment URL not found in response.';
        print_r($response_data);
    }
}

curl_close($curl);
?>

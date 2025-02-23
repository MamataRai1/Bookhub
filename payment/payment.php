<?php
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

// Check if book_id is set
if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    die("Invalid book ID.");
}

$book_id = intval($_GET['book_id']); // Convert to integer safely

// Fetch book price
$stmt = $conn->prepare("SELECT price, title FROM book WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $book = $result->fetch_assoc();
    $book_price = $book['price'] * 100; // Convert to paisa
    $book_title = $book['title'];
} else {
    die("Book not found.");
}

 


// Khalti Payment Integration
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/initiate/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode(array(
        "return_url" => "http://localhost/bookhub/payment/payment_success.php",
        "website_url" => "https://localhost/bookhub/",
        "amount" => $book_price, // Use the fetched book price
        "purchase_order_id" => "Order_" . $book_id,
        "purchase_order_name" => $book_title,
        "customer_info" => array(
            "name" => "Test Bahadur",
            "email" => "test@khalti.com",
            "phone" => "9800000001"
        )
    )),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Key 6d6cb9155ad4475484968b379b6b24d0', // Replace with your Khalti key
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

<?php
session_start(); 
$servername = "localhost";
$username = "root";
$password = "";
$database = "bookhub";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['purchase_order_id'], $_GET['amount'], $_GET['purchase_order_name'], $_GET['status'], $_GET['transaction_id'])) {
    die("Invalid Payment Details.");
}

// Extracting data from the URL
$order_id = htmlspecialchars($_GET['purchase_order_id']);
$amount = htmlspecialchars($_GET['amount']) / 100; // Convert paisa to rupees
$book_title = htmlspecialchars($_GET['purchase_order_name']);
$status = htmlspecialchars($_GET['status']);
$transaction_id = htmlspecialchars($_GET['transaction_id']);
$mobile = isset($_GET['mobile']) ? htmlspecialchars($_GET['mobile']) : 'N/A';

// Update order status in database if payment is completed
if ($status === 'Completed') {
    // Extract the numeric order ID from "Order_X" format
    $numeric_order_id = str_replace('Order_', '', $order_id);
    
    $update_query = "UPDATE orders SET status = 'Delivered' WHERE order_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $numeric_order_id);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .receipt-container {
            background: #fff;
            width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1 {
            color: #4CAF50;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .details {
            text-align: left;
            margin-top: 15px;
            border-top: 2px solid #ddd;
            padding-top: 15px;
        }

        .details p {
            font-size: 16px;
            margin: 8px 0;
        }

        .status {
            font-size: 18px;
            font-weight: bold;
            padding: 8px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }

        .success {
            color: #fff;
            background-color: #4CAF50;
        }

        .failed {
            color: #fff;
            background-color: #E74C3C;
        }

        .qr-code {
            margin-top: 15px;
        }

        .buttons {
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            margin: 5px;
        }

        .btn-home {
            background-color: #3498db;
        }

        .btn-print {
            background-color: #2ecc71;
        }

        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <h1>Payment Receipt</h1>
        <div class="details">
            <p><strong>Order ID:</strong> <?php echo $order_id; ?></p>
            <p><strong>Book Title:</strong> <?php echo $book_title; ?></p>
            <p><strong>Amount Paid:</strong> Rs. <?php echo number_format($amount, 2); ?></p>
            <p><strong>Transaction ID:</strong> <?php echo $transaction_id; ?></p>
            <p><strong>Mobile Number:</strong> <?php echo $mobile; ?></p>
            <p><strong>Status:</strong>
                <span class="status <?php echo $status === 'Completed' ? 'success' : 'failed'; ?>">
                    <?php echo $status; ?>
                </span>
            </p>
        </div>

        <div class="buttons">
            <a href="../buyers/b_dashboard.php" class="btn btn-home">Back to Home</a>
            <button onclick="window.print()" class="btn btn-print">Print Receipt</button>
        </div>
    </div>

    <?php if ($status === 'Completed'): ?>
    <script>
        // Show success message
        setTimeout(function() {
            alert('Payment successful! Your order has been confirmed.');
        }, 1000);
    </script>
    <?php endif; ?>
</body>

</html>
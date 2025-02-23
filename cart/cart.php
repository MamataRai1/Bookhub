<?php
session_start();
include('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../buyers/b_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get buyer_id
$buyer_query = "SELECT buyer_id FROM buyers WHERE user_id = ?";
$stmt = $conn->prepare($buyer_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$buyer = $result->fetch_assoc();
$buyer_id = $buyer['buyer_id'];

// Get cart items with book details including image
$cart_query = "SELECT c.cart_id, c.quantity, c.added_at, 
               b.book_id, b.title, b.price, b.image, b.author, b.description 
               FROM cart c 
               JOIN book b ON c.book_id = b.book_id 
               WHERE c.buyer_id = ? 
               ORDER BY c.added_at DESC";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$cart_items = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        .cart-item img {
            width: 120px;
            height: 180px;
            object-fit: cover;
            margin-right: 20px;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        .item-details {
            flex: 1;
        }
        .item-details h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .item-details p {
            margin: 5px 0;
            color: #666;
        }
        .author {
            font-style: italic;
            color: #888;
        }
        .price {
            font-weight: bold;
            color: #2ecc71;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        .quantity-btn {
            padding: 5px 15px;
            border: none;
            background: #f1f1f1;
            cursor: pointer;
            border-radius: 3px;
            transition: background 0.3s;
        }
        .quantity-btn:hover {
            background: #e1e1e1;
        }
        .remove-btn {
            color: #e74c3c;
            cursor: pointer;
            padding: 10px;
            transition: color 0.3s;
        }
        .remove-btn:hover {
            color: #c0392b;
        }
        .cart-summary {
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            text-align: right;
        }
        .checkout-btn {
            padding: 12px 25px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .checkout-btn:hover {
            background: #27ae60;
        }
        .empty-cart {
            text-align: center;
            padding: 50px 20px;
        }
        .empty-cart a {
            color: #3498db;
            text-decoration: none;
        }
        .empty-cart a:hover {
            text-decoration: underline;
        }
        .added-time {
            font-size: 0.8em;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <h1>Shopping Cart</h1>
        
        <?php if ($cart_items->num_rows > 0): ?>
            <?php while ($item = $cart_items->fetch_assoc()): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <div class="cart-item" id="cart-item-<?php echo $item['cart_id']; ?>">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="author">By <?php echo htmlspecialchars($item['author']); ?></p>
                        <p class="price">Rs. <?php echo number_format($item['price'], 2); ?></p>
                        <div class="quantity-controls">
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 'decrease')">-</button>
                            <span id="quantity-<?php echo $item['cart_id']; ?>"><?php echo $item['quantity']; ?></span>
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 'increase')">+</button>
                        </div>
                        <p>Subtotal: Rs. <span id="subtotal-<?php echo $item['cart_id']; ?>"><?php echo number_format($subtotal, 2); ?></span></p>
                        <p class="added-time">Added on: <?php echo date('M d, Y H:i', strtotime($item['added_at'])); ?></p>
                    </div>
                    <i class="fas fa-trash remove-btn" onclick="removeItem(<?php echo $item['cart_id']; ?>)"></i>
                </div>
            <?php endwhile; ?>

            <div class="cart-summary">
                <h2>Total: Rs. <span id="cart-total"><?php echo number_format($total, 2); ?></span></h2>
                <button class="checkout-btn" onclick="checkout()">Proceed to Checkout</button>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any books to your cart yet.</p>
                <a href="../landing/index.php">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateQuantity(cartId, action) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `cart_id=${cartId}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById(`quantity-${cartId}`).textContent = data.quantity;
                    document.getElementById(`subtotal-${cartId}`).textContent = data.subtotal;
                    document.getElementById('cart-total').textContent = data.total;
                }
            });
        }

        function removeItem(cartId) {
            if (confirm('Are you sure you want to remove this item?')) {
                fetch('remove_cart_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `cart_id=${cartId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById(`cart-item-${cartId}`).remove();
                        document.getElementById('cart-total').textContent = data.total;
                        if (data.total === '0.00') {
                            location.reload();
                        }
                    }
                });
            }
        }

        function checkout() {
            window.location.href = '../payment/payment.php';
        }
    </script>
</body>
</html>
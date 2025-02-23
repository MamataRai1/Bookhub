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


// Check if 'id' is set in the URL
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch book details along with store name
    $query = "SELECT book.*, store.store_name FROM book 
              JOIN store ON book.store_id = store.store_id 
              WHERE book.book_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        echo "Book not found.";
        exit;
    }
} else {
    echo "Invalid book ID.";
    exit;
}

// Fetch ratings & reviews
$review_query = "SELECT * FROM reviews WHERE book_id = ?";
$stmt = $conn->prepare($review_query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$reviews = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - BookHub</title>
    <link rel="stylesheet" href="bookd.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>

    <div class="book-details-container">
        <div class="book-image">
            <img src="../assets/img/<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
        </div>

        <div class="book-info">
            <h1><?php echo htmlspecialchars($book['title']); ?></h1>
            <p class="author">by <?php echo htmlspecialchars($book['author']); ?></p>
            <p class="shop-name">Sold by: <?php echo htmlspecialchars($book['store_name']); ?></p>
            <p class="price">Rs. <?php echo htmlspecialchars($book['price']); ?></p>

            <div class="ratings">
                <p>Ratings:
                    <?php
                    $stars = rand(3, 5); // Fake star rating (you can fetch from DB)
                    for ($i = 0; $i < $stars; $i++) {
                        echo "⭐";
                    }
                    ?>
                </p>
            </div>

            <div class="delivery-info">
                <p><i class="fas fa-truck"></i> Standard Delivery: Rs. 125 (2-3 days)</p>
                <p><i class="fas fa-money-bill-wave"></i> Cash on Delivery Available</p>
            </div>

            <p class="description"><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
            <div class="buttons">
                <button onclick="showOrderDetails(<?php echo $book['book_id']; ?>, <?php echo $book['price']; ?>, '<?php echo htmlspecialchars($book['title']); ?>')" class="buy-btn">Buy Now</button>
                <button onclick="window.location.href='../buyers/add_to_cart.php?book_id=<?php echo $book['book_id']; ?>' " class="cart-btn">Add to Cart</button>

            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section">
        <h2>Reviews</h2>
        <?php if ($reviews->num_rows > 0): ?>
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="review">
                    <p><strong><?php echo htmlspecialchars($review['username']); ?></strong></p>
                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>

    <!-- Login Form Popup (Buyer's Login) -->
    <div id="loginpopup" class="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closeBuyerLogin()">&times;</span>
            <iframe src="../buyers/b_login.php" width="100%" height="400px" style="border: none;"></iframe>
        </div>
    </div>

    <!-- Add this simplified Order Details Popup -->
    <div id="orderDetailsPopup" class="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closeOrderDetails()">&times;</span>
            <h2>Order Details</h2>
            
            <div class="order-summary">
                <div class="price-breakdown">
                    <p>Book Price: <span id="bookPrice">Rs. 0</span></p>
                    <p>Delivery Fee: <span>Rs. 125</span></p>
                    <hr>
                    <p class="total">Total Amount: <span id="totalPrice">Rs. 0</span></p>
                    <p class="tax-note">All taxes included</p>
                </div>
                <button onclick="confirmOrder()" class="proceed-btn">Proceed to Pay</button>
            </div>
        </div>
    </div>

    <!-- JavaScript for Popup -->
    <script>
    let currentBookId, currentPrice;

    function showOrderDetails(bookId, price, title) {
        currentBookId = bookId;
        currentPrice = price;
        
        document.getElementById('bookPrice').textContent = 'Rs. ' + price;
        document.getElementById('totalPrice').textContent = 'Rs. ' + (price + 125); // Adding delivery charge
        
        document.body.classList.add("popup-open");
        document.getElementById("orderDetailsPopup").style.display = "flex";
    }

    function closeOrderDetails() {
        document.body.classList.remove("popup-open");
        document.getElementById("orderDetailsPopup").style.display = "none";
    }

    function confirmOrder() {
        const totalPrice = currentPrice + 125; // Adding delivery charge
        window.location.href = `../payment/payment.php?book_id=${currentBookId}&total=${totalPrice}`;
    }

    function showBuyerLogin() {
        document.body.classList.add("popup-open");
        document.getElementById("loginpopup").style.display = "flex";
    }

    function closeBuyerLogin() {
        document.body.classList.remove("popup-open");
        document.getElementById("loginpopup").style.display = "none";
    }
    </script>

    
</body>

</html>
<?php
session_start();

// print_r($_SESSION);

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

// Fetch Best-Selling Books (LIMIT 8)
$stmt = $conn->prepare("SELECT * FROM book LIMIT 8");
$stmt->execute();
$books = $stmt->get_result();

// Fetch Buyer Info (Replace with session values if using login system)
$buyer_name = "John Doe";  // Replace with actual session variable
$buyer_email = "johndoe@example.com"; // Replace with actual session variable

if (isset($_SESSION['buyer_id'])) {
    $cartQuery = $conn->prepare("SELECT COUNT(*) AS total FROM cart WHERE buyer_id = ?");
    $cartQuery->bind_param("i", $_SESSION['buyer_id']);
    $cartQuery->execute();
    $result = $cartQuery->get_result();
    $cartCount = $result->fetch_assoc()['total'];
} else {
    $cartCount = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard | BookHub</title>
     
    <link rel="stylesheet" href="buyer.css">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    
 
</head>

<body>
    <!-- HEADER -->
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <h2>BookHub</h2>
            </div>
            <div class="menubar">
                <ul>
                    <li><a href="category">Categories</a></li>
                    <li><a href="/BOOKHUB/sellers/s_signup.php">Be a seller</a></li>
                    <li><a href="#contact_section">Contact us</a></li>
                </ul>
            </div>
            <div class="icons">
            <form id="searchForm" action="search.php" method="GET">
              <input type="text" id="searchInput" name="search" placeholder="Search books..." required>
              <a href="#" class="fas fa-magnifying-glass" id="searchBtn"></a>
            </form>

            <form action="cart.php" method="POST">
               <input type="hidden" name="book_id" value="BOOK_ID_HERE ">
                <a href="../cart/cart.php" class="fas fa-cart-shopping"></a>
           </form>


                    <span><?php echo $cartCount; ?></span>
                 </a>
                 
                 <form action="wishlist.php" method="POST">
                  <input type="hidden" name="book_id" value="BOOK_ID_HERE">
                  <a href="wishlist.php" class="fas fa-heart"></a>

                  </form>

                <div class="profile-menu">
                    <a href="profile.php" class="fas fa-user profile-icon"></a>
                    
                         
                       
                    </div>
                    <div class="logout-container">
                      <a href="logout.php" class="logout-btn">LOG OUT</a>
                        </div>

                
                </div>
            </div>
        </nav>
    </header>

    <!-- HOME SECTION -->
    <!-- HERO SECTION -->
<section class="home">
    <div class="content">
        <!-- Left Side - Text -->
        <div class="text-section">
            <h3>Buy your favorite book from here</h3>
            <p>Discover amazing books at affordable prices. We offer a variety of books in different categories.</p>
            <a href="#"><button class="button">Shop Now</button></a>
        </div>
        
        <!-- Right Side - Image -->
        <div class="image-section">
            <img src="../assets/img/bg.jpg" alt="Bookshelf Image">
        </div>
    </div>
</section>

     
    <!-- BEST-SELLING BOOKS -->
<section class="best-selling">
    <h2>Best Selling Items</h2>
    <div class="item-grid">
        <?php while ($row = $books->fetch_assoc()): ?>
            <div class="item-card">
                <a href="../landing/book_details.php?id=<?php echo $row['book_id']; ?>">
                    <img src="../assets/img/<?php echo htmlspecialchars($row['image']); ?>" alt="">
                </a>
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p>by <?php echo htmlspecialchars($row['author']); ?></p>
                <p class="price"><b>Rs. <?php echo $row['price']; ?></b></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <button class="add-to-cart-btn" data-id="<?php echo $row['book_id']; ?>">
                        <i class="fas fa-cart-shopping"></i> Add to Cart
                    </button>
                    <a href="../landing/book_details.php?id=<?php echo $row['book_id']; ?>" class="fa-solid fa-eye"></a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

    <!-- CONTACT -->
    <section id="contact_section">
        <div class="contact-form">
            <h2>Contact Us</h2>
            <form action="#" method="post">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Your Message..." required></textarea>
                </div>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer_main">
            <div class="footer_tag">
                <h2>Contact</h2>
                <p>+9779827384746</p>
                <p>+9779873615261</p>
                <p>En@gmail.com</p>
                <p>Bookhub@gmail.com</p>
            </div>
            <div class="footer_tag">
                <h2>Our Service</h2>
                <p>Exclusive Discount</p>
                <p>Secure Payments</p>
                <p>24/7 Service</p>
                <p>Fast and Reliable Delivery</p>
            </div>
            <div class="footer_tag">
                <h2>Follow us</h2>
                <i class="fa-brands fa-facebook-f"></i>
                <i class="fa-brands fa-twitter"></i>
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-linkedin-in"></i>
            </div>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('searchBtn').addEventListener('click', function () {
    document.getElementById('searchForm').submit();
});

$(document).ready(function() {
    $(".add-to-cart-btn").click(function() {
        let book_id = $(this).data("id");

        $.ajax({
            url: "add_to_cart.php",
            type: "POST",
            data: { book_id: book_id },
            success: function(response) {
                let data = JSON.parse(response);
                if (data.status == "success") {
                    alert("Book added to cart!");
                    $(".fas.fa-cart-shopping span").text(data.cart_count); // Update cart count
                } else {
                    alert("Please log in first!");
                    window.location.href = "../buyers/login.php";
                }
            },
            error: function() {
                alert("Something went wrong!");
            }
        });
    });
});
</script>

</body>
</html>

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
    
    <style>
        /* Profile Dropdown */
        .profile-menu {
            position: relative;
            display: inline-block;
        }

        .profile-icon {
            font-size: 22px;
            cursor: pointer;
            color: #333;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            min-width: 180px;
            box-shadow:  rgba(255, 255, 255, 1);
            padding: 10px;
            border-radius: 5px;
            z-index: 100;
        }

        .dropdown-content p {
            margin: 5px 0;
            font-size: 14px;
        }

        .dropdown-content .logout-btn {
            display: block;
            margin-top: 10px;
            text-align: center;
            background: #e74c3c;
            color: white;
            padding: 8px;
            text-decoration: none;
            border-radius: 5px;
        }

        .dropdown-content .logout-btn:hover {
            background: #c0392b;
        }

        .profile-menu:hover .dropdown-content {
            display: block;
        }
    </style>
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
                <a href="#" class="fas fa-magnifying-glass"></a>
                <a href="../buyers/cart.php" class="fas fa-cart-shopping">
                    <span><?php echo $cartCount; ?></span>
                 </a>
                <a href="#" class="fas fa-cart-shopping"></a>
                <div class="profile-menu">
                    <a href="profile.php" class="fas fa-user profile-icon"></a>
                    
                         
                       
                    </div>
               <button> <a href="logout.php" class="logout-btn">LOG OUT</a></button>
                <!-- Profile Dropdown -->
                
                </div>
            </div>
        </nav>
    </header>

    <!-- HOME SECTION -->
    <section class="home" id="home">
        <div class="content">
            <img src="../assets/img/bg.jpg" alt="Background">
            <h3>Buy your favorite book from here</h3>
            <p>Discover amazing books at affordable prices. We offer a variety of books in different categories.</p>
            <a href="#"><button class="button">Shop Now</button></a>
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

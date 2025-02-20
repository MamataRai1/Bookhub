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
$stmt = $conn->prepare("SELECT * FROM book LIMIT 8");
$stmt->execute();
$result = $stmt->get_result();


// Fetch best-selling books
$query = "SELECT * FROM book LIMIT 8";  // Fetches only 8 books
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore</title>

    <link rel="stylesheet" href="landing.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<body>
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
            <form id="searchForm" action="filter.php" method="GET">
               <input type="text" id="searchInput" name="search" placeholder="Search books..." required>
                <a href="#" class="fas fa-magnifying-glass" id="searchBtn"></a>
            </form>

            <form action="add_to_cart.php" method="POST">
               <input type="hidden" name="book_id" value="BOOK_ID_HERE">
                <a href="cart.php" class="fas fa-cart-shopping"></a>
           </form>


                     
                  
                 
                 <form action="add_to_cart.php" method="POST">
                  <input type="hidden" name="book_id" value="BOOK_ID_HERE">
                  <a href="wishlist.php" class="fas fa-heart"></a>

                  </form>
                <a href="/BOOKHUB/buyers/b_login.php">
                    <button><i class="fa-solid fa-user"></i>LOG IN</button>
                </a>
                <a href="/BOOKHUB/buyers/b_singup.php">
                    <button>SIGN UP</button>
                </a>
            </div>
        </nav>
    </header>

    <!-- home section -->
    <section class="home" id="home">
        <div class="content">
            <div class="text-section">
                <h3>Buy your favourite book from here</h3>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit odio praesentium
                    ipsa quisquam minima tempore! Soluta rem incidunt quam quo doloribus mollitia dicta,
                    laudantium molestias assumenda cum blanditiis possimus libero?</p>
                <a href="ldkksdl" target="none">
                    <button class="button">Shop Now</button>
                </a>
            </div>
            <div class="image-section">
                <img src="../assets/img/bg.jpg" alt="Books">
            </div>
        </div>
    </section>

    <section class="best-selling">
        <h2>Best Selling Items</h2>
        <div class="item-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="item-card">
                    <a href="book_details.php?id=<?php echo $row['book_id']; ?>">
                        <img src="../assets/img/<?php echo htmlspecialchars($row['image']); ?>" alt="">
                    </a>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p>by <?php echo htmlspecialchars($row['author']); ?></p>
                    <p class="price"><b>Rs.<?php echo $row['price']; ?></b></p>
                    <div class="icons">
                        <a href="#" class="fas fa-heart"></a>
                        <a href="#" class="fas fa-cart-shopping"></a>
                        <a href="book_details.php?id=<?php echo $row['book_id']; ?>" class="fa-solid fa-eye"></a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <?php $conn->close(); ?>

    <!-- discount -->
    <div class="container">
        <div class="discount-banner">
            <img src="../assets/img/book10.png" alt="">
            <div class="discount-content">
                <h2>30% Discount<br>On All Items.<br>Hurry Up !!!</h2>
                <div class="countdown">
                    <div class="countdown-item">
                        <span id="days">13</span>
                        <p>Days</p>
                    </div>
                    <div class="countdown-item">
                        <span id="hours">23</span>
                        <p>Hrs</p>
                    </div>
                    <div class="countdown-item">
                        <span id="minutes">58</span>
                        <p>Min</p>
                    </div>
                    <div class="countdown-item">
                        <span id="seconds">52</span>
                        <p>Sec</p>
                    </div>
                </div>
                <div>
                    <a href="#" class="shop-btn">Shop Collection</a>
                </div>
            </div>
        </div>
    </div>
    <br>

    <!-- Contact -->
    <section id="contact_section">
        <div class="contact-form">
            <h2>Contact Us</h2>
            <form action="#" method="post">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="your name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="your email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Text me..." required></textarea>
                </div>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </section>




    <!-- footer -->
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
                <p>Fast and reliable delivery</p>
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

    <script>

document.getElementById('searchBtn').addEventListener('click', function () {
    document.getElementById('searchForm').submit();
});
        const countdownDate = new Date("February 28, 2025 00:00:00").getTime();

        const timer = setInterval(() => {
            const now = new Date().getTime();
            const timeLeft = countdownDate - now;

            if (timeLeft <= 0) {
                clearInterval(timer);
                document.getElementById("days").innerHTML = "0";
                document.getElementById("hours").innerHTML = "0";
                document.getElementById("minutes").innerHTML = "0";
                document.getElementById("seconds").innerHTML = "0";
            } else {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                document.getElementById("days").innerHTML = days;
                document.getElementById("hours").innerHTML = hours;
                document.getElementById("minutes").innerHTML = minutes;
                document.getElementById("seconds").innerHTML = seconds;
            }
        }, 1000);
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore</title>
    <link rel="stylesheet" href="./buyer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <h2>Bookhub</h2>
            </div>
            <div class="menubar">
                <ul>
           
                    <li><a href="category">Categories</a></li>
  
                    
                    <li><a href="#profile_section">Profile</a></li>
                    <li><a href="#orders_section">Orders</a></li>
                    <li><a href="#cart_section">Cart</a></li>
                </ul>
            </div>
            <div class="icons">
                <a href="#" class="fas fa-magnifying-glass"></a>
                <a href="#" class="fas fa-heart"></a>
                <a href="#cart_section" class="fas fa-cart-shopping"></a>
  
                <button><a href="/BOOKHUB/buyers/logout.php">Log Out</a></button>
            </div>
        </nav>
    </header>

    <!-- Home Section -->
    <section class="home" id="home">
        <div class="content">
            <h3>Buy your favourite book from here</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit odio praesentium <br>
                ipsa quisquam minima tempore! Soluta rem incidunt quam quo doloribus mollitia dicta,<br>
                laudantium molestias assumenda cum blanditiis possimus libero?</p>
            <a href="ldkksdl" target="none"><br>
                <button class="button"> Shop Now</button>
            </a>
        </div>
    </section>

    <!-- Best Selling Items -->
    <section class="best-selling">
        <h2>Best Selling Items</h2>
        <div class="item-grid">
            <!-- Repeat this block for each book -->
            <div class="item-card">
                <a href="page.html"><img src="../assets/img/book2.jpg" alt=""></a>
                <h3>Power</h3>
                <p>by Robert</p>
                <p class="price"><b>Rs.400</b> <span>Rs.350</span></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#cart_section" class="fas fa-cart-shopping"></a>
                    <button><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
            <!-- Repeat for other books -->
        </div>
    </section>

    <!-- Discount Banner -->
    <div class="container">
        <div class="discount-banner">
            <img src="../assets/img/book10.png" width="500px" height="500px" alt="">
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
            <a href="#" class="shop-btn">Shop Collection</a>
        </div>
    </div>

     
     

    <!-- Cart Section -->
    
         

    <!-- Footer -->
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
        const timer = setInterval(() => {
            const now = new Date().getTime();
            const timeLeft = countdownDate - now;

            if (timeLeft > 0) {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                document.getElementById("days").innerText = days;
                document.getElementById("hours").innerText = hours;
                document.getElementById("minutes").innerText = minutes;
                document.getElementById("seconds").innerText = seconds;
            } else {
                clearInterval(timer);
            }
        }, 1000);
    </script>
</body>
</html>
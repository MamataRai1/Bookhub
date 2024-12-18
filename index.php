<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h2>Bookhub</h2>
            </div>
            <div class="menubar">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="category">Categories</a></li>
                    <li><a href="seller.html">Be a seller</a></li>
                    <li><a href="contactus.html">Contact us</a></li>
                </ul>
            </div>
            <div class="icons">
                <a href="#" class="fas fa-magnifying-glass"></a>
                <a href="#" class="fas fa-heart"></a>
                <a href="#" class="fas fa-cart-shopping"></a>
                <button><i class="fa-solid fa-user"></i> Login</button>
            </div>
        </nav>
    </header>

    <!-- home section -->
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
            <div class="item-card">
                <img src="./assets/img/book2.jpg" alt="">
                <h3>Power</h3>
                <p>by Robert</p>
                <p class="price"><b>Rs.400</b> <span>Rs.350</span></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="fas fa-cart-shopping"></a>
                    <button><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
            <div class="item-card">
                <img src="./assets/img/book2.jpg" alt="">
                <h3>Thorns and Roses</h3>
                <p>by Sarah</p>
                <p class="price"><b>Rs.600</b> <span>Rs.500</span></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="fas fa-cart-shopping"></a>
                    <button><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
            <div class="item-card">
                <img src="./assets/img/book3.jpg" alt="">
                <h3>Broken pieces</h3>
                <p>by Tillie</p>
                <p class="price"><b>Rs.1000</b> <span>Rs.800</span></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="fas fa-cart-shopping"></a>
                    <button><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
            <div class="item-card">
                <img src="./assets/img/book4.jpg" alt="">
                <h3>Games</h3>
                <p>by Ana</p>
                <p class="price"><b>Rs.800</b> <span>Rs.750</span></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="fas fa-cart-shopping"></a>
                    <button><i class="fa-solid fa-eye"></i></button>
                </div>
            </div> 
            <div class="item-card">
                <img src="./assets/img/book5.jpg" alt="">
                <h3>Atomic habits</h3>
                <p>by James</p>
                <p class="price"><b>Rs.600</b> <span>Rs.500</span></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="fas fa-cart-shopping"></a>
                    <button><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
            <div class="item-card">
                <img src="./assets/img/book6.jpg" alt="">
                <h3>Haunting adeline</h3>
                <p>by Colleen</p>
                <p class="price"><b>Rs.500</b> <span>Rs.350</span></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="fas fa-cart-shopping"></a>
                    <button><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
            <div class="item-card">
                <img src="./assets/img/book7.jpg" alt="">
                <h3>It ends with us</h3>
                <p>by Colleen</p>
                <p class="price"><b>Rs.1100</b> <span>Rs.1000</span></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="fas fa-cart-shopping"></a>
                    <button><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
            <div class="item-card">
                <img src="./assets/img/book8.jpg" alt="">
                <h3>It start with us</h3>
                <p>by Colleen</p>
                <p class="price"><b>Rs.800</b> <span>Rs.550</span></p>
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="fas fa-cart-shopping"></a>
                    <button><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- discount -->
    <div class="container">
        <div class="discount-banner">
            <img src="./assets/img/book10.png" width="500px" height="500px" alt="">
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
    <br>

    <!-- Contact -->
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
    

<!-- premium -->
<div class="plan">
    <h2>Choose your plan</h2>
    <div class="price-row">
        <div class="price-col">
            <p>Starter</p>
            <h3>19$ / month</h3>
            <ul>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
            </ul>
            <button>Add To Cart</button>
        </div>
        <div class="price-col">
            <p>Advanced</p>
            <h3>19$ / month</h3>
            <ul>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
            </ul>
            <button>Add To Cart</button>
        </div>
        <div class="price-col">
            <p>Premium</p>
            <h3>19$ / month</h3>
            <ul>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
                <li>1 Website</li>
            </ul>
            <button>Add To Cart</button>
        </div>
    </div>
</div>

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
        // Add event listeners for icon hover effects
        //const icons = document.querySelectorAll('.item-card .icons a, .item-card .icons button, .footer_tag i');
        //icons.forEach(icon => {
           // icon.addEventListener('mouseover', () => {
                //icon.style.color = '#666';
            //});
            //icon.addEventListener('mouseout', () => {
                //icon.style.color = '#333';
          //  });
       // });


// Countdown Target Date
const countdownDate = new Date("2024-12-31T00:00:00").getTime();

// Update Countdown Every Second
const timer = setInterval(() => {
    const now = new Date().getTime();
    const timeLeft = countdownDate - now;

    if (timeLeft > 0) {
        // Calculate Days, Hours, Minutes, Seconds
        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor(
            (timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        );
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        // Update HTML Elements
        document.getElementById("days").textContent = days.toString().padStart(2, '0');
        document.getElementById("hours").textContent = hours.toString().padStart(2, '0');
        document.getElementById("minutes").textContent = minutes.toString().padStart(2, '0');
        document.getElementById("seconds").textContent = seconds.toString().padStart(2, '0');
    } else {
        clearInterval(timer);
        document.querySelector(".countdown").innerHTML = "<h2>Time's up!</h2>";
    }
}, 1000);

    </script>
</body>
</html>
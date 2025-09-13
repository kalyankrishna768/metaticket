<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaTicket - Travel Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Root Variables for Consistent Theming */
        :root {
            --primary-color: #1a73e8;
            --secondary-color: #f8f9fa;
            --accent-color: #34a853;
            --dark-color: #202124;
            --light-color: #ffffff;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Button Styles */
        .btn {
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: var(--light-color);
            border-radius: 30px;
            font-weight: 600;
            display: inline-block;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn:hover {
            background-color: #0d5bba;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--primary-color);
            color: var(--light-color);
        }

        /* Header/Navbar */
        .header {
            background-color: var(--light-color);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        /* Hero Section */
        .hero {
            padding: 60px 0 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            border-radius: 0 0 30px 30px;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--dark-color);
        }

        .hero p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            color: #555;
        }

        .hero-image {
            margin-top: 30px;
            position: relative;
        }

        .hero-image img {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            max-height: 400px;
            object-fit: cover;
        }

        /* Search Form */
        .search-form {
            background-color: var(--light-color);
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin: 0 auto;
            max-width: 800px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            position: relative;
            transform: translateY(-30px);
        }

        .form-group {
            flex: 1 0 200px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }

        .search-btn {
            flex: 1 0 auto;
            align-self: flex-end;
        }

        /* Categories Section */
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            font-size: 1.8rem;
            color: var(--dark-color);
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
            margin: 10px auto 0;
            border-radius: 2px;
        }

        .categories {
            padding: 50px 0;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .category-card {
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .category-img {
            height: 200px;
            overflow: hidden;
        }

        .category-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .category-card:hover .category-img img {
 Focused: Grok 3 built by xAI
            transform: scale(1.05);
        }

        .category-content {
            padding: 20px;
            text-align: center;
        }

        .category-content h3 {
            margin-bottom: 10px;
            color: var(--dark-color);
            font-size: 1.3rem;
        }

        .category-content p {
            color: #666;
            margin-bottom: 15px;
        }

        /* Features Section */
        .features {
            padding: 50px 0;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature-card {
            text-align: center;
            padding: 30px 20px;
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--dark-color);
        }

        .feature-text {
            color: #666;
        }

        /* Footer */
        .footer {
            background-color: var(--dark-color);
            color: var(--light-color);
            padding: 50px 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .footer-col h4 {
            margin-bottom: 20px;
            font-size: 1.2rem;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-col h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 2px;
            background-color: var(--primary-color);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #bbb;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary-color);
            padding-left: 5px;
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #bbb;
            font-size: 0.9rem;
        }

        /* Media Queries */
        @media (max-width: 992px) {
            .hero h2 {
                font-size: 2rem;
            }

            .search-form {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .hero {
                padding: 40px 0 30px;
            }

            .hero h2 {
                font-size: 1.8rem;
            }

            .search-form {
                flex-direction: column;
                gap: 10px;
                padding: 15px;
                margin-top: -20px;
            }

            .form-group {
                flex: 1 0 100%;
            }

            .feature-card {
                padding: 20px 15px;
            }
        }

        @media (max-width: 576px) {
            .container {
                width: 95%;
                padding: 0 10px;
            }

            .hero h2 {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .nav-links {
                gap: 10px;
            }

            .btn {
                padding: 10px 18px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <img src="assets/images/logo.png" alt="MetaTicket Logo">
                    <h1>MetaTicket</h1>
                </div>
                <div class="nav-links">
                    <a href="newsignin.php" class="btn btn-outline">Sign In</a>
                    <a href="newsignup.php" class="btn">Sign Up</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Travel Made Simple, Journey Made Better</h2>
                <p>Book your tickets instantly and enjoy hassle-free travel experiences with MetaTicket</p>
                <a href="newsignup.php" class="btn">Start Booking Now</a>
            </div>
            <div class="hero-image">
                <img src="assets/images/dashboard_pic.jpg" alt="Travel Booking">
            </div>
        </div>
    </section>

    <!-- Search Form -->
    <div class="container">
        <form class="search-form" method="POST" action="available_buses.php">
            <div class="form-group">
                <label for="from">From</label>
                <input type="text" id="from" name="from" class="form-control" placeholder="Departure City">
            </div>
            <div class="form-group">
                <label for="to">To</label>
                <input type="text" id="to" name="to" class="form-control" placeholder="Destination City">
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" class="form-control">
            </div>
            <div class="form-group">
                <button type="submit" class="btn search-btn">Search Tickets</button>
            </div>
        </form>
    </div>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">How Would You Like to Travel?</h2>
            <div class="category-grid">
                <div class="category-card">
                    <div class="category-img">
                        <img src="assets/images/busPic.jpg" alt="Bus Travel">
                    </div>
                    <div class="category-content">
                        <h3>Bus Booking</h3>
                        <p>Comfortable buses with modern amenities</p>
                        <a href="newsignup.php" class="btn">Book Now</a>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">
                        <img src="assets/images/ticket.jpg" alt="Available Tickets">
                    </div>
                    <div class="category-content">
                        <h3>Available Tickets</h3>
                        <p>Browse all available tickets for your journey</p>
                        <a href="newsignup.php" class="btn">Check Availability</a>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">
                        <img src="assets/images/route.jpg" alt="Express Routes">
                    </div>
                    <div class="category-content">
                        <h3>Express Routes</h3>
                        <p>Get to your destination faster with express service</p>
                        <a href="newsignup.php" class="btn">Explore Routes</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose MetaTicket</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h3 class="feature-title">Instant Bookings</h3>
                    <p class="feature-text">Book your tickets instantly with just a few clicks</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h3 class="feature-title">Best Prices</h3>
                    <p class="feature-text">Get the best deals and discounts on your bookings</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-text">Our customer support team is always ready to help</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Secure Payments</h3>
                    <p class="feature-text">Your payment information is always secure with us</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4><i class="fas fa-building"></i> Company</h4>
                    <ul class="footer-links">
                        <li><a href="about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
                        <li><a href="privacy.php"><i class="fas fa-shield-alt"></i> Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><i class="fas fa-question-circle"></i> Get Help</h4>
                    <ul class="footer-links">
                        <li><a href="FAQ.php"><i class="fas fa-question"></i> FAQ</a></li>
                        <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><i class="fas fa-ticket-alt"></i> Online Booking</h4>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-bus"></i> Bus</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><i class="fas fa-share-alt"></i> Follow Us</h4>
                    <p>Connect with us on social media for updates and offers</p>
                </div>
            </div>
            <div class="copyright">
                <p>© 2025 MetaTicket. All Rights Reserved | Privacy Policy | Terms & Conditions</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Date Input - Set Min Date to Today
        const dateInput = document.getElementById('date');
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        dateInput.value = today;
    </script>
</body>
</html>
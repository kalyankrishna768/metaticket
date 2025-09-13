<?php
include 'config.php';
session_start();
$username = $_SESSION['username'];
$email = $_SESSION['email'];
?>

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
        position: relative;
        overflow-x: hidden;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    img {
        max-width: 100%;
        height: auto;
        /* Added to maintain aspect ratio */
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

    .btn-cancel {
        color: #fff;
        background-color: #ff4d4d;
        border: none;
        padding: 8px 16px;
        border-radius: 30px;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-cancel:hover {
        background-color: #e60000;
        transform: translateY(-2px);
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

    .logo-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-container img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .logo-container h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .nav-links {
        display: flex;
        gap: 20px;
    }

    .nav-links a {
        font-weight: 500;
        transition: var(--transition);
        color: var(--dark-color);
        position: relative;
    }

    .nav-links a:hover {
        color: var(--primary-color);
    }

    .nav-links a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -5px;
        left: 0;
        background-color: var(--primary-color);
        transition: var(--transition);
    }

    .nav-links a:hover::after {
        width: 100%;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .sign-out {
        color: var(--dark-color);
        display: flex;
        align-items: center;
        transition: var(--transition);
    }

    .sign-out i {
        margin-right: 5px;
    }

    .sign-out:hover {
        color: #ff4d4d;
    }

    .menu-btn {
        display: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    /* Enhanced Sidebar Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: -300px;
        /* Increased width */
        width: 300px;
        height: 100%;
        background: linear-gradient(to bottom, #1a73e8, #0d47a1);
        color: var(--light-color);
        transition: transform 0.3s ease;
        z-index: 1001;
        padding-top: 50px;
        box-shadow: 2px 0 15px rgba(0, 0, 0, 0.3);
        overflow-y: auto;
        transform: translateX(0);
    }

    .sidebar.active {
        transform: translateX(300px);
    }

    .sidebar-close {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 22px;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .sidebar-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .sidebar-user {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 0 30px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-user img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 15px;
        border: 3px solid rgba(255, 255, 255, 0.2);
        padding: 3px;
    }

    .sidebar-user span {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .sidebar-user small {
        font-size: 14px;
        opacity: 0.8;
    }

    .sidebar-nav {
        padding: 20px;
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        color: var(--light-color);
        padding: 15px;
        font-size: 16px;
        margin-bottom: 8px;
        border-radius: 10px;
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar-nav a i {
        margin-right: 15px;
        width: 24px;
        text-align: center;
        font-size: 18px;
    }

    .sidebar-nav a:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(5px);
    }

    /* Improved overlay for better performance */
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        backdrop-filter: blur(3px);
    }

    .overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* For native-like swipe feel */
    body.sidebar-open {
        overflow: hidden;
        position: fixed;
        width: 100%;
    }

    /* Hero Section */
    .hero {
        padding: 40px 0 30px;
        /* Reduced padding for better mobile view */
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
        font-size: 2.2rem;
        /* Slightly reduced for better mobile scaling */
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
        padding: 0 10px;
        /* Added padding for mobile */
    }

    .hero-image {
        margin-top: 30px;
        position: relative;
    }

    .hero-image img {
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        max-height: 350px;
        /* Slightly reduced for better scaling */
        object-fit: cover;
        width: 100%;
    }

    /* Search Form */
    .search-form {
        background-color: var(--light-color);
        padding: 25px 20px;
        /* Adjusted padding for mobile */
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin: 0 auto;
        max-width: 800px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        position: relative;
        transform: translateY(-25px);
        /* Adjusted for better mobile layout */
    }

    .form-group {
        flex: 1 0 200px;
        min-width: 0;
        /* Added to fix flex width issues */
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
        width: 100%;
        /* Full width button for mobile */
        margin-top: 10px;
        justify-content: center;
        display: flex;
        align-items: center;
    }

    /* Categories Section */
    .section-title {
        text-align: center;
        margin-bottom: 40px;
        font-size: 1.8rem;
        color: var(--dark-color);
        position: relative;
        padding: 0 10px;
        /* Added padding for mobile */
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
        padding: 40px 0;
        /* Adjusted padding */
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
        gap: 20px;
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
        height: 180px;
        /* Reduced for better proportion on mobile */
        overflow: hidden;
    }

    .category-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
    }

    .category-card:hover .category-img img {
        transform: scale(1.05);
    }

    .category-content {
        padding: 20px;
        text-align: center;
    }

    .category-content h3 {
        margin-bottom: 10px;
        color: var(--dark-color);
        font-size: 1.2rem;
        /* Slightly reduced for mobile */
    }

    .category-content p {
        color: #666;
        margin-bottom: 15px;
    }

    /* Features Section */
    .features {
        padding: 40px 0;
        /* Adjusted padding */
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        /* Reduced min width for mobile */
        gap: 20px;
    }

    .feature-card {
        text-align: center;
        padding: 25px 15px;
        /* Adjusted padding */
        background-color: var(--light-color);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        transition: var(--transition);
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .feature-icon {
        font-size: 2.2rem;
        /* Slightly reduced for mobile */
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .feature-title {
        font-size: 1.1rem;
        /* Slightly reduced for mobile */
        margin-bottom: 10px;
        color: var(--dark-color);
    }

    .feature-text {
        color: #666;
        font-size: 0.95rem;
        /* Added for better mobile reading */
    }

    /* Footer */
    .footer {
        background-color: var(--dark-color);
        color: var(--light-color);
        padding: 40px 0 20px;
        /* Reduced padding */
        margin-top: 40px;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        /* Reduced for mobile */
        gap: 25px;
        width: 90%;
        max-width: 1200px;
        margin: 0 auto 30px;
    }

    .footer-col h4 {
        margin-bottom: 15px;
        font-size: 1.1rem;
        /* Reduced for mobile */
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
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Media Queries - Enhanced for Better Responsiveness */
    @media (max-width: 992px) {
        .hero h2 {
            font-size: 1.8rem;
        }

        .hero p {
            font-size: 1rem;
        }

        .section-title {
            font-size: 1.6rem;
        }

        .feature-card {
            padding: 20px 15px;
        }
    }

    @media (max-width: 768px) {

        .nav-links,
        .user-profile .sign-out,
        .user-profile span {
            display: none;
        }

        .menu-btn {
            display: block;
        }

        .search-form {
            flex-direction: column;
            gap: 10px;
            padding: 15px;
            margin-top: -20px;
            transform: translateY(-15px);
        }

        .form-group {
            flex: 1 0 100%;
        }

        .hero h2 {
            font-size: 1.5rem;
            padding: 0 10px;
        }

        .hero p {
            font-size: 0.95rem;
            padding: 0 15px;
        }

        .hero-image img {
            max-height: 300px;
        }

        .category-img {
            height: 160px;
        }

        .footer-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
    }

    @media (max-width: 576px) {
        .container {
            width: 95%;
            padding: 0 10px;
        }

        .hero {
            padding: 30px 0 20px;
        }

        .hero h2 {
            font-size: 1.4rem;
            margin-bottom: 15px;
        }

        .hero p {
            margin-bottom: 20px;
        }

        .search-form {
            padding: 15px 12px;
        }

        .form-control {
            padding: 10px 12px;
        }

        .btn {
            padding: 10px 18px;
            font-size: 0.95rem;
        }

        .category-content {
            padding: 15px 10px;
        }

        .category-img {
            height: 150px;
        }

        .features-grid {
            grid-template-columns: 1fr;
        }

        .footer-grid {
            grid-template-columns: 1fr 1fr;
        }

        .logo-container h1 {
            font-size: 1.3rem;
        }

        .copyright {
            font-size: 0.8rem;
        }
    }

    /* Added for even smaller devices */
    @media (max-width: 420px) {
        .hero h2 {
            font-size: 1.3rem;
        }

        .category-grid {
            grid-template-columns: 1fr;
        }

        .footer-grid {
            grid-template-columns: 1fr;
        }

        .sidebar {
            width: 260px;
        }
    }
    </style>
</head>

<body>

    <!-- Updated Sidebar HTML Structure -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-close" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </div>
        <div class="sidebar-user">
            <img src="assets/images/user.jpeg" alt="User">
            <span><?php echo $username; ?></span>
            <small><?php echo $email; ?></small>
        </div>
        <div class="sidebar-nav">
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="mybookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
            <a href="myuploads.php"><i class="fas fa-upload"></i> My Uploads</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            <a href="index.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
        </div>
    </div>

    <!-- Updated Overlay -->
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo-container">
                    <img src="assets/images/logo.png" alt="MetaTicket Logo">
                    <h1>MetaTicket</h1>
                </div>
                <div class="nav-links">
                    <a href="profile.php">Profile</a>
                    <a href="mybookings.php">My Bookings</a>
                    <a href="myuploads.php">My Uploads</a>
                    <a href="wallet.php">Wallet</a>
                </div>
                <div class="user-profile">
                    <a href="profile.php">
                        <img src="assets/images/user.jpeg" alt="User">
                    </a>
                    <span><?php echo $username; ?><br><small><?php echo $email; ?></small></span>
                    <!-- Sign Out Button -->
                    <a href="index.php" class="sign-out">
                        <i class="fas fa-sign-out-alt"></i> Sign Out
                    </a>
                    <div class="menu-btn" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </div>
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
                <a href="busbuy.php" class="btn">Start Booking Now</a>
            </div>
            <div class="hero-image">
                <img src="assets/images/dashboard_pic.jpg" alt="Travel Booking">
            </div>
        </div>
    </section>

    <!-- Search Form -->
    <div class="container">
        <form class="search-form" method="POST" action="search_bus.php">
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
                        <a href="bushome.php">
                            <img src="assets/images/busPic.jpg" alt="Bus Travel">
                        </a>
                    </div>
                    <div class="category-content">
                        <h3>Bus Booking</h3>
                        <p>Comfortable buses with modern amenities</p>
                        <a href="bushome.php" class="btn">Book Now</a>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">
                        <a href="xticket.php">
                            <img src="assets/images/ticket.jpg" alt="Available Tickets">
                        </a>
                    </div>
                    <div class="category-content">
                        <h3>Available Tickets</h3>
                        <p>Browse all available tickets for your journey</p>
                        <a href="xticket.php" class="btn">Check Availability</a>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-img">
                        <a href="explore_routes.php">
                            <img src="assets/images/route.jpg" alt="Express Routes">
                        </a>
                    </div>
                    <div class="category-content">
                        <h3>Express Routes</h3>
                        <p>Get to your destination faster with express service</p>
                        <a href="explore_routes.php" class="btn">Explore Routes</a>
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
                    <li><a href="bushome.php"><i class="fas fa-bus"></i> Bus</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><i class="fas fa-share-alt"></i> Follow Us</h4>
                <p>Connect with us on social media for updates and offers</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 MetaTicket. All Rights Reserved | Privacy Policy | Terms & Conditions</p>
        </div>
    </footer>

    <!-- JavaScript for Sidebar Toggle and Enhanced Mobile Experience -->
    <script>
    // Date Input - Set Min Date to Today
    const dateInput = document.getElementById('date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.min = today;
    dateInput.value = today;


    // Enhanced mobile sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const body = document.body;
    let touchStartX = 0;
    let touchEndX = 0;
    let isSwiping = false;

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        body.classList.toggle('sidebar-open');
    }

    // Improved touch handling for sidebar
    document.addEventListener('touchstart', function(event) {
        touchStartX = event.changedTouches[0].screenX;

        // Only initiate swipe if near the edge when closed or anywhere when open
        if ((touchStartX < 50 && !sidebar.classList.contains('active')) ||
            sidebar.classList.contains('active')) {
            isSwiping = true;
        }
    }, {
        passive: true
    });

    document.addEventListener('touchmove', function(event) {
        if (!isSwiping) return;

        const currentX = event.changedTouches[0].screenX;
        const diff = currentX - touchStartX;

        // Handle open sidebar drag
        if (sidebar.classList.contains('active')) {
            if (diff < 0) {
                // Don't let it go beyond the closed position
                const newPosition = Math.max(0, 300 + diff);
                sidebar.style.transform = `translateX(${newPosition}px)`;

                // Adjust overlay opacity for visual feedback
                const progress = newPosition / 300;
                overlay.style.opacity = progress;
            }
        }
        // Handle closed sidebar drag
        else if (diff > 0 && touchStartX < 50) {
            // Don't let it go beyond the fully open position
            const newPosition = Math.min(diff, 300);
            sidebar.style.transform = `translateX(${newPosition}px)`;

            // Show overlay with matching opacity for visual feedback
            overlay.style.visibility = 'visible';
            const progress = newPosition / 300;
            overlay.style.opacity = progress;
        }
    }, {
        passive: true
    });

    document.addEventListener('touchend', function(event) {
        if (!isSwiping) return;

        touchEndX = event.changedTouches[0].screenX;
        const swipeDistance = touchEndX - touchStartX;

        // Reset transform for CSS transitions to work
        sidebar.style.transform = '';
        overlay.style.opacity = '';

        // Right swipe to open sidebar (when closed)
        if (swipeDistance > 100 && !sidebar.classList.contains('active')) {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            body.classList.add('sidebar-open');
        }

        // Left swipe to close sidebar (when open)
        else if (swipeDistance < -50 && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            body.classList.remove('sidebar-open');
        }
        // If not enough distance to trigger action, reset to original state
        else {
            if (sidebar.classList.contains('active')) {
                sidebar.classList.add('active');
                overlay.classList.add('active');
            } else {
                overlay.style.visibility = '';
            }
        }

        isSwiping = false;
    }, {
        passive: true
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            body.classList.remove('sidebar-open');
        }
    });

    // Prevent scrolling on the body when sidebar is open
    overlay.addEventListener('touchmove', function(e) {
        e.preventDefault();
    }, {
        passive: false
    });
    </script>

</body>

</html>
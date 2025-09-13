<?php
include 'config.php';
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - MetaTicket</title>
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

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100%;
            background: linear-gradient(to bottom, #1a73e8, #0d47a1);
            color: var(--light-color);
            transition: var(--transition);
            z-index: 1000;
            padding-top: 60px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
        }

        .sidebar-user {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-user img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .sidebar-nav {
            padding: 20px;
        }

        .sidebar-nav a {
            display: block;
            color: var(--light-color);
            padding: 15px 0;
            font-size: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
        }

        .sidebar-nav a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar-nav a:hover {
            color: var(--primary-color);
            padding-left: 5px;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .overlay.active {
            display: block;
        }

        /* About Section */
        .about-section {
            padding: 60px 0;
        }

        .about-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .about-header h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .about-header p {
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
            color: #666;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }

        .about-card {
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            padding: 30px;
            text-align: center;
        }

        .about-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .about-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .about-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--dark-color);
        }

        .about-card p {
            color: #666;
        }

        .team-section {
            margin-top: 60px;
        }

        .team-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .team-header h2 {
            font-size: 2rem;
            color: var(--dark-color);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .team-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .team-header p {
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
            color: #666;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .team-member {
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .team-img {
            height: 250px;
            overflow: hidden;
        }

        .team-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .team-member:hover .team-img img {
            transform: scale(1.05);
        }

        .team-info {
            padding: 20px;
            text-align: center;
        }

        .team-info h3 {
            margin-bottom: 5px;
            color: var(--dark-color);
        }

        .team-info p {
            color: #666;
            font-style: italic;
            margin-bottom: 15px;
        }

        .mission-section {
            background-color: var(--secondary-color);
            padding: 50px 0;
            margin: 60px 0;
            border-radius: var(--border-radius);
        }

        .mission-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 40px;
        }

        .mission-content {
            flex: 1 1 500px;
        }

        .mission-content h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--dark-color);
        }

        .mission-content p {
            margin-bottom: 15px;
            color: #666;
        }

        .mission-image {
            flex: 1 1 400px;
        }

        .mission-image img {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        /* Footer */
        .footer {
            background-color: var(--dark-color);
            color: var(--light-color);
            padding: 50px 0 20px;
            margin-top: 50px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto 40px;
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
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Media Queries */
        @media (max-width: 992px) {
            .about-header h2 {
                font-size: 2rem;
            }

            .mission-container {
                flex-direction: column;
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

            .about-header h2 {
                font-size: 1.8rem;
            }

            .about-grid {
                gap: 20px;
            }

            .team-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        @media (max-width: 576px) {
            .about-header h2 {
                font-size: 1.5rem;
            }

            .about-card {
                padding: 20px;
            }

            .team-img {
                height: 200px;
            }

            .mission-content h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar for Mobile -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-close" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </div>
        <div class="sidebar-user">
            
        </div>
        <div class="sidebar-nav">
            
        <a href="privacy.php"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
            <a href="FAQ.php"><i class="fas fa-question-circle"></i> FAQ</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
        </div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo-container">
                    <img src="assets/images/logo.png" alt="MetaTicket Logo">
                    <h1>MetaTicket</h1>
                </div>
                
                <div class="user-profile">
                    
                </div>
                <div class="menu-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="nav-links">
                <a href="privacy.php"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
            <a href="FAQ.php"><i class="fas fa-question-circle"></i> FAQ</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-header">
                <h2><i class="fas fa-info-circle"></i> About MetaTicket</h2>
                <p>Your trusted platform for hassle-free travel booking and ticket exchange</p>
            </div>

            <div class="about-grid">
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3>Our Story</h3>
                    <p>Founded in 2024, MetaTicket was born out of the need to simplify travel booking and ticket exchange. We started as a small team with a big vision to transform how people travel.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Our Mission</h3>
                    <p>To create a seamless, user-friendly platform that connects travelers with reliable transportation services while ensuring security, convenience, and exceptional value.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Our Vision</h3>
                    <p>To become the leading global platform for travel booking and ticket exchange, transforming the travel experience for millions of people around the world.</p>
                </div>
            </div>

            <div class="mission-section">
                <div class="container">
                    <div class="mission-container">
                    <div class="mission-content">
                            <h2><i class="fas fa-star"></i> What Makes Us Different</h2>
                            <p><i class="fas fa-user-check"></i> At MetaTicket, we believe in putting the customer first. Our platform is designed with you in mind, making it easy to book travel tickets, exchange unused tickets, and explore new destinations.</p>
                            <p><i class="fas fa-handshake"></i> We partner with trusted transportation providers to ensure you have access to quality services at competitive prices. Our secure payment system, user-friendly interface, and dedicated customer support team are all part of our commitment to providing you with the best possible experience.</p>
                            <p><i class="fas fa-road"></i> Whether you're planning a business trip, family vacation, or last-minute getaway, MetaTicket is here to make your journey smoother and more enjoyable.</p>
                        </div>
                        <div class="mission-image">
                            <img src="assets/images/dashboard_pic.jpg" alt="Our Mission">
                        </div>
                    </div>
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
                    <li><a href="#"><i class="fas fa-bus"></i> Bus</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><i class="fas fa-share-alt"></i> Follow Us</h4>
                <p>Connect with us on social media for updates and offers</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy;  2025 MetaTicket. All Rights Reserved | Privacy Policy | Terms & Conditions</p>
        </div>
    </footer>

    <!-- JavaScript for Sidebar Toggle -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');
        }
    </script>

</body>

</html>
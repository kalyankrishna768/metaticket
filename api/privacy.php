<?php
include 'config.php';
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - MetaTicket</title>
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

        /* Privacy Policy Section */
        .privacy-policy {
            padding: 60px 0;
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            margin: 30px auto;
            box-shadow: var(--shadow);
        }

        .policy-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .policy-header h2 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .policy-header p {
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        .policy-section {
            margin-bottom: 30px;
        }

        .policy-section h3 {
            color: var(--dark-color);
            margin-bottom: 15px;
            font-size: 1.3rem;
            border-left: 4px solid var(--primary-color);
            padding-left: 15px;
        }

        .policy-section p {
            margin-bottom: 15px;
            color: #444;
        }

        .policy-section ul {
            list-style-type: disc;
            margin-left: 30px;
            margin-bottom: 15px;
        }

        .policy-section ul li {
            margin-bottom: 10px;
            color: #444;
        }

        .last-updated {
            font-style: italic;
            color: #777;
            text-align: center;
            margin-top: 40px;
            font-size: 0.9rem;
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
            .policy-header h2 {
                font-size: 1.8rem;
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

            .privacy-policy {
                padding: 30px 20px;
                margin: 20px 15px;
            }

            .policy-header h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .policy-section h3 {
                font-size: 1.2rem;
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
            
        <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
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
                <div class="policy-header">
                
            </div>
            <div class="menu-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="nav-links">
                <a href="FAQ.php"><i class="fas fa-question-circle"></i> FAQ</a>
                    <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
                    <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Privacy Policy Section -->
    <section class="privacy-policy">
        <div class="container">
            <div class="policy-header">
            <h2><i class="fas fa-shield-alt"></i> Privacy Policy</h2>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-info-circle"></i> Information We Collect</h3>
                <p>We collect information to provide and improve our ticket booking and exchange services. The types of information we collect include:</p>
                <ul>
                    <li><strong>Personal Information:</strong> Name, email address, phone number, and billing information.</li>
                    <li><strong>Account Information:</strong> Username, password, and profile details.</li>
                    <li><strong>Transaction Information:</strong> Details about tickets purchased, exchanged, or sold.</li>
                    <li><strong>Travel Information:</strong> Journey details, preferences, and frequency of travel.</li>
                    <li><strong>Device Information:</strong> IP address, browser type, device type, and operating system.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-cogs"></i> How We Use Your Information</h3>
                <p>We use the information we collect for various purposes, including:</p>
                <ul>
                    <li>Processing and confirming your ticket bookings and exchanges</li>
                    <li>Managing your account and providing customer support</li>
                    <li>Sending booking confirmations, updates, and alerts</li>
                    <li>Improving our services and user experience</li>
                    <li>Personalizing content and recommendations</li>
                    <li>Detecting and preventing fraudulent activities</li>
                    <li>Complying with legal obligations</li>
                </ul>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-share-alt"></i> Information Sharing and Disclosure</h3>
                <p>We may share your information with:</p>
                <ul>
                    <li><strong>Service Providers:</strong> Third parties that help us operate our platform (payment processors, cloud services, etc.)</li>
                    <li><strong>Travel Partners:</strong> Bus operators and other service providers necessary to complete your booking</li>
                    <li><strong>Legal Authorities:</strong> When required by law or to protect our rights</li>
                </ul>
                <p>We do not sell your personal information to third parties for marketing purposes.</p>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-exchange-alt"></i> Ticket Exchange Platform Specific Policies</h3>
                <p>For our ticket exchange service, please note the following:</p>
                <ul>
                    <li>When you list a ticket for exchange, certain information (such as seat details and date) will be visible to potential buyers.</li>
                    <li>Personal contact information is only shared between parties after a transaction is confirmed.</li>
                    <li>All communications between buyers and sellers should be conducted through our platform to ensure safety and privacy.</li>
                    <li>MetaTicket stores records of exchanges for security and dispute resolution purposes.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-lock"></i> Data Security</h3>
                <p>We implement robust security measures to protect your personal information from unauthorized access, disclosure, alteration, or destruction. These measures include:</p>
                <ul>
                    <li>Encryption of sensitive data</li>
                    <li>Secure payment processing</li>
                    <li>Regular security assessments</li>
                    <li>Access controls for our systems</li>
                </ul>
                <p>However, no method of transmission over the Internet or electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your personal information, we cannot guarantee its absolute security.</p>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-user-check"></i> Your Rights and Choices</h3>
                <p>You have certain rights regarding your personal information:</p>
                <ul>
                    <li>Access and view personal information we have about you</li>
                    <li>Update or correct inaccuracies in your information</li>
                    <li>Request deletion of your data (subject to legal requirements)</li>
                    <li>Opt-out of marketing communications</li>
                    <li>Set preferences for cookies and tracking technologies</li>
                </ul>
                <p>To exercise these rights, please contact us through the methods listed at the end of this policy.</p>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-cookie"></i> Cookies and Tracking Technologies</h3>
                <p>We use cookies and similar technologies to enhance your experience on our platform. These technologies help us:</p>
                <ul>
                    <li>Remember your preferences and settings</li>
                    <li>Understand how you use our services</li>
                    <li>Authenticate users and prevent fraud</li>
                    <li>Improve performance and functionality</li>
                </ul>
                <p>You can control cookies through your browser settings. However, disabling cookies may limit your ability to use certain features of our platform.</p>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-child"></i> Children's Privacy</h3>
                <p>Our services are not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If you are a parent or guardian and believe your child has provided us with personal information, please contact us, and we will take steps to delete such information.</p>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-sync-alt"></i> Changes to This Privacy Policy</h3>
                <p>We may update this Privacy Policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. We will notify you of any material changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
                <p>We encourage you to review this Privacy Policy periodically to stay informed about how we are protecting your information.</p>
            </div>

            <div class="policy-section">
                <h3><i class="fas fa-envelope"></i> Contact Us</h3>
                <p>If you have any questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us at:</p>
                <p><strong>Email:</strong> privacy@metaticket.com</p>
                <p><strong>Phone:</strong> +1 (555) 123-4567</p>
                <p><strong>Address:</strong> 123 Booking Street, Travel City, TC 10101</p>
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
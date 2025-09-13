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
    <title>FAQ - MetaTicket</title>
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

        /* FAQ Section */
        .faq-section {
            padding: 60px 0;
        }

        .faq-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .faq-header h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .faq-header p {
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
            color: #666;
        }

        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-categories {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 40px;
        }

        .category-btn {
            padding: 10px 20px;
            background-color: var(--secondary-color);
            color: var(--dark-color);
            border-radius: 30px;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid #ddd;
        }

        .category-btn.active,
        .category-btn:hover {
            background-color: var(--primary-color);
            color: var(--light-color);
            border-color: var(--primary-color);
        }

        .faq-item {
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .faq-question {
            padding: 20px;
            cursor: pointer;
            position: relative;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-question i {
            transition: var(--transition);
        }

        .faq-question.active i {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: var(--transition);
            border-top: 0 solid #eee;
        }

        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 500px;
            border-top: 1px solid #eee;
        }

        .search-container {
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
        }

        .search-box {
            display: flex;
            width: 100%;
            max-width: 500px;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .search-input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            outline: none;
            font-size: 1rem;
        }

        .search-btn {
            background-color: var(--primary-color);
            color: var(--light-color);
            border: none;
            padding: 0 20px;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-btn:hover {
            background-color: #0d5bba;
        }

        .category-content {
            display: none;
        }

        .category-content.active {
            display: block;
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
            .faq-header h2 {
                font-size: 2rem;
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

            .faq-header h2 {
                font-size: 1.8rem;
            }

            .category-btn {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .faq-header h2 {
                font-size: 1.5rem;
            }

            .faq-question {
                padding: 15px;
                font-size: 0.95rem;
            }

            .faq-item.active .faq-answer {
                padding: 15px;
            }

            .search-input {
                padding: 12px 15px;
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
            <a href="privacy.php"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
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
                
                    <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
                    <a href="privacy.php"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
                    <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="faq-header">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to the most common questions about our platform</p>
            </div>

            <div class="faq-container">
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" class="search-input" id="searchFaq" placeholder="Search for questions...">
                        <button class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="faq-categories">
                <button class="category-btn active" data-category="general"><i class="fas fa-globe"></i> General</button>
                    <button class="category-btn" data-category="booking"><i class="fas fa-ticket-alt"></i> Booking</button>
                    <button class="category-btn" data-category="payment"><i class="fas fa-credit-card"></i> Payment</button>
                    <button class="category-btn" data-category="account"><i class="fas fa-user"></i> Account</button>
                    <button class="category-btn" data-category="refunds"><i class="fas fa-undo"></i> Refunds & Cancellations</button>
                </div>

                <!-- General FAQ -->
                <div class="category-content active" id="general">
                    <div class="faq-item">
                        <div class="faq-question">
                            What is MetaTicket?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>MetaTicket is a comprehensive online platform that allows users to book travel tickets,
                                exchange unused tickets, and explore new destinations. We partner with trusted
                                transportation providers to ensure you have access to quality services at competitive
                                prices.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do I contact customer support?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>You can reach our customer support team through multiple channels:</p>
                            <ul>
                                <li>Email: support@metaticket.com</li>
                                <li>Phone: 1-800-META-TIX (1-800-638-2849)</li>
                                <li>Live Chat: Available on our website during business hours</li>
                                <li>Contact Form: Visit our Contact page to submit a request</li>
                            </ul>
                            <p>Our support team is available 24/7 to assist you with any questions or issues.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Is MetaTicket available worldwide?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Currently, MetaTicket services are available in india only. We're constantly expanding our reach to offer our services in
                                more regions. Check our homepage or contact customer support to see if we operate in
                                your area.</p>
                        </div>
                    </div>
                </div>

                <!-- Booking FAQ -->
                <div class="category-content" id="booking">
                    <div class="faq-item">
                        <div class="faq-question">
                            How do I book a ticket?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Booking a ticket on MetaTicket is simple:</p>
                            <ol>
                                <li>Sign in to your MetaTicket account</li>
                                <li>Select your departure and arrival locations</li>
                                <li>Choose your travel dates</li>
                                <li>Browse available options and select your preferred service</li>
                                <li>Fill in passenger details</li>
                                <li>Complete payment</li>
                                <li>Receive your e-ticket via mybookings in your account</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Can I book for someone else?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, you can book tickets for family members, friends, or colleagues. During the booking
                                process, you'll have the option to enter passenger details that differ from your account
                                information. Just make sure the passenger's name matches their ID as it will be verified
                                during travel.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How far in advance can I book tickets?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Most services on our platform allow bookings up to 3 months in advance. Some seasonal or
                                special services may have different booking windows. We recommend booking early for
                                popular routes and travel dates to secure the best prices and availability.</p>
                        </div>
                    </div>
                </div>

                <!-- Payment FAQ -->
                <div class="category-content" id="payment">
                    <div class="faq-item">
                        <div class="faq-question">
                            What payment methods do you accept?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>MetaTicket accepts various payment methods:</p>
                            <ul>
                                <li>Credit/Debit Cards (Visa, MasterCard, American Express, Discover)</li>
                                <li>PayPal</li>
                                <li>Google Pay</li>
                                <li>Bank Transfer (for select regions)</li>
                                <li>MetaTicket Wallet (store credit)</li>
                            </ul>
                            <p>First you should add money to wallet, then have to book your tickets.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Is my payment information secure?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Absolutely. MetaTicket employs industry-standard encryption and security protocols to
                                protect your payment information. We are PCI DSS compliant and do not store your
                                complete credit card details on our servers. All payment processing is handled through
                                secure, trusted payment processors.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Can I pay in installments?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>No, the feature was not available.</p>
                        </div>
                    </div>
                </div>

                <!-- Account FAQ -->
                <div class="category-content" id="account">
                    <div class="faq-item">
                        <div class="faq-question">
                            How do I create an account?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Creating a MetaTicket account is easy:</p>
                            <ol>
                                <li>Click on "Register" at the top of the page</li>
                                <li>Enter your email address and create a password</li>
                                <li>Fill in your personal details</li>
                                <li>Verify your email address</li>
                                <li>Complete your profile by adding additional information (optional)</li>
                            </ol>
                            <p>You can also register using your Google, Facebook, or Apple account for a faster signup
                                process.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do I reset my password?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Sorry! the feature was not available. if you forgot your password, then you creat a new account</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            Can I have multiple profiles under one account?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>While you cannot have multiple profiles under one account, you can save passenger details
                                for frequent travelers in your account. This allows you to quickly select saved
                                passengers when making bookings without creating separate accounts.</p>
                        </div>
                    </div>
                </div>

                <!-- Refunds FAQ -->
                <div class="category-content" id="refunds">
                    <div class="faq-item">
                        <div class="faq-question">
                            What is your cancellation policy?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Our cancellation policy varies depending on the service provider and ticket type.
                                Generally:</p>
                            <ul>
                                <li>Flexible tickets: Full or partial refund available up to 24 hours before departure
                                </li>
                                <li>Standard tickets: Partial refund available up to 24 hours before departure</li>
                                <li>Promotional or discounted tickets: May have limited or no refund options</li>
                            </ul>
                            <p>The specific cancellation terms for your booking will be clearly displayed during the
                                booking process and in your confirmation email.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How do I cancel my booking?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>To cancel a booking:</p>
                            <ol>
                                <li>Log in to your MetaTicket account</li>
                                <li>Go to "My Bookings"</li>
                                <li>Select the booking you wish to cancel</li>
                                <li>Click on "Cancel Booking"</li>
                                <li>Follow the prompts to complete the cancellation</li>
                            </ol>
                            <p>You'll receive an email confirming your cancellation and any applicable refund details.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            How long does it take to process a refund?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>After a successful cancellation, money refund to your wallet immidiately</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle FAQ answers
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', function () {
                    const faqItem = this.parentElement;
                    faqItem.classList.toggle('active');
                    this.classList.toggle('active');
                });
            });

            // Category switching
            const categoryBtns = document.querySelectorAll('.category-btn');
            const categoryContents = document.querySelectorAll('.category-content');

            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    // Remove active class from all buttons
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');

                    // Hide all category contents
                    categoryContents.forEach(content => content.classList.remove('active'));

                    // Show selected category content
                    const category = this.getAttribute('data-category');
                    document.getElementById(category).classList.add('active');
                });
            });

            // Search functionality
            const searchInput = document.getElementById('searchFaq');
            const faqItems = document.querySelectorAll('.faq-item');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                if (searchTerm.length > 2) {
                    // Hide all category sections initially
                    categoryContents.forEach(content => content.classList.remove('active'));

                    // Reset active state on category buttons
                    categoryBtns.forEach(btn => btn.classList.remove('active'));

                    let found = false;

                    faqItems.forEach(item => {
                        const question = item.querySelector('.faq-question').textContent.toLowerCase();
                        const answer = item.querySelector('.faq-answer').textContent.toLowerCase();

                        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                            // Show parent category
                            const parentCategory = item.closest('.category-content');
                            parentCategory.classList.add('active');

                            // Show this item
                            item.style.display = 'block';

                            // Highlight the active category button
                            const categoryId = parentCategory.getAttribute('id');
                            document.querySelector(`[data-category="${categoryId}"]`).classList.add('active');

                            found = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (!found) {
                        // If no results, show the general category as fallback
                        document.getElementById('general').classList.add('active');
                        document.querySelector('[data-category="general"]').classList.add('active');
                    }
                } else if (searchTerm.length === 0) {
                    // Reset to default view when search is cleared
                    categoryBtns.forEach(btn => btn.classList.remove('active'));
                    document.querySelector('[data-category="general"]').classList.add('active');

                    categoryContents.forEach(content => content.classList.remove('active'));
                    document.getElementById('general').classList.add('active');

                    // Show all items
                    faqItems.forEach(item => {
                        item.style.display = 'block';
                    });
                }
            });

            // Sidebar toggle functionality
            window.toggleSidebar = function () {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('overlay');

                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');

                // Prevent body scrolling when sidebar is open
                if (sidebar.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = 'auto';
                }
            }
        });
    </script>
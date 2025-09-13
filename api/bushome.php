<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaTicket - Your Travel Companion</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a73e8;
            --primary-dark: #0d5bba;
            --accent-color: #34a853;
            --dark-bg: #202124;
            --card-bg: rgba(255, 255, 255, 0.1);
            --footer-bg: #202124;
            --text-color: #ffffff;
            --light-color: #f5f7fa;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition-speed: 0.3s;
            --card-radius: 12px;
            --button-radius: 30px;
            --sidebar-width: 280px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, html {
            height: 100%;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            color: var(--text-color);
            overflow-x: hidden;
            scroll-behavior: smooth;
            background-color: var(--light-color);
            position: relative;
        }

        .background {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), 
                        url('assets/images/back1.jpg') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        /* Glass morphism effect */
        .glass {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
        }

        /* Header and Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            z-index: 1000;
            transition: all var(--transition-speed);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow);
            color: var(--dark-bg);
        }

        .navbar.scrolled {
            padding: 10px 5%;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            height: 40px;
            border-radius: 50%;
            transition: all var(--transition-speed);
        }

        .navbar-brand a {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .navbar a {
            color: var(--dark-bg);
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: all var(--transition-speed);
            position: relative;
            padding: 5px 0;
        }

        .navbar a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--primary-color);
            transition: width var(--transition-speed);
        }

        .navbar a:hover {
            color: var(--primary-color);
        }

        .navbar a:hover:after {
            width: 100%;
        }

        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
            border: none;
            background: transparent;
            color: var(--dark-bg);
            transition: all var(--transition-speed);
        }

        .menu-toggle:hover {
            color: var(--primary-color);
        }

        /* Improved Sidebar for Mobile */
        .sidebar {
            position: fixed;
            top: 0;
            left: calc(var(--sidebar-width) * -1);
            width: var(--sidebar-width);
            height: 100%;
            background: linear-gradient(to bottom, #1a73e8, #0d47a1);
            color: var(--light-color);
            transition: transform 0.3s ease-out;
            z-index: 1001;
            overflow-y: auto;
            transform: translateX(0);
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
        }

        .sidebar.active {
            transform: translateX(var(--sidebar-width));
        }

        .sidebar-header {
            padding: 20px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-logo img {
            height: 35px;
            border-radius: 50%;
        }

        .sidebar-logo span {
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--primary-color);
        }

        .sidebar-close {
            font-size: 24px;
            cursor: pointer;
            color: var(--light-color);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .sidebar-close:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav {
            padding: 15px 0;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--light-color);
            font-size: 16px;
            transition: all 0.2s;
            text-decoration: none;
            border-left: 3px solid transparent;
            position: relative;
        }

        .sidebar-nav a i {
            margin-right: 15px;
            width: 24px;
            font-size: 1.2rem;
            text-align: center;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .sidebar-nav a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 1px;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-footer {
            padding: 15px 20px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            color: #aaa;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            z-index: 1000;
            display: none;
            transition: opacity 0.3s;
            opacity: 0;
        }

        .overlay.active {
            display: block;
            opacity: 1;
        }

        /* Swipe indicator */
        .swipe-indicator {
            position: fixed;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 5px;
            height: 80px;
            background-color: var(--primary-color);
            border-radius: 0 3px 3px 0;
            opacity: 0.7;
            z-index: 999;
            transition: opacity 0.3s;
        }

        /* Hero Section */
        .hero-section {
            text-align: center;
            padding: 150px 20px 80px;
            max-width: 800px;
            width: 90%;
            animation: fadeIn 1s ease;
        }

        .hero-section h1 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            color: var(--text-color);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            font-weight: 700;
        }

        .hero-section p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
            color: #ddd;
        }

        .button-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .button {
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: var(--text-color);
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: var(--button-radius);
            cursor: pointer;
            text-decoration: none;
            box-shadow: var(--shadow);
            transition: all var(--transition-speed);
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .button:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s ease;
        }

        .button:hover:before {
            left: 100%;
        }

        .button i {
            font-size: 1.1em;
        }

        /* Features Section */
        .features-section {
            background: linear-gradient(180deg, var(--light-color), #e4e8f0);
            padding: 40px 5%;
            width: 100%;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
            font-size: 1.8rem;
            color: var(--dark-bg);
            position: relative;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            padding: 25px 15px;
            border-radius: var(--card-radius);
            text-align: center;
            transition: all var(--transition-speed);
            background: var(--text-color);
            box-shadow: var(--shadow);
            color: var(--dark-bg);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .feature-card i {
            font-size: 2.2rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .feature-card h3 {
            margin-bottom: 10px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .feature-card p {
            font-size: 0.95rem;
            color: #666;
        }

        /* Footer */
        .footer {
            background: var(--dark-bg);
            color: var(--text-color);
            padding: 40px 20px 20px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 25px;
            margin: 30px 0;
        }

        .footer-links a {
            color: #bbb;
            text-decoration: none;
            transition: all var(--transition-speed);
            font-weight: 500;
            padding: 5px 0;
            position: relative;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .footer-links a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--primary-color);
            transition: width var(--transition-speed);
        }

        .footer-links a:hover:after {
            width: 100%;
        }

        .copyright {
            text-align: center;
            margin-top: 20px;
            color: #bbb;
            font-size: 0.9rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Back to top button */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary-color);
            color: var(--text-color);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: var(--shadow);
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-speed);
            z-index: 999;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: var(--primary-dark);
            transform: translateY(-5px);
        }

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-links {
                display: none;
            }

            .hero-section {
                padding: 120px 15px 40px;
            }

            .hero-section h1 {
                font-size: 1.5rem;
            }

            .hero-section p {
                font-size: 0.95rem;
            }

            .button {
                width: 100%;
                justify-content: center;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
            
            /* Enhanced mobile sidebar visibility */
            .sidebar-nav a {
                padding: 18px 20px; /* Larger touch targets */
                font-size: 18px;
            }
            
            .sidebar-nav a i {
                font-size: 1.3rem;
            }
        }

        @media screen and (max-width: 480px) {
            .navbar-brand img {
                height: 35px;
            }

            .navbar-brand a {
                font-size: 1.3rem;
            }

            .hero-section h1 {
                font-size: 1.4rem;
            }

            .feature-card {
                padding: 20px 15px;
            }
            
            /* Make sidebar take up most of the screen width on small phones */
            :root {
                --sidebar-width: 85vw;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>
    
    <!-- Swipe indicator -->
    <div class="swipe-indicator" id="swipeIndicator"></div>

    <!-- Improved Sidebar for Mobile -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                
            </div>
            <div class="sidebar-close" id="sidebarClose">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Home</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="mybookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
            <a href="myuploads.php"><i class="fas fa-upload"></i> My Uploads</a>
            
        </div>
        
    </div>

    <div class="background">
        <nav class="navbar" id="mainNav">
            <div class="navbar-brand">
                <img src="assets/images/logo.png" alt="MetaTicket Logo">
                <a>MetaTicket</a>
            </div>
            <div class="nav-links" id="navLinks">
                <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="mybookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                <a href="myuploads.php"><i class="fas fa-upload"></i> My Uploads</a>
                
            </div>
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
        </nav>

        <section class="hero-section">
            <h1>Welcome to MetaTicket</h1>
            <p>Your one-stop destination for hassle-free ticket trading. Buy and sell tickets securely with our trusted platform.</p>
            <div class="button-container">
                <a href="xticket.php" class="button">
                    <i class="fas fa-ticket-alt"></i> Available Tickets
                </a>
                <a href="busbuy.php" class="button">
                    <i class="fas fa-shopping-cart"></i> Book Ticket
                </a>
            </div>
        </section>
    </div>

    <!-- Features Section -->
    <section class="features-section">
        <h2 class="section-title">Why Choose Us</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure Transactions</h3>
                <p>All transactions are protected with advanced encryption.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-bolt"></i>
                <h3>Fast & Easy</h3>
                <p>Book or sell tickets in minutes with our streamlined process.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-hand-holding-usd"></i>
                <h3>No Hidden Fees</h3>
                <p>Transparent pricing with no surprise charges.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-users"></i>
                <h3>Verified Community</h3>
                <p>Deal with real people and legitimate tickets.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-headset"></i>
                <h3>24/7 Support</h3>
                <p>Our team is available round the clock.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p class="copyright">© 2025 MetaTicket. All rights reserved.</p>
    </footer>

    <a href="#" class="back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </a>

    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarClose = document.getElementById('sidebarClose');
        const swipeIndicator = document.getElementById('swipeIndicator');
        
        // Toggle sidebar
        function toggleSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Hide swipe indicator when sidebar is open
            if (sidebar.classList.contains('active')) {
                swipeIndicator.style.opacity = '0';
            } else {
                setTimeout(() => {
                    swipeIndicator.style.opacity = '0.7';
                }, 300);
            }
        }
        
        menuToggle.addEventListener('click', toggleSidebar);
        sidebarClose.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        // Close menu when clicking outside
        document.addEventListener('click', (event) => {
            if (sidebar.classList.contains('active') && 
                !sidebar.contains(event.target) && 
                !menuToggle.contains(event.target)) {
                toggleSidebar();
            }
        });

        // Scroll effects
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('mainNav');
            const backToTop = document.getElementById('backToTop');
            navbar.classList.toggle('scrolled', window.scrollY > 50);
            backToTop.classList.toggle('visible', window.scrollY > 300);
            
            // Hide swipe indicator when scrolling down
            if (window.scrollY > 100) {
                swipeIndicator.style.opacity = '0';
            } else if (!sidebar.classList.contains('active')) {
                swipeIndicator.style.opacity = '0.7';
            }
        });

        // Back to top functionality
        document.getElementById('backToTop').addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Improved Touch swipe detection for sidebar
        let touchStartX = 0;
        let touchEndX = 0;
        let touchStartY = 0;
        let touchEndY = 0;
        let isDragging = false;
        
        // Document touch events for opening sidebar with swipe
        document.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            
            // Only initiate dragging if starting from left edge
            if (touchStartX < 50 && !sidebar.classList.contains('active')) {
                isDragging = true;
                sidebar.style.transition = 'none';
            }
        });

        document.addEventListener('touchmove', (e) => {
            if (!isDragging || sidebar.classList.contains('active')) return;
            
            const currentX = e.touches[0].clientX;
            const currentY = e.touches[0].clientY;
            
            // Calculate horizontal and vertical movement
            const diffX = currentX - touchStartX;
            const diffY = currentY - touchStartY;
            
            // If more vertical than horizontal movement, cancel the sidebar drag
            if (Math.abs(diffY) > Math.abs(diffX) * 1.5) {
                isDragging = false;
                sidebar.style.transform = 'translateX(0)';
                sidebar.style.transition = 'transform 0.3s ease-out';
                return;
            }
            
            // Calculate how much to move the sidebar
            const translateX = Math.max(0, Math.min(diffX, parseInt(getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width'))));
            
            // Apply the transform
            sidebar.style.transform = `translateX(${translateX}px)`;
        });

        document.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            
            isDragging = false;
            sidebar.style.transition = 'transform 0.3s ease-out';
            
            // Determine if swipe was significant enough
            const touchEndX = e.changedTouches[0].clientX;
            const swipeDistance = touchEndX - touchStartX;
            const threshold = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width')) * 0.3;
            
            if (swipeDistance > threshold) {
                toggleSidebar();
            } else {
                sidebar.style.transform = 'translateX(0)';
            }
        });

        // Enhanced closing sidebar with right-to-left swipe
        sidebar.addEventListener('touchstart', (e) => {
            if (!sidebar.classList.contains('active')) return;
            
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            isDragging = true;
            sidebar.style.transition = 'none';
        });

        sidebar.addEventListener('touchmove', (e) => {
            if (!isDragging || !sidebar.classList.contains('active')) return;
            
            const currentX = e.touches[0].clientX;
            const currentY = e.touches[0].clientY;
            
            // Calculate horizontal and vertical movement
            const diffX = currentX - touchStartX;
            const diffY = currentY - touchStartY;
            
            // If more vertical than horizontal movement, cancel the swipe
            if (Math.abs(diffY) > Math.abs(diffX) * 1.5) {
                return;
            }
            
            // Only allow swiping left (negative diffX)
            if (diffX > 0) return;
            
            // Calculate transform value - don't move sidebar beyond its width
            const translateX = Math.max(diffX, -parseInt(getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width')));
            
            sidebar.style.transform = `translateX(${translateX}px)`;
        });

        sidebar.addEventListener('touchend', (e) => {
            if (!isDragging || !sidebar.classList.contains('active')) return;
            
            isDragging = false;
            sidebar.style.transition = 'transform 0.3s ease-out';
            
            touchEndX = e.changedTouches[0].clientX;
            const swipeDistance = touchStartX - touchEndX;
            const threshold = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width')) * 0.3;
            
            if (swipeDistance > threshold) {
                toggleSidebar();
            } else {
                sidebar.style.transform = 'translateX(0)';
            }
        });
        
        // Add active class to current page in sidebar
        function setActivePage() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.sidebar-nav a');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath.split('/').pop()) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }
        
        // Call on page load
        setActivePage();
        
        // Haptic feedback function (vibration API)
        function vibrate(duration = 20) {
            if (navigator.vibrate) {
                navigator.vibrate(duration);
            }
        }
        
        // Add haptic feedback to sidebar interactions
        menuToggle.addEventListener('click', () => vibrate());
        sidebarClose.addEventListener('click', () => vibrate());
        
        // Optional: add feedback when swiping sidebar
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', () => vibrate(30));
        });
        
        // Flash swipe indicator periodically for discovery
        function pulseSwipeIndicator() {
            if (sidebar.classList.contains('active') || window.scrollY > 100) return;
            
            swipeIndicator.style.opacity = '1';
            setTimeout(() => {
                swipeIndicator.style.opacity = '0.7';
            }, 800);
        }
        
        // Pulse the indicator a few times when the page loads
        for (let i = 0; i < 3; i++) {
            setTimeout(pulseSwipeIndicator, 1500 + (i * 2000));
        }
    </script>
</body>
</html>
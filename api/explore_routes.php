<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Initialize variables
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : 'all';

// Fetch routes from database - alternative approach using subquery to eliminate duplicates
$query = "SELECT r.id, r.from_location, r.to_location, r.departure_date, r.departure_time, 
          r.distance, r.duration, r.base_fare, r.status, r.bus_id 
          FROM routes r 
          WHERE 1=1";

// Apply date filters if provided
if ($filter_date == 'today') {
    $query .= " AND r.departure_date = CURDATE()";
} elseif ($filter_date == 'tomorrow') {
    $query .= " AND r.departure_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
} else {
    $query .= " AND r.departure_date >= '$date'";
}

// Add subquery to get only the first route for each date, from_location, to_location combination
$query .= " AND r.id IN (
                SELECT MIN(id) 
                FROM routes 
                GROUP BY departure_date, from_location, to_location
            )";

$query .= " ORDER BY r.departure_date, r.departure_time";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaTicket - Explore Routes</title>
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
            --sidebar-width: 280px; /* Added for touch events */
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

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: calc(var(--sidebar-width) * -1);
            width: var(--sidebar-width);
            height: 100%;
            background-color: var(--dark-color);
            color: var(--light-color);
            transition: var(--transition);
            z-index: 1001;
            padding-top: 60px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            transform: translateX(0); /* Added for smooth touch sliding */
        }

        .sidebar.active {
            left: 0;
            transform: translateX(0);
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

        .btn-small {
            padding: 8px 16px;
            font-size: 0.9rem;
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
            left: -280px;
            width: 280px;
            height: 100%;
            background: linear-gradient(to bottom, #1a73e8, #0d47a1);
            color: var(--light-color);
            transition: var(--transition);
            z-index: 1001;
            padding-top: 60px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
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
            padding: 5px;
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
            z-index: 1000;
            display: none;
        }

        .overlay.active {
            display: block;
        }

        /* Page title */
        .page-title {
            background-color: var(--primary-color);
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .page-title h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .page-title p {
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
        }

        /* Filter section */
        .filter-section {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .form-group {
            flex: 1 0 200px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .date-filter-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .date-filter-option {
            flex: 1 0 80px;
        }

        .date-filter-option input[type="radio"] {
            display: none;
        }

        .date-filter-option label {
            display: block;
            text-align: center;
            padding: 12px;
            background-color: #f5f7fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        .date-filter-option input[type="radio"]:checked+label {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Routes cards - always hidden now */
        .routes-cards {
            display: none; /* Always hidden regardless of screen size */
        }

        /* Routes table */
        .routes-container {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow);
            overflow-x: auto; /* Enable horizontal scrolling for small screens */
        }

        .routes-table {
            width: 100%;
            border-collapse: collapse;
            display: table; /* Always display the table regardless of screen size */
        }

        .routes-table th {
            background-color: #f5f7fa;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark-color);
            border-bottom: 2px solid #eee;
        }

        .routes-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .routes-table tr:last-child td {
            border-bottom: none;
        }

        .routes-table tr:hover {
            background-color: #f8f9fa;
        }

        .route-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-scheduled {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-delayed {
            background-color: #fff8e1;
            color: #f57c00;
        }

        .status-cancelled {
            background-color: #ffebee;
            color: #c62828;
        }

        .route-actions {
            display: flex;
            gap: 10px;
        }

        /* No routes message */
        .no-routes {
            text-align: center;
            padding: 40px 0;
            color: #666;
        }

        .no-routes i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        /* Footer */
        .footer {
            background-color: var(--dark-color);
            color: var(--light-color);
            padding: 40px 0 20px;
            margin-top: 40px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 25px;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto 30px;
        }

        .footer-col h4 {
            margin-bottom: 15px;
            font-size: 1.1rem;
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

        /* Responsive - MODIFIED to keep consistent table view */
        @media (max-width: 768px) {
            .container {
                width: 100%; /* Use full width on mobile */
                padding: 0 10px;
            }
            
            .nav-links {
                display: none; /* Hide navigation links on mobile */
            }
            
            .user-profile .sign-out,
            .user-profile span {
                display: none;
            }

            .menu-btn {
                display: block;
            }

            .form-group {
                flex: 1 0 100%;
            }

            .date-filter-group {
                flex-direction: row;
                flex-wrap: nowrap;
            }

            

            /* Modified: Always show table and make it scrollable */
            .routes-container {
                padding: 10px;
                overflow-x: auto; /* Allow horizontal scrolling */
            }

            .routes-table {
                display: table; /* Force display table */
                min-width: 650px; /* Ensure table has minimum width for readability */
            }
            
            .routes-cards {
                display: none; /* Always hide cards */
            }

            .page-title h2 {
                font-size: 1.5rem;
            }

            .page-title p {
                font-size: 0.9rem;
            }
            
            /* Make buttons more touch-friendly */
            .btn-small {
                padding: 10px 18px;
                font-size: 0.95rem;
            }
        }

        /* Extra small devices - ensure better table visibility */
        @media (max-width: 480px) {
            .container {
                width: 100%;
                padding: 0 10px;
            }

            .logo-container h1 {
                font-size: 1.2rem;
            }

            /* Smaller padding but still good for touch */
            .btn {
                padding: 10px 16px;
            }
            
            /* Adjust filter options */
            .filter-section {
                padding: 15px;
            }

            .date-filter-option label {
                padding: 8px;
                font-size: 0.85rem;
            }
            
            /* Make table more compact but still readable */
            .routes-table th,
            .routes-table td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar for Mobile -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-close">
            <i class="fas fa-times"></i>
        </div>
        <div class="sidebar-user">
            <div class="user-details">
                
            </div>
        </div>
        <div class="sidebar-nav">
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="mybookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
            <a href="myuploads.php"><i class="fas fa-upload"></i> My Uploads</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
        </div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

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
                <div class="menu-btn">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="nav-links">
                    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                    <a href="mybookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                    <a href="myuploads.php"><i class="fas fa-upload"></i> My Uploads</a>
                    <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Page Title -->
    <div class="page-title">
        <div class="container">
            <h2><i class="fas fa-route"></i> Explore Available Routes</h2>
            <p>Find the perfect bus journey for your travel needs</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="container">
    <div class="filter-section">
            <form class="filter-form" method="GET" action="explore_routes.php">
                <div class="form-group">
                    <label for="date">Custom Date</label>
                    <input type="date" id="date" name="date" class="form-control" value="<?php echo $date; ?>">
                </div>
                <div class="form-group">
                    <label>Quick Filter</label>
                    <div class="date-filter-group">
                        <div class="date-filter-option">
                            <input type="radio" id="filter_all" name="filter_date" value="all" <?php echo ($filter_date == 'all' || $filter_date == '') ? 'checked' : ''; ?>>
                            <label for="filter_all">All Dates</label>
                        </div>
                        <div class="date-filter-option">
                            <input type="radio" id="filter_today" name="filter_date" value="today" <?php echo ($filter_date == 'today') ? 'checked' : ''; ?>>
                            <label for="filter_today">Today</label>
                        </div>
                        <div class="date-filter-option">
                            <input type="radio" id="filter_tomorrow" name="filter_date" value="tomorrow" <?php echo ($filter_date == 'tomorrow') ? 'checked' : ''; ?>>
                            <label for="filter_tomorrow">Tomorrow</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn"><i class="fas fa-search"></i> Filter Routes</button>
                </div>
            </form>
        </div>

        <!-- Routes Display -->
        <div class="routes-container">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <!-- Table for all screen sizes -->
                <table class="routes-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-location-arrow"></i> From</th>
                            <th><i class="fas fa-map-marker-alt"></i> To</th>
                            <th><i class="far fa-calendar-alt"></i> Date</th>
                            <th><i class="far fa-clock"></i> Duration</th>
                            <th><i class="fas fa-road"></i> Distance</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                            <th><i class="fas fa-ticket-alt"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($result, 0);
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                            <tr>
                                <td><?php echo $row['from_location']; ?></td>
                                <td><?php echo $row['to_location']; ?></td>
                                <td><?php echo date('d M Y', strtotime($row['departure_date'])); ?></td>
                                <td><?php echo floor($row['duration']) . 'h'; ?></td>
                                <td><?php echo number_format($row['distance'], 1) . ' km'; ?></td>
                                <td>
                                    <span class="route-status status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="route-actions">
                                    <form action="search_bus.php" method="POST">
                                        <input type="hidden" name="from" value="<?php echo $row['from_location']; ?>">
                                        <input type="hidden" name="to" value="<?php echo $row['to_location']; ?>">
                                        <input type="hidden" name="date" value="<?php echo $row['departure_date']; ?>">
                                        <button type="submit" class="btn btn-small">Book</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Cards section is kept but always hidden -->
                <div class="routes-cards">
                    <!-- This section won't display regardless of screen size -->
                </div>
            <?php else: ?>
                <div class="no-routes">
                    <i class="fas fa-route"></i>
                    <h3>No Routes Available</h3>
                    <p>There are no routes available for the selected criteria. Please try a different date or filter option.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="copyright">
            <p>© 2025 MetaTicket. All Rights Reserved</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Function to toggle sidebar and overlay
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Basic click events
        function setupBasicEvents() {
            document.querySelector('.menu-btn').addEventListener('click', toggleSidebar);
            document.getElementById('overlay').addEventListener('click', toggleSidebar);
            document.querySelector('.sidebar-close').addEventListener('click', toggleSidebar);
            document.querySelectorAll('.sidebar-nav a').forEach(link => {
                link.addEventListener('click', toggleSidebar);
            });
        }

        // Touch events for sidebar
        function setupTouchEvents() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            let touchStartX = 0;
            let touchCurrentX = 0;
            let isDragging = false;

            // Prevent scrolling when interacting with sidebar
            sidebar.addEventListener('touchmove', (e) => {
                e.preventDefault();
            }, { passive: false });

            // Start touch on sidebar
            sidebar.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].clientX;
                isDragging = true;
                sidebar.style.transition = 'none';
            });

            // Move touch on sidebar
            sidebar.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                
                touchCurrentX = e.touches[0].clientX;
                const translateX = Math.min(0, touchCurrentX - touchStartX);
                
                if (sidebar.classList.contains('active')) {
                    sidebar.style.transform = `translateX(${translateX}px)`;
                }
            });

            // End touch on sidebar
            sidebar.addEventListener('touchend', () => {
                if (!isDragging) return;
                
                isDragging = false;
                sidebar.style.transition = 'all 0.3s ease';
                
                const difference = touchStartX - touchCurrentX;
                const threshold = 100;
                
                if (difference > threshold && sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
                
                sidebar.style.transform = 'translateX(0)';
            });

            // Swipe right from screen edge to open
            document.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].clientX;
                if (touchStartX < 50 && !sidebar.classList.contains('active')) {
                    isDragging = true;
                }
            });

            document.addEventListener('touchmove', (e) => {
                if (!isDragging || sidebar.classList.contains('active')) return;
                
                touchCurrentX = e.touches[0].clientX;
                const translateX = Math.min(280, touchCurrentX - touchStartX);
                sidebar.style.transform = `translateX(${translateX - 280}px)`;
            });

            document.addEventListener('touchend', () => {
                if (!isDragging || sidebar.classList.contains('active')) return;
                
                isDragging = false;
                sidebar.style.transition = 'all 0.3s ease';
                
                const difference = touchCurrentX - touchStartX;
                if (difference > 100) {
                    toggleSidebar();
                }
                sidebar.style.transform = 'translateX(-280px)';
            });
        }

        // Initialize all events when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            setupBasicEvents();
            setupTouchEvents();
        });

        // Prevent accidental page refresh on pull down
        document.addEventListener('touchmove', (e) => {
            if (e.touches.length > 1) {
                e.preventDefault();
            }
        }, { passive: false });
    </script>
</body>
</html>
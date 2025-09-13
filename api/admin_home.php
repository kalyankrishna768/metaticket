<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Meta Ticket Exchange</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --sidebar-width: 280px;
        --topbar-height: 64px;
        --primary-color: #1a1f2b;
        --secondary-color: #252b3b;
        --accent-color: #6366f1;
        --text-light: #ffffff;
        --card-bg: #ffffff;
        --transition-speed: 0.3s;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--primary-color);
        color: var(--text-light);
        transition: transform var(--transition-speed) ease;
        z-index: 1000;
        overflow-y: auto;
    }

    @media (max-width: 991px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }
    }

    .sidebar-header {
        padding: 20px;
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-logo img {
        width: 40px;
        border-radius: 8px;
        transition: transform var(--transition-speed);
    }

    .sidebar-logo img:hover {
        transform: scale(1.1);
    }

    .sidebar-menu {
        padding: 15px 0;
        list-style: none;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 25px;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        transition: all var(--transition-speed);
    }

    .sidebar-link:hover,
    .sidebar-link.active {
        background: var(--secondary-color);
        color: var(--accent-color);
        padding-left: 30px;
    }

    .sidebar-link i {
        width: 24px;
        margin-right: 15px;
    }

    /* Main Content */
    .main-content {
        margin-left: var(--sidebar-width);
        transition: margin-left var(--transition-speed);
        min-height: 100vh;
    }

    @media (max-width: 991px) {
        .main-content {
            margin-left: 0;
        }
    }

    .topbar {
        background: var(--card-bg);
        height: var(--topbar-height);
        padding: 0 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 999;
    }

    .menu-toggle {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--primary-color);
        cursor: pointer;
        transition: transform var(--transition-speed);
        display: none;
        /* Hidden by default */
    }

    .menu-toggle:hover {
        transform: rotate(90deg);
    }

    @media (max-width: 991px) {
        .menu-toggle {
            display: block;
            /* Show only on mobile */
        }
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(99, 102, 241, 0.3);
    }

    /* Dashboard Cards */
    .stat-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all var(--transition-speed);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        font-size: 2rem;
        color: var(--accent-color);
        background: rgba(99, 102, 241, 0.1);
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.9rem;
    }

    /* Booking Table */
    .booking-table {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .booking-table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .booking-table table {
        width: 100%;
        margin-bottom: 0;
        min-width: 700px;
    }

    .booking-table th {
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        padding: 15px;
        font-weight: 600;
        color: #374151;
        white-space: nowrap;
        border-bottom: 2px solid #e5e7eb;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        transition: background 0.3s ease;
    }

    .booking-table th:hover {
        background: linear-gradient(135deg, #eef2f7 0%, #e5e7eb 100%);
    }

    .booking-table th .d-flex {
        justify-content: flex-start;
    }

    .booking-table th i {
        font-size: 1.1rem;
        opacity: 0.9;
        transition: transform 0.3s ease;
    }

    .booking-table th:hover i {
        transform: scale(1.15);
    }

    .booking-table td {
        padding: 15px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-confirmed {
        background-color: #d4edda;
        color: #155724;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-cancelled {
        background-color: rgb(201, 247, 247);
        color: rgb(100, 75, 241);
    }

    /* Quick Actions */
    .quick-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all var(--transition-speed);
        text-decoration: none;
        color: var(--primary-color);
        border: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .quick-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-color);
        color: var(--accent-color);
    }

    .quick-card i {
        font-size: 2rem;
        margin-bottom: 12px;
        color: var(--accent-color);
        transition: all var(--transition-speed);
    }

    .quick-card:hover i {
        transform: scale(1.1);
    }

    .quick-card .action-text {
        font-size: 0.95rem;
        font-weight: 500;
        margin: 0;
    }

    /* Modal */
    .ticket-modal .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    }

    .ticket-modal .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
    }

    .ticket-modal .modal-body {
        padding: 25px;
    }

    .ticket-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .ticket-label {
        color: #6b7280;
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .stat-card {
            padding: 15px;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .quick-card {
            padding: 15px;
        }

        .booking-table th,
        .booking-table td {
            padding: 10px;
            font-size: 0.85rem;
        }

        .quick-card i {
            font-size: 1.8rem;
        }

        .quick-card .action-text {
            font-size: 0.85rem;
        }
    }

    @media (max-width: 576px) {
        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 1.3rem;
        }

        .quick-card i {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .quick-card .action-text {
            font-size: 0.8rem;
        }

        .ticket-modal .modal-body {
            padding: 15px;
        }

        .booking-table th,
        .booking-table td {
            padding: 8px;
            font-size: 0.75rem;
        }

        .booking-table .btn-sm {
            padding: 4px 8px;
            font-size: 0.7rem;
        }
    }

    /* Content Styles */
    .page-title {
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 1.8rem;
        font-size: 2.2rem;
        letter-spacing: -0.5px;
        position: relative;
        display: inline-block;
    }

    .page-title:after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--accent-color);
        border-radius: 2px;
    }

    @media (max-width: 768px) {
        .booking-table th {
            padding: 12px;
            font-size: 0.8rem;
        }

        .booking-table th i {
            font-size: 1rem;
        }
    }
    </style>
</head>

<body>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ticket";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql_tickets = "SELECT COUNT(*) as total_tickets FROM new_bookings";
    $total_tickets = $conn->query($sql_tickets)->fetch_assoc()["total_tickets"] ?? 0;

    $sql_users = "SELECT COUNT(*) as total_users FROM signup WHERE user_type = 2";
    $total_users = $conn->query($sql_users)->fetch_assoc()["total_users"] ?? 0;

    $sql_agencies = "SELECT COUNT(*) as total_agencies FROM signup WHERE user_type = 3";
    $total_agencies = $conn->query($sql_agencies)->fetch_assoc()["total_agencies"] ?? 0;

    $sql_revenue = "SELECT SUM(convenience_fee) as total_revenue FROM new_bookings";
    $total_revenue = $conn->query($sql_revenue)->fetch_assoc()["total_revenue"] ?? 0;
    ?>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="assets/images/logo.png" alt="MetaTicket Logo">
                <h2 style="font-size: 1.5rem; font-weight: 600;">MetaTicket</h2>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_home.php" class="sidebar-link active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="admin_bookings.php" class="sidebar-link"><i class="fas fa-ticket-alt"></i> Bookings</a></li>
            <li><a href="manage_users.php" class="sidebar-link"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="manage_agency.php" class="sidebar-link"><i class="fas fa-building"></i> Bus Agencies</a></li>
            <li><a href="admin_wallet.php" class="sidebar-link"><i class="fas fa-wallet"></i> Wallet</a></li>
            <li><a href="index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
        </ul>
    </aside>

    <div class="main-content" id="main-content">
        <div class="topbar">
            <div class="user-profile ms-auto">
                <!-- Moved to right with ms-auto -->
                <div class="user-avatar">A</div>
                <span class="d-none d-md-inline fw-medium">Admin</span>
                <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
            </div>
        </div>

        <div class="container-fluid py-4">
            <h1 class="page-title text-center">Dashboard Overview</h1>

            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
                        <div class="stat-value"><?php echo $total_tickets; ?></div>
                        <div class="stat-label">Total Tickets</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-value"><?php echo $total_users; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-building"></i></div>
                        <div class="stat-value"><?php echo $total_agencies; ?></div>
                        <div class="stat-label">Bus Agencies</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="stat-value">₹<?php echo number_format($total_revenue, 2); ?></div>
                        <div class="stat-label">Revenue</div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <h5 class="mb-3 fw-semibold">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="admin_messages.php" class="quick-card">
                                <i class="fas fa-envelope"></i>
                                <span class="action-text">Massages</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="admin_buses.php" class="quick-card">
                                <i class="fas fa-bus"></i>
                                <span class="action-text">Buses</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="manage_users.php" class="quick-card">
                                <i class="fas fa-users"></i>
                                <span class="action-text">Users</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="manage_agency.php" class="quick-card">
                                <i class="fas fa-building"></i>
                                <span class="action-text">Agencies</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <h5 class="mb-3 fw-semibold">Recent Bookings</h5>
                    <div class="booking-table">
                        <div class="booking-table-wrapper">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-hashtag text-primary"></i>
                                                <span>Ticket ID</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-user text-primary"></i>
                                                <span>Passenger</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-route text-primary"></i>
                                                <span>Journey</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-calendar-alt text-primary"></i>
                                                <span>Date</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-rupee-sign text-primary"></i>
                                                <span>Price</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-info-circle text-primary"></i>
                                                <span>Status</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-cog text-primary"></i>
                                                <span>Action</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM new_bookings ORDER BY booking_date DESC LIMIT 5";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $statusClass = $row["booking_status"] === "CONFIRMED" ? "status-confirmed" : ($row["booking_status"] === "PENDING" ? "status-pending" : "status-cancelled");
                                            echo "<tr>
                                                <td>#{$row['id']}</td>
                                                <td>{$row['passenger_name']}</td>
                                                <td>{$row['from_location']} to {$row['to_location']}</td>
                                                <td>" . date('M d', strtotime($row["journey_date"])) . "</td>
                                                <td>₹{$row['total_amount']}</td>
                                                <td><span class='status-badge $statusClass'>{$row['booking_status']}</span></td>
                                                <td>
                                                    <button class='btn btn-sm btn-outline-primary view-ticket'
                                                        data-bs-toggle='modal'
                                                        data-bs-target='#ticketModal'
                                                        data-id='{$row['id']}'
                                                        data-passenger-name='" . htmlspecialchars($row['passenger_name']) . "'
                                                        data-from-location='" . htmlspecialchars($row['from_location']) . "'
                                                        data-to-location='" . htmlspecialchars($row['to_location']) . "'
                                                        data-journey-date='" . htmlspecialchars($row['journey_date']) . "'
                                                        data-boarding-time='" . htmlspecialchars($row['boarding_time'] ?? '') . "'
                                                        data-dropping-time='" . htmlspecialchars($row['dropping_time'] ?? '') . "'
                                                        data-bus-id='" . htmlspecialchars($row['bus_id'] ?? '') . "'
                                                        data-seat-no='" . htmlspecialchars($row['seat_no'] ?? '') . "'
                                                        data-passenger-email='" . htmlspecialchars($row['passenger_email'] ?? '') . "'
                                                        data-phone-number='" . htmlspecialchars($row['phone_number'] ?? '') . "'
                                                        data-booking-date='" . htmlspecialchars($row['booking_date']) . "'
                                                        data-ticket-price='" . htmlspecialchars($row['ticket_price'] ?? '') . "'
                                                        data-total-amount='" . htmlspecialchars($row['total_amount']) . "'
                                                        data-convenience-fee='" . htmlspecialchars($row['convenience_fee'] ?? '') . "'
                                                        data-payment-status='" . htmlspecialchars($row['payment_status'] ?? '') . "'
                                                        data-booking-status='" . htmlspecialchars($row['booking_status']) . "'>
                                                        View
                                                    </button>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center py-4'>No recent bookings found</td></tr>";
                                    }
                                    $conn->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <a href="admin_bookings.php" class="btn btn-outline-primary btn-sm">View All Bookings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade ticket-modal" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">Ticket Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ticket-detail-row"><span class="ticket-label">Booking ID:</span><span
                            id="modal-ticket-id"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Passenger:</span><span
                            id="modal-passenger"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">From:</span><span id="modal-from"></span>
                    </div>
                    <div class="ticket-detail-row"><span class="ticket-label">To:</span><span id="modal-to"></span>
                    </div>
                    <div class="ticket-detail-row"><span class="ticket-label">Journey Date:</span><span
                            id="modal-journey-date"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Departure:</span><span
                            id="modal-departure-time"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Arrival:</span><span
                            id="modal-arrival-time"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Bus ID:</span><span
                            id="modal-bus-id"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Seats:</span><span
                            id="modal-seats"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Email:</span><span
                            id="modal-email"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Mobile:</span><span
                            id="modal-mobile"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Booking Date:</span><span
                            id="modal-booking-date"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Ticket Price:</span><span
                            id="modal-ticket-price"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Conv. Fee:</span><span
                            id="modal-convenience-fee"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Total Amount:</span><span
                            id="modal-amount"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Payment:</span><span
                            id="modal-payment-method"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Status:</span><span class="status-badge"
                            id="modal-status"></span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menu-toggle');

    menuToggle.addEventListener('click', () => sidebar.classList.toggle('show'));

    document.addEventListener('click', (e) => {
        if (window.innerWidth < 992 && !sidebar.contains(e.target) && !menuToggle.contains(e.target) && sidebar
            .classList.contains('show')) {
            sidebar.classList.remove('show');
        }
    });

    document.querySelectorAll('.view-ticket').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;

            // Helper function to safely get and display data
            const getValue = (value) => value || 'N/A';

            // Populate modal fields
            document.getElementById('modal-ticket-id').textContent = '#' + getValue(data.id);
            document.getElementById('modal-passenger').textContent = getValue(data.passengerName);
            document.getElementById('modal-from').textContent = getValue(data.fromLocation);
            document.getElementById('modal-to').textContent = getValue(data.toLocation);
            document.getElementById('modal-journey-date').textContent = getValue(data.journeyDate);
            document.getElementById('modal-departure-time').textContent = getValue(data.boardingTime);
            document.getElementById('modal-arrival-time').textContent = getValue(data.droppingTime);
            document.getElementById('modal-bus-id').textContent = getValue(data.busId);
            document.getElementById('modal-seats').textContent = getValue(data.seatNo);
            document.getElementById('modal-email').textContent = getValue(data.passengerEmail);
            document.getElementById('modal-mobile').textContent = getValue(data.phoneNumber);
            document.getElementById('modal-booking-date').textContent = getValue(data.bookingDate);
            document.getElementById('modal-ticket-price').textContent = '₹' + getValue(data
            .ticketPrice);
            document.getElementById('modal-convenience-fee').textContent = '₹' + getValue(data
                .convenienceFee);
            document.getElementById('modal-amount').textContent = '₹' + getValue(data.totalAmount);
            document.getElementById('modal-payment-method').textContent = getValue(data.paymentStatus);

            // Handle status with proper formatting
            const statusElement = document.getElementById('modal-status');
            const status = getValue(data.bookingStatus);
            statusElement.textContent = status;
            statusElement.className = 'status-badge ' + (
                status === 'CONFIRMED' ? 'status-confirmed' :
                status === 'PENDING' ? 'status-pending' :
                status === 'CANCELLED' ? 'status-cancelled' : ''
            );
        });
    });
    </script>
</body>

</html>
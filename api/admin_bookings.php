<?php
include 'config.php';
session_start();

// Get current date and time
$current_datetime = new DateTime('now', new DateTimeZone('Asia/Kolkata')); // Adjust timezone as needed
$current_date = $current_datetime->format('Y-m-d');
$current_time = $current_datetime->format('H:i:s');

// Update booking status to COMPLETED for past journeys
$update_sql = "UPDATE new_bookings 
               SET booking_status = 'COMPLETED' 
               WHERE booking_status = 'CONFIRMED' 
               AND (journey_date < ? 
                    OR (journey_date = ? AND boarding_time < ?))";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("sss", $current_date, $current_date, $current_time);
$update_stmt->execute();

// Initialize variables for filtering
$filter_status = isset($_GET['status']) ? $_GET['status'] : "";
$filter_date = isset($_GET['date']) ? $_GET['date'] : "";
$search_term = isset($_GET['search']) ? $_GET['search'] : "";

// Build the SQL query with prepared statements
$sql = "SELECT * FROM new_bookings WHERE 1=1";
$params = array();
$types = "";

if (!empty($filter_status)) {
    $sql .= " AND booking_status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

if (!empty($filter_date)) {
    $sql .= " AND DATE(journey_date) = ?";
    $params[] = $filter_date;
    $types .= "s";
}

if (!empty($search_term)) {
    $sql .= " AND (passenger_name LIKE ? OR passenger_email LIKE ? OR ticket_id LIKE ? OR from_location LIKE ? OR to_location LIKE ?)";
    $search_pattern = "%$search_term%";
    $params[] = $search_pattern;
    $params[] = $search_pattern;
    $params[] = $search_pattern;
    $params[] = $search_pattern;
    $params[] = $search_pattern;
    $types .= "sssss";
}

$sql .= " ORDER BY booking_date DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Bookings | Meta Ticket Exchange</title>
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
    }

    .menu-toggle:hover {
        transform: rotate(90deg);
    }

    @media (max-width: 991px) {
        .menu-toggle {
            display: block;
        }
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-left: auto;
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

    /* Filter Card */
    .filter-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all var(--transition-speed);
    }

    .filter-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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

    .status-completed {
        background-color: rgb(186, 238, 244);
        color: rgb(70, 90, 223);
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Form Controls */
    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.7rem 1rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        border-color: var(--accent-color);
    }

    /* Buttons */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.7rem 1.2rem;
        transition: all 0.3s;
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
        .filter-card {
            padding: 15px;
        }

        .booking-table th,
        .booking-table td {
            padding: 10px;
            font-size: 0.85rem;
        }

        .form-control,
        .form-select {
            padding: 0.6rem 0.8rem;
        }

        .btn {
            padding: 0.6rem 1rem;
        }
    }

    @media (max-width: 576px) {
        .filter-card {
            padding: 10px;
        }

        .booking-table th,
        .booking-table td {
            padding: 8px;
            font-size: 0.75rem;
        }

        .status-badge {
            padding: 4px 8px;
            font-size: 0.7rem;
        }

        .form-control,
        .form-select {
            padding: 0.5rem 0.7rem;
            font-size: 0.85rem;
        }

        .btn {
            padding: 0.5rem 0.8rem;
            font-size: 0.85rem;
        }

        .ticket-modal .modal-body {
            padding: 15px;
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
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="assets/images/logo.png" alt="MetaTicket Logo">
                <h2 style="font-size: 1.5rem; font-weight: 600;">MetaTicket</h2>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_home.php" class="sidebar-link"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="admin_bookings.php" class="sidebar-link active"><i class="fas fa-ticket-alt"></i> Bookings</a>
            </li>
            <li><a href="manage_users.php" class="sidebar-link"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="manage_agency.php" class="sidebar-link"><i class="fas fa-building"></i> Bus Agencies</a></li>
            <li><a href="admin_wallet.php" class="sidebar-link"><i class="fas fa-wallet"></i> Wallet</a></li>
            <li><a href="index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="topbar">
            <div class="user-profile ms-auto">
                <div class="user-avatar">A</div>
                <span class="d-none d-md-inline fw-medium">Admin</span>
                <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
            </div>
        </div>

        <div class="container-fluid py-4">
            <h1 class="page-title text-center">Booking Management</h1>

            <!-- Filter Card -->
            <div class="filter-card">
                <form method="GET" class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-filter text-muted"></i></span>
                            <select class="form-select" name="status">
                                <option value="">All Statuses</option>
                                <option value="CONFIRMED" <?= $filter_status == "CONFIRMED" ? "selected" : "" ?>>
                                    Confirmed</option>
                                <option value="COMPLETED" <?= $filter_status == "COMPLETED" ? "selected" : "" ?>>
                                    Completed</option>
                                <option value="CANCELLED" <?= $filter_status == "CANCELLED" ? "selected" : "" ?>>
                                    Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i
                                    class="fas fa-calendar-alt text-muted"></i></span>
                            <input type="date" name="date" class="form-control"
                                value="<?= htmlspecialchars($filter_date) ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search bookings..."
                                value="<?= htmlspecialchars($search_term) ?>">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bookings Table -->
            <div class="booking-table">
                <div class="booking-table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-hashtag text-primary"></i>
                                        <span>ID</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-ticket-alt text-primary"></i>
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
                                        <span>Date & Time</span>
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
                            <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                                    $statusClass = $row["booking_status"] === "CONFIRMED" ? "status-confirmed" : 
                                                  ($row["booking_status"] === "COMPLETED" ? "status-completed" : "status-cancelled");
                                    ?>
                            <tr>
                                <td>#<?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['ticket_id']) ?></td>
                                <td><?= htmlspecialchars($row['passenger_name']) ?><br><small><?= htmlspecialchars($row['passenger_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['from_location']) ?> to
                                    <?= htmlspecialchars($row['to_location']) ?></td>
                                <td><?= date('M d, Y', strtotime($row['journey_date'])) ?>
                                    <?= htmlspecialchars($row['boarding_time'] ?? '') ?></td>
                                <td>₹<?= number_format($row['total_amount'], 2) ?></td>
                                <td><span
                                        class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($row['booking_status']) ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary view-ticket" data-bs-toggle="modal"
                                        data-bs-target="#ticketModal" data-id="<?= htmlspecialchars($row['id']) ?>"
                                        data-ticket-id="<?= htmlspecialchars($row['ticket_id']) ?>"
                                        data-passenger-name="<?= htmlspecialchars($row['passenger_name']) ?>"
                                        data-passenger-email="<?= htmlspecialchars($row['passenger_email']) ?>"
                                        data-from-location="<?= htmlspecialchars($row['from_location']) ?>"
                                        data-to-location="<?= htmlspecialchars($row['to_location']) ?>"
                                        data-journey-date="<?= htmlspecialchars($row['journey_date']) ?>"
                                        data-boarding-time="<?= htmlspecialchars($row['boarding_time'] ?? '') ?>"
                                        data-total-amount="<?= htmlspecialchars($row['total_amount']) ?>"
                                        data-booking-status="<?= htmlspecialchars($row['booking_status']) ?>">
                                        View
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">No bookings found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Modal -->
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
                    <div class="ticket-detail-row"><span class="ticket-label">Ticket ID:</span><span
                            id="modal-ticket-id-specific"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Passenger:</span><span
                            id="modal-passenger"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Email:</span><span
                            id="modal-email"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">From:</span><span id="modal-from"></span>
                    </div>
                    <div class="ticket-detail-row"><span class="ticket-label">To:</span><span id="modal-to"></span>
                    </div>
                    <div class="ticket-detail-row"><span class="ticket-label">Journey Date:</span><span
                            id="modal-journey-date"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Departure:</span><span
                            id="modal-departure-time"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Total Amount:</span><span
                            id="modal-amount"></span></div>
                    <div class="ticket-detail-row"><span class="ticket-label">Status:</span><span class="status-badge"
                            id="modal-status"></span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="print-ticket">
                        <i class="fas fa-print me-2"></i>Print Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Sidebar toggle functionality
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menu-toggle');

    menuToggle.addEventListener('click', () => sidebar.classList.toggle('show'));

    document.addEventListener('click', (e) => {
        if (window.innerWidth < 992 && !sidebar.contains(e.target) && !menuToggle.contains(e.target) && sidebar
            .classList.contains('show')) {
            sidebar.classList.remove('show');
        }
    });

    // Ticket modal functionality
    document.querySelectorAll('.view-ticket').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;

            // Helper function to safely get and display data
            const getValue = (value) => value || 'N/A';

            // Populate modal fields
            document.getElementById('modal-ticket-id').textContent = '#' + getValue(data.id);
            document.getElementById('modal-ticket-id-specific').textContent = getValue(data.ticketId);
            document.getElementById('modal-passenger').textContent = getValue(data.passengerName);
            document.getElementById('modal-email').textContent = getValue(data.passengerEmail);
            document.getElementById('modal-from').textContent = getValue(data.fromLocation);
            document.getElementById('modal-to').textContent = getValue(data.toLocation);
            document.getElementById('modal-journey-date').textContent = getValue(data.journeyDate);
            document.getElementById('modal-departure-time').textContent = getValue(data.boardingTime);
            document.getElementById('modal-amount').textContent = '₹' + getValue(Number(data
                .totalAmount).toFixed(2));

            // Handle status with proper formatting
            const statusElement = document.getElementById('modal-status');
            const status = getValue(data.bookingStatus);
            statusElement.textContent = status;
            statusElement.className = 'status-badge ' + (
                status === 'CONFIRMED' ? 'status-confirmed' :
                status === 'COMPLETED' ? 'status-completed' :
                status === 'CANCELLED' ? 'status-cancelled' : ''
            );
        });
    });

    // Print ticket functionality
    document.getElementById('print-ticket').addEventListener('click', function() {
        const modalContent = document.querySelector('.modal-body').cloneNode(true);
        const printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write(`
                <html>
                <head>
                    <title>Print Ticket</title>
                    <style>
                        body { font-family: 'Inter', sans-serif; padding: 20px; }
                        .ticket-detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
                        .ticket-label { color: #6b7280; font-weight: 500; }
                        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
                        .status-confirmed { background-color: #d4edda; color: #155724; }
                        .status-completed { background-color: rgb(186, 238, 244); color: rgb(70, 90, 223); }
                        .status-cancelled { background-color: #f8d7da; color: #721c24; }
                    </style>
                </head>
                <body>
                    <h2>Ticket Details</h2>
                    ${modalContent.innerHTML}
                </body>
                </html>
            `);
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    });
    </script>
</body>

</html>

<?php
$conn->close();
?>
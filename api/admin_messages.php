<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Messages - Meta Ticket Exchange</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    /* Reuse the same CSS from admin_home.php */
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

    /* Messages Table */
    .messages-table {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .messages-table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .messages-table table {
        width: 100%;
        margin-bottom: 0;
        min-width: 700px;
    }

    .messages-table th {
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

    .messages-table th:hover {
        background: linear-gradient(135deg, #eef2f7 0%, #e5e7eb 100%);
    }

    .messages-table th .d-flex {
        justify-content: flex-start;
    }

    .messages-table th i {
        font-size: 1.1rem;
        opacity: 0.9;
        transition: transform 0.3s ease;
    }

    .messages-table th:hover i {
        transform: scale(1.15);
    }

    .messages-table td {
        padding: 15px;
        vertical-align: middle;
        white-space: nowrap;
    }

    /* Modal */
    .message-modal .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    }

    .message-modal .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
    }

    .message-modal .modal-body {
        padding: 25px;
    }

    .message-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .message-label {
        color: #6b7280;
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 768px) {

        .messages-table th,
        .messages-table td {
            padding: 10px;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 576px) {

        .messages-table th,
        .messages-table td {
            padding: 8px;
            font-size: 0.75rem;
        }

        .messages-table .btn-sm {
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
                <div class="user-avatar">A</div>
                <span class="d-none d-md-inline fw-medium">Admin</span>
                <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
            </div>
        </div>

        <div class="container-fluid py-4">
            <h1 class="page-title text-center">Messages</h1>

            <div class="row g-4 mt-2">
                <div class="col-12">
                    <h5 class="mb-3 fw-semibold">Recent Messages</h5>
                    <div class="messages-table">
                        <div class="messages-table-wrapper">
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
                                                <i class="fas fa-user text-primary"></i>
                                                <span>Username</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-envelope text-primary"></i>
                                                <span>Email</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-heading text-primary"></i>
                                                <span>Subject</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-comment text-primary"></i>
                                                <span>Message</span>
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
                                    $sql = "SELECT * FROM contact ORDER BY id DESC LIMIT 10";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                <td>#{$row['id']}</td>
                                                <td>" . htmlspecialchars($row['username']) . "</td>
                                                <td>" . htmlspecialchars($row['email']) . "</td>
                                                <td>" . htmlspecialchars($row['subject']) . "</td>
                                                <td>" . htmlspecialchars(substr($row['message'], 0, 50)) . (strlen($row['message']) > 50 ? '...' : '') . "</td>
                                                <td>
                                                    <button class='btn btn-sm btn-outline-primary view-message'
                                                        data-bs-toggle='modal'
                                                        data-bs-target='#messageModal'
                                                        data-id='{$row['id']}'
                                                        data-username='" . htmlspecialchars($row['username']) . "'
                                                        data-email='" . htmlspecialchars($row['email']) . "'
                                                        data-subject='" . htmlspecialchars($row['subject']) . "'
                                                        data-message='" . htmlspecialchars($row['message']) . "'>
                                                        View
                                                    </button>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center py-4'>No messages found</td></tr>";
                                    }
                                    $conn->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade message-modal" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="message-detail-row"><span class="message-label">Message ID:</span><span
                            id="modal-message-id"></span></div>
                    <div class="message-detail-row"><span class="message-label">Username:</span><span
                            id="modal-username"></span></div>
                    <div class="message-detail-row"><span class="message-label">Email:</span><span
                            id="modal-email"></span></div>
                    <div class="message-detail-row"><span class="message-label">Subject:</span><span
                            id="modal-subject"></span></div>
                    <div class="message-detail-row"><span class="message-label">Message:</span><span
                            id="modal-message"></span></div>
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

    document.querySelectorAll('.view-message').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;

            // Helper function to safely get and display data
            const getValue = (value) => value || 'N/A';

            // Populate modal fields
            document.getElementById('modal-message-id').textContent = '#' + getValue(data.id);
            document.getElementById('modal-username').textContent = getValue(data.username);
            document.getElementById('modal-email').textContent = getValue(data.email);
            document.getElementById('modal-subject').textContent = getValue(data.subject);
            document.getElementById('modal-message').textContent = getValue(data.message);
        });
    });
    </script>
</body>

</html>
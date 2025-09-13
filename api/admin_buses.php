<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buses - Meta Ticket Exchange</title>
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

    /* Buses Table */
    .buses-table {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .buses-table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .buses-table table {
        width: 100%;
        margin-bottom: 0;
        min-width: 700px;
    }

    .buses-table th {
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

    .buses-table th:hover {
        background: linear-gradient(135deg, #eef2f7 0%, #e5e7eb 100%);
    }

    .buses-table th .d-flex {
        justify-content: flex-start;
    }

    .buses-table th i {
        font-size: 1.1rem;
        opacity: 0.9;
        transition: transform 0.3s ease;
    }

    .buses-table th:hover i {
        transform: scale(1.15);
    }

    .buses-table td {
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

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-maintenance {
        background-color: #fff3cd;
        color: #856404;
    }

    /* Filter Dropdown */
    .filter-container {
        margin-bottom: 20px;
    }

    .filter-container select {
        max-width: 200px;
        padding: 8px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        font-size: 0.9rem;
        color: #374151;
    }

    /* Modal */
    .bus-modal .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    }

    .bus-modal .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
    }

    .bus-modal .modal-body {
        padding: 25px;
    }

    .bus-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .bus-label {
        color: #6b7280;
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 768px) {

        .buses-table th,
        .buses-table td {
            padding: 10px;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 576px) {

        .buses-table th,
        .buses-table td {
            padding: 8px;
            font-size: 0.75rem;
        }

        .buses-table .btn-sm {
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

    // Fetch unique agency usernames for the filter dropdown
    $agency_query = "SELECT DISTINCT username FROM buses ORDER BY username";
    $agency_result = $conn->query($agency_query);
    $agencies = [];
    while ($row = $agency_result->fetch_assoc()) {
        $agencies[] = $row['username'];
    }

    // Handle filter
    $selected_agency = isset($_GET['agency']) ? $_GET['agency'] : '';
    $where_clause = $selected_agency ? "WHERE username = '" . $conn->real_escape_string($selected_agency) . "'" : '';
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
            <h1 class="page-title text-center">Manage Buses</h1>

            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="filter-container">
                        <form method="GET" action="">
                            <label for="agency-filter" class="me-2 fw-medium">Filter by Agency:</label>
                            <select name="agency" id="agency-filter" onchange="this.form.submit()">
                                <option value="">All Agencies</option>
                                <?php
                                foreach ($agencies as $agency) {
                                    $selected = ($agency == $selected_agency) ? 'selected' : '';
                                    echo "<option value='$agency' $selected>" . htmlspecialchars($agency) . "</option>";
                                }
                                ?>
                            </select>
                        </form>
                    </div>
                    <h5 class="mb-3 fw-semibold">Bus List</h5>
                    <div class="buses-table">
                        <div class="buses-table-wrapper">
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
                                                <i class="fas fa-bus text-primary"></i>
                                                <span>Bus Number</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-users text-primary"></i>
                                                <span>Capacity</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-info-circle text-primary"></i>
                                                <span>Type</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-signal text-primary"></i>
                                                <span>Status</span>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-user text-primary"></i>
                                                <span>Agency</span>
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
                                    $sql = "SELECT * FROM buses $where_clause ORDER BY id DESC";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $statusClass = $row["status"] === "Active" ? "status-active" : ($row["status"] === "Inactive" ? "status-inactive" : "status-maintenance");
                                            echo "<tr>
                                                <td>#{$row['id']}</td>
                                                <td>" . htmlspecialchars($row['bus_number']) . "</td>
                                                <td>" . htmlspecialchars($row['capacity']) . "</td>
                                                <td>" . htmlspecialchars($row['type']) . "</td>
                                                <td><span class='status-badge $statusClass'>" . htmlspecialchars($row['status']) . "</span></td>
                                                <td>" . htmlspecialchars($row['username']) . "</td>
                                                <td>
                                                    <button class='btn btn-sm btn-outline-primary view-bus'
                                                        data-bs-toggle='modal'
                                                        data-bs-target='#busModal'
                                                        data-id='{$row['id']}'
                                                        data-bus-number='" . htmlspecialchars($row['bus_number']) . "'
                                                        data-capacity='" . htmlspecialchars($row['capacity']) . "'
                                                        data-type='" . htmlspecialchars($row['type']) . "'
                                                        data-status='" . htmlspecialchars($row['status']) . "'
                                                        data-created-at='" . htmlspecialchars($row['created_at']) . "'
                                                        data-updated-at='" . htmlspecialchars($row['updated_at']) . "'
                                                        data-user-name='" . htmlspecialchars($row['username']) . "'
                                                        data-email='" . htmlspecialchars($row['email']) . "'>
                                                        View
                                                    </button>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center py-4'>No buses found</td></tr>";
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

    <div class="modal fade bus-modal" id="busModal" tabindex="-1" aria-labelledby="busModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="busModalLabel">Bus Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="bus-detail-row"><span class="bus-label">Bus ID:</span><span id="modal-bus-id"></span>
                    </div>
                    <div class="bus-detail-row"><span class="bus-label">Bus Number:</span><span
                            id="modal-bus-number"></span></div>
                    <div class="bus-detail-row"><span class="bus-label">Capacity:</span><span
                            id="modal-capacity"></span></div>
                    <div class="bus-detail-row"><span class="bus-label">Type:</span><span id="modal-type"></span></div>
                    <div class="bus-detail-row"><span class="bus-label">Status:</span><span class="status-badge"
                            id="modal-status"></span></div>
                    <div class="bus-detail-row"><span class="bus-label">Created At:</span><span
                            id="modal-created-at"></span></div>
                    <div class="bus-detail-row"><span class="bus-label">Updated At:</span><span
                            id="modal-updated-at"></span></div>
                    <div class="bus-detail-row"><span class="bus-label">Agency Username:</span><span
                            id="modal-user-name"></span></div>
                    <div class="bus-detail-row"><span class="bus-label">Agency Email:</span><span
                            id="modal-email"></span></div>
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

    document.querySelectorAll('.view-bus').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;

            // Helper function to safely get and display data
            const getValue = (value) => value || 'N/A';

            // Populate modal fields
            document.getElementById('modal-bus-id').textContent = '#' + getValue(data.id);
            document.getElementById('modal-bus-number').textContent = getValue(data.busNumber);
            document.getElementById('modal-capacity').textContent = getValue(data.capacity);
            document.getElementById('modal-type').textContent = getValue(data.type);
            document.getElementById('modal-created-at').textContent = getValue(data.createdAt);
            document.getElementById('modal-updated-at').textContent = getValue(data.updatedAt);
            document.getElementById('modal-user-name').textContent = getValue(data.userName);
            document.getElementById('modal-email').textContent = getValue(data.email);

            // Handle status with proper formatting
            const statusElement = document.getElementById('modal-status');
            const status = getValue(data.status);
            statusElement.textContent = status;
            statusElement.className = 'status-badge ' + (
                status === 'Active' ? 'status-active' :
                status === 'Inactive' ? 'status-inactive' :
                status === 'Maintenance' ? 'status-maintenance' : ''
            );
        });
    });
    </script>
</body>

</html>
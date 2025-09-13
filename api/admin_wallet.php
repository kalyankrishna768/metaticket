<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Wallet - MetaTicket Admin</title>
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

    .transactions-table {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .transactions-table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .transactions-table table {
        width: 100%;
        margin-bottom: 0;
        min-width: 700px;
    }

    .transactions-table th {
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

    .transactions-table th:hover {
        background: linear-gradient(135deg, #eef2f7 0%, #e5e7eb 100%);
    }

    .transactions-table td {
        padding: 15px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .form-control {
        border-radius: 8px;
        padding: 0.7rem 1rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        border-color: var(--accent-color);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.7rem 1.2rem;
        transition: all 0.3s;
    }

    .wallet-summary {
        display: flex;
        justify-content: space-around;
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-bottom: 25px;
        transition: all var(--transition-speed);
    }

    .wallet-summary:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .summary-card {
        text-align: center;
        padding: 15px;
    }

    .summary-card h4 {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .summary-card h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .transaction-credit {
        color: #28a745;
        font-weight: 600;
    }

    .transaction-debit {
        color: #dc3545;
        font-weight: 600;
    }

    @media (max-width: 768px) {

        .filter-card,
        .wallet-summary {
            padding: 15px;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 10px;
            font-size: 0.85rem;
        }

        .form-control {
            padding: 0.6rem 0.8rem;
        }

        .btn {
            padding: 0.6rem 1rem;
        }

        .wallet-summary {
            flex-direction: column;
            gap: 15px;
        }
    }

    @media (max-width: 576px) {

        .filter-card,
        .wallet-summary {
            padding: 10px;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 8px;
            font-size: 0.75rem;
        }

        .form-control {
            padding: 0.5rem 0.7rem;
            font-size: 0.85rem;
        }

        .btn {
            padding: 0.5rem 0.8rem;
            font-size: 0.85rem;
        }

        .summary-card h2 {
            font-size: 1.2rem;
        }

        .summary-card h4 {
            font-size: 0.9rem;
        }
    }

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
    $conn = new mysqli('localhost', 'root', '', 'ticket');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $added_amount = 0;
    $message = '';
    $message_type = '';

    $wallet_result = $conn->query("SELECT available_money FROM admin_wallet WHERE id = 1");
    if ($wallet_result && $wallet_result->num_rows > 0) {
        $wallet_data = $wallet_result->fetch_assoc();
        $available_money = $wallet_data['available_money'];
    } else {
        $available_money = 0;
    }

    $bus_result = $conn->query("SELECT SUM(ticketprice) AS total_revenue, COUNT(*) AS total_bookings FROM bookings");
    $bus_data = $bus_result->fetch_assoc();

    $train_result = $conn->query("SELECT SUM(ticketprice) AS total_revenue, COUNT(*) AS total_bookings FROM train_bookings");
    $train_data = $train_result->fetch_assoc();

    $total_revenue = $bus_data['total_revenue'] + $train_data['total_revenue'];
    $total_bookings = $bus_data['total_bookings'] + $train_data['total_bookings'];
    $platform_commission = $total_revenue * 0.1;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_money'])) {
        $added_amount = floatval($_POST['amount']);

        if ($added_amount <= 0) {
            $message = 'Please enter a valid amount greater than zero.';
            $message_type = 'danger';
        } else {
            $available_money += $added_amount;
            $description = "Manual wallet top-up";

            $stmt = $conn->prepare("UPDATE admin_wallet SET available_money = ? WHERE id = 1");
            $stmt->bind_param("d", $available_money);

            if ($stmt->execute()) {
                $stmt->close();
                $stmt = $conn->prepare("INSERT INTO admin_wallet_transactions (admin_wallet_id, transaction_type, amount, description) VALUES (1, 'credit', ?, ?)");
                $stmt->bind_param("ds", $added_amount, $description);

                if ($stmt->execute()) {
                    $message = 'Amount ₹' . number_format($added_amount, 2) . ' successfully added to wallet.';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to record transaction: ' . $stmt->error;
                    $message_type = 'danger';
                }
            } else {
                $message = 'Failed to update wallet: ' . $stmt->error;
                $message_type = 'danger';
            }
            $stmt->close();
        }
    }

    $where_clause = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter_date'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        if (!empty($start_date) && !empty($end_date)) {
            $where_clause = " WHERE transaction_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_filter'])) {
        $where_clause = "";
    }

    $transactions_result = $conn->query("SELECT id, transaction_type, amount, transaction_date, description FROM admin_wallet_transactions" . $where_clause . " ORDER BY transaction_date DESC LIMIT 10");

    $stats_query = "SELECT 
                    SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
                    SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits
                FROM admin_wallet_transactions" . $where_clause;
    $stats_result = $conn->query($stats_query);
    if ($stats_result && $stats_result->num_rows > 0) {
        $stats_data = $stats_result->fetch_assoc();
        $total_credits = $stats_data['total_credits'] ?? 0;
        $total_debits = $stats_data['total_debits'] ?? 0;
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
            <li><a href="admin_home.php" class="sidebar-link"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="admin_bookings.php" class="sidebar-link"><i class="fas fa-ticket-alt"></i> Bookings</a></li>
            <li><a href="manage_users.php" class="sidebar-link"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="manage_agency.php" class="sidebar-link"><i class="fas fa-building"></i> Bus Agencies</a></li>
            <li><a href="admin_wallet.php" class="sidebar-link active"><i class="fas fa-wallet"></i> Wallet</a></li>
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
            <h1 class="page-title text-center">Wallet Management</h1>

            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="wallet-summary">
                <div class="summary-card">
                    <h4><i class="fas fa-wallet me-2"></i>Available Balance</h4>
                    <h2>₹<?php echo number_format($available_money, 2); ?></h2>
                </div>
                <div class="summary-card">
                    <h4><i class="fas fa-plus-circle me-2"></i>Total Credits</h4>
                    <h2>₹<?php echo number_format($total_credits, 2); ?></h2>
                </div>
                <div class="summary-card">
                    <h4><i class="fas fa-minus-circle me-2"></i>Total Debits</h4>
                    <h2>₹<?php echo number_format($total_debits, 2); ?></h2>
                </div>
            </div>

            <div class="filter-card">
                <h4 class="mb-3">Add Money to Wallet</h4>
                <form method="POST" action="" class="row g-3 align-items-center">
                    <div class="col-md-6 col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount"
                                placeholder="Enter amount" required>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <button type="submit" name="add_money" class="btn btn-primary w-100"><i
                                class="fas fa-plus me-2"></i>Add Money</button>
                    </div>
                </form>
            </div>

            <div class="filter-card">
                <h4 class="mb-3">Filter Transactions by Date</h4>
                <form method="POST" action="" class="row g-3 align-items-center">
                    <div class="col-md-3 col-sm-6">
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <button type="submit" name="filter_date" class="btn btn-primary w-100"><i
                                class="fas fa-filter me-2"></i>Filter</button>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="admin_wallet.php" class="btn btn-outline-secondary w-100">Clear Filters</a>
                    </div>
                </form>
            </div>

            <div class="transactions-table">
                <div class="transactions-table-wrapper">
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
                                        <i class="fas fa-exchange-alt text-primary"></i>
                                        <span>Type</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-rupee-sign text-primary"></i>
                                        <span>Amount</span>
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
                                        <i class="fas fa-info-circle text-primary"></i>
                                        <span>Description</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTableBody">
                            <?php if ($transactions_result && $transactions_result->num_rows > 0): ?>
                            <?php while ($transaction = $transactions_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($transaction['id']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($transaction['transaction_type'])); ?></td>
                                <td
                                    class="<?php echo $transaction['transaction_type'] == 'credit' ? 'transaction-credit' : 'transaction-debit'; ?>">
                                    <?php echo $transaction['transaction_type'] == 'credit' ? '+' : '-'; ?>₹<?php echo number_format($transaction['amount'], 2); ?>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                                <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">No transactions found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
    </script>
</body>

</html>
<?php
include 'config.php';

session_start();

// Check if the user is logged in and has an email stored in session
if (!isset($_SESSION['id'])) {
    echo "Error: User is not logged in.";
    exit();
}

// Fetch email of the logged-in user
$user_email = $_SESSION['email'];

// Get filter parameter if exists
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Prepare SQL query based on filter
$bus_query = "SELECT nb.*, 'Bus' as type, nb.from_location, nb.to_location, nb.boarding_point, nb.dropping_point, 
              nb.seat_no as selected_seats, nb.total_amount, nb.boarding_time, nb.dropping_time 
              FROM new_bookings nb WHERE nb.user_email = ?";

if ($filter === 'upcoming' || $filter === 'completed') {
    $bus_query .= " AND nb.journey_date " . ($filter === 'upcoming' ? '>=' : '<') . " CURDATE()";
}

$bus_query .= " ORDER BY nb.journey_date, nb.boarding_time";

$stmt_bus = $conn->prepare($bus_query);
$stmt_bus->bind_param("s", $user_email);
$stmt_bus->execute();
$bus_result = $stmt_bus->get_result();

// Get the current date and time for comparison
$current_datetime = new DateTime();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Bookings | Travel Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .booking-card {
            transition: transform 0.2s ease-in-out;
        }
        .booking-card:hover {
            transform: translateY(-2px);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
        }
        .mobile-booking-card {
            transition: all 0.3s ease;
        }
        .mobile-booking-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: -300px;
            width: 300px;
            height: 100%;
            background: linear-gradient(to bottom, #1a73e8, #0d47a1);
            color: white;
            transition: transform 0.3s ease;
            z-index: 1001;
            padding-top: 50px;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.3);
        }
        .sidebar.active {
            transform: translateX(300px);
        }
        .sidebar-close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            color: white;
            padding: 15px;
            font-size: 16px;
            margin-bottom: 8px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }
        .overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .menu-btn {
            display: none;
        }
        .filter-btn {
            transition: all 0.2s ease;
        }
        .filter-btn.active {
            background-color: #1a73e8;
            color: white;
        }
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .menu-btn {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-close" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </div>
        <div class="sidebar-nav">
            <a href="profile.php"><i class="fas fa-user mr-2"></i> Profile</a>
            <a href="mybookings.php"><i class="fas fa-upload mr-2"></i> My Bookings</a>
            <a href="myuploads.php"><i class="fas fa-upload mr-2"></i> My Uploads</a>
            <a href="contact.php"><i class="fas fa-envelope mr-2"></i> Contact</a>
            <a href="wallet.php"><i class="fas fa-wallet mr-2"></i> Wallet</a>
        </div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4 sm:px-6 py-3">
            <nav class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="assets/images/logo.png" alt="MetaTicket Logo" class="w-10 h-10 rounded-full">
                    <h1 class="text-xl font-bold text-blue-600">MetaTicket</h1>
                </div>
                <div class="menu-btn md:hidden text-2xl cursor-pointer" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="nav-links hidden md:flex items-center space-x-6">
                    <a href="mybookings.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">My Bookings</a>
                    <a href="myuploads.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">My Uploads</a>
                    <a href="profile.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">Profile</a>
                    <a href="wallet.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">Wallet</a>
                    <a href="busbuy.php" class="bg-blue-600 text-white px-3 py-1 rounded-full shadow hover:bg-blue-700 transition duration-200 text-sm">
                        <i class="fas fa-ticket-alt mr-1"></i> Book New Tickets
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Page Title -->
    <div class="gradient-bg text-white py-6 text-center shadow-lg rounded-b-xl">
        <div class="container mx-auto px-4 sm:px-6">
            <h2 class="text-2xl sm:text-3xl font-semibold">New Bookings</h2>
            <p class="text-sm sm:text-base mt-1">View and manage your platform bookings</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-bus text-xl sm:text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm sm:text-base">New Bus Bookings</p>
                        <p class="text-xl sm:text-2xl font-semibold"><?php echo $bus_result->num_rows; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-wallet text-xl sm:text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm sm:text-base">Total Spent</p>
                        <p class="text-xl sm:text-2xl font-semibold">₹
                            <?php
                            $total_spent = 0;
                            $bus_result->data_seek(0);
                            while ($row = $bus_result->fetch_assoc()) {
                                $total_spent += $row['total_amount'] ?? $row['ticket_price'];
                            }
                            echo number_format($total_spent, 2);
                            $bus_result->data_seek(0);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="mb-6 flex justify-center space-x-4">
            <a href="?filter=all" class="filter-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-blue-600 hover:text-white <?php echo $filter === 'all' ? 'active' : ''; ?>">
                All Bookings
            </a>
            <a href="?filter=upcoming" class="filter-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-blue-600 hover:text-white <?php echo $filter === 'upcoming' ? 'active' : ''; ?>">
                Upcoming
            </a>
            <a href="?filter=completed" class="filter-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-blue-600 hover:text-white <?php echo $filter === 'completed' ? 'active' : ''; ?>">
                Completed
            </a>
        </div>

        <!-- Desktop Bookings Table - Hidden on Mobile -->
        <div class="hidden sm:block bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">New Bookings</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Journey Details</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route & Seats</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passenger</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $bus_result->data_seek(0);
                        while ($row = $bus_result->fetch_assoc()) {
                            $journey_datetime = new DateTime($row['journey_date'] . ' ' . $row['boarding_time']);
                            $is_upcoming = $journey_datetime > $current_datetime;
                            ?>
                            <tr class="hover:bg-gray-50 booking-card">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex items-center rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-bus mr-2"></i>Bus
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['bus_id']); ?></div>
                                    <div class="text-sm text-gray-500">
                                        <i class="far fa-calendar mr-1"></i><?php echo $journey_datetime->format('d M Y'); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i class="far fa-clock mr-1"></i>Departure: <?php echo date('h:i A', strtotime($row['boarding_time'])); ?>
                                        <?php if ($row['dropping_time']): ?>
                                            <br><i class="far fa-clock mr-1"></i>Arrival: <?php echo date('h:i A', strtotime($row['dropping_time'])); ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <i class="fas fa-map-marker-alt text-red-500 mr-1"></i><?php echo htmlspecialchars($row['from_location']); ?> → <?php echo htmlspecialchars($row['to_location']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Boarding: <?php echo htmlspecialchars($row['boarding_point']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Dropping: <?php echo htmlspecialchars($row['dropping_point']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Seat: <?php echo htmlspecialchars($row['selected_seats']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Ticket ID: <?php echo htmlspecialchars($row['ticket_id']); ?>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['passenger_name']); ?></div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-mobile-alt mr-1"></i><?php echo htmlspecialchars($row['phone_number']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($row['passenger_email']); ?>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">₹<?php echo number_format($row['total_amount'] ?? $row['ticket_price'], 2); ?></div>
                                    <div class="text-xs text-gray-500">Booked on <?php echo date('d M Y', strtotime($row['booking_date'])); ?></div>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <?php if ($is_upcoming): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Upcoming</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="x_ticket_template.php?id=<?php echo $row['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-eye mr-1"></i>Show
                                    </a>
                                </td>
                            </tr>
                        <?php } 
                        if ($bus_result->num_rows == 0) {
                            echo '<tr><td colspan="7" class="px-4 sm:px-6 py-4 text-center text-gray-500">No bookings found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View - Only visible on mobile devices -->
        <div class="sm:hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">New Bookings</h2>
            
            <?php if ($bus_result->num_rows == 0): ?>
                <div class="bg-white rounded-lg shadow p-4 text-center text-gray-500">
                    No bookings found
                </div>
            <?php else: ?>
                <?php 
                $bus_result->data_seek(0);
                while ($row = $bus_result->fetch_assoc()) {
                    $journey_datetime = new DateTime($row['journey_date'] . ' ' . $row['boarding_time']);
                    $is_upcoming = $journey_datetime > $current_datetime;
                ?>
                    <div class="bg-white rounded-lg shadow-md p-4 mb-4 mobile-booking-card">
                        <div class="flex justify-between items-start mb-3">
                            <span class="px-3 py-1 inline-flex items-center rounded-full bg-blue-100 text-blue-800 text-xs">
                                <i class="fas fa-bus mr-1"></i>Bus
                            </span>
                            <?php if ($is_upcoming): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Upcoming</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Completed</span>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3 pb-3 border-b border-gray-100">
                            <div class="font-medium text-gray-900 mb-1"><?php echo htmlspecialchars($row['bus_id']); ?></div>
                            <div class="text-sm text-gray-700 mb-1">
                                <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                <?php echo htmlspecialchars($row['from_location']); ?> → <?php echo htmlspecialchars($row['to_location']); ?>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <div>
                                    <i class="far fa-calendar mr-1"></i><?php echo $journey_datetime->format('d M Y'); ?>
                                </div>
                                <div>
                                    <i class="far fa-clock mr-1"></i><?php echo date('h:i A', strtotime($row['boarding_time'])); ?>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3 pb-3 border-b border-gray-100">
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Boarding</div>
                                <div class="text-sm"><?php echo htmlspecialchars($row['boarding_point']); ?></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Dropping</div>
                                <div class="text-sm"><?php echo htmlspecialchars($row['dropping_point']); ?></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Seat</div>
                                <div class="text-sm font-medium"><?php echo htmlspecialchars($row['selected_seats']); ?></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Price</div>
                                <div class="text-sm font-medium">₹<?php echo number_format($row['total_amount'] ?? $row['ticket_price'], 2); ?></div>
                            </div>
                        </div>
                        <div class="mb-3 pb-3 border-b border-gray-100">
                            <div class="text-xs text-gray-500 mb-1">Passenger</div>
                            <div class="text-sm font-medium"><?php echo htmlspecialchars($row['passenger_name']); ?></div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-mobile-alt mr-1"></i><?php echo htmlspecialchars($row['phone_number']); ?>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="text-xs text-gray-500">
                                Ticket ID: <?php echo htmlspecialchars($row['ticket_id']); ?>
                            </div>
                            <a href="x_ticket_template.php?id=<?php echo $row['id']; ?>" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm transition duration-200">
                                <i class="fas fa-eye mr-1"></i>Show Ticket
                            </a>
                        </div>
                    </div>
                <?php } ?>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $stmt_bus->close();
    $conn->close();
    ?>

    <!-- JavaScript for Sidebar -->
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const body = document.body;

        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        body.classList.toggle('sidebar-open');
    }
    </script>
</body>
</html>
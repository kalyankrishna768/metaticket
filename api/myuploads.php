<?php
// Include database configuration
include 'config.php';

// Start session to track logged-in users
session_start();

// Check if the user is logged in and has an email stored in session
if (!isset($_SESSION['email'])) {
    echo "Error: User is not logged in.";
    exit();
}

// Fetch email of the logged-in user
$userEmail = $_SESSION['email'];

// Fetch user details for personalization
$user_query = "SELECT username FROM signup WHERE email = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("s", $userEmail);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();

// Queries for fetching bus bookings
$bus_query = "SELECT * FROM bus_sell WHERE email = ? ORDER BY journeydate DESC";

// Fetch bus bookings
$stmt_bus = $conn->prepare($bus_query);
if (!$stmt_bus) {
    die("Bus query preparation failed: " . $conn->error);
}
$stmt_bus->bind_param("s", $userEmail);
$stmt_bus->execute();
$bus_result = $stmt_bus->get_result();

// Get today's date at midnight for comparison
$today = new DateTime();
$today->setTime(0, 0, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Uploads | Travel Dashboard</title>
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
        @media (max-width: 640px) {
            .booking-info {
                display: flex;
                flex-direction: column;
            }
            .booking-card-mobile {
                border-bottom: 1px solid #e5e7eb;
                margin-bottom: 1rem;
                padding-bottom: 1rem;
            }
            .action-buttons {
                display: flex;
                gap: 0.5rem;
                margin-top: 0.5rem;
            }
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 80%;
            max-width: 300px;
        }
        .modal-button {
            width: 100%;
            margin-bottom: 8px;
            padding: 8px 0;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .disabled-btn {
            opacity: 0.5;
            cursor: not-allowed;
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
            <a href="mybookings.php"><i class="fas fa-ticket-alt mr-2"></i> My Bookings</a>
            <a href="new_bookings.php"><i class="fas fa-bookmark mr-2"></i> New Bookings</a>
            <a href="contact.php"><i class="fas fa-envelope mr-2"></i> Contact</a>
            <a href="wallet.php"><i class="fas fa-wallet mr-2"></i> Wallet</a>
        </div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4 py-3">
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
                    <a href="new_bookings.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">New Bookings</a>
                    <a href="profile.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">Profile</a>
                    <a href="wallet.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">Wallet</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Page Title -->
    <div class="gradient-bg text-white py-6 text-center shadow-lg rounded-b-xl">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl sm:text-3xl font-semibold">My Uploads</h2>
            <p class="text-sm sm:text-base mt-1">View and manage your uploaded tickets</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-bus text-xl sm:text-2xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4">
                        <p class="text-gray-500 text-sm sm:text-base">Uploaded Tickets</p>
                        <p class="text-xl sm:text-2xl font-semibold"><?php echo $bus_result->num_rows; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-wallet text-xl sm:text-2xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4">
                        <p class="text-gray-500 text-sm sm:text-base">Total Value</p>
                        <p class="text-xl sm:text-2xl font-semibold">₹
                            <?php
                            $total_value = 0;
                            $bus_result->data_seek(0);
                            while($row = $bus_result->fetch_assoc()) {
                                $total_value += $row['ticketprice'];
                            }
                            echo number_format($total_value, 2);
                            $bus_result->data_seek(0);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hidden sm:block">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Uploaded Tickets</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Journey Details</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route & Seat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if ($bus_result->num_rows > 0) {
                            $bus_result->data_seek(0);
                            while ($row = $bus_result->fetch_assoc()) {
                                $journey_date = new DateTime($row['journeydate']);
                                $journey_date->setTime(0, 0, 0);
                                $is_upcoming = $journey_date >= $today;
                                ?>
                                <tr class="hover:bg-gray-50 booking-card">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex items-center rounded-full bg-blue-100 text-blue-800 text-xs">
                                            <i class="fas fa-bus mr-1"></i>Bus
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['busname']); ?></div>
                                        <div class="text-xs text-gray-500">
                                            <i class="far fa-calendar mr-1"></i><?php echo $journey_date->format('d M Y'); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <i class="far fa-clock mr-1"></i>Departure: <?php echo htmlspecialchars($row['boarding_time']); ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-1"></i><?php echo htmlspecialchars($row['fromplace']); ?> → <?php echo htmlspecialchars($row['toplace']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Seat: <?php echo htmlspecialchars($row['seat_no']); ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">₹<?php echo number_format($row['ticketprice'], 2); ?></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?php if($row['status'] == 'Confirmed' && $is_upcoming): ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Upcoming</span>
                                        <?php elseif($row['status'] == 'Confirmed'): ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Completed</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800"><?php echo htmlspecialchars($row['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs">
                                        <a href="details.php?id=<?php echo $row['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                            <i class="fas fa-eye mr-1"></i>Show
                                        </a>
                                    </td>
                                </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                                    No uploaded tickets found.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="sm:hidden">
            <div class="bg-white rounded-lg shadow-lg p-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Uploaded Tickets</h2>
                <?php
                if ($bus_result->num_rows > 0) {
                    $bus_result->data_seek(0);
                    while ($row = $bus_result->fetch_assoc()) {
                        $journey_date = new DateTime($row['journeydate']);
                        $journey_date->setTime(0, 0, 0);
                        $is_upcoming = $journey_date >= $today;
                        ?>
                        <div class="booking-card-mobile">
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-1 inline-flex items-center rounded-full bg-blue-100 text-blue-800 text-xs">
                                    <i class="fas fa-bus mr-1"></i>Bus
                                </span>
                                <?php if($row['status'] == 'Confirmed' && $is_upcoming): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Upcoming</span>
                                <?php elseif($row['status'] == 'Confirmed'): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Completed</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800"><?php echo htmlspecialchars($row['status']); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($row['busname']); ?></h3>
                            <div class="text-sm text-gray-700 mt-2">
                                <div class="flex items-start mb-1">
                                    <i class="far fa-calendar mt-1 mr-2 text-gray-500"></i>
                                    <div><?php echo $journey_date->format('d M Y'); ?></div>
                                </div>
                                <div class="flex items-start mb-1">
                                    <i class="fas fa-map-marker-alt mt-1 mr-2 text-red-500"></i>
                                    <div><?php echo htmlspecialchars($row['fromplace']); ?> → <?php echo htmlspecialchars($row['toplace']); ?></div>
                                </div>
                                <div class="flex items-start mb-1">
                                    <i class="far fa-clock mt-1 mr-2 text-gray-500"></i>
                                    <div>Departure: <?php echo htmlspecialchars($row['boarding_time']); ?></div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mt-2">
                                    <div>
                                        <div>Seat:</div>
                                        <div class="font-medium"><?php echo htmlspecialchars($row['seat_no']); ?></div>
                                    </div>
                                    <div>
                                        <div>Price:</div>
                                        <div class="font-medium">₹<?php echo number_format($row['ticketprice'], 2); ?></div>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mt-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">₹<?php echo number_format($row['ticketprice'], 2); ?></div>
                                    </div>
                                    <div class="action-buttons">
                                        <a href="details.php?id=<?php echo $row['id']; ?>" class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-md text-xs">
                                            <i class="fas fa-eye mr-1"></i>Show
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } 
                } else { ?>
                    <div class="text-center text-gray-500 py-4">
                        No uploaded tickets found.
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center text-gray-500 mt-8 pb-4">
        <p>© <?php echo date('Y'); ?> MetaTicket. All rights reserved.</p>
    </footer>

    <!-- JavaScript -->
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

<?php
$stmt_bus->close();
$stmt_user->close();
$conn->close();
?>
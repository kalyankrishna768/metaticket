<?php
include 'config.php';

session_start();

// Check if the user is logged in and has an email stored in session
if (!isset($_SESSION['id'])) {
    echo "Error: User is not logged in.";
    exit();
}

// Fetch user ID of the logged-in user
$user_id = $_SESSION['id'];

// Get filter parameter if exists
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Prepare SQL query based on filter
$bus_query = "SELECT b.*, 'Bus' as type, b.from_location, b.to_location, b.boarding_point, b.dropping_point, 
              b.selected_seats, b.total_amount, b.boarding_time, b.dropping_time, b.booking_status 
              FROM bookings b 
              WHERE b.user_id = ? 
              AND NOT EXISTS (
                  SELECT 1 FROM new_bookings nb 
                  WHERE nb.seat_no = b.selected_seats 
                  AND nb.from_location = b.from_location 
                  AND nb.to_location = b.to_location 
                  AND nb.journey_date = b.journeydate 
                  AND nb.bus_id = b.bus_id
              )";

if ($filter === 'upcoming') {
    $bus_query .= " AND b.journeydate >= CURDATE() AND b.booking_status != 'cancelled'";
} elseif ($filter === 'cancelled') {
    $bus_query .= " AND b.booking_status = 'cancelled'";
} elseif ($filter === 'completed') {
    $bus_query .= " AND b.journeydate < CURDATE() AND b.booking_status != 'cancelled'";
}

$bus_query .= " ORDER BY b.journeydate, b.boarding_time";

$stmt_bus = $conn->prepare($bus_query);
$stmt_bus->bind_param("i", $user_id);
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
    <title>My Bookings | Travel Dashboard</title>
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
            <a href="new_bookings.php"><i class="fas fa-bookmark mr-2"></i> New Bookings</a>
            <a href="myuploads.php"><i class="fas fa-upload mr-2"></i> My Uploads</a>
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
                    <a href="myuploads.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">My Uploads</a>
                    <a href="profile.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">Profile</a>
                    <a href="wallet.php" class="text-gray-700 hover:text-blue-600 font-medium transition duration-200">Wallet</a>
                    <a href="new_bookings.php" class="bg-blue-600 text-white px-3 py-1 rounded-full shadow hover:bg-blue-700 transition duration-200 text-sm">
                        <i class="fas fa-bookmark mr-1"></i> New Bookings
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Page Title -->
    <div class="gradient-bg text-white py-6 text-center shadow-lg rounded-b-xl">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl sm:text-3xl font-semibold">My Bookings</h2>
            <p class="text-sm sm:text-base mt-1">Manage your travel bookings</p>
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
                        <p class="text-gray-500 text-sm sm:text-base">Bus Bookings</p>
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
                        <p class="text-gray-500 text-sm sm:text-base">Total Spent</p>
                        <p class="text-xl sm:text-2xl font-semibold">₹
                            <?php
                            $total_spent = 0;
                            $bus_result->data_seek(0);
                            while($row = $bus_result->fetch_assoc()) {
                                if ($row['booking_status'] != 'cancelled') {
                                    $total_spent += $row['total_amount'] ?? $row['ticketprice'];
                                }
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
            <a href="?filter=cancelled" class="filter-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-blue-600 hover:text-white <?php echo $filter === 'cancelled' ? 'active' : ''; ?>">
                Cancelled
            </a>
            <a href="?filter=completed" class="filter-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-blue-600 hover:text-white <?php echo $filter === 'completed' ? 'active' : ''; ?>">
                Completed
            </a>
        </div>

        <!-- Bookings Table for larger screens -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hidden sm:block">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Recent Bookings</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Journey Details</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route & Seats</th>
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
                                $can_cancel = $journey_date >= $today && $row['booking_status'] != 'cancelled';
                                $is_cancelled = $row['booking_status'] == 'cancelled';

                                $check_sell_query = "SELECT COUNT(*) FROM bus_sell WHERE booking_id = ? AND seat_no = ?";
                                $stmt_sell = $conn->prepare($check_sell_query);
                                $stmt_sell->bind_param("is", $row['id'], $row['selected_seats']);
                                $stmt_sell->execute();
                                $stmt_sell->bind_result($sell_count);
                                $stmt_sell->fetch();
                                $stmt_sell->close();
                                $is_sold = $sell_count > 0;

                                $can_cancel = $can_cancel && !$is_sold;
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
                                            <i class="far fa-clock mr-1"></i>Departure: <?php echo date('h:i A', strtotime($row['boarding_time'])); ?>
                                            <?php if($row['dropping_time']): ?>
                                                <br><i class="far fa-clock mr-1"></i>Arrival: <?php echo date('h:i A', strtotime($row['dropping_time'])); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
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
                                            Seats: <?php echo htmlspecialchars($row['selected_seats']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Booking ID: <?php echo htmlspecialchars($row['id']); ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">₹<?php echo number_format($row['total_amount'] ?? $row['ticketprice'], 2); ?></div>
                                        <div class="text-xs text-gray-500">Booked on <?php echo date('d M Y', strtotime($row['booking_date'])); ?></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?php if($is_cancelled): ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelled</span>
                                        <?php elseif($is_upcoming): ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Upcoming</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs">
                                        <?php if(!$is_cancelled): ?>
                                            <a href="ticket_template.php?id=<?php echo $row['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                <i class="fas fa-eye mr-1"></i>Show
                                            </a>
                                        <?php endif; ?>
                                        <?php if($can_cancel): ?>
                                            <button class="text-red-600 hover:text-red-900 cancel-btn" data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-times mr-1"></i>Cancel
                                            </button>
                                        <?php elseif($is_sold): ?>
                                            <span class="text-gray-500 disabled-btn"><i class="fas fa-times mr-1"></i>Sold</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                                    No bookings found matching the selected filter.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Booking Cards -->
        <div class="sm:hidden">
            <div class="bg-white rounded-lg shadow-lg p-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Bookings</h2>
                
                <?php
                if ($bus_result->num_rows > 0) {
                    $bus_result->data_seek(0);
                    while ($row = $bus_result->fetch_assoc()) {
                        $journey_date = new DateTime($row['journeydate']);
                        $journey_date->setTime(0, 0, 0);
                        
                        $is_upcoming = $journey_date >= $today;
                        $can_cancel = $journey_date >= $today && $row['booking_status'] != 'cancelled';
                        $is_cancelled = $row['booking_status'] == 'cancelled';

                        $check_sell_query = "SELECT COUNT(*) FROM bus_sell WHERE booking_id = ? AND seat_no = ?";
                        $stmt_sell = $conn->prepare($check_sell_query);
                        $stmt_sell->bind_param("is", $row['id'], $row['selected_seats']);
                        $stmt_sell->execute();
                        $stmt_sell->bind_result($sell_count);
                        $stmt_sell->fetch();
                        $stmt_sell->close();
                        $is_sold = $sell_count > 0;

                        $can_cancel = $can_cancel && !$is_sold;
                        ?>
                        <div class="booking-card-mobile">
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-1 inline-flex items-center rounded-full bg-blue-100 text-blue-800 text-xs">
                                    <i class="fas fa-bus mr-1"></i>Bus
                                </span>
                                <?php if($is_cancelled): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelled</span>
                                <?php elseif($is_upcoming): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Upcoming</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Completed</span>
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
                                    <div><?php echo htmlspecialchars($row['from_location']); ?> → <?php echo htmlspecialchars($row['to_location']); ?></div>
                                </div>
                                <div class="flex items-start mb-1">
                                    <i class="far fa-clock mt-1 mr-2 text-gray-500"></i>
                                    <div>
                                        <div>Departure: <?php echo date('h:i A', strtotime($row['boarding_time'])); ?></div>
                                        <?php if($row['dropping_time']): ?>
                                            <div>Arrival: <?php echo date('h:i A', strtotime($row['dropping_time'])); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mt-2">
                                    <div>
                                        <div>Boarding:</div>
                                        <div class="font-medium"><?php echo htmlspecialchars($row['boarding_point']); ?></div>
                                    </div>
                                    <div>
                                        <div>Dropping:</div>
                                        <div class="font-medium"><?php echo htmlspecialchars($row['dropping_point']); ?></div>
                                    </div>
                                    <div>
                                        <div>Seats:</div>
                                        <div class="font-medium"><?php echo htmlspecialchars($row['selected_seats']); ?></div>
                                    </div>
                                    <div>
                                        <div>Booking ID:</div>
                                        <div class="font-medium"><?php echo htmlspecialchars($row['id']); ?></div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center mt-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">₹<?php echo number_format($row['total_amount'] ?? $row['ticketprice'], 2); ?></div>
                                        <div class="text-xs text-gray-500">Booked on <?php echo date('d M Y', strtotime($row['booking_date'])); ?></div>
                                    </div>
                                    
                                    <div class="action-buttons">
                                        <?php if(!$is_cancelled): ?>
                                            <a href="ticket_template.php?id=<?php echo $row['id']; ?>" class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-md text-xs">
                                                <i class="fas fa-eye mr-1"></i>Show
                                            </a>
                                        <?php endif; ?>
                                        <?php if($can_cancel): ?>
                                            <button class="bg-red-100 text-red-600 px-3 py-1 rounded-md text-xs cancel-btn-mobile" data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-times mr-1"></i>Cancel
                                            </button>
                                        <?php elseif($is_sold): ?>
                                            <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-md text-xs disabled-btn">
                                                <i class="fas fa-times mr-1"></i>Sold
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } 
                } else { ?>
                    <div class="text-center text-gray-500 py-4">
                        No bookings found matching the selected filter.
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Modal for Cancel/Sell Options -->
    <div id="optionsModal" class="modal">
        <div class="modal-content">
            <h3 class="text-lg font-medium text-gray-900 mb-4">What would you like to do?</h3>
            <button id="cancelOption" class="modal-button bg-red-100 text-red-600 hover:bg-red-200">
                <i class="fas fa-times mr-2"></i>Cancel Booking
            </button>
            <button id="sellOption" class="modal-button bg-green-100 text-green-600 hover:bg-green-200">
                <i class="fas fa-tag mr-2"></i>Sell Ticket
            </button>
            <button id="closeModal" class="modal-button bg-gray-100 text-gray-600 hover:bg-gray-200">
                <i class="fas fa-times-circle mr-2"></i>Close
            </button>
        </div>
    </div>

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

    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('optionsModal');
        var cancelOption = document.getElementById('cancelOption');
        var sellOption = document.getElementById('sellOption');
        var closeModal = document.getElementById('closeModal');
        var currentBookingId = null;

        var cancelButtons = document.querySelectorAll('.cancel-btn, .cancel-btn-mobile');
        
        cancelButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                currentBookingId = this.getAttribute('data-id');
                modal.style.display = 'block';
            });
        });

        cancelOption.addEventListener('click', function() {
            window.location.href = 'cancel.php?id=' + currentBookingId;
        });

        sellOption.addEventListener('click', function() {
            window.location.href = 'sell.php?id=' + currentBookingId;
        });

        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>
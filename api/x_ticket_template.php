<?php
include 'config.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    echo "Error: Booking ID not provided.";
    exit();
}

$booking_id = $_GET['id'];
$user_email = $_SESSION['email'];

// Fetch booking details from new_bookings table
$query = "SELECT nb.*, 'Bus' as type
          FROM new_bookings nb 
          WHERE nb.id = ? AND nb.user_email = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $booking_id, $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Error: Booking not found or you don't have permission to view this ticket.";
    exit();
}

$booking = $result->fetch_assoc();

// Format dates
$journey_date = new DateTime($booking['journey_date']);
$formatted_journey_date = $journey_date->format('d M Y');
$booking_date = new DateTime($booking['booking_date']);
$formatted_booking_date = $booking_date->format('d M Y');

// Generate a ticket number using booking id and some random chars
$ticket_number = $booking['ticket_id'] ?? "TKT" . strtoupper(substr(md5($booking_id), 0, 8)) . $booking_id;

// Calculate journey time
if (isset($booking['boarding_time']) && isset($booking['dropping_time'])) {
    $boarding_time = new DateTime($booking['boarding_time']);
    $dropping_time = new DateTime($booking['dropping_time']);
    $time_diff = $boarding_time->diff($dropping_time);
    $journey_hours = $time_diff->h + ($time_diff->days * 24);
    $journey_minutes = $time_diff->i;
    $journey_time = "{$journey_hours}h {$journey_minutes}m";
} else {
    $journey_time = "N/A";
}

// Get seat info
$seat_info = $booking['seat_no'] ?? 'N/A';

// Check if cancelled
$is_cancelled = isset($booking['booking_status']) && strtolower($booking['booking_status']) == 'cancelled';
$is_confirmed = isset($booking['booking_status']) && strtolower($booking['booking_status']) == 'confirmed';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket | <?php echo htmlspecialchars($booking['bus_id']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .ticket {
            background-color: white;
            position: relative;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .ticket:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .ticket-divider {
            height: 1px;
            width: 100%;
            background: repeating-linear-gradient(90deg, #e2e8f0, #e2e8f0 6px, transparent 6px, transparent 12px);
        }
        .ticket-hole {
            height: 25px;
            width: 25px;
            background-color: #f3f4f6;
            border-radius: 50%;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                background-color: white !important;
            }
            .ticket {
                box-shadow: none;
            }
        }
        .route-line {
            position: relative;
            height: 2px;
            background-color: #d1d5db;
            margin: 0 10px;
        }
        .route-line:before, .route-line:after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #764ba2;
            border-radius: 50%;
            top: -4px;
        }
        .route-line:before {
            left: -10px;
        }
        .route-line:after {
            right: -10px;
        }
        .barcode {
            background-image: linear-gradient(90deg, #000 0%, #000 8%, transparent 8%, transparent 12%,
                                              #000 12%, #000 16%, transparent 16%, transparent 20%,
                                              #000 20%, #000 28%, transparent 28%, transparent 32%,
                                              #000 32%, #000 40%, transparent 40%, transparent 44%,
                                              #000 44%, #000 52%, transparent 52%, transparent 56%,
                                              #000 56%, #000 64%, transparent 64%, transparent 68%,
                                              #000 68%, #000 76%, transparent 76%, transparent 80%,
                                              #000 80%, #000 88%, transparent 88%, transparent 92%,
                                              #000 92%, #000 100%);
            height: 60px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            color: white;
        }

        .user-profile img {
            width: 40px;
            margin-right: 10px;
            border-radius: 50%;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-10">
    <div class="container mx-auto px-4">
        <!-- Back Button -->
        <div class="mb-6 no-print">
            <a href="new_bookings.php" class="inline-flex items-center text-purple-600 hover:text-purple-800 transition-all duration-300">
                <i class="fas fa-chevron-left mr-2"></i>
                <span>Back to Bookings</span>
            </a>
            <button class="ml-4 bg-purple-600 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-700 transition duration-200 no-print" onclick="window.print()">
                <i class="fas fa-print mr-2"></i>Print Ticket
            </button>
        </div>

        <!-- Ticket Container -->
        <div class="max-w-3xl mx-auto">
            <!-- Ticket Header -->
            <div class="ticket rounded-lg overflow-hidden mb-8">
                <div class="ticket-header text-white p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="user-profile">
                                <img src="assets/images/logo.png" alt="Bus Ticket Logo">
                                <h1 class="text-2xl font-bold">Bus Ticket</h1>
                            </div>
                        </div>
                        <div class="text-right">
                            <?php if ($is_cancelled): ?>
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">CANCELLED</span>
                            <?php elseif ($is_confirmed): ?>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">CONFIRMED</span>
                            <?php else: ?>
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">PENDING</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Journey Details -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <p class="text-gray-500 text-sm">Bus</p>
                            <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($booking['bus_id']); ?></h2>
                            <p class="text-sm opacity-80"><?php echo htmlspecialchars($ticket_number); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500 text-sm">Journey Date</p>
                            <h2 class="text-xl font-bold text-gray-800"><?php echo $formatted_journey_date; ?></h2>
                        </div>
                    </div>

                    <!-- Route Information -->
                    <div class="mb-6">
                        <div class="grid grid-cols-5 gap-4 items-center mb-4">
                            <div class="col-span-2">
                                <p class="text-gray-500 text-sm">From</p>
                                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($booking['from_location']); ?></h3>
                                <p class="text-gray-600 text-sm">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                    <?php echo htmlspecialchars($booking['boarding_point']); ?>
                                </p>
                                <p class="text-gray-600 text-sm">
                                    <i class="far fa-clock text-blue-500 mr-1"></i>
                                    <?php echo date('h:i A', strtotime($booking['boarding_time'])); ?>
                                </p>
                            </div>
                            <div class="col-span-1 flex justify-center items-center">
                                <div class="route-line flex-grow"></div>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-500 text-sm">To</p>
                                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($booking['to_location']); ?></h3>
                                <p class="text-gray-600 text-sm">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                    <?php echo htmlspecialchars($booking['dropping_point']); ?>
                                </p>
                                <p class="text-gray-600 text-sm">
                                    <i class="far fa-clock text-blue-500 mr-1"></i>
                                    <?php echo isset($booking['dropping_time']) ? date('h:i A', strtotime($booking['dropping_time'])) : 'N/A'; ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between text-sm text-gray-500 mt-2">
                            <div>
                                <i class="fas fa-clock mr-1"></i>Journey Time: <?php echo $journey_time; ?>
                            </div>
                            <div>
                                <i class="fas fa-calendar-alt mr-1"></i>Booked on: <?php echo $formatted_booking_date; ?>
                            </div>
                        </div>
                    </div>

                    <div class="ticket-divider my-4"></div>

                    <!-- Passenger Details -->
                    <div class="mb-4">
                        <p class="text-gray-500 text-sm mb-2">Passenger Details</p>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Name</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Age</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Gender</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Seat</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t">
                                        <td class="px-4 py-2 text-sm text-gray-800"><?php echo htmlspecialchars($booking['passenger_name']); ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-800"><?php echo htmlspecialchars($booking['age']); ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-800"><?php echo htmlspecialchars($booking['gender']); ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-800"><?php echo htmlspecialchars($booking['seat_no']); ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-600">
                                            <?php if (!empty($booking['phone_number'])): ?>
                                                <div><i class="fas fa-phone text-green-500 mr-1"></i><?php echo htmlspecialchars($booking['phone_number']); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($booking['passenger_email'])): ?>
                                                <div><i class="fas fa-envelope text-blue-500 mr-1"></i><?php echo htmlspecialchars($booking['passenger_email']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="ticket-divider my-4"></div>

                    <!-- Payment Details -->
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-500 text-sm">Payment</p>
                            <h3 class="text-lg font-semibold text-gray-800">Booking ID: <?php echo htmlspecialchars($booking['id']); ?></h3>
                            <p class="text-gray-600 text-sm"><?php echo isset($booking['payment_method']) ? htmlspecialchars($booking['payment_method']) : 'Online Payment'; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500 text-sm">Total Amount</p>
                            <h3 class="text-xl font-bold text-gray-800">₹<?php echo number_format($booking['total_amount'], 2); ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Ticket Footer -->
                <div class="bg-gray-50 p-6">
                    <div class="flex justify-center mb-4">
                        <div class="text-center">
                            <div class="barcode w-64 mx-auto mb-2"></div>
                            <p class="text-gray-600 text-sm"><?php echo $ticket_number; ?></p>
                        </div>
                    </div>
                    
                    <div class="text-gray-500 text-sm text-center">
                        <p>This is a computer-generated ticket and does not require a physical signature.</p>
                        <p class="mt-2">For any support, contact us at: support@example.com | +91 99999 99999</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
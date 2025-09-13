<?php
include 'config.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: newsignin.php");
    exit();
}

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    header("Location: mybookings.php");
    exit();
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['id'];

// Fetch booking details
$query = "SELECT b.*, 'Bus' as type, b.bus_id, b.from_location, b.to_location, b.boarding_point, b.dropping_point, 
          b.selected_seats, b.total_amount, b.boarding_time, b.dropping_time, b.booking_status, b.journeydate 
          FROM bookings b 
          WHERE b.id = ? AND b.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$booking = $result->fetch_assoc();

// Split seats for display
$seats_array = explode(',', $booking['selected_seats']);
$total_seats = count($seats_array);
$amount_per_seat = $booking['total_amount'] / $total_seats;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Ticket | Travel Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .policy-checkbox:checked+label::before {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        
        .card-shadow {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .card-shadow:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .btn-hover {
            transition: transform 0.2s ease-in-out;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
        }
        
        .feature-icon {
            color: #4f46e5;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Top Navigation -->
    <nav class="gradient-bg text-white shadow-lg sticky top-0 z-10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-ticket-alt text-xl sm:text-2xl mr-3 rotate-45"></i>
                    <h1 class="text-lg sm:text-2xl font-bold">Sell Ticket</h1>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-2xl mx-auto card-shadow">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <div class="flex items-center">
                    <i class="fas fa-hand-holding-dollar text-2xl text-indigo-600 mr-3"></i>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Sell Your Ticket</h2>
                        <p class="text-gray-600 mt-1">List your ticket for sale to other travelers</p>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center mb-4">
                    <i class="fas fa-info-circle text-lg text-indigo-600 mr-2"></i>
                    <h3 class="text-lg font-medium text-gray-800">Booking Details</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start">
                        <i class="fas fa-bus text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">Bus Name</p>
                            <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($booking['busname']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-calendar-alt text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">Journey Date</p>
                            <p class="text-gray-800 font-medium">
                                <?php echo date('d M Y', strtotime($booking['journeydate'])); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">From</p>
                            <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($booking['from_location']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-map-pin text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">To</p>
                            <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($booking['to_location']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-clock text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">Departure Time</p>
                            <p class="text-gray-800 font-medium">
                                <?php echo date('h:i A', strtotime($booking['boarding_time'])); ?></p>
                        </div>
                    </div>
                    <?php if ($booking['dropping_time']): ?>
                        <div class="flex items-start">
                            <i class="fas fa-hourglass-end text-indigo-500 mt-1 mr-3 feature-icon"></i>
                            <div>
                                <p class="text-sm text-gray-500">Arrival Time</p>
                                <p class="text-gray-800 font-medium">
                                    <?php echo date('h:i A', strtotime($booking['dropping_time'])); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="flex items-start">
                        <i class="fas fa-sign-in-alt text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">Boarding Point</p>
                            <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($booking['boarding_point']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-sign-out-alt text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">Dropping Point</p>
                            <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($booking['dropping_point']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-chair text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">Seats</p>
                            <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($booking['selected_seats']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-rupee-sign text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">Amount per Seat</p>
                            <p class="text-gray-800 font-medium">₹<?php echo number_format($amount_per_seat, 2); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-rupee-sign text-indigo-500 mt-1 mr-3 feature-icon"></i>
                        <div>
                            <p class="text-sm text-gray-500">Total Amount</p>
                            <p class="text-gray-800 font-medium">₹<?php echo number_format($booking['total_amount'], 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Policy Notes -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center mb-4">
                    <i class="fas fa-clipboard-list text-lg text-indigo-600 mr-2"></i>
                    <h3 class="text-lg font-medium text-gray-800">Policy Notes</h3>
                </div>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-start">
                        <i class="fas fa-exclamation-circle mt-1 mr-2"></i>
                        <div><?php echo $error_message; ?></div>
                    </div>
                <?php endif; ?>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <span class="font-bold">Note:</span> Once you list your ticket for sale, it will be
                                available for other travelers to purchase.
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="sellDB.php">
                    <div class="space-y-5">
                        <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                            <div class="flex items-center h-5 mt-1">
                                <input id="policy1" name="policy1" type="checkbox"
                                    class="policy-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded" checked
                                    disabled>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="policy1" class="font-medium text-gray-700 flex items-start">
                                    <i class="fas fa-wallet text-indigo-500 mr-2 mt-1"></i>
                                    <span>If anyone buys your ticket, only then will your ticket money be transferred to your wallet. You can check in
                                    available tickets page. If anyone books it, it will disappear from available tickets
                                    page.</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                            <div class="flex items-center h-5 mt-1">
                                <input id="policy2" name="policy2" type="checkbox"
                                    class="policy-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded" checked
                                    disabled>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="policy2" class="font-medium text-gray-700 flex items-start">
                                    <i equallyclass="fas fa-headset text-indigo-500 mr-2 mt-1"></i>
                                    <span>If you want to cancel your sold ticket and continue your journey, please contact us through the contact
                                    page.</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-start mt-6 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                            <div class="flex items-center h-5 mt-1">
                                <input id="agree_policy" name="agree_policy" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="agree_policy" class="font-medium text-gray-700 flex items-center">
                                    <i class="fas fa-check-circle text-indigo-500 mr-2"></i>
                                    <span>I have read and agree to the ticket selling policy</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for booking details -->
                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                    <input type="hidden" name="busname" value="<?php echo htmlspecialchars($booking['busname']); ?>">
                    <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($booking['bus_id']); ?>">
                    <input type="hidden" name="journeydate" value="<?php echo htmlspecialchars($booking['journeydate']); ?>">
                    <input type="hidden" name="from_location" value="<?php echo htmlspecialchars($booking['from_location']); ?>">
                    <input type="hidden" name="to_location" value="<?php echo htmlspecialchars($booking['to_location']); ?>">
                    <input type="hidden" name="boarding_time" value="<?php echo htmlspecialchars($booking['boarding_time']); ?>">
                    <input type="hidden" name="dropping_time" value="<?php echo htmlspecialchars($booking['dropping_time']); ?>">
                    <input type="hidden" name="boarding_point" value="<?php echo htmlspecialchars($booking['boarding_point']); ?>">
                    <input type="hidden" name="dropping_point" value="<?php echo htmlspecialchars($booking['dropping_point']); ?>">
                    <input type="hidden" name="selected_seats" value="<?php echo htmlspecialchars($booking['selected_seats']); ?>">
                    <input type="hidden" name="amount_per_seat" value="<?php echo htmlspecialchars($amount_per_seat); ?>">
                    <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($booking['total_amount']); ?>">
                    <input type="hidden" name="booking_status" value="<?php echo htmlspecialchars($booking['booking_status']); ?>">

                    <div class="mt-8 flex items-center justify-between flex-wrap gap-4">
                        <a href="mybookings.php"
                            class="bg-gray-100 py-2 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center btn-hover">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center btn-hover">
                            <i class="fas fa-tag mr-2"></i>
                            List Ticket for Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Additional Information Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-2xl mx-auto card-shadow mt-6">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
                <div class="flex items-center">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                    <h3 class="text-md font-medium text-gray-800">Helpful Tips</h3>
                </div>
            </div>
            <div class="p-4">
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span class="text-sm text-gray-600">Your ticket will be visible to all users looking for tickets on this route.</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span class="text-sm text-gray-600">For any issues or queries, contact customer support.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const agreeCheckbox = document.getElementById('agree_policy');
            if (!agreeCheckbox.checked) {
                e.preventDefault();
                alert('Please agree to the ticket selling policy to continue.');
            }
        });
    </script>
</body>

</html>
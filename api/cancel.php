<?php
include 'config.php';

session_start();

// Set the timezone to ensure consistency with the database
date_default_timezone_set('UTC'); // Adjust this to your server's timezone, e.g., 'Asia/Kolkata'

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    echo "Error: No booking ID provided.";
    exit();
}

$booking_id = $_GET['id'];

// Fetch booking details
$booking_query = "SELECT b.*, 
                 u.email as user_email 
                 FROM bookings b 
                 JOIN signup u ON b.user_id = u.id
                 WHERE b.id = ? AND b.user_id = ?";
$stmt_booking = $conn->prepare($booking_query);
$stmt_booking->bind_param("ii", $booking_id, $user_id);
$stmt_booking->execute();
$booking_result = $stmt_booking->get_result();

if ($booking_result->num_rows == 0) {
    echo "Error: Booking not found or does not belong to you.";
    exit();
}

$booking = $booking_result->fetch_assoc();

$from_location = $booking['from_location'] ?? '';
$to_location = $booking['to_location'] ?? '';
$selected_seats = $booking['selected_seats'] ?? '';
$bus_id = $booking['bus_id'] ?? '';
$journey_date = $booking['journey_date'] ?? '';

// Check if booking is already cancelled
if ($booking['booking_status'] == 'cancelled') {
    echo "This booking has already been cancelled.";
    exit();
}

// Fetch agency email from agency_bookings table
$agency_query = "SELECT agency_email FROM agency_bookings WHERE bus_id = ? LIMIT 1";
$stmt_agency = $conn->prepare($agency_query);
$stmt_agency->bind_param("i", $booking['bus_id']);
$stmt_agency->execute();
$agency_result = $stmt_agency->get_result();

if ($agency_result->num_rows == 0) {
    echo "Error: Agency information not found.";
    exit();
}

$agency_data = $agency_result->fetch_assoc();
$agency_email = $agency_data['agency_email'];

// Calculate refund amount based on policy
$current_time = new DateTime();
$journey_datetime = new DateTime($booking['journeydate'] . ' ' . $booking['boarding_time']);
$time_difference = $current_time->diff($journey_datetime);

// Convert time difference to hours
$hours_difference = $time_difference->days * 24 + $time_difference->h;
$refund_percentage = ($hours_difference > 24) ? 0.8 : 0;
$refund_amount = $booking['total_amount'] * $refund_percentage;

// Process cancellation if form is submitted
$cancellation_processed = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_cancel'])) {
    try {
        // Start transaction
        $conn->begin_transaction();

        // Update booking status in bookings
        $update_booking = "UPDATE bookings SET booking_status = 'cancelled' WHERE id = ?";
        $stmt_update = $conn->prepare($update_booking);
        $stmt_update->bind_param("i", $booking_id);
        $stmt_update->execute();

        // Extract seat numbers from the selected_seats string
        $seats_array = explode(',', $booking['selected_seats']);

        // Update all rows in agency_bookings with the given booking_id
        $update_agency_booking = "UPDATE agency_bookings 
SET booking_status = 'Cancelled' 
WHERE booking_id = ?";
        $stmt_update_agency = $conn->prepare($update_agency_booking);
        $stmt_update_agency->bind_param("i", $booking_id);
        $stmt_update_agency->execute();

        // Check if any rows were affected
        if ($stmt_update_agency->affected_rows == 0) {
            // If no rows were affected with booking_id, fall back to the original approach
            foreach ($seats_array as $seat) {
                $seat = trim($seat);
                $update_agency_booking = "UPDATE agency_bookings 
SET booking_status = 'Cancelled' 
WHERE bus_id = ? AND from_location = ? AND to_location = ? AND journey_date = ? AND seat_number = ?";

                $stmt_update_agency = $conn->prepare($update_agency_booking);
                $stmt_update_agency->bind_param(
                    "issss",
                    $booking['bus_id'],
                    $booking['from_location'],
                    $booking['to_location'],
                    $booking['journey_date'],
                    $seat
                );
                $stmt_update_agency->execute();
            }
        }

        // Process refund if applicable
        if ($refund_amount > 0) {
            // Deduct from agency wallet
            $update_agency_wallet = "UPDATE agency_wallet SET balance = balance - ? WHERE email = ?";
            $stmt_agency_wallet = $conn->prepare($update_agency_wallet);
            $stmt_agency_wallet->bind_param("ds", $refund_amount, $agency_email);
            $stmt_agency_wallet->execute();

            if ($stmt_agency_wallet->affected_rows == 0) {
                throw new Exception("Agency wallet not found or insufficient balance.");
            }

            // FIX 2: First add or update user wallet
            $check_wallet = "SELECT id FROM user_wallets WHERE user_id = ? LIMIT 1";
            $stmt_check = $conn->prepare($check_wallet);
            $stmt_check->bind_param("i", $user_id);
            $stmt_check->execute();
            $wallet_result = $stmt_check->get_result();

            if ($wallet_result->num_rows > 0) {
                // Wallet exists - update it
                $wallet_data = $wallet_result->fetch_assoc();
                $wallet_id = $wallet_data['id'];

                $update_wallet = "UPDATE user_wallets SET balance = balance + ? WHERE id = ?";
                $stmt_wallet_update = $conn->prepare($update_wallet);
                $stmt_wallet_update->bind_param("di", $refund_amount, $wallet_id);
                $stmt_wallet_update->execute();
            } else {
                // Create new wallet
                $insert_wallet = "INSERT INTO user_wallets (user_id, balance) VALUES (?, ?)";
                $stmt_insert_wallet = $conn->prepare($insert_wallet);
                $stmt_insert_wallet->bind_param("id", $user_id, $refund_amount);
                $stmt_insert_wallet->execute();

                // Get the newly created wallet ID
                $wallet_id = $conn->insert_id;
            }

            // Record transaction in wallet_transactions using NOW() for transaction_date
            $transaction_type = "Deposit";
            $insert_transaction = "INSERT INTO wallet_transactions (wallet_id, type, amount, transaction_date) 
                                VALUES (?, ?, ?, NOW())";
            $stmt_transaction = $conn->prepare($insert_transaction);
            $stmt_transaction->bind_param("isd", $wallet_id, $transaction_type, $refund_amount);
            $stmt_transaction->execute();

            // FIX 3: Update agency_wallet_transactions with correct transaction type and current time
            $transaction_type = "debit"; // Agency is losing money, so it's a debit
            $description = "Refund for booking #" . $booking_id;
            $status = "completed";
            $transaction_id = "TXN" . time() . rand(1000, 9999);

            $insert_agency_transaction = "INSERT INTO agency_wallet_transactions 
                                        (transaction_id, email, amount, type, description, status, transaction_date) 
                             VALUES (?, ?, ?, ?, ?, ?, NOW())";

            $stmt_agency_transaction = $conn->prepare($insert_agency_transaction);
            $stmt_agency_transaction->bind_param(
                "ssdsss",
                $transaction_id,
                $agency_email,
                $refund_amount,
                $transaction_type,
                $description,
                $status
            );
            $stmt_agency_transaction->execute();
        }

        // Commit transaction
        $conn->commit();
        $cancellation_processed = true;

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $error_message = "Error processing cancellation: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking | Travel System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        @media (max-width: 640px) {
            .card-container {
                padding: 0.75rem;
            }
            .info-grid {
                gap: 1rem;
            }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
            100% {
                opacity: 1;
            }
        }
        
        .custom-shadow {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .custom-shadow:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <nav class="gradient-bg text-white shadow-lg sticky top-0 z-10">
        <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-calendar-times text-xl sm:text-2xl mr-2 sm:mr-3"></i>
                    <h1 class="text-lg sm:text-2xl font-bold">Cancel Booking</h1>
                </div>
                <div class="w-6 sm:w-24"></div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-3 sm:px-6 py-4 sm:py-8">
        <?php if ($cancellation_processed): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="font-bold">Booking Cancelled Successfully</p>
                </div>
                <p class="ml-8 mt-2">Your booking has been cancelled. <?php if ($refund_amount > 0): ?>A refund of
                        ₹<?php echo number_format($refund_amount, 2); ?> has been processed to your wallet.<?php endif; ?></p>
                <div class="mt-4 text-center">
                    <a href="mybookings.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow transition-all duration-300 inline-flex items-center">
                        <i class="fas fa-list-ul mr-2"></i>
                        Return to My Bookings
                    </a>
                </div>
            </div>
        <?php elseif ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                    <p class="font-bold">Error</p>
                </div>
                <p class="ml-8 mt-2"><?php echo $error_message; ?></p>
                <div class="mt-4 text-center">
                    <a href="mybookings.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow transition-all duration-300 inline-flex items-center">
                        <i class="fas fa-list-ul mr-2"></i>
                        Return to My Bookings
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden custom-shadow">
                <div class="gradient-bg p-4 sm:p-6 text-white border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-xl mr-3"></i>
                        <h2 class="text-lg sm:text-xl font-semibold">Cancellation Details</h2>
                    </div>
                </div>

                <div class="p-4 sm:p-6 card-container">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-8 info-grid">
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <h3 class="text-lg font-semibold mb-3 flex items-center">
                                <i class="fas fa-ticket-alt text-indigo-600 mr-2"></i>
                                Booking Information
                            </h3>
                            <div class="space-y-2 text-gray-700">
                                <p class="flex items-start">
                                    <i class="fas fa-bus text-gray-500 mr-2 mt-1 w-5"></i>
                                    <span class="font-medium">Bus:</span>
                                    <span class="ml-2"><?php echo htmlspecialchars($booking['busname']); ?></span>
                                </p>
                                <p class="flex items-start">
                                    <i class="fas fa-calendar-day text-gray-500 mr-2 mt-1 w-5"></i>
                                    <span class="font-medium">Journey Date:</span>
                                    <span class="ml-2"><?php echo date('d M Y', strtotime($booking['journeydate'])); ?></span>
                                </p>
                                <p class="flex items-start">
                                    <i class="fas fa-clock text-gray-500 mr-2 mt-1 w-5"></i>
                                    <span class="font-medium">Departure:</span>
                                    <span class="ml-2"><?php echo date('h:i A', strtotime($booking['boarding_time'])); ?></span>
                                </p>
                                <p class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-gray-500 mr-2 mt-1 w-5"></i>
                                    <span class="font-medium">From:</span>
                                    <span class="ml-2"><?php echo htmlspecialchars($booking['from_location']); ?></span>
                                </p>
                                <p class="flex items-start">
                                    <i class="fas fa-map-pin text-gray-500 mr-2 mt-1 w-5"></i>
                                    <span class="font-medium">To:</span>
                                    <span class="ml-2"><?php echo htmlspecialchars($booking['to_location']); ?></span>
                                </p>
                                <p class="flex items-start">
                                    <i class="fas fa-chair text-gray-500 mr-2 mt-1 w-5"></i>
                                    <span class="font-medium">Seats:</span>
                                    <span class="ml-2"><?php echo htmlspecialchars($booking['selected_seats']); ?></span>
                                </p>
                                <p class="flex items-start">
                                    <i class="fas fa-rupee-sign text-gray-500 mr-2 mt-1 w-5"></i>
                                    <span class="font-medium">Amount Paid:</span>
                                    <span class="ml-2">₹<?php echo number_format($booking['total_amount'], 2); ?></span>
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <h3 class="text-lg font-semibold mb-3 flex items-center">
                                <i class="fas fa-file-contract text-indigo-600 mr-2"></i>
                                Cancellation Policy
                            </h3>
                            <div class="space-y-3">
                                <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                    <p class="font-medium text-blue-800 flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Refund Information:
                                    </p>
                                    <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1 ml-2">
                                        <li>80% refund if cancelled 24+ hours before departure</li>
                                        <li>No refund if cancelled within 24 hours of departure</li>
                                        <li>Refunds are processed to your wallet immediately</li>
                                    </ul>
                                </div>

                                <div class="p-4 <?php echo ($refund_percentage > 0) ? 'bg-green-50 border-l-4 border-green-500' : 'bg-yellow-50 border-l-4 border-yellow-500'; ?> rounded-lg">
                                    <p class="font-medium <?php echo ($refund_percentage > 0) ? 'text-green-800' : 'text-yellow-800'; ?> flex items-center">
                                        <i class="<?php echo ($refund_percentage > 0) ? 'fas fa-money-bill-wave' : 'fas fa-exclamation-circle'; ?> mr-2"></i>
                                        Your Refund Status:
                                    </p>
                                    <p class="mt-2 ml-2">
                                        <?php if ($refund_percentage > 0): ?>
                                            You will receive a refund of <span class="font-semibold">₹<?php echo number_format($refund_amount, 2); ?></span>
                                            (80% of ₹<?php echo number_format($booking['total_amount'], 2); ?>)
                                        <?php else: ?>
                                            No refund will be issued as the cancellation is within 24 hours of departure.
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" class="mt-6 sm:mt-8">
                        <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="confirm" name="confirm_checkbox" type="checkbox" required
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="confirm" class="font-medium text-gray-700">I understand and agree to the
                                        cancellation policy</label>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between gap-3 sm:gap-0">
                            <a href="mybookings.php"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg shadow transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Go Back
                            </a>
                            <button type="submit" name="confirm_cancel"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg shadow transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-ban mr-2"></i>
                                Confirm Cancellation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-6 bg-white rounded-lg shadow-lg p-4 sm:p-6 custom-shadow">
                <div class="flex items-center mb-3 text-indigo-700">
                    <i class="fas fa-question-circle text-xl mr-2"></i>
                    <h3 class="text-lg font-semibold">Need Help?</h3>
                </div>
                <p class="text-gray-700 mb-3">
                    If you have any questions about your cancellation or refund, please contact our customer support.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="contact.php" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-envelope mr-2"></i>
                        Contact US
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const confirmCheckbox = document.getElementById('confirm');
            const submitButton = document.querySelector('button[name="confirm_cancel"]');

            if (confirmCheckbox && submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');

                confirmCheckbox.addEventListener('change', function () {
                    if (this.checked) {
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        submitButton.disabled = true;
                        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                });
            }
        });
    </script>
</body>
</html>
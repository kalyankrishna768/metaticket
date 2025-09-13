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

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Error: No booking ID provided.";
    exit();
}

$bookingId = intval($_GET['id']);

// Fetch user details for personalization
$user_query = "SELECT username FROM signup WHERE email = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("s", $userEmail);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();

// Fetch the specific booking details
$booking_query = "SELECT * FROM bus_sell WHERE id = ? AND email = ?";
$stmt_booking = $conn->prepare($booking_query);
if (!$stmt_booking) {
    die("Booking query preparation failed: " . $conn->error);
}
$stmt_booking->bind_param("is", $bookingId, $userEmail);
$stmt_booking->execute();
$result = $stmt_booking->get_result();

// Check if booking exists and belongs to this user
if ($result->num_rows === 0) {
    echo "Error: Booking not found or does not belong to you.";
    exit();
}

$booking = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }

        .ticket-header {
            background: linear-gradient(135deg, #3b82f6 0%, #7c3aed 100%);
        }

        .ticket {
            position: relative;
            background-color: white;
        }

        .ticket::before {
            content: '';
            position: absolute;
            top: 0;
            left: -10px;
            width: 20px;
            height: 100%;
            background-color: white;
            z-index: -1;
        }

        .dotted-border {
            border-top: 2px dashed #e2e8f0;
        }

        .barcode {
            height: 60px;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCAxMDAgNTAiPgogIDxnIGZpbGw9IiMzMzMiPgogICAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjMiIGhlaWdodD0iNTAiLz4KICAgIDxyZWN0IHg9IjYiIHk9IjAiIHdpZHRoPSIxIiBoZWlnaHQ9IjUwIi8+CiAgICA8cmVjdCB4PSIxMCIgeT0iMCIgd2lkdGg9IjIiIGhlaWdodD0iNTAiLz4KICAgIDxyZWN0IHg9IjE1IiB5PSIwIiB3aWR0aD0iMyIgaGVpZ2h0PSI1MCIvPgogICAgPHJlY3QgeD0iMjEiIHk9IjAiIHdpZHRoPSIyIiBoZWlnaHQ9IjUwIi8+CiAgICA8cmVjdCB4PSIyNiIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iNTAiLz4KICAgIDxyZWN0IHg9IjMwIiB5PSIwIiB3aWR0aD0iNCIgaGVpZ2h0PSI1MCIvPgogICAgPHJlY3QgeD0iMzciIHk9IjAiIHdpZHRoPSIxIiBoZWlnaHQ9IjUwIi8+CiAgICA8cmVjdCB4PSI0MSIgeT0iMCIgd2lkdGg9IjIiIGhlaWdodD0iNTAiLz4KICAgIDxyZWN0IHg9IjQ2IiB5PSIwIiB3aWR0aD0iMyIgaGVpZ2h0PSI1MCIvPgogICAgPHJlY3QgeD0iNTIiIHk9IjAiIHdpZHRoPSIyIiBoZWlnaHQ9IjUwIi8+CiAgICA8cmVjdCB4PSI1NyIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iNTAiLz4KICAgIDxyZWN0IHg9IjYxIiB5PSIwIiB3aWR0aD0iNCIgaGVpZ2h0PSI1MCIvPgogICAgPHJlY3QgeD0iNjgiIHk9IjAiIHdpZHRoPSIxIiBoZWlnaHQ9IjUwIi8+CiAgICA8cmVjdCB4PSI3MiIgeT0iMCIgd2lkdGg9IjIiIGhlaWdodD0iNTAiLz4KICAgIDxyZWN0IHg9Ijc3IiB5PSIwIiB3aWR0aD0iMyIgaGVpZ2h0PSI1MCIvPgogICAgPHJlY3QgeD0iODMiIHk9IjAiIHdpZHRoPSIyIiBoZWlnaHQ9IjUwIi8+CiAgICA8cmVjdCB4PSI4OCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iNTAiLz4KICAgIDxyZWN0IHg9IjkyIiB5PSIwIiB3aWR0aD0iNCIgaGVpZ2h0PSI1MCIvPgogICAgPHJlY3QgeD0iOTkiIHk9IjAiIHdpZHRoPSIxIiBoZWlnaHQ9IjUwIi8+CiAgPC9nPgo8L3N2Zz4=');
            background-repeat: repeat-x;
        }
    </style>
</head>

<body class="min-h-screen text-gray-900">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header Section -->
        <header class="mb-8 flex items-center justify-between">
            <div>
                <h1
                    class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-2">
                    Ticket Details
                </h1>
                
            </div>
            <div class="flex items-center space-x-4">
                
            </div>
        </header>

        <!-- Booking Details Section -->
        <div class="ticket bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
            <!-- Ticket Header -->
            <div class="ticket-header p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($booking['busname']); ?></h2>
                        <p class="text-sm opacity-80">Booking Reference: #<?php echo $booking['id']; ?></p>
                    </div>
                    <div>
                        <span
                            class="inline-block px-3 py-1 rounded-full bg-white <?php echo $booking['status'] == 'Confirmed' ? 'text-green-600' : 'text-yellow-600'; ?> font-semibold text-sm">
                            <?php echo htmlspecialchars($booking['status']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Ticket Journey Information -->
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="text-center">
                        <p class="text-gray-500 text-sm">From</p>
                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($booking['fromplace']); ?></h3>
                    </div>
                    <div class="flex-1 px-4">
                        <div class="flex items-center justify-center">
                            <div class="h-0.5 bg-gray-300 flex-1"></div>
                            <div class="mx-2">
                                <i class="ri-bus-line text-2xl text-blue-500"></i>
                            </div>
                            <div class="h-0.5 bg-gray-300 flex-1"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-sm">To</p>
                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($booking['toplace']); ?></h3>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                    <div class="border border-gray-200 rounded-lg p-3">
                        <p class="text-gray-500 text-sm">Journey Date</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($booking['journeydate']); ?></p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3">
                        <p class="text-gray-500 text-sm">Departure Time</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($booking['boarding_time']); ?></p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3">
                        <p class="text-gray-500 text-sm">Seat Number</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($booking['seat_no']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Dotted Line Separator -->
            <div class="dotted-border mx-6"></div>

            <!-- Ticket Details -->
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Details</h3>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Booking Date</p>
                        <p class="font-semibold"><?php echo date('d M Y', strtotime($booking['booking_date'] ?? 'now')); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Payment Method</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($booking['payment_method'] ?? 'Online Payment'); ?></p>
                    </div>
                    
                    <div>
                        <p class="text-gray-500 text-sm">email</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($booking['contact'] ?? $userEmail); ?></p>
                    </div>
                </div>

                <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                    <div>
                        <p class="text-gray-500 text-sm">Ticket Price</p>
                        <p class="text-xl font-bold text-gray-800">₹<?php echo number_format($booking['ticketprice'], 2); ?></p>
                    </div>
                    
                </div>
            </div>

            <!-- Barcode Section -->
            <div class="p-6 text-center">
                <div class="barcode mx-auto mb-2"></div>
                <p class="text-gray-500 text-sm">Booking ID: <?php echo str_pad($booking['id'], 8, '0', STR_PAD_LEFT); ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap justify-center gap-4 mt-8">
            <a href="#" onclick="printTicket()"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg flex items-center transition">
                <i class="ri-printer-line mr-2"></i>Print Receipt
            </a>
            <?php if ($booking['status'] == 'Confirmed') { ?>
                <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg flex items-center transition"
                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                    <i class="ri-close-circle-line mr-2"></i>Cancel Booking
                </a>
            <?php } ?>
            <a href="contact.php?booking=<?php echo $booking['id']; ?>"
                class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg flex items-center transition">
                <i class="ri-customer-service-2-line mr-2"></i>Contact Support
            </a>
        </div>
    </div>

    <footer class="text-center text-gray-500 mt-8 pb-4">
        <p>© <?php echo date('Y'); ?> Meta Ticket. All rights reserved.</p>
    </footer>

    <script>
        function printTicket() {
            window.print();
        }
    </script>
</body>

</html>

<?php
$stmt_booking->close();
$stmt_user->close();
$conn->close();
?>
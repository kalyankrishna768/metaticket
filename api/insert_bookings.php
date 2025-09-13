<?php
include 'config.php';
// Enable error reporting and logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');
error_reporting(E_ALL);

session_start();


// Retrieve hidden input values
$route_id = $_POST['route_id'] ?? '';
$bus_id = $_POST['bus_id'] ?? '';
$agency_email = $_POST['agency_email'] ?? '';
$busname = $_POST['busname'] ?? '';
$journeydate = $_POST['journeydate'] ?? '';
$startingtime = $_POST['startingtime'] ?? '';
$total_amount = $_POST['total_amount'] ?? '';
$ticketprice = $_POST['ticketprice'] ?? '';
$from = $_POST['from'] ?? '';
$to = $_POST['to'] ?? '';
$useremail = $_SESSION['email'] ?? '';
$user_id = $_SESSION['id'] ?? '';
$selected_seats = $_POST['selected_seats'] ?? '';
$boarding_time = $_POST['boarding_time'] ?? '';
$dropping_time = $_POST['dropping_time'] ?? '';
$status = "Confirmed";

// Validate required data
if (!$route_id || !$bus_id || !$busname || !$journeydate || !$selected_seats) {
    die("Error: Missing required parameters.");
}

// Fetch route points
$route_query = "SELECT from_points, to_points FROM routes WHERE id = ?";
$stmt = $conn->prepare($route_query);
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();
$route_data = $result->fetch_assoc();

if (!$route_data) {
    die("Error: Route not found.");
}

// Decode JSON data for boarding and dropping points
$boarding_points = json_decode($route_data['from_points'], true);
$dropping_points = json_decode($route_data['to_points'], true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding points data: " . json_last_error_msg());
}

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_points'])) {
    try {
        $conn->begin_transaction();

        // Clean total amount
        $total_amount_clean = str_replace(['₹', ','], '', $total_amount);

        // Check user wallet balance
        $user_wallet_query = "SELECT balance FROM user_wallets WHERE user_id = ? FOR UPDATE";
        $stmt = $conn->prepare($user_wallet_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_wallet_result = $stmt->get_result();
        $user_wallet_data = $user_wallet_result->fetch_assoc();

        if (!$user_wallet_data) {
            throw new Exception("Error: User wallet not found.");
        }

        if ($user_wallet_data['balance'] < $total_amount_clean) {
            throw new Exception("Error: Insufficient wallet balance.");
        }

        // Check agency wallet
        $agency_wallet_query = "SELECT balance FROM agency_wallet WHERE email = ? FOR UPDATE";
        $stmt = $conn->prepare($agency_wallet_query);
        $stmt->bind_param("s", $agency_email);
        $stmt->execute();
        $agency_wallet_result = $stmt->get_result();
        $agency_wallet_data = $agency_wallet_result->fetch_assoc();

        if (!$agency_wallet_data) {
            throw new Exception("Error: Agency wallet not found.");
        }

        // Generate transaction ID
        $transaction_id = 'TXN' . time() . rand(1000, 9999);

        // Update user wallet balance
        $new_user_balance = $user_wallet_data['balance'] - $total_amount_clean;
        $update_user_wallet = "UPDATE user_wallets SET balance = ?, updated_at = NOW() WHERE user_id = ?";
        $stmt = $conn->prepare($update_user_wallet);
        $stmt->bind_param("di", $new_user_balance, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating user wallet.");
        }

        // Update agency wallet balance
        $new_agency_balance = $agency_wallet_data['balance'] + $total_amount_clean;
        $update_agency_wallet = "UPDATE agency_wallet SET balance = ? WHERE email = ?";
        $stmt = $conn->prepare($update_agency_wallet);
        $stmt->bind_param("ds", $new_agency_balance, $agency_email);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating agency wallet.");
        }

        // Record user wallet transaction
        $user_transaction_query = "INSERT INTO wallet_transactions (wallet_id, type, amount, transaction_date) 
                         SELECT id, 'Withdrawal', ?, NOW() FROM user_wallets WHERE user_id = ? LIMIT 1";
        $stmt = $conn->prepare($user_transaction_query);
        $stmt->bind_param("di", $total_amount_clean, $user_id); 
        
        if (!$stmt->execute()) {
            throw new Exception("Error recording user transaction.");
        }

        // Record agency wallet transaction
        $agency_transaction_query = "INSERT INTO agency_wallet_transactions 
            (transaction_id, email, amount, type, description, status, transaction_date) 
            VALUES (?, ?, ?, 'credit', 'Booking payment received', 'completed', NOW())";
        $stmt = $conn->prepare($agency_transaction_query);
        $stmt->bind_param("ssd", $transaction_id, $agency_email, $total_amount_clean);
        
        if (!$stmt->execute()) {
            throw new Exception("Error recording agency transaction.");
        }

        // Validate boarding and dropping points
        $boarding_point = $_POST['boarding_point'] ?? '';
        $dropping_point = $_POST['dropping_point'] ?? '';
        $boarding_time = $_POST['boarding_time'] ?? '';
        $dropping_time = $_POST['dropping_time'] ?? '';

        if (!$boarding_point || !$dropping_point || !$boarding_time || !$dropping_time) {
            throw new Exception("Error: Missing boarding or dropping point.");
        }

        $boarding_valid = false;
        $dropping_valid = false;

        foreach ($boarding_points as $point) {
            if ($point['name'] === $boarding_point && $point['time'] === $boarding_time) {
                $boarding_valid = true;
                break;
            }
        }

        foreach ($dropping_points as $point) {
            if ($point['name'] === $dropping_point && $point['time'] === $dropping_time) {
                $dropping_valid = true;
                break;
            }
        }

        if (!$boarding_valid || !$dropping_valid) {
            throw new Exception("Error: Invalid boarding or dropping point or time selected.");
        }

        // Insert into bookings table
$booking_query = "INSERT INTO bookings (
    user_id, bus_id, busname, journeydate, ticketprice,
    booking_date, email, boarding_point, boarding_time,
    dropping_point, dropping_time, from_location, 
    to_location, selected_seats, total_amount
) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($booking_query);

if (!$stmt) {
    throw new Exception("Error preparing booking query: " . $conn->error);
}

$total_amount_clean = str_replace(['₹', ','], '', $total_amount);

// Correct number of parameters to match the placeholders
$stmt->bind_param(
    "iissdssssssssd",  // 14 type specifiers for 14 parameters (excluding NOW())
    $user_id,
    $bus_id,
    $busname,
    $journeydate,
    $ticketprice,
    $useremail,
    $boarding_point,
    $boarding_time,
    $dropping_point,
    $dropping_time,
    $from,
    $to,
    $selected_seats,
    $total_amount_clean
);

        if (!$stmt->execute()) {
            throw new Exception("Error executing booking query: " . $stmt->error);
        }

        $booking_id = $conn->insert_id;

        // Insert into agency_bookings table
$agency_booking_query = "INSERT INTO agency_bookings (
    booking_id,
    route_id, 
    bus_id, 
    journey_date,
    agency_email,
    user_email,
    passenger_name,
    seat_number,
    ticket_price,
    booking_status,
    boarding_point,
    dropping_point,
    from_location,
    to_location,
    created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP())";

$stmt = $conn->prepare($agency_booking_query);

if (!$stmt) {
    throw new Exception("Error preparing agency booking query: " . $conn->error);
}

$selected_seats_array = explode(', ', $selected_seats);

// Loop through each passenger and seat
for ($i = 0; $i < count($_POST['passenger_name']); $i++) {
    // Ensure data fits within varchar limits
    $truncated_agency_email = substr($agency_email, 0, 255);
    $truncated_user_email = substr($useremail, 0, 100);
    $truncated_passenger_name = substr($_POST['passenger_name'][$i], 0, 255);
    $truncated_seat = substr($selected_seats_array[$i], 0, 10);
    $truncated_boarding = substr($boarding_point, 0, 255);
    $truncated_dropping = substr($dropping_point, 0, 255);
    $truncated_from = substr($from, 0, 255);
    $truncated_to = substr($to, 0, 255);

    $stmt->bind_param(
        "iiisssssdsssss",
        $booking_id,
        $route_id,
        $bus_id,
        $journeydate,
        $truncated_agency_email,
        $truncated_user_email,
        $truncated_passenger_name,
        $truncated_seat,
        $ticketprice,
        $status,
        $truncated_boarding,
        $truncated_dropping,
        $truncated_from,
        $truncated_to
    );

    if (!$stmt->execute()) {
        throw new Exception("Error executing agency booking query: " . $stmt->error);
    }
}

        // Insert passenger details
$passenger_query = "INSERT INTO passengers (
    agency_email, booking_id, name, age, gender, seat_number, phone_number, passenger_email, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($passenger_query);

if (!$stmt) {
    throw new Exception("Error preparing passenger query: " . $conn->error);
}

for ($i = 0; $i < count($_POST['passenger_name']); $i++) {
    $stmt->bind_param(
        "sisissss",
        $agency_email,
        $booking_id,
        $_POST['passenger_name'][$i],
        $_POST['passenger_age'][$i],
        $_POST['passenger_gender'][$i],
        $selected_seats_array[$i],
        $_POST['passenger_phone'][$i],
        $_POST['passenger_email'][$i]
    );

            if (!$stmt->execute()) {
                throw new Exception("Error executing passenger query: " . $stmt->error);
            }
        }

        $conn->commit();
        header("Location: success.php?booking_id=$booking_id&busname=$busname&journeydate=$journeydate&startingtime=$startingtime&ticketprice=$ticketprice&total_amount=$total_amount_clean&from=$from&to=$to&boarding_point=$boarding_point&dropping_point=$dropping_point&boarding_time=$boarding_time&dropping_time=$dropping_time&selected_seats=" . urlencode($selected_seats));
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color: red;'>Booking failed: " . $e->getMessage() . "</p>";
    }
}
?>
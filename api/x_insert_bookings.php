<?php
include 'config.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: xticket.php");
    exit();
}

// Retrieve all data from the form
$ticket_id = $_POST['ticket_id'] ?? '';
$journey_date = $_POST['journey_date'] ?? '';
$from_location = $_POST['from_location'] ?? '';
$to_location = $_POST['to_location'] ?? '';
$boarding_point = $_POST['boarding_point'] ?? '';
$boarding_time = $_POST['boarding_time'] ?? '';
$dropping_point = $_POST['dropping_point'] ?? '';
$dropping_time = $_POST['dropping_time'] ?? '';
$seat_no = $_POST['seat_no'] ?? '';
$ticket_price = $_POST['ticket_price'] ?? 0;
$convenience_fee = $_POST['convenience_fee'] ?? 0;
$total_amount = $ticket_price + $convenience_fee;
$route_id = $_POST['route_id'] ?? '';
$bus_id = $_POST['bus_id'] ?? '';
$agency_email = $_POST['agency_email'] ?? '';
$seller_email = $_POST['seller_email'] ?? '';
$user_email = $_POST['user_email'] ?? '';

// Passenger details
$passenger_name = $_POST['passenger_name'] ?? '';
$age = $_POST['age'] ?? '';
$gender = $_POST['gender'] ?? '';
$passenger_phone = $_POST['passenger_phone'] ?? '';
$passenger_email = $_POST['passenger_email'] ?? '';

// Start transaction
$conn->begin_transaction();

try {
    // 1. Get specific user ID first to avoid multiple user issue
    $user_id_query = "SELECT id FROM signup WHERE email = ? ORDER BY id LIMIT 1";
    $stmt = $conn->prepare($user_id_query);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $user_id_result = $stmt->get_result();
    
    if ($user_id_result->num_rows === 0) {
        throw new Exception("User not found with email: " . $user_email);
    }
    
    $user_data = $user_id_result->fetch_assoc();
    $user_id = $user_data['id'];
    
    // 2. Check if user has sufficient balance using the specific user ID
    $user_balance_query = "SELECT w.balance FROM user_wallets w WHERE w.user_id = ?";
    $stmt = $conn->prepare($user_balance_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    
    if ($user_result->num_rows === 0) {
        throw new Exception("Wallet not initialized for user ID: " . $user_id);
    }
    
    $user_wallet = $user_result->fetch_assoc();
    $user_balance = $user_wallet['balance'];
    
    if ($user_balance < $total_amount) {
        throw new Exception("Insufficient balance. Please add funds to your wallet.");
    }
    
    // 3. Get seller's user ID to avoid multiple user issue
    $seller_id_query = "SELECT id FROM signup WHERE email = ? ORDER BY id LIMIT 1";
    $stmt = $conn->prepare($seller_id_query);
    $stmt->bind_param("s", $seller_email);
    $stmt->execute();
    $seller_id_result = $stmt->get_result();
    
    if ($seller_id_result->num_rows === 0) {
        throw new Exception("Seller not found with email: " . $seller_email);
    }
    
    $seller_data = $seller_id_result->fetch_assoc();
    $seller_id = $seller_data['id'];
    
    // 4. Deduct total amount from user wallet using specific user ID
    $update_user_wallet = "UPDATE user_wallets SET balance = balance - ?, updated_at = CURRENT_TIMESTAMP() 
                       WHERE user_id = ?";
    $stmt = $conn->prepare($update_user_wallet);
    $stmt->bind_param("di", $total_amount, $user_id);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("Failed to update user wallet for user ID: " . $user_id);
    }
    
    // 5. Get wallet ID for transaction record
    $wallet_id_query = "SELECT id FROM user_wallets WHERE user_id = ?";
    $stmt = $conn->prepare($wallet_id_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $wallet_result = $stmt->get_result();
    
    if ($wallet_result->num_rows === 0) {
        throw new Exception("Wallet ID not found for user ID: " . $user_id);
    }
    
    $wallet_data = $wallet_result->fetch_assoc();
    $user_wallet_id = $wallet_data['id'];
    
    // 6. Add transaction record for user deduction
    $add_user_transaction = "INSERT INTO wallet_transactions (wallet_id, type, amount, transaction_date) 
                        VALUES (?, 'Withdrawal', ?, CURRENT_TIMESTAMP())";
    $stmt = $conn->prepare($add_user_transaction);
    $stmt->bind_param("id", $user_wallet_id, $total_amount);
    $stmt->execute();
    
    // 7. Get the admin wallet ID
    $admin_wallet_query = "SELECT id FROM admin_wallet LIMIT 1";
    $admin_wallet_result = $conn->query($admin_wallet_query);
    
    if ($admin_wallet_result->num_rows === 0) {
        throw new Exception("Admin wallet not found");
    }
    
    $admin_wallet_data = $admin_wallet_result->fetch_assoc();
    $admin_wallet_id = $admin_wallet_data['id'];
    
    // 8. Add total amount to admin wallet
    $update_admin_wallet = "UPDATE admin_wallet SET available_money = available_money + ?";
    $stmt = $conn->prepare($update_admin_wallet);
    $stmt->bind_param("d", $total_amount);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("Failed to update admin wallet");
    }
    
    // 9. Add transaction record for admin wallet (credit - booking payment received)
    $transaction_description = "Booking payment for ticket " . $ticket_id . " from " . $user_email;
    $add_admin_transaction = "INSERT INTO admin_wallet_transactions (admin_wallet_id, transaction_type, amount, transaction_date, description) 
                          VALUES (?, 'credit', ?, CURRENT_TIMESTAMP(), ?)";
    $stmt = $conn->prepare($add_admin_transaction);
    $stmt->bind_param("ids", $admin_wallet_id, $total_amount, $transaction_description);
    $stmt->execute();
    
    // 10. Get seller's wallet ID
    $seller_wallet_id_query = "SELECT id FROM user_wallets WHERE user_id = ?";
    $stmt = $conn->prepare($seller_wallet_id_query);
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $seller_wallet_result = $stmt->get_result();
    
    if ($seller_wallet_result->num_rows === 0) {
        throw new Exception("Wallet ID not found for seller ID: " . $seller_id);
    }
    
    $seller_wallet_data = $seller_wallet_result->fetch_assoc();
    $seller_wallet_id = $seller_wallet_data['id'];
    
    // 11. Only transfer ticket price (not convenience fee) from admin to seller
    $update_seller_wallet = "UPDATE user_wallets SET balance = balance + ?, updated_at = CURRENT_TIMESTAMP() 
                        WHERE user_id = ?";
    $stmt = $conn->prepare($update_seller_wallet);
    $stmt->bind_param("di", $ticket_price, $seller_id);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("Failed to update seller wallet for seller ID: " . $seller_id);
    }
    
    // 12. Add transaction record for admin wallet (debit - payment to seller)
    $seller_payment_description = "Payment to seller " . $seller_email . " for ticket " . $ticket_id;
    $add_admin_debit_transaction = "INSERT INTO admin_wallet_transactions (admin_wallet_id, transaction_type, amount, transaction_date, description) 
                                VALUES (?, 'debit', ?, CURRENT_TIMESTAMP(), ?)";
    $stmt = $conn->prepare($add_admin_debit_transaction);
    $stmt->bind_param("ids", $admin_wallet_id, $ticket_price, $seller_payment_description);
    $stmt->execute();
    
    // 13. Add transaction record for seller addition
    $add_seller_transaction = "INSERT INTO wallet_transactions (wallet_id, type, amount, transaction_date) 
                          VALUES (?, 'Deposit', ?, CURRENT_TIMESTAMP())";
    $stmt = $conn->prepare($add_seller_transaction);
    $stmt->bind_param("id", $seller_wallet_id, $ticket_price);
    $stmt->execute();
    
    // 14. Insert booking details into new_bookings table
    $insert_booking = "INSERT INTO new_bookings (
                        ticket_id, user_email, passenger_name, age, gender, 
                        phone_number, passenger_email, journey_date, 
                        from_location, to_location, boarding_point, boarding_time, 
                        dropping_point, dropping_time, seat_no, ticket_price, 
                        convenience_fee, total_amount, route_id, bus_id, 
                        agency_email, booking_date, booking_status, payment_status
                    ) VALUES (
                        ?, ?, ?, ?, ?, 
                        ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, CURRENT_TIMESTAMP(), 'CONFIRMED' , 'PAID'
                    )";
    
    $stmt = $conn->prepare($insert_booking);
    $stmt->bind_param(
        "sssissssssssssdddisis",  
        $ticket_id, $user_email, $passenger_name, $age, $gender,
        $passenger_phone, $passenger_email, $journey_date,
        $from_location, $to_location, $boarding_point, $boarding_time,
        $dropping_point, $dropping_time, $seat_no, $ticket_price,
        $convenience_fee, $total_amount, $route_id, $bus_id,
        $agency_email
    );
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("Failed to insert booking details");
    }
    
    // Get the newly created booking ID
    $booking_id = $conn->insert_id;
    
    // Commit the transaction
    $conn->commit();
    
    // Redirect to success page with booking ID
    header("Location: x_booking_success.php?ticket_id=$ticket_id&passenger_name=$passenger_name&journey_date=$journey_date&from_location=$from_location&to_location=$to_location&boarding_point=$boarding_point&dropping_point=$dropping_point&ticket_price=$ticket_price&total_amount=$total_amount&seat_no=$seat_no&convenience_fee=$convenience_fee&boarding_time=$boarding_time&dropping_time=$dropping_time&booking_id=" . $booking_id);
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    // Display error message
    echo "<div style='color: red; font-weight: bold;'>Error: " . $e->getMessage() . "</div>";
    
    exit();
}
?>
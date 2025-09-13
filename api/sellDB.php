<?php
include 'config.php';
session_start();
$email = $_SESSION['email'];

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: newsignin.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user agreed to the policy
    if (!isset($_POST['agree_policy'])) {
        header("Location: sell.php?id=" . $_POST['booking_id'] . "&error=policy");
        exit();
    }
    
    // Get form data
    $bus_id = $_POST['bus_id'];
    $busname = $_POST['busname'];
    $journeydate = $_POST['journeydate'];
    $fromplace = $_POST['from_location'];
    $toplace = $_POST['to_location'];
    $boarding_time = $_POST['boarding_time'];
    $selected_seats = $_POST['selected_seats'];
    $amount_per_seat = $_POST['amount_per_seat'];
    $booking_id = $_POST['booking_id'];
    $ticketUpload = "from my bookings";
    $status = "accepted";
    
    // Split seats into array
    $seats_array = explode(',', $selected_seats);
    
    // Prepare the SQL statement
    $insert_query = "INSERT INTO bus_sell (email, seat_no, bus_id, busname, journeydate, fromplace, toplace, boarding_time, ticketprice, booking_id, ticketUpload, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert_query);
    
    // Insert each seat separately
    $success = true;
    foreach ($seats_array as $seat) {
        $seat = trim($seat); // Remove any whitespace
        $stmt->bind_param("siissssdiiss", $email, $seat, $bus_id, $busname, $journeydate, $fromplace, $toplace, $boarding_time, $amount_per_seat, $booking_id, $ticketUpload, $status);
        
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        echo "<script>alert('Tickets Uploaded successfully!'); window.location.href = 'mybookings.php';</script>";
        exit();
    } else {
        header("Location: sell.php?id=" . $booking_id . "&error=database");
        exit();
    }
} else {
    header("Location: sell.php");
    exit();
}
?>
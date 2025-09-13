<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
    $passenger_name = $_GET['passenger_name'];
    $journey_date = $_GET['journey_date'];
    $from_location = $_GET['from_location'];
    $to_location = $_GET['to_location'];
    $ticket_price = $_GET['ticket_price'];
    $total_amount = $_GET['total_amount'];
    $boarding_point = $_GET['boarding_point'];
    $dropping_point = $_GET['dropping_point'];
    $boarding_time = $_GET['boarding_time'];
    $dropping_time = $_GET['dropping_time'];
    $seat_no = $_GET['seat_no'];
    $convenience_fee = $_GET['convenience_fee'];
} else {
    echo "<p style='color:red;'>Error: Booking details not found.</p>";
}

// Get booking details from database
$sql = "SELECT * FROM new_bookings WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

// Clear the session variables
unset($_SESSION['booking_success']);
unset($_SESSION['last_booking_id']);

// Format date for display
$formatted_date = date('D, M d, Y', strtotime($journey_date));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - MetaTicket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4caf50;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --border-radius: 10px;
            --box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            background-color: #f5f7ff;
            color: var(--dark-color);
            padding: 10px;
        }
        
        .container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .success-icon {
            font-size: 50px;
            margin-bottom: 15px;
            color: white;
            background-color: var(--success-color);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .header p {
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .ticket-container {
            padding: 20px;
        }
        
        .ticket {
            background-color: white;
            border-radius: var(--border-radius);
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }
        
        .ticket-header {
            background-color: var(--light-color);
            padding: 15px;
            border-bottom: 1px dashed #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .ticket-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .ticket-id {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .travel-info {
            padding: 20px 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .route {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .route-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .location {
            display: flex;
            flex-direction: column;
            max-width: 45%;
        }
        
        .location-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
            word-wrap: break-word;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .location-detail {
            font-size: 13px;
            color: var(--gray-color);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .journey-line {
            display: flex;
            align-items: center;
            margin: 15px 0;
            color: var(--primary-color);
            justify-content: center;
        }
        
        .journey-line .dot {
            width: 10px;
            height: 10px;
            background-color: var(--primary-color);
            border-radius: 50%;
        }
        
        .journey-line .line {
            flex-grow: 1;
            height: 2px;
            background-color: var(--primary-color);
            position: relative;
            margin: 0 5px;
        }
        
        .journey-line .bus-icon {
            position: absolute;
            top: -9px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 16px;
        }
        
        .passenger-info {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        @media (min-width: 576px) {
            .passenger-info {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        .info-item {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        
        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            color: var(--gray-color);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .info-value {
            font-size: 15px;
            font-weight: 500;
            word-break: break-word;
        }
        
        .price-info {
            background-color: var(--light-color);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .price-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 3px;
        }
        
        .price-label {
            font-size: 12px;
            text-transform: uppercase;
            color: var(--gray-color);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .price-value {
            font-size: 15px;
            font-weight: 600;
        }
        
        .total-price {
            font-size: 17px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        
        .qr-code img {
            max-width: 130px;
            padding: 5px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        
        .qr-code p {
            margin-top: 8px;
            font-size: 13px;
            color: var(--gray-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }
        
        @media (min-width: 500px) {
            .buttons {
                flex-direction: row;
                justify-content: center;
            }
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            font-size: 14px;
            width: 100%;
        }
        
        @media (min-width: 500px) {
            .btn {
                width: auto;
                min-width: 160px;
            }
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 3px 10px rgba(67, 97, 238, 0.3);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: white;
            color: var(--gray-color);
            border: 1px solid #e0e0e0;
        }
        
        .btn-secondary:hover {
            background-color: var(--light-color);
            transform: translateY(-2px);
        }
        
        @media print {
            .buttons {
                display: none;
            }
            
            .container {
                box-shadow: none;
                max-width: 100%;
                margin: 0;
            }
            
            body {
                background-color: white;
                padding: 0;
            }
        }
        
        @media (max-width: 360px) {
            .success-icon {
                width: 60px;
                height: 60px;
                line-height: 60px;
                font-size: 35px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .ticket-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .location-name {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1><i class="fas fa-ticket-alt"></i> Booking Confirmed!</h1>
            <p><i class="fas fa-bus"></i> Your journey has been successfully booked</p>
        </div>
        
        <div class="ticket-container">
            <div class="ticket">
                <div class="ticket-header">
                    <div class="ticket-title"><i class="fas fa-info-circle"></i> E-Ticket / Booking Details</div>
                    <div class="ticket-id"><i class="fas fa-barcode"></i> PNR: MT<?php echo str_pad(htmlspecialchars($booking_id), 6, '0', STR_PAD_LEFT); ?></div>
                </div>
                
                <div class="travel-info">
                    <div class="route">
                        <div class="route-flex">
                            <div class="location">
                                <div class="location-name"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($from_location); ?></div>
                                <div class="location-detail"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($boarding_time); ?></div>
                                <div class="location-detail"><i class="fas fa-sign-out-alt"></i> <?php echo htmlspecialchars($boarding_point); ?></div>
                            </div>
                            
                            <div class="location" style="text-align: right;">
                                <div class="location-name"><i class="fas fa-flag-checkered"></i> <?php echo htmlspecialchars($to_location); ?></div>
                                <div class="location-detail"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($dropping_time); ?></div>
                                <div class="location-detail"><i class="fas fa-sign-in-alt"></i> <?php echo htmlspecialchars($dropping_point); ?></div>
                            </div>
                        </div>
                        
                        <div class="journey-line">
                            <div class="dot"></div>
                            <div class="line">
                                <i class="fas fa-bus bus-icon"></i>
                            </div>
                            <div class="dot"></div>
                        </div>
                    </div>
                </div>
                
                <div class="passenger-info">
                    <div class="info-column">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-user"></i> Passenger</div>
                            <div class="info-value"><?php echo htmlspecialchars($passenger_name); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-calendar-alt"></i> Journey Date</div>
                            <div class="info-value"><?php echo htmlspecialchars($formatted_date); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-column">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-chair"></i> Seat Number</div>
                            <div class="info-value"><?php echo htmlspecialchars($seat_no); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-calendar-check"></i> Booking Date</div>
                            <div class="info-value"><?php echo date('M d, Y'); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="price-info">
                    <div class="price-item">
                        <div class="price-label"><i class="fas fa-ticket-alt"></i> Ticket Price</div>
                        <div class="price-value">₹<?php echo htmlspecialchars($ticket_price); ?></div>
                    </div>
                    
                    <div class="price-item">
                        <div class="price-label"><i class="fas fa-hand-holding-usd"></i> Convenience Fee</div>
                        <div class="price-value">₹<?php echo htmlspecialchars($convenience_fee); ?></div>
                    </div>
                    
                    <div class="price-item">
                        <div class="price-label"><i class="fas fa-coins"></i> Total Amount</div>
                        <div class="price-value total-price">₹<?php echo htmlspecialchars($total_amount); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=METATICKET-<?php echo htmlspecialchars($booking_id); ?>-<?php echo htmlspecialchars($journey_date); ?>" alt="Ticket QR Code">
                <p><i class="fas fa-qrcode"></i> Scan for ticket verification</p>
            </div>
            
            <div class="buttons">
                <a href="javascript:window.print();" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Ticket
                </a>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
<?php
include 'config.php';
session_start();
$user_email = $_SESSION['email'];

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ticketx.php");
    exit();
}

// Retrieve data from hidden fields
$ticket_id = $_POST['ticket_id'] ?? '';
$journey_date = $_POST['journey_date'] ?? '';
$from_location = $_POST['from_location'] ?? '';
$to_location = $_POST['to_location'] ?? '';
$ticket_price = $_POST['ticket_price'] ?? '';
// Add convenience fee
$convenience_fee = 50;
$total_price = $ticket_price + $convenience_fee;
$seat_no = $_POST['seat_no'] ?? '';
$boarding_time = $_POST['boarding_time'] ?? '';
$route_id = $_POST['route_id'] ?? '';
$bus_id = $_POST['bus_id'] ?? '';
$agency_email = $_POST['agency_email'] ?? '';
$seller_email = $_POST['seller_email'] ?? '';

// Retrieve passenger details
$passenger_name = $_POST['passenger_name'] ?? '';
$age = $_POST['age'] ?? '';
$gender = $_POST['gender'] ?? '';
$passenger_phone = $_POST['passenger_phone'] ?? '';
$passenger_email = $_POST['passenger_email'] ?? '';

// Fetch boarding and dropping points from routes table
$points_query = "SELECT from_points, to_points FROM routes WHERE id = ?";
$stmt = $conn->prepare($points_query);
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();
$points = $result->fetch_assoc();

// Decode JSON points data
$boarding_points = json_decode($points['from_points'], true) ?? [];
$dropping_points = json_decode($points['to_points'], true) ?? [];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Boarding Point - MetaTicket</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #eef2ff;
            --secondary-color: #10b981;
            --accent-color: #f59e0b;
            --danger-color: #ef4444;
            --background-color: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #1e293b;
            --text-medium: #64748b;
            --text-light: #94a3b8;
            --border-color: #e2e8f0;
            --border-radius: 10px;
            --box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--background-color);
            color: var(--text-dark);
            min-height: 100vh;
            padding: 20px;
        }

        .page-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            margin-bottom: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 24px;
            color: var(--primary-color);
        }

        .logo span {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 20px;
            color: var(--text-dark);
        }

        .back-link {
            color: var(--text-medium);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        .back-link i {
            margin-right: 5px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .progress-container {
            margin-bottom: 30px;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--border-color);
            transform: translateY(-50%);
            z-index: 1;
        }

        .progress-bar {
            position: absolute;
            top: 50%;
            left: 0;
            height: 3px;
            width: 66.66%;
            background: var(--primary-color);
            transform: translateY(-50%);
            z-index: 2;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--card-bg);
            border: 3px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--text-medium);
            position: relative;
            z-index: 3;
        }

        .step.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .step-label {
            position: absolute;
            top: 45px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: var(--text-medium);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 20px;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.07);
        }

        .card-header {
            padding: 15px 25px;
            background: var(--primary-light);
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h2 {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }

        select {
            width: 100%;
            padding: 12px 15px;
            background: var(--background-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            appearance: none;
        }

        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .point-details {
            margin-top: 10px;
            padding: 10px;
            background: var(--primary-light);
            border-radius: 6px;
            color: var(--text-medium);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .price-breakdown {
            padding: 20px;
            background: var(--primary-light);
            border-radius: var(--border-radius);
            margin: 20px 0;
        }

        .price-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
            align-items: center;
        }

        .price-item i {
            margin-right: 8px;
            color: var(--text-medium);
        }

        .total-price {
            font-weight: 600;
            color: var(--primary-color);
            margin-top: 10px;
        }

        .policy-card {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger-color);
            padding: 15px;
            border-radius: var(--border-radius);
            margin: 20px 0;
        }

        .policy-card h3 {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .confirm-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        .confirm-checkbox label {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .submit-btn {
            background: var(--primary-color);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .user-profile img {
            width: 40px;
            margin-right: 10px;
            border-radius: 50%;
        }

        .user-profile {
            display: flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="page-container">
        <div class="top-nav">
            <a class="user-profile" href="#">
                <img src="assets/images/logo.png" alt="MetaTicket Logo">
                <span>MetaTicket</span>
            </a>
            
        </div>

        <div class="container">
            <div class="progress-container">
                <div class="progress-steps">
                    <div class="progress-bar"></div>
                    <div class="step active"><span>1</span><span class="step-label"><i class="fas fa-user"></i>
                            Passenger Details</span></div>
                    <div class="step active"><span>2</span><span class="step-label"><i class="fas fa-map-pin"></i>
                            Boarding Points</span></div>
                    <div class="step"><span>3</span><span class="step-label"><i class="fas fa-credit-card"></i>
                            Payment</span></div>
                </div>
            </div>

            <div class="header">
                <h1><i class="fas fa-route"></i> Select Boarding & Dropping Points</h1>
                <p>Choose your preferred pickup and drop-off locations</p>
            </div>

            <form method="POST" action="x_insert_bookings.php" id="boardingForm">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-map-marker-alt"></i> Travel Points</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label><i class="fas fa-sign-out-alt"></i> Select Boarding Point</label>
                            <select name="boarding_point" id="boarding_point" required>
                                <option value="">Choose Boarding Point</option>
                                <?php foreach ($boarding_points as $point): ?>
                                    <option value="<?php echo htmlspecialchars($point['name']); ?>"
                                        data-time="<?php echo htmlspecialchars($point['time']); ?>">
                                        <?php echo htmlspecialchars($point['name'] . ' (' . $point['time'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="point-details" id="boarding-details"><i class="fas fa-info-circle"></i></div>
                            <input type="hidden" name="boarding_time" id="boarding_time">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-sign-in-alt"></i> Select Dropping Point</label>
                            <select name="dropping_point" id="dropping_point" required>
                                <option value="">Choose Dropping Point</option>
                                <?php foreach ($dropping_points as $point): ?>
                                    <option value="<?php echo htmlspecialchars($point['name']); ?>"
                                        data-time="<?php echo htmlspecialchars($point['time']); ?>">
                                        <?php echo htmlspecialchars($point['name'] . ' (' . $point['time'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="point-details" id="dropping-details"><i class="fas fa-info-circle"></i></div>
                            <input type="hidden" name="dropping_time" id="dropping_time">
                        </div>
                    </div>
                </div>

                <div class="price-breakdown">
                    <h3><i class="fas fa-money-bill-wave"></i> Price Breakdown</h3>
                    <div class="price-item">
                        <span><i class="fas fa-ticket-alt"></i> Base Ticket Price</span>
                        <span>₹<?php echo htmlspecialchars($ticket_price); ?></span>
                    </div>
                    <div class="price-item">
                        <span><i class="fas fa-hand-holding-usd"></i> Convenience Fee</span>
                        <span>₹<?php echo htmlspecialchars($convenience_fee); ?></span>
                    </div>
                    <div class="price-item total-price">
                        <span><i class="fas fa-coins"></i> Total Amount</span>
                        <span>₹<?php echo htmlspecialchars($total_price); ?></span>
                    </div>
                </div>

                <div class="policy-card">
                    <h3><i class="fas fa-exclamation-triangle"></i> Cancellation Policy</h3>
                    <p><i class="fas fa-ban"></i> This ticket is non-cancellable and non-refundable once booked.</p>
                </div>

                <div class="confirm-checkbox">
                    <input type="checkbox" id="confirmPolicy" name="confirmPolicy" required>
                    <label for="confirmPolicy">I agree to the non-cancellation policy</label>
                </div>

                <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket_id); ?>">
                <input type="hidden" name="journey_date" value="<?php echo htmlspecialchars($journey_date); ?>">
                <input type="hidden" name="from_location" value="<?php echo htmlspecialchars($from_location); ?>">
                <input type="hidden" name="to_location" value="<?php echo htmlspecialchars($to_location); ?>">
                <input type="hidden" name="seat_no" value="<?php echo htmlspecialchars($seat_no); ?>">
                <input type="hidden" name="boarding_time" value="<?php echo htmlspecialchars($boarding_time); ?>">
                <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route_id); ?>">
                <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus_id); ?>">
                <input type="hidden" name="agency_email" value="<?php echo htmlspecialchars($agency_email); ?>">
                <input type="hidden" name="passenger_name" value="<?php echo htmlspecialchars($passenger_name); ?>">
                <input type="hidden" name="age" value="<?php echo htmlspecialchars($age); ?>">
                <input type="hidden" name="gender" value="<?php echo htmlspecialchars($gender); ?>">
                <input type="hidden" name="passenger_phone" value="<?php echo htmlspecialchars($passenger_phone); ?>">
                <input type="hidden" name="passenger_email" value="<?php echo htmlspecialchars($passenger_email); ?>">
                <input type="hidden" name="ticket_price" value="<?php echo htmlspecialchars($ticket_price); ?>">
                <input type="hidden" name="convenience_fee" value="<?php echo htmlspecialchars($convenience_fee); ?>">
                <input type="hidden" name="seller_email" value="<?php echo htmlspecialchars($seller_email); ?>">
                <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($user_email); ?>">
                
                <?php foreach ($_POST as $key => $value):
                    if (!in_array($key, ['boarding_point', 'dropping_point', 'boarding_time', 'dropping_time'])): ?>
                        <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                            value="<?php echo htmlspecialchars($value); ?>">
                    <?php endif; endforeach; ?>
                <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($user_email); ?>">

                <button type="submit" class="submit-btn">
                    <i class="fas fa-credit-card"></i> Proceed to Payment <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const boardingSelect = document.getElementById('boarding_point');
            const droppingSelect = document.getElementById('dropping_point');
            const boardingDetails = document.getElementById('boarding-details');
            const droppingDetails = document.getElementById('dropping-details');
            const boardingTime = document.getElementById('boarding_time');
            const droppingTime = document.getElementById('dropping_time');
            const form = document.getElementById('boardingForm');

            function updateDetails(select, detailsDiv, timeInput) {
                const selected = select.options[select.selectedIndex];
                if (selected && selected.value) {
                    const time = selected.getAttribute('data-time');
                    detailsDiv.innerHTML = `<i class="fas fa-clock"></i> Scheduled Time: ${time}`;
                    timeInput.value = time;
                } else {
                    detailsDiv.innerHTML = '<i class="fas fa-info-circle"></i> Select a point to see details';
                    timeInput.value = '';
                }
            }

            boardingSelect.addEventListener('change', () => updateDetails(boardingSelect, boardingDetails, boardingTime));
            droppingSelect.addEventListener('change', () => updateDetails(droppingSelect, droppingDetails, droppingTime));
            updateDetails(boardingSelect, boardingDetails, boardingTime);
            updateDetails(droppingSelect, droppingDetails, droppingTime);

            form.addEventListener('submit', function (e) {
                if (boardingSelect.value === droppingSelect.value) {
                    e.preventDefault();
                    alert('Boarding and dropping points cannot be the same!');
                    return;
                }

                if (!document.getElementById('confirmPolicy').checked) {
                    e.preventDefault();
                    alert('Please agree to the cancellation policy');
                    return;
                }

                if (!confirm(`Confirm payment of ₹${<?php echo json_encode($total_price); ?>}? This action cannot be undone.`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>
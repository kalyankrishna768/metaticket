<?php
include 'config.php';
session_start();

// Retrieve hidden input values
$route_id = isset($_POST['route_id']) ? $_POST['route_id'] : '';
$bus_id = isset($_POST['bus_id']) ? $_POST['bus_id'] : '';
$agency_email = isset($_POST['agency_email']) ? $_POST['agency_email'] : '';
$busname = isset($_POST['busname']) ? $_POST['busname'] : '';
$journeydate = isset($_POST['journeydate']) ? $_POST['journeydate'] : '';
$startingtime = isset($_POST['startingtime']) ? $_POST['startingtime'] : '';
$ticketprice = isset($_POST['ticketprice']) ? $_POST['ticketprice'] : '';
$from = isset($_POST['from']) ? $_POST['from'] : '';
$to = isset($_POST['to']) ? $_POST['to'] : '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    // Get user wallet balance
    $user_query = "SELECT balance FROM user_wallets WHERE user_id = (SELECT id FROM users WHERE email = ?)";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    $user_balance = $user_data['balance'];
    $stmt->close();

    // Calculate total amount
    $total_amount = str_replace(['₹', ','], '', $_POST['total_amount']);
}

// Get form data from previous page
$selected_seats = isset($_POST['selected_seats']) ? $_POST['selected_seats'] : '';
$total_amount = isset($_POST['total_amount']) ? $_POST['total_amount'] : '';
$num_seats = count(explode(', ', $selected_seats));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    :root {
        --primary-color: #e84118;
        --secondary-color: #28a745;
        --background-color: rgba(0, 0, 0, 0.8);
        --text-color: #fff;
        --card-bg: rgba(255, 255, 255, 0.1);
        --border-radius: 12px;
        --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: url('assets/images/search_bus.jpg') no-repeat center center/cover;
        background-attachment: fixed;
        min-height: 100vh;
        color: var(--text-color);
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: var(--background-color);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
    }

    .header {
        text-align: center;
        margin-bottom: 30px;
    }

    .header h1 {
        font-size: 2.5rem;
        font-weight: 600;
        color: var(--primary-color);
        animation: fadeIn 1s ease-out;
    }

    .passenger-form {
        background: var(--card-bg);
        padding: 20px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
        position: relative;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-color);
        font-weight: 500;
        transition: all 0.3s ease;
    }

    input,
    select {
        width: 100%;
        padding: 10px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius);
        color: var(--text-color);
        font-size: 14px;
        transition: all 0.3s ease;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    input:focus,
    select:focus {
        outline: none;
        border-color: var(--primary-color);
        background: rgba(255, 255, 255, 0);
        box-shadow: 0 0 0 3px rgba(232, 65, 24, 0.2);
    }

    /* Custom select styling */
    .select-wrapper {
        position: relative;
    }

    .select-wrapper::after {
        content: '\f107';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-color);
        pointer-events: none;
        transition: all 0.3s ease;
    }

    .select-wrapper:hover::after {
        color: var(--primary-color);
    }

    select {
        cursor: pointer;
        padding-right: 40px;
    }

    /* Custom option styling */
    select option {
        background-color: rgba(41, 41, 42, 0.76);
        color: var(--text-color);
        padding: 12px;
    }

    /* Input focus effects */
    input:focus+label,
    select:focus+label {
        color: var(--primary-color);
    }

    /* Enhanced passenger form styling */
    .passenger-form {
        background: rgba(255, 255, 255, 0.05);
        padding: 25px;
        border-radius: var(--border-radius);
        margin-bottom: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .passenger-form:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
    }

    .passenger-form h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
    }

    .passenger-form h3::before {
        content: '\f007';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-right: 10px;
        font-size: 1rem;
    }

    .submit-btn {
        background: var(--primary-color);
        color: white;
        padding: 12px;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        width: 100%;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .submit-btn::before {
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
    }

    .submit-btn:hover {
        background: #c73615;
        transform: translateY(-2px);
    }

    .error-message {
        background: #dc3545;
        color: white;
        padding: 10px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
    }

    .booking-summary {
        background: var(--card-bg);
        padding: 20px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
    }

    /* Form validation styling */
    input:invalid,
    select:invalid {
        border-color: #dc3545;
    }

    input:invalid:focus,
    select:invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Enter Passenger Details</h1>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>

        <div class="booking-summary">
            <h2>Booking Summary</h2>
            <div class="form-row">
                <div>
                    <p><strong><i class="fas fa-map-marker-alt"></i> From:</strong>
                        <?php echo htmlspecialchars($_POST['from']); ?></p>
                    <p><strong><i class="fas fa-map-pin"></i> To:</strong> <?php echo htmlspecialchars($_POST['to']); ?>
                    </p>
                </div>
                <div>
                    <p><strong><i class="far fa-calendar-alt"></i> Date:</strong>
                        <?php echo htmlspecialchars($_POST['journeydate']); ?></p>
                    <p><strong><i class="fas fa-ticket-alt text-indigo-600"></i> Selected Seats:</strong>
                        <?php echo htmlspecialchars($selected_seats); ?></p>
                </div>
                <div>
                    <p><strong><i class="fas fa-tag"></i> Total Amount:</strong>
                        <?php echo htmlspecialchars($total_amount); ?></p>
                </div>
            </div>
        </div>

        <form method="POST" action="boarding_points.php">
            <?php for ($i = 0; $i < $num_seats; $i++): ?>
            <div class="passenger-form">
                <h3>Passenger <?php echo $i + 1; ?></h3>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fa fa-user"></i> Full Name</label>
                        <input type="text" name="passenger_name[]" required pattern="[A-Za-z ]{1,255}"
                            title="Please enter a valid name (letters and spaces only)">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-birthday-cake"></i> Age</label>
                        <input type="number" name="passenger_age[]" min="1" max="120" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-venus-mars mr-1"></i> Gender</label>
                        <select name="passenger_gender[]" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fa fa-phone"></i> Phone Number</label>
                        <input type="tel" name="passenger_phone[]" required pattern="[0-9]{10,15}"
                            title="Please enter a valid phone number (10-15 digits)">
                    </div>
                    <div class="form-group">
                        <label><i class="fa fa-envelope"></i> Email</label>
                        <input type="email" name="passenger_email[]" required>
                    </div>

                </div>
            </div>
            <?php endfor; ?>

            <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route_id); ?>">
            <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus_id); ?>">
            <input type="hidden" name="agency_email" value="<?php echo htmlspecialchars($agency_email); ?>">
            <input type="hidden" name="busname" value="<?php echo htmlspecialchars($busname); ?>">
            <input type="hidden" name="journeydate" value="<?php echo htmlspecialchars($journeydate); ?>">
            <input type="hidden" name="startingtime" value="<?php echo htmlspecialchars($startingtime); ?>">
            <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
            <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
            <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>">
            <input type="hidden" name="ticketprice" value="<?php echo htmlspecialchars($ticketprice); ?>">
            <input type="hidden" name="selected_seats" value="<?php echo htmlspecialchars($selected_seats); ?>">

            <button type="submit" name="confirm_booking" class="submit-btn">Confirm Booking</button>
        </form>
    </div>
</body>

</html>

<?php
$conn->close();
?>
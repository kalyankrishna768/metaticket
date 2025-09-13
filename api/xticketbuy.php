<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: newsignin.php");
    exit();
}

// Get ticket ID from URL
$ticket_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$ticket_id) {
    header("Location: xticket.php");
    exit();
}

// Fetch ticket details from bus_sell table
$ticket_query = "SELECT * FROM bus_sell WHERE id = ? AND status = 'accepted'";
$stmt = $conn->prepare($ticket_query);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$ticket_result = $stmt->get_result();
$ticket = $ticket_result->fetch_assoc();

// Use the values from the ticket
$from = $ticket['fromplace'];
$to = $ticket['toplace'];
$date = $ticket['journeydate'];
$bus_id = $ticket['bus_id'];
$seller_email = $ticket['email'];

// Modified query to fetch route_id and agency_email
$sql = "SELECT id, bus_id, email, username
        FROM routes 
        WHERE from_location = ? AND to_location = ? AND departure_date = ? AND bus_id = ? AND status = 'active'";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("SQL statement preparation failed: " . $conn->error);
}


$stmt -> bind_param("ssss", $from, $to, $date, $bus_id);
$stmt->execute();
$result = $stmt->get_result();

// Get route data - we'll need this for the hidden fields
$route_data = $result->fetch_assoc();
$route_id = $route_data['id'] ?? '';
$agency_email = $route_data['email'] ?? '';

// Reset result pointer to beginning so the while loop works later
$result->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket - MetaTicket</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
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
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .page-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            text-decoration: none;
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
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-medium);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            flex: 1;
        }

        .progress-container {
            margin-bottom: 30px;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 10px;
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
            width: 33.33%;
            background: var(--primary-color);
            transform: translateY(-50%);
            z-index: 2;
            transition: width 0.4s ease;
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
            font-size: 16px;
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
            white-space: nowrap;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-medium);
        }

        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .header p {
            color: var(--text-medium);
            font-size: 14px;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.07), 0 10px 15px -6px rgba(0, 0, 0, 0.03);
            transform: translateY(-3px);
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

        .ticket-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .ticket-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .ticket-item i {
            background: var(--primary-light);
            color: var(--primary-color);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-top: 2px;
        }

        .ticket-content strong {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-medium);
            margin-bottom: 3px;
        }

        .ticket-content span {
            font-weight: 500;
            color: var(--text-dark);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: var(--background-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-dark);
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 40px;
            color: var(--text-light);
            font-size: 16px;
            pointer-events: none;
        }

        .has-icon .form-control {
            padding-left: 45px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        select.form-control {
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 45px;
        }

        .submit-btn {
            background: var(--primary-color);
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
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

        .submit-btn i {
            font-size: 14px;
        }

        .form-footer {
            margin-top: 15px;
            text-align: center;
            font-size: 13px;
            color: var(--text-medium);
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .form-feedback {
            margin-top: 5px;
            font-size: 12px;
            display: none;
        }

        .form-feedback.error {
            color: var(--danger-color);
        }

        .form-feedback.success {
            color: var(--secondary-color);
        }

        .form-control.error {
            border-color: var(--danger-color);
        }

        .form-control.success {
            border-color: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .ticket-info {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .step-label {
                font-size: 10px;
            }
        }

        .tag {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 5px;
        }

        .tag-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--secondary-color);
        }

        .seat-preview {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            background: var(--primary-light);
            color: var(--primary-color);
            border-radius: 6px;
            font-weight: 600;
            margin-left: 5px;
        }

        .loader {
            width: 20px;
            height: 20px;
            border: 2px solid #FFF;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .user-profile img {
            width: 40px;
            margin-right: 10px;
            border-radius: 50%;
        }

        .user-profile {
            display: flex;
            align-items: center;
            color: blue;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="top-nav">
            <a class="user-profile">
                <img src="assets/images/logo.png" alt="MetaTicket Logo">
                <span>MetaTicket</span>
            </a>
            
        </div>

        <div class="container">
            <div class="progress-container">
                <div class="progress-steps">
                    <div class="progress-bar"></div>
                    <div class="step active">
                        1
                        <span class="step-label"><i class="fas fa-user"></i> Passenger Details</span>
                    </div>
                    <div class="step">
                        2
                        <span class="step-label"><i class="fas fa-map-pin"></i> Boarding Points</span>
                    </div>
                    <div class="step">
                        3
                        <span class="step-label"><i class="fas fa-credit-card"></i> Payment</span>
                    </div>
                </div>
            </div>

            <div class="header">
                <h1>Passenger Details</h1>
                <p>Please enter passenger information for your journey</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-info-circle"></i> Ticket Information</h2>
                </div>
                <div class="card-body">
                    <div class="ticket-info">
                        <div class="ticket-item">
                        <i class="fas fa-couch"></i>
                            <div class="ticket-content">
                                <strong>Seat Number</strong>
                                <span>
                                    <?php echo htmlspecialchars($ticket['seat_no']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="ticket-item">
                            <i class="fas fa-bus"></i>
                            <div class="ticket-content">
                                <strong>Bus Name</strong>
                                <span>
                                <?php echo htmlspecialchars($ticket['busname']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="ticket-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="ticket-content">
                                <strong>From</strong>
                                <span><?php echo htmlspecialchars($ticket['fromplace']); ?></span>
                            </div>
                        </div>
                        <div class="ticket-item">
                            <i class="fas fa-map-pin"></i>
                            <div class="ticket-content">
                                <strong>To</strong>
                                <span><?php echo htmlspecialchars($ticket['toplace']); ?></span>
                            </div>
                        </div>
                        <div class="ticket-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="ticket-content">
                                <strong>Journey Date</strong>
                                <span><?php echo htmlspecialchars($ticket['journeydate']); ?></span>
                            </div>
                        </div>
                        <div class="ticket-item">
                        <i class="fas fa-tag"></i>
                            <div class="ticket-content">
                                <strong>Price</strong>
                                <span>₹<?php echo htmlspecialchars($ticket['ticketprice']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="x_boarding_points.php" method="POST" id="passengerForm">
                <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket_id); ?>">
                <input type="hidden" name="journey_date" value="<?php echo htmlspecialchars($ticket['journeydate']); ?>">
                <input type="hidden" name="from_location" value="<?php echo htmlspecialchars($ticket['fromplace']); ?>">
                <input type="hidden" name="to_location" value="<?php echo htmlspecialchars($ticket['toplace']); ?>">
                <input type="hidden" name="ticket_price" value="<?php echo htmlspecialchars($ticket['ticketprice']); ?>">
                <input type="hidden" name="seat_no" value="<?php echo htmlspecialchars($ticket['seat_no']); ?>">
                <input type="hidden" name="boarding_time" value="<?php echo htmlspecialchars($ticket['boarding_time']); ?>">
                <input type="hidden" name="seller_email" value="<?php echo htmlspecialchars($ticket['email']); ?>">
                <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($ticket['bus_id']); ?>">
                <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route_id); ?>">
                <input type="hidden" name="agency_email" value="<?php echo htmlspecialchars($agency_email); ?>">

                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-user"></i> Passenger Information</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group has-icon">
                                <label for="passenger_name">Full Name</label>
                                <input type="text" id="passenger_name" name="passenger_name" class="form-control" required pattern="[A-Za-z ]{1,255}" title="Please enter a valid name (letters and spaces only)">
                                <i class="fas fa-user input-icon"></i>
                                <div class="form-feedback" id="name-feedback"></div>
                            </div>

                            <div class="form-group has-icon">
                                <label for="age">Age</label>
                                <input type="number" id="age" name="age" class="form-control" min="1" max="120" required>
                                <i class="fas fa-birthday-cake input-icon"></i>
                                <div class="form-feedback" id="age-feedback"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group has-icon">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                <i class="fas fa-venus-mars input-icon"></i>
                                <div class="form-feedback" id="gender-feedback"></div>
                            </div>

                            <div class="form-group has-icon">
                                <label for="phoneNO">Phone Number</label>
                                <input type="tel" id="phoneNO" name="passenger_phone" class="form-control" required pattern="[0-9]{10,15}" title="Please enter a valid phone number (10-15 digits)">
                                <i class="fas fa-phone-alt input-icon"></i>
                                <div class="form-feedback" id="phone-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group has-icon">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" class="form-control" name="passenger_email" required>
                            <i class="fas fa-envelope input-icon"></i>
                            <div class="form-feedback" id="email-feedback"></div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    Continue to Boarding Points
                    <i class="fas fa-arrow-right"></i>
                </button>
                
                <div class="form-footer">
                    By continuing, you agree to our <a href="privacy.php">Privacy Policy</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Form validation
    const form = document.getElementById('passengerForm');
    const nameInput = document.getElementById('passenger_name');
    const ageInput = document.getElementById('age');
    const genderInput = document.getElementById('gender');
    const phoneInput = document.getElementById('phoneNO');
    const emailInput = document.getElementById('email');
    
    const nameFeedback = document.getElementById('name-feedback');
    const ageFeedback = document.getElementById('age-feedback');
    const genderFeedback = document.getElementById('gender-feedback');
    const phoneFeedback = document.getElementById('phone-feedback');
    const emailFeedback = document.getElementById('email-feedback');
    
    // Validate name
    nameInput.addEventListener('input', function() {
        if (this.validity.patternMismatch) {
            this.classList.add('error');
            this.classList.remove('success');
            nameFeedback.textContent = 'Please enter a valid name (letters and spaces only)';
            nameFeedback.classList.add('error');
            nameFeedback.style.display = 'block';
        } else if (this.validity.valueMissing) {
            this.classList.add('error');
            this.classList.remove('success');
            nameFeedback.textContent = 'Name is required';
            nameFeedback.classList.add('error');
            nameFeedback.style.display = 'block';
        } else {
            this.classList.remove('error');
            this.classList.add('success');
            nameFeedback.textContent = 'Looks good!';
            nameFeedback.classList.remove('error');
            nameFeedback.classList.add('success');
            nameFeedback.style.display = 'block';
        }
    });
    
    // Validate age
    ageInput.addEventListener('input', function() {
        const age = parseInt(this.value);
        if (isNaN(age) || age < 1 || age > 120) {
            this.classList.add('error');
            this.classList.remove('success');
            ageFeedback.textContent = 'Please enter a valid age between 1 and 120';
            ageFeedback.classList.add('error');
            ageFeedback.style.display = 'block';
        } else {
            this.classList.remove('error');
            this.classList.add('success');
            ageFeedback.textContent = 'Looks good!';
            ageFeedback.classList.remove('error');
            ageFeedback.classList.add('success');
            ageFeedback.style.display = 'block';
        }
    });
    
    // Validate gender
    genderInput.addEventListener('change', function() {
        if (this.value === '') {
            this.classList.add('error');
            this.classList.remove('success');
            genderFeedback.textContent = 'Please select a gender';
            genderFeedback.classList.add('error');
            genderFeedback.style.display = 'block';
        } else {
            this.classList.remove('error');
            this.classList.add('success');
            genderFeedback.textContent = 'Looks good!';
            genderFeedback.classList.remove('error');
            genderFeedback.classList.add('success');
            genderFeedback.style.display = 'block';
        }
    });
    
    // Validate phone
    phoneInput.addEventListener('input', function() {
        if (this.validity.patternMismatch) {
            this.classList.add('error');
            this.classList.remove('success');
            phoneFeedback.textContent = 'Please enter a valid phone number (10-15 digits)';
            phoneFeedback.classList.add('error');
            phoneFeedback.style.display = 'block';
        } else if (this.validity.valueMissing) {
            this.classList.add('error');
            this.classList.remove('success');
            phoneFeedback.textContent = 'Phone number is required';
            phoneFeedback.classList.add('error');
            phoneFeedback.style.display = 'block';
        } else {
            this.classList.remove('error');
            this.classList.add('success');
            phoneFeedback.textContent = 'Looks good!';
            phoneFeedback.classList.remove('error');
            phoneFeedback.classList.add('success');
            phoneFeedback.style.display = 'block';
        }
    });
    
    // Validate email
    emailInput.addEventListener('input', function() {
        if (this.validity.typeMismatch) {
            this.classList.add('error');
            this.classList.remove('success');
            emailFeedback.textContent = 'Please enter a valid email address';
            emailFeedback.classList.add('error');
            emailFeedback.style.display = 'block';
        } else if (this.validity.valueMissing) {
            this.classList.add('error');
            this.classList.remove('success');
            emailFeedback.textContent = 'Email is required';
            emailFeedback.classList.add('error');
            emailFeedback.style.display = 'block';
        } else {
            this.classList.remove('error');
            this.classList.add('success');
            emailFeedback.textContent = 'Looks good!';
            emailFeedback.classList.remove('error');
            emailFeedback.classList.add('success');
            emailFeedback.style.display = 'block';
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        // Check all inputs are valid
        if (!nameInput.validity.valid || !ageInput.validity.valid || 
            !genderInput.validity.valid || !phoneInput.validity.valid || 
            !emailInput.validity.valid) {
            e.preventDefault();
            
            // Trigger validation on all fields
            if (!nameInput.validity.valid) {
                nameInput.dispatchEvent(new Event('input'));
            }
            
            if (!ageInput.validity.valid) {
                ageInput.dispatchEvent(new Event('input'));
            }
            
            if (!genderInput.validity.valid) {
                genderInput.dispatchEvent(new Event('change'));
            }
            
            if (!phoneInput.validity.valid) {
                phoneInput.dispatchEvent(new Event('input'));
            }
            
            if (!emailInput.validity.valid) {
                emailInput.dispatchEvent(new Event('input'));
            }
        } else {
            // Add loading effect when form is submitted
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<span class="loader"></span> Processing...';
            submitBtn.disabled = true;
        }
    });
    </script>
</body>
</html>
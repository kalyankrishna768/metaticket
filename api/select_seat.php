<?php
include 'config.php';
session_start();

// Retrieve POST data
$route_id = isset($_POST['route_id']) ? $_POST['route_id'] : '';
$bus_id = isset($_POST['bus_id']) ? $_POST['bus_id'] : '';
$agency_email = isset($_POST['agency_email']) ? $_POST['agency_email'] : '';
$busname = isset($_POST['username']) ? $_POST['username'] : '';
$journeydate = isset($_POST['journeydate']) ? $_POST['journeydate'] : '';
$startingtime = isset($_POST['startingtime']) ? $_POST['startingtime'] : '';
$ticketprice = isset($_POST['ticketprice']) ? $_POST['ticketprice'] : '';
$from = isset($_POST['from']) ? $_POST['from'] : '';
$to = isset($_POST['to']) ? $_POST['to'] : '';

// Modified query to get booked seats from the bookings table
$booked_seats_query = "SELECT selected_seats FROM bookings 
                      WHERE busname = ? 
                      AND journeydate = ? 
                      AND from_location = ? 
                      AND to_location = ?";
$stmt = $conn->prepare($booked_seats_query);
$stmt->bind_param("ssss", $busname, $journeydate, $from, $to);
$stmt->execute();
$booked_result = $stmt->get_result();

$booked_seats = [];
while($row = $booked_result->fetch_assoc()) {
    // Split the comma-separated seat numbers and add them to the array
    $seats = explode(',', str_replace(' ', '', $row['selected_seats']));
    $booked_seats = array_merge($booked_seats, $seats);
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats</title>
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
        --card-hover-bg: rgba(255, 255, 255, 0.2);
        --border-radius: 12px;
        --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        --available-seat-color: rgba(255, 255, 255, 0.1);
        --selected-seat-color: #28a745;
        --booked-seat-color: #dc3545;
        --warning-color: #ffc107;
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

    .journey-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        padding: 20px;
        background: var(--card-bg);
        border-radius: var(--border-radius);
    }

    .seats-container {
        display: grid;
        grid-template-columns: 3fr 1.5fr;
        gap: 20px;
        margin-top: 20px;
    }

    .bus-layout {
        background: var(--card-bg);
        padding: 20px;
        border-radius: var(--border-radius);
    }

    .seat-legend {
        display: flex;
        justify-content: space-around;
        margin: 20px 0;
        padding: 10px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: var(--border-radius);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
    }

    .available-seat {
        background-color: var(--available-seat-color);
    }

    .selected-seat {
        background-color: var(--selected-seat-color);
    }

    .booked-seat {
        background-color: var(--booked-seat-color);
    }

    .seats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-top: 5px;
    }

    .seat {
        padding: 15px;
        text-align: center;
        background: var(--available-seat-color);
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: all 0.3s ease;
        color: var(--text-color);
    }

    .seat:not(.booked):hover {
        background: var(--primary-color);
        transform: scale(1.05);
    }

    .seat.selected {
        background: var(--selected-seat-color);
    }

    .seat.booked {
        background: var(--booked-seat-color);
        cursor: not-allowed;
        opacity: 0.7;
    }

    .booking-details {
        background: var(--card-bg);
        padding: 20px;
        border-radius: var(--border-radius);
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-color);
    }

    select,
    input {
        width: 100%;
        padding: 10px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius);
        color: var(--text-color);
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
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        background: #c73615;
        transform: translateY(-2px);
    }

    .seat-limit-warning {
        background-color: var(--warning-color);
        color: #000;
        padding: 10px;
        border-radius: var(--border-radius);
        text-align: center;
        margin-top: 15px;
        font-weight: 500;
        display: none;
    }

    .seat-count {
        font-weight: bold;
        margin-bottom: 10px;
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

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-5px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(5px);
        }
    }

    .shake {
        animation: shake 0.8s ease;
    }

    @media (max-width: 768px) {
        .seats-container {
            grid-template-columns: 1fr;
        }

        .seat-legend {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Select Your Seats</h1>
        </div>

        <div class="journey-details">
            <div>
                <strong><i class="fas fa-map-marker-alt"></i> From:</strong> <?php echo htmlspecialchars($from); ?>
            </div>
            <div>
                <strong><i class="fas fa-map-pin"></i> To:</strong> <?php echo htmlspecialchars($to); ?>
            </div>
            <div>
                <strong><i class="far fa-calendar-alt"></i> Date:</strong> <?php echo htmlspecialchars($journeydate); ?>
            </div>
            <div>
                <strong><i class="far fa-clock"></i> Time:</strong> <?php echo htmlspecialchars($startingtime); ?>
            </div>
            <div>
                <strong><i class="fas fa-tag"></i> Price:</strong> ₹<?php echo htmlspecialchars($ticketprice); ?>
            </div>
            <div>
                <strong><i class="fas fa-bus-alt"></i> Bus Name :</strong> <?php echo htmlspecialchars($busname); ?>
            </div>
        </div>

        <form action="passenger_details.php" method="POST">
            <div class="seats-container">
                <div class="bus-layout">
                    <h2>Select Seats</h2>

                    <div class="seat-count">Selected: <span id="seat-counter">0</span>/10 seats</div>

                    <!-- Add seat color legend -->
                    <div class="seat-legend">
                        <div class="legend-item">
                            <div class="legend-color available-seat"></div>
                            <span>Available</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color selected-seat"></div>
                            <span>Selected</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color booked-seat"></div>
                            <span>Booked</span>
                        </div>
                    </div>

                    <div class="seats-grid">
                        <?php
                        for ($i = 1; $i <= 40; $i++) {
                            $seatClass = in_array($i, $booked_seats) ? 'seat booked' : 'seat';
                            echo "<button type='button' class='$seatClass' data-seat='$i'>$i</button>";
                        }
                        ?>
                    </div>

                    <div id="seat-limit-warning" class="seat-limit-warning">
                        <i class="fas fa-exclamation-triangle"></i> Maximum 10 seats allowed per booking!
                    </div>
                </div>

                <div class="booking-details">
                    <h2>Booking Details</h2>

                    <div class="form-group">
                        <label><i class="fas fa-ticket-alt text-indigo-600"></i> Selected Seats</label>
                        <input type="text" name="selected_seats" id="selected_seats" readonly required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Total Amount</label>
                        <input type="text" name="total_amount" id="total_amount" readonly required>
                    </div>

                    <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route_id); ?>">
                    <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus_id); ?>">
                    <input type="hidden" name="agency_email" value="<?php echo htmlspecialchars($agency_email); ?>">
                    <input type="hidden" name="busname" value="<?php echo htmlspecialchars($busname); ?>">
                    <input type="hidden" name="journeydate" value="<?php echo htmlspecialchars($journeydate); ?>">
                    <input type="hidden" name="startingtime" value="<?php echo htmlspecialchars($startingtime); ?>">
                    <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                    <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
                    <input type="hidden" name="ticketprice" value="<?php echo htmlspecialchars($ticketprice); ?>">

                    <button type="submit" class="submit-btn">Continue to Book</button>
                </div>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const seats = document.querySelectorAll('.seat:not(.booked)');
        const selectedSeatsInput = document.getElementById('selected_seats');
        const totalAmountInput = document.getElementById('total_amount');
        const seatCounter = document.getElementById('seat-counter');
        const seatLimitWarning = document.getElementById('seat-limit-warning');
        const basePrice = <?php echo $ticketprice; ?>;
        let selectedSeats = [];
        const MAX_SEATS = 10;

        // Set initial total amount to ₹0
        totalAmountInput.value = '₹0';

        seats.forEach(seat => {
            seat.addEventListener('click', function() {
                const seatNumber = this.dataset.seat;

                if (this.classList.contains('selected')) {
                    // Unselect a seat
                    this.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(num => num !== seatNumber);

                    // Hide warning if it was showing
                    seatLimitWarning.style.display = 'none';
                } else {
                    // Try to select a seat
                    if (selectedSeats.length >= MAX_SEATS) {
                        // Cannot select more than MAX_SEATS seats
                        seatLimitWarning.style.display = 'block';
                        seatLimitWarning.classList.add('shake');

                        // Remove shake animation after it completes
                        setTimeout(() => {
                            seatLimitWarning.classList.remove('shake');
                        }, 800);

                        return;
                    }

                    this.classList.add('selected');
                    selectedSeats.push(seatNumber);
                }

                // Update counter
                seatCounter.textContent = selectedSeats.length;

                // Sort the selected seats numerically
                selectedSeats.sort((a, b) => parseInt(a) - parseInt(b));
                selectedSeatsInput.value = selectedSeats.join(', ');
                totalAmountInput.value = '₹' + (selectedSeats.length * basePrice);
            });
        });

        // Add validation to form submission
        document.querySelector('form').addEventListener('submit', function(event) {
            if (selectedSeats.length === 0) {
                event.preventDefault();
                alert('Please select at least one seat to continue.');
            }
        });
    });
    </script>
</body>

</html>

<?php
$conn->close();
?>
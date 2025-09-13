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
$total_amount = isset($_POST['total_amount']) ? $_POST['total_amount'] : '';
$ticketprice = isset($_POST['ticketprice']) ? $_POST['ticketprice'] : '';
$from = isset($_POST['from']) ? $_POST['from'] : '';
$to = isset($_POST['to']) ? $_POST['to'] : '';
$useremail = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : '';
$selected_seats = isset($_POST['selected_seats']) ? $_POST['selected_seats'] : '';

// Validate required data
if(!$route_id || !$bus_id || !$busname || !$journeydate || !$selected_seats) {
    die("Missing required parameters");
}

// Fetch route points
$route_query = "SELECT from_points, to_points FROM routes WHERE id = ?";
$stmt = $conn->prepare($route_query);
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();
$route_data = $result->fetch_assoc();

if (!$route_data) {
    die("Route not found");
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

        // Validate boarding and dropping points
        $boarding_point = $_POST['boarding_point'];
        $dropping_point = $_POST['dropping_point'];
        $boarding_time = $_POST['boarding_time'];
        $dropping_time = $_POST['dropping_time'];
        
        $boarding_valid = false;
        $dropping_valid = false;
        
        foreach ($boarding_points as $point) {
            if ($point['name'] === $boarding_point) {
                $boarding_valid = true;
                break;
            }
        }

        foreach ($boarding_time as $point) {
            if ($point['time'] === $boarding_time) {
                $boarding_valid = true;
                break;
            }
        }
        
        foreach ($dropping_points as $point) {
            if ($point['name'] === $dropping_point) {
                $dropping_valid = true;
                break;
            }
        }

        foreach ($dropping_time as $point) {
            if ($point['time'] === $dropping_time) {
                $dropping_valid = true;
                break;
            }
        }
        
        if (!$boarding_valid || !$dropping_valid) {
            throw new Exception("Invalid boarding or dropping point or time selected");
        }

    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Booking failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Boarding & Dropping Points</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        max-width: 800px;
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
    }

    .points-selection {
        background: var(--card-bg);
        padding: 20px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-color);
        font-weight: 500;
    }

    select {
        width: 100%;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius);
        color: var(--text-color);
        font-size: 16px;
    }

    .point-details {
        margin-top: 10px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: var(--border-radius);
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

    .error-message {
        background: #dc3545;
        color: white;
        padding: 10px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
    }

    select option {
        background-color: rgba(41, 41, 42, 0.76);
        color: var(--text-color);
        padding: 12px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-bus"></i> Select Boarding & Dropping Points</h1>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="insert_bookings.php">
            <div class="points-selection">
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Select Boarding Point</label>
                    <select name="boarding_point" id="boarding_point" required>
                        <option value="">Select Boarding Point</option>
                        <?php 
                        if (is_array($boarding_points)) {
                            foreach ($boarding_points as $point): 
                                $location = isset($point['name']) ? $point['name'] : '';
                                $time = isset($point['time']) ? $point['time'] : '';
                        ?>
                        <option value="<?php echo htmlspecialchars($location); ?>"
                            data-time="<?php echo htmlspecialchars($time); ?>">
                            <?php echo htmlspecialchars($location); ?>
                            (Time: <?php echo htmlspecialchars($time); ?>)
                        </option>
                        <?php 
                            endforeach;
                        } 
                        ?>
                    </select>
                    <input type="hidden" name="boarding_time" id="boarding_time" value="">
                    <div class="point-details" id="boarding-details">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-flag-checkered"></i> Select Dropping Point</label>
                    <select name="dropping_point" id="dropping_point" required>
                        <option value="">Select Dropping Point</option>
                        <?php 
                        if (is_array($dropping_points)) {
                            foreach ($dropping_points as $point): 
                                $location = isset($point['name']) ? $point['name'] : ''; 
                                $time = isset($point['time']) ? $point['time'] : ''; 
                        ?>
                        <option value="<?php echo htmlspecialchars($location); ?>"
                            data-time="<?php echo htmlspecialchars($time); ?>">
                            <?php echo htmlspecialchars($location); ?>
                            (Time: <?php echo htmlspecialchars($time); ?>)
                        </option>
                        <?php 
                            endforeach;
                        } 
                        ?>
                    </select>
                    <input type="hidden" name="dropping_time" id="dropping_time" value="">
                    <div class="point-details" id="dropping-details">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <!-- Hidden fields -->
            <?php foreach ($_POST as $key => $value): 
                if ($key !== 'id' && $key !== 'boarding_point' && $key !== 'dropping_point' && $key !== 'boarding_time' && $key !== 'dropping_time'): ?>
            <?php if (is_array($value)): ?>
            <?php foreach ($value as $item): ?>
            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>[]"
                value="<?php echo htmlspecialchars($item); ?>">
            <?php endforeach; ?>
            <?php else: ?>
            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                value="<?php echo htmlspecialchars($value); ?>">
            <?php endif; ?>
            <?php endif; ?>
            <?php endforeach; ?>

            <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route_id); ?>">
            <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus_id); ?>">
            <input type="hidden" name="useremail" value="<?php echo htmlspecialchars($useremail); ?>">
            <input type="hidden" name="journeydate" value="<?php echo htmlspecialchars($journeydate); ?>">
            <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
            <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
            <input type="hidden" name="ticketprice" value="<?php echo htmlspecialchars($ticketprice); ?>">
            <input type="hidden" name="selected_seats" value="<?php echo htmlspecialchars($selected_seats); ?>">
            <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>">
            <input type="hidden" name="selected_seats" value="<?php echo htmlspecialchars($selected_seats); ?>">


            <button type="submit" name="confirm_points" class="submit-btn"><i class="fas fa-check"></i> Confirm</button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const boardingSelect = document.getElementById('boarding_point');
        const droppingSelect = document.getElementById('dropping_point');
        const boardingDetails = document.getElementById('boarding-details');
        const droppingDetails = document.getElementById('dropping-details');
        const boardingTimeInput = document.getElementById('boarding_time');
        const droppingTimeInput = document.getElementById('dropping_time');

        function updatePointDetails(select, detailsDiv, timeInput) {
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const time = selectedOption.getAttribute('data-time');
                detailsDiv.innerHTML =
                    `<i class="fas fa-clock"></i> <strong>Scheduled Time:</strong> ${time || 'Not specified'}`;
                timeInput.value = time || '';
            } else {
                detailsDiv.innerHTML = '<i class="fas fa-clock"></i>';
                timeInput.value = '';
            }
        }

        // Initial update
        updatePointDetails(boardingSelect, boardingDetails, boardingTimeInput);
        updatePointDetails(droppingSelect, droppingDetails, droppingTimeInput);

        boardingSelect.addEventListener('change', () => {
            updatePointDetails(boardingSelect, boardingDetails, boardingTimeInput);
        });

        droppingSelect.addEventListener('change', () => {
            updatePointDetails(droppingSelect, droppingDetails, droppingTimeInput);
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const boarding = boardingSelect.value;
            const dropping = droppingSelect.value;
            const boardingTime = boardingTimeInput.value;
            const droppingTime = droppingTimeInput.value;

            if (!boarding || !dropping || !boardingTime || !droppingTime) {
                e.preventDefault();
                alert('Please select both boarding and dropping points with valid times');
                return;
            }

            if (boarding === dropping) {
                e.preventDefault();
                alert('Boarding and dropping points cannot be the same!');
                return;
            }
        });
    });
    </script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>
<?php
include 'config.php';

// Retrieve form inputs
$date = isset($_POST['date']) ? $_POST['date'] : '';
$from = isset($_POST['from']) ? $_POST['from'] : '';
$to = isset($_POST['to']) ? $_POST['to'] : '';
$username = isset($_POST['username']) ? $_POST['username'] : '';

// Validate form inputs
if (empty($date) || empty($from) || empty($to)) {
    die("All fields are required.");
}

// Prepare the query to fetch tickets from routes table
$sql = "SELECT id, bus_id, email, username, departure_date, departure_time, base_fare 
        FROM routes 
        WHERE from_location = ? AND to_location = ? AND departure_date = ? AND status = 'active'";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("SQL statement preparation failed: " . $conn->error);
}

$stmt->bind_param("sss", $from, $to, $date);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Buses</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    :root {
        --primary-color: #e84118;
        --secondary-color: #28a745;
        --background-color: rgba(0, 0, 0, 0.7);
        --text-color: #fff;
        --shadow-color: rgba(0, 0, 0, 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: url('assets/images/search_bus.jpg') no-repeat center center/cover;
        background-attachment: fixed;
        color: var(--text-color);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 15px;
    }

    .container {
        width: 100%;
        max-width: 1000px;
        margin: 20px auto;
        padding: 20px;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 10px;
        box-shadow: 0 15px 30px var(--shadow-color);
        overflow: hidden;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgba(232, 65, 24, 0.2);
    }

    h1 {
        color: var(--primary-color);
        font-size: clamp(1.5rem, 5vw, 2.5rem);
        font-weight: 600;
        margin-bottom: 10px;
    }

    .journey-details {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
        color: #ccc;
        font-size: clamp(0.9rem, 3vw, 1.1rem);
    }

    .journey-details span {
        display: flex;
        align-items: center;
        gap: 5px;
        margin: 0 5px;
    }

    .bus-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 3px 10px var(--shadow-color);
    }

    .bus-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .bus-info {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .bus-info-item {
        flex: 1 1 120px;
        margin: 5px;
    }

    .bus-info-label {
        font-size: 0.85rem;
        color: #aaa;
        margin-bottom: 5px;
    }

    .bus-info-value {
        font-size: 1rem;
        font-weight: 600;
    }

    .bus-actions {
        text-align: right;
    }

    /* Table styles for larger screens */
    .table-container {
        overflow-x: auto;
        margin: 20px 0;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
        color: #fff;
        display: none;
    }

    thead tr {
        background: var(--primary-color);
        color: white;
    }

    th,
    td {
        padding: 15px;
        text-align: left;
        font-weight: 500;
    }

    tbody tr {
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 3px 10px var(--shadow-color);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    td:first-child {
        border-radius: 8px 0 0 8px;
    }

    td:last-child {
        border-radius: 0 8px 8px 0;
    }

    .bus-name {
        font-weight: 600;
        color: #fff;
    }

    .price {
        font-weight: 600;
        color: white;
    }

    .book-now-btn {
        display: inline-block;
        padding: 10px 20px;
        background: rgb(211, 62, 12);
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-align: center;
    }

    .book-now-btn:hover {
        background: rgb(211, 62, 12);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .back-btn {
        display: inline-block;
        padding: 12px 30px;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        margin-top: 20px;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        background: #c73615;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(232, 65, 24, 0.3);
    }

    .no-results {
        text-align: center;
        padding: 40px 20px;
        color: #ccc;
    }

    .no-results p {
        font-size: 1.2em;
        margin-bottom: 20px;
    }

    .actions {
        text-align: center;
        margin-top: 30px;
    }

    /* Media queries for responsive design */
    @media (min-width: 768px) {
        .bus-cards {
            display: none;
        }

        table {
            display: table;
        }

        .journey-details {
            flex-direction: row;
        }
    }

    @media (max-width: 767px) {
        .container {
            padding: 15px;
            margin: 10px auto;
        }

        .journey-details {
            flex-direction: column;
            align-items: center;
        }

        .bus-cards {
            display: block;
        }

        .book-now-btn {
            width: 100%;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-bus"></i> Available Buses</h1>
            <div class="journey-details">
                <span><i class="fas fa-map-marker-alt"></i> From: <?php echo htmlspecialchars($from); ?></span>
                <span><i class="fas fa-arrow-right"></i></span>
                <span><i class="fas fa-map-pin"></i> To: <?php echo htmlspecialchars($to); ?></span>
                <span>|</span>
                <span><i class="far fa-calendar-alt"></i> Date: <?php echo htmlspecialchars($date); ?></span>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>
        <!-- Card view for mobile devices -->
        <div class="bus-cards">
            <?php
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()):
                    ?>
            <div class="bus-card">
                <div class="bus-info">
                    <div class="bus-info-item">
                        <div class="bus-info-label"><i class="fas fa-bus"></i> Bus Name</div>
                        <div class="bus-info-value"><?php echo htmlspecialchars($row['username']); ?></div>
                    </div>
                    <div class="bus-info-item">
                        <div class="bus-info-label"><i class="far fa-calendar"></i> Date</div>
                        <div class="bus-info-value"><?php echo htmlspecialchars($row['departure_date']); ?></div>
                    </div>
                    <div class="bus-info-item">
                        <div class="bus-info-label"><i class="far fa-clock"></i> Time</div>
                        <div class="bus-info-value"><?php echo htmlspecialchars($row['departure_time']); ?></div>
                    </div>
                    <div class="bus-info-item">
                        <div class="bus-info-label"><i class="fas fa-rupee-sign"></i> Price</div>
                        <div class="bus-info-value">₹<?php echo htmlspecialchars($row['base_fare']); ?></div>
                    </div>
                </div>
                <div class="bus-actions">
                    <form action="select_seat.php" method="POST">
                        <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($row['bus_id']); ?>">
                        <input type="hidden" name="agency_email" value="<?php echo htmlspecialchars($row['email']); ?>">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($row['username']); ?>">
                        <input type="hidden" name="journeydate"
                            value="<?php echo htmlspecialchars($row['departure_date']); ?>">
                        <input type="hidden" name="startingtime"
                            value="<?php echo htmlspecialchars($row['departure_time']); ?>">
                        <input type="hidden" name="ticketprice"
                            value="<?php echo htmlspecialchars($row['base_fare']); ?>">
                        <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                        <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
                        <button type="submit" class="book-now-btn"><i class="fas fa-ticket-alt"></i> Book Now</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Table view for desktop -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-bus"></i> Bus Name</th>
                        <th><i class="far fa-calendar"></i> Journey Date</th>
                        <th><i class="far fa-clock"></i> Departure Time</th>
                        <th><i class="fas fa-rupee-sign"></i> Price</th>
                        <th><i class="fas fa-ticket-alt"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()):
                            ?>
                    <tr>
                        <td class="bus-name"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['departure_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                        <td class="price">₹<?php echo htmlspecialchars($row['base_fare']); ?></td>
                        <td>
                            <form action="select_seat.php" method="POST">
                                <input type="hidden" name="route_id"
                                    value="<?php echo htmlspecialchars($row['id']); ?>">
                                <input type="hidden" name="bus_id"
                                    value="<?php echo htmlspecialchars($row['bus_id']); ?>">
                                <input type="hidden" name="agency_email"
                                    value="<?php echo htmlspecialchars($row['email']); ?>">
                                <input type="hidden" name="username"
                                    value="<?php echo htmlspecialchars($row['username']); ?>">
                                <input type="hidden" name="journeydate"
                                    value="<?php echo htmlspecialchars($row['departure_date']); ?>">
                                <input type="hidden" name="startingtime"
                                    value="<?php echo htmlspecialchars($row['departure_time']); ?>">
                                <input type="hidden" name="ticketprice"
                                    value="<?php echo htmlspecialchars($row['base_fare']); ?>">
                                <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                                <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
                                <button type="submit" class="book-now-btn"><i class="fas fa-ticket-alt"></i> Book
                                    Now</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-results">
            <p><i class="fas fa-exclamation-circle"></i> No buses available for the selected route, date, or status.</p>
        </div>
        <?php endif; ?>

        <div class="actions">
            <a href="busbuy.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Search</a>
        </div>
    </div>
</body>

</html>
<?php
$stmt->close();
$conn->close();
?>
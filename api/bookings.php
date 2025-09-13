<?php
include 'config.php';
session_start();

// Check if the user is logged in as an agency
if (!isset($_SESSION['email']) || !isset($_SESSION['username'])) {
    header("Location: newsignin.php");
    exit();
}

class Bookings
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli("localhost", "root", "", "ticket");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getAllBookings($search = "", $filterDate = null)
    {
        // First, update statuses for past journey dates
        $this->updateCompletedBookings();

        $sql = "SELECT 
                    ab.id,
                    ab.user_email,
                    ab.agency_email,
                    ab.journey_date,
                    ab.seat_number,
                    ab.passenger_name,
                    ab.ticket_price,
                    ab.booking_status,
                    ab.boarding_point,
                    ab.dropping_point,
                    ab.created_at,
                    ab.route_id,
                    r.from_location,
                    r.to_location,
                    CONCAT(r.from_location, ' - ', r.to_location) as route
                FROM 
                    agency_bookings ab
                JOIN 
                    routes r ON ab.route_id = r.id
                WHERE 
                    ab.agency_email = ?";

        if ($filterDate === 'today') {
            $sql .= " AND DATE(ab.journey_date) = CURDATE()";
        } elseif (!empty($filterDate)) {
            $dateRange = explode(' to ', $filterDate);
            if (count($dateRange) == 2) {
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $sql .= " AND ab.journey_date BETWEEN ? AND ?";
            }
        }

        if (!empty($search)) {
            $sql .= " AND (
                ab.user_email LIKE ? OR
                ab.seat_number LIKE ? OR
                ab.passenger_name LIKE ? OR 
                r.from_location LIKE ? OR
                ab.route_id LIKE ? OR 
                r.to_location LIKE ? OR 
                ab.boarding_point LIKE ? OR 
                ab.dropping_point LIKE ?
            )";
        }

        $sql .= " ORDER BY ab.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        if ($filterDate === 'today') {
            if (!empty($search)) {
                $searchParam = "%$search%";
                $stmt->bind_param(
                    "sssssssss",
                    $_SESSION['email'],
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam
                );
            } else {
                $stmt->bind_param("s", $_SESSION['email']);
            }
        } elseif (!empty($filterDate) && count($dateRange) == 2) {
            if (!empty($search)) {
                $searchParam = "%$search%";
                $stmt->bind_param(
                    "sssssssssss",
                    $_SESSION['email'],
                    $startDate,
                    $endDate,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam
                );
            } else {
                $stmt->bind_param("sss", $_SESSION['email'], $startDate, $endDate);
            }
        } else {
            if (!empty($search)) {
                $searchParam = "%$search%";
                $stmt->bind_param(
                    "sssssssss",
                    $_SESSION['email'],
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam,
                    $searchParam
                );
            } else {
                $stmt->bind_param("s", $_SESSION['email']);
            }
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // New method to update bookings to "Completed" if journey date is past
    private function updateCompletedBookings()
    {
        $stmt = $this->conn->prepare("
            UPDATE agency_bookings 
            SET booking_status = 'Completed'
            WHERE journey_date < CURDATE() 
            AND booking_status NOT IN ('Cancelled', 'Completed')
            AND agency_email = ?
        ");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
    }

    public function getBookingDetails($bookingId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                ab.*,
                r.from_location,
                r.to_location
            FROM 
                agency_bookings ab
            JOIN 
                routes r ON ab.route_id = r.id
            WHERE 
                ab.id = ? AND ab.agency_email = ?
        ");
        $stmt->bind_param("is", $bookingId, $_SESSION['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateBookingStatus($bookingId, $status)
    {
        $stmt = $this->conn->prepare("
            UPDATE agency_bookings 
            SET booking_status = ? 
            WHERE id = ? AND agency_email = ?
        ");
        $stmt->bind_param("sis", $status, $bookingId, $_SESSION['email']);
        return $stmt->execute();
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

$bookings = new Bookings();
$searchQuery = isset($_GET['search']) ? $_GET['search'] : "";
$dateFilter = isset($_GET['date_filter']) ? $_GET['date_filter'] : "";

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $dateFilter = $_GET['start_date'] . ' to ' . $_GET['end_date'];
}

$allBookings = $bookings->getAllBookings($searchQuery, $dateFilter);

if (isset($_GET['action']) && $_GET['action'] === 'getBookingDetails' && isset($_GET['id'])) {
    $bookingDetails = $bookings->getBookingDetails($_GET['id']);
    header('Content-Type: application/json');
    echo json_encode($bookingDetails);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'updateStatus' && isset($_POST['id']) && isset($_POST['status'])) {
    $result = $bookings->updateBookingStatus($_POST['id'], $_POST['status']);
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Home - Bookings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    @media (max-width: 768px) {
        #sidebar {
            transition: transform 0.3s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 40;
            transform: translateX(-100%);
        }

        #sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            transition: margin-left 0.3s ease-in-out;
        }

        body.sidebar-open {
            overflow: hidden;
        }

        .sidebar-backdrop {
            transition: opacity 0.3s ease-in-out;
            opacity: 0;
            pointer-events: none;
        }

        .sidebar-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }
    }

    .search-button {
        background-color: #3b82f6;
        color: white;
        transition: all 0.3s ease;
    }

    .search-button:hover {
        background-color: #2563eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .modal-content {
        animation: modalFade 0.3s ease-out;
    }

    @keyframes modalFade {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .filter-section {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        justify-content: space-between;
    }

    @media (max-width: 768px) {
        .filter-section {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-section form,
        .filter-section a {
            width: 100%;
        }

        .filter-section select,
        .filter-section input {
            width: 100%;
        }
    }

    /* Custom status colors */
    .status-confirmed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-completed {
        background-color: #bbf7d0;
        color: #166534;
    }

    .status-cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }
    </style>
</head>

<body class="bg-gray-50">
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 sidebar-backdrop md:hidden"
        onclick="toggleSidebar()"></div>

    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Mobile Header -->
        <div class="md:hidden bg-blue-700 text-white p-4 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-2xl font-bold"><?php echo ucfirst($_SESSION['username']); ?></h1>
                <p class="text-sm text-blue-200"><i class="fas fa-ticket-alt w-5"></i> Bookings</p>
            </div>
            <button id="navToggle" class="text-white focus:outline-none" onclick="toggleSidebar()">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Sidebar -->
        <div id="sidebar" class="w-64 bg-blue-700 text-white md:block">
            <div class="p-6">
                <h1 class="text-2xl font-bold"><?php echo ucfirst($_SESSION['username']); ?></h1>
                <p class="text-sm text-blue-200"><i class="fas fa-ticket-alt w-5"></i> Bookings</p>
            </div>

            <nav class="mt-4 flex-grow">
                <a href="agency_home.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="managebus.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-bus w-5"></i>
                    <span class="ml-3">Manage Buses</span>
                </a>
                <a href="routes.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-route w-5"></i>
                    <span class="ml-3">Routes</span>
                </a>
                <a href="bookings.php" class="flex items-center px-6 py-3 bg-blue-800">
                    <i class="fas fa-ticket-alt w-5"></i>
                    <span class="ml-3">Bookings</span>
                </a>
                <a href="x_bookings.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-clipboard-list w-5"></i>
                    <span class="ml-3">New Bookings</span>
                </a>
                <a href="agency_wallet.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-wallet w-5"></i>
                    <span class="ml-3">Wallet</span>
                </a>
            </nav>

            <div class="md:hidden p-4 border-t border-blue-800">
                <button onclick="toggleSidebar()" class="flex items-center text-blue-100 hover:text-white">
                    <i class="fas fa-arrow-left mr-2"></i> Close Menu
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto main-content">
            <!-- Desktop Header -->
            <header class="bg-white shadow-sm hidden md:block">
                <div class="flex items-center justify-between px-4 md:px-6 py-4">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold"><i class="fas fa-ticket-alt text-blue-600 mr-2"></i>Bookings
                            Management</h1>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Search and Filter Section -->
            <div class="p-4 bg-white shadow-sm">
                <div class="filter-section mb-4">
                    <form method="GET" action="" class="flex items-center flex-grow">
                        <div class="relative flex items-center flex-grow">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-500"></i>
                            </span>
                            <input type="text" name="search" placeholder="Search bookings..."
                                value="<?php echo htmlspecialchars($searchQuery); ?>"
                                class="pl-10 pr-16 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                            <button type="submit"
                                class="absolute right-0 top-0 bottom-0 px-3 bg-blue-600 text-white rounded-r-lg search-button">
                                <i class="fas fa-search mr-1"></i>
                            </button>
                        </div>
                    </form>

                    <form method="GET" action="" id="dateFilterForm" class="flex items-center">
                        <select name="date_filter" onchange="handleDateFilterChange(this)"
                            class="py-2 px-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Dates</option>
                            <option value="today" <?php echo $dateFilter === 'today' ? 'selected' : ''; ?>>Today's
                                Bookings</option>
                            <option value="select_date">Select Date Range</option>
                        </select>
                    </form>

                    <?php if (!empty($searchQuery) || !empty($dateFilter)): ?>
                    <a href="bookings.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-times-circle mr-1"></i> Clear Filters
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Main Content -->
            <main class="p-4 md:p-6">
                <!-- Select Date Modal -->
                <div id="selectDateModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeSelectDateModal()"></div>
                    <div class="relative flex items-center justify-center min-h-screen p-4">
                        <div
                            class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white modal-content">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>Select Date
                                </h3>
                                <button type="button" onclick="closeSelectDateModal()"
                                    class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form id="selectDateForm" method="GET" action="">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                        <input type="date" name="start_date" id="startDateInput"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                                        <input type="date" name="end_date" id="endDateInput"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end space-x-3">
                                    <button type="button" onclick="closeSelectDateModal()"
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition duration-150">
                                        Cancel
                                    </button>
                                    <button type="button" onclick="applyDateRange()"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150">
                                        Apply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-hashtag mr-1"></i> Booking ID
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-chair mr-1"></i> Seat
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-user mr-1"></i> Passenger
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-calendar-alt mr-1"></i> Journey Date
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-map-pin mr-1"></i> Route ID
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-map-marked-alt mr-1"></i> Route
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-rupee-sign mr-1"></i> Amount
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-info-circle mr-1"></i> Status
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-cogs mr-1"></i> Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($allBookings) > 0): ?>
                                <?php foreach ($allBookings as $booking): ?>
                                <tr class="hover:bg-gray-50" data-booking-id="<?php echo $booking['id']; ?>">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        #<?php echo htmlspecialchars($booking['id']); ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <?php echo htmlspecialchars($booking['seat_number']); ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($booking['passenger_name']); ?>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-4 md:px-6 py-4 whitespace-nowrap">
                                        <?php echo date('d M Y', strtotime($booking['journey_date'])); ?>
                                    </td>
                                    <td
                                        class="hidden md:table-cell px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php echo htmlspecialchars($booking['route_id']); ?>
                                    </td>
                                    <td
                                        class="hidden md:table-cell px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>
                                            <?php echo htmlspecialchars($booking['from_location'] . ' - ' . $booking['to_location']); ?>
                                        </div>
                                        <div class="text-xs ml-5">
                                            <?php echo htmlspecialchars($booking['boarding_point']) . ' → ' . htmlspecialchars($booking['dropping_point']); ?>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap font-medium text-green-600">
                                        <?php echo number_format($booking['ticket_price'], 2); ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <?php
                                                $statusClasses = [
                                                    'Confirmed' => 'status-confirmed',
                                                    'Completed' => 'status-completed',
                                                    'Cancelled' => 'status-cancelled'
                                                ];
                                                $statusIcons = [
                                                    'Confirmed' => '<i class="fas fa-check-circle mr-1"></i>',
                                                    'Completed' => '<i class="fas fa-check-double mr-1"></i>',
                                                    'Cancelled' => '<i class="fas fa-times-circle mr-1"></i>'
                                                ];
                                                $statusClass = $statusClasses[$booking['booking_status']] ?? 'bg-gray-100 text-gray-800';
                                                $statusIcon = $statusIcons[$booking['booking_status']] ?? '<i class="fas fa-question-circle mr-1"></i>';
                                                ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                            <?php echo $statusIcon . htmlspecialchars($booking['booking_status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                                        <button onclick="viewBooking(<?php echo $booking['id']; ?>)"
                                            class="text-blue-600 hover:text-blue-900 mr-2 transition duration-150"
                                            title="View Booking">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                        <i class="fas fa-search-minus text-gray-400 text-3xl mb-2"></i>
                                        <p>No bookings
                                            found<?php echo !empty($searchQuery) ? ' matching "' . htmlspecialchars($searchQuery) . '"' : ''; ?>
                                        </p>
                                        <?php if (!empty($searchQuery)): ?>
                                        <a href="bookings.php"
                                            class="text-blue-600 hover:text-blue-800 inline-block mt-2">
                                            <i class="fas fa-times-circle mr-1"></i> Clear Search
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Booking Details Modal -->
                <div id="bookingDetailsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
                    <div class="relative flex items-center justify-center min-h-screen p-4">
                        <div
                            class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white modal-content">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900"><i
                                        class="fas fa-info-circle text-blue-600 mr-2"></i>Booking Details</h3>
                                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="loadingIndicator" class="py-8 flex justify-center items-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <span class="ml-2">Loading...</span>
                            </div>
                            <div id="bookingDetailContent" class="space-y-3 hidden"></div>
                            <div class="mt-4 flex justify-end">
                                <button type="button" onclick="closeModal()"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150">
                                    <i class="fas fa-times-circle mr-1"></i> Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function handleDateFilterChange(selectElement) {
        if (selectElement.value === 'select_date') {
            document.getElementById('selectDateModal').classList.remove('hidden');
        } else if (selectElement.value === 'today') {
            selectElement.form.submit();
        } else if (selectElement.value === '') {
            window.location.href = 'bookings.php';
        }
    }

    function closeSelectDateModal() {
        document.getElementById('selectDateModal').classList.add('hidden');
        document.querySelector('select[name="date_filter"]').value = '';
    }

    function applyDateRange() {
        const startDate = document.getElementById('startDateInput').value;
        const endDate = document.getElementById('endDateInput').value;

        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date must be before or equal to end date');
            return;
        }

        window.location.href =
            `bookings.php?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const body = document.body;

        sidebar.classList.toggle('active');
        backdrop.classList.toggle('active');
        body.classList.toggle('sidebar-open');
    }

    document.querySelectorAll('#sidebar a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                toggleSidebar();
            }
        });
    });

    function viewBooking(id) {
        document.getElementById('bookingDetailsModal').classList.remove('hidden');
        document.getElementById('loadingIndicator').classList.remove('hidden');
        document.getElementById('bookingDetailContent').classList.add('hidden');

        setTimeout(() => {
            const bookingRows = document.querySelectorAll('tbody tr');
            let bookingData = null;

            for (let row of bookingRows) {
                if (row.getAttribute('data-booking-id') == id) {
                    const cells = row.querySelectorAll('td');
                    const bookingId = cells[0].textContent.trim();
                    const seatNumber = cells[1].textContent.trim();
                    const passengerName = cells[2].textContent.trim();
                    let journeyDate = "N/A";
                    const journeyDateCell = row.querySelector('td:nth-child(4)');
                    if (journeyDateCell) {
                        journeyDate = journeyDateCell.textContent.trim();
                    }
                    let routeId = "N/A";
                    let route = "N/A";
                    const routeIdCell = row.querySelector('td:nth-child(5)');
                    const routeCell = row.querySelector('td:nth-child(6)');
                    if (routeIdCell) {
                        routeId = routeIdCell.textContent.trim();
                    }
                    if (routeCell) {
                        route = routeCell.textContent.trim();
                    }
                    const amount = cells[6].textContent.trim();
                    const statusElement = cells[7].querySelector('span');
                    const status = statusElement.textContent.trim();
                    const statusClass = statusElement.className;

                    bookingData = {
                        id: bookingId,
                        seat: seatNumber,
                        passenger: passengerName,
                        journeyDate: journeyDate,
                        routeId: routeId,
                        route: route,
                        amount: amount,
                        status: status,
                        statusClass: statusClass
                    };
                    break;
                }
            }

            document.getElementById('loadingIndicator').classList.add('hidden');

            if (bookingData) {
                const detailContent = document.getElementById('bookingDetailContent');
                detailContent.innerHTML = `
                        <div class="grid grid-cols-2 gap-3">
                            <div class="text-gray-600"><i class="fas fa-ticket-alt mr-1"></i> Booking ID:</div>
                            <div class="font-medium">${bookingData.id}</div>
                            <div class="text-gray-600"><i class="fas fa-user mr-1"></i> Passenger:</div>
                            <div class="font-medium">${bookingData.passenger}</div>
                            <div class="text-gray-600"><i class="fas fa-chair mr-1"></i> Seat Number:</div>
                            <div class="font-medium">${bookingData.seat}</div>
                            <div class="text-gray-600"><i class="fas fa-calendar-alt mr-1"></i> Journey Date:</div>
                            <div class="font-medium">${bookingData.journeyDate}</div>
                            <div class="text-gray-600"><i class="fas fa-route mr-1"></i> Route ID:</div>
                            <div class="font-medium">${bookingData.routeId}</div>
                            <div class="text-gray-600"><i class="fas fa-map-signs mr-1"></i> Route:</div>
                            <div class="font-medium">${bookingData.route}</div>
                            <div class="text-gray-600"><i class="fas fa-rupee-sign mr-1"></i> Amount:</div>
                            <div class="font-medium">${bookingData.amount}</div>
                            <div class="text-gray-600"><i class="fas fa-info-circle mr-1"></i> Status:</div>
                            <div class="font-medium"><span class="${bookingData.statusClass}">${bookingData.status}</span></div>
                        </div>
                    `;
                detailContent.classList.remove('hidden');
            } else {
                const detailContent = document.getElementById('bookingDetailContent');
                detailContent.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle text-red-500 text-3xl mb-2"></i>
                            <p>Booking information not found.</p>
                        </div>
                    `;
                detailContent.classList.remove('hidden');
            }
        }, 500);
    }

    function closeModal() {
        document.getElementById('bookingDetailsModal').classList.add('hidden');
    }
    </script>
</body>

</html>
<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

class Dashboard
{ 
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli("localhost", "root", "", "ticket");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getTotalBookings()
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM agency_bookings
            WHERE agency_email = ?
        ");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['total'];
    }

    public function getRecentBookings($limit = 5)
    {
        $sql = "SELECT 
                    ab.id,
                    ab.passenger_name,
                    ab.journey_date,
                    ab.ticket_price,
                    ab.booking_status,
                    r.from_location,
                    r.to_location
                FROM 
                    agency_bookings ab
                JOIN 
                    routes r ON ab.route_id = r.id
                WHERE 
                    ab.agency_email = ?
                ORDER BY 
                    ab.created_at DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $_SESSION['email'], $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalRoutes()
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM routes
            WHERE email = ?
        ");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['total'];
    }

    public function getTotalRevenue()
    {
        $stmt = $this->conn->prepare("
            SELECT SUM(ticket_price) as total
            FROM agency_bookings
            WHERE agency_email = ? AND booking_status = 'Confirmed'
        ");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['total'] ?: 0;
    }

    public function getTotalBuses()
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM buses
            WHERE email = ?
        ");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['total'];
    }

    public function getUpcomingTrips()
    {
        $sql = "SELECT 
                    r.id,
                    r.from_location,
                    r.to_location,
                    r.departure_date,
                    r.departure_time,
                    r.status,
                    (SELECT COUNT(*) FROM agency_bookings WHERE route_id = r.id AND booking_status = 'Confirmed') as booked_seats
                FROM 
                    routes r
                WHERE 
                    r.email = ? AND
                    r.departure_date >= CURDATE()
                ORDER BY 
                    r.departure_date ASC
                LIMIT 5";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

// Initialize Dashboard class
$dashboard = new Dashboard();
$totalBookings = $dashboard->getTotalBookings();
$recentBookings = $dashboard->getRecentBookings();
$totalRoutes = $dashboard->getTotalRoutes();
$totalRevenue = $dashboard->getTotalRevenue();
$totalBuses = $dashboard->getTotalBuses();
$upcomingTrips = $dashboard->getUpcomingTrips();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    @media (max-width: 768px) {
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="relative min-h-screen md:flex">
        <!-- Mobile menu button -->
        <div class="md:hidden fixed top-0 left-0 z-50 w-full bg-blue-700 flex items-center p-4">

            <div class="ml-4 text-white">
                <h1 class="font-bold"><?php echo ucfirst($_SESSION['username']); ?></h1>
                <p class="text-xs text-blue-200"><i class="fas fa-tachometer-alt w-5"></i> Dashboard</p>
            </div>
            <div class="ml-auto">
                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
            </div>
            <button id="mobile-menu-button" class="text-white focus:outline-none">
                <i class="fas fa-bars fa-lg"></i>
            </button>
        </div>

        <!-- Left Sidebar -->
        <div id="sidebar"
            class="mobile-menu fixed inset-y-0 left-0 z-40 w-64 bg-blue-700 text-white transform md:relative md:translate-x-0 transition duration-300 ease-in-out">
            <div class="p-6 md:pt-6 pt-16">

                <h1 class="text-2xl font-bold"><?php echo ucfirst($_SESSION['username']); ?></h1>
                <p class="text-sm text-blue-200"><i class="fas fa-tachometer-alt w-5"></i> Dashboard</p>
            </div>

            <nav class="mt-4">
                <a href="agency_home.php" class="flex items-center px-6 py-3 bg-blue-800">
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

                <a href="bookings.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
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

                <a href="index.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="ml-3">Sign Out</span>
                </a>
            </nav>
        </div>

        <!-- Overlay for mobile menu -->
        <div id="overlay" class="md:hidden fixed inset-0 z-30 bg-black opacity-50 hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto md:pt-0 pt-16">
            <!-- Top Navigation (Desktop only) -->
            <header class="hidden md:block bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold"><i class="fas fa-tachometer-alt w-5"></i> Dashboard</h1>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="p-4 md:p-6 mt-2">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                    <!-- Total Bookings -->
                    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                                <i class="fas fa-ticket-alt fa-lg md:fa-2x"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-xs md:text-sm"><i
                                        class="fas fa-ticket-alt fa-lg md:fa-2x"></i> Total Bookings</p>
                                <h3 class="font-bold text-xl md:text-2xl"><?php echo $totalBookings; ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Total Revenue -->
                    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-500">
                                <i class="fas fa-rupee-sign fa-lg md:fa-2x"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-xs md:text-sm">
                                    <i class="fas fa-rupee-sign fa-lg md:fa-2x"></i> Total Revenue
                                </p>
                                <h3 class="font-bold text-xl md:text-2xl">
                                    ₹<?php echo number_format($totalRevenue, 2); ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Total Routes -->
                    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                                <i class="fas fa-route fa-lg md:fa-2x"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-xs md:text-sm"><i class="fas fa-route fa-lg md:fa-2x"></i>
                                    Total Routes</p>
                                <h3 class="font-bold text-xl md:text-2xl"><?php echo $totalRoutes; ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Total Buses -->
                    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                                <i class="fas fa-bus fa-lg md:fa-2x"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-xs md:text-sm"><i class="fas fa-bus fa-lg md:fa-2x"></i>
                                    Total Buses</p>
                                <h3 class="font-bold text-xl md:text-2xl"><?php echo $totalBuses; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings and Upcoming Trips -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Bookings -->
                    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-base md:text-lg font-semibold"><i
                                    class="fas fa-ticket-alt fa-lg md:fa-2x"></i> Recent Bookings</h2>
                            <a href="bookings.php" class="text-blue-600 hover:text-blue-800 text-xs md:text-sm">View
                                All</a>
                        </div>

                        <div class="overflow-x-auto -mx-4 md:mx-0">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID</th>
                                        <th
                                            class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Passenger</th>
                                        <th
                                            class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Route</th>
                                        <th
                                            class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($recentBookings)): ?>
                                    <tr>
                                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">No recent bookings
                                            found</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm">#<?php echo $booking['id']; ?>
                                        </td>
                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm">
                                            <?php echo htmlspecialchars($booking['passenger_name']); ?></td>
                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm">
                                            <?php echo htmlspecialchars($booking['from_location'] . ' - ' . $booking['to_location']); ?>
                                            <div class="text-xs text-gray-500">
                                                <?php echo date('d M Y', strtotime($booking['journey_date'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-2 md:px-4 py-2">
                                            <?php
                                                    $statusClasses = [
                                                        'Confirmed' => 'bg-green-100 text-green-800',
                                                        'Cancelled' => 'bg-red-100 text-red-800',
                                                        'Completed' => 'bg-blue-100 text-blue-800'
                                                    ];
                                                    $statusClass = $statusClasses[$booking['booking_status']] ?? 'bg-gray-100 text-gray-800';
                                                    ?>
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($booking['booking_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Upcoming Trips -->
                    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-base md:text-lg font-semibold"><i class="fas fa-route fa-lg md:fa-2x"></i>
                                Upcoming Trips</h2>
                            <a href="routes.php" class="text-blue-600 hover:text-blue-800 text-xs md:text-sm">View
                                All</a>
                        </div>

                        <div class="overflow-x-auto -mx-4 md:mx-0">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Route</th>
                                        <th
                                            class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date & Time</th>
                                        <th
                                            class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Bookings</th>
                                        <th
                                            class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($upcomingTrips)): ?>
                                    <tr>
                                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">No upcoming trips
                                            found</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($upcomingTrips as $trip): ?>
                                    <tr>
                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm font-medium">
                                            <?php echo htmlspecialchars($trip['from_location'] . ' - ' . $trip['to_location']); ?>
                                        </td>
                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm">
                                            <?php echo date('d M Y', strtotime($trip['departure_date'])); ?>
                                            <div class="text-xs text-gray-500">
                                                <?php echo date('h:i A', strtotime($trip['departure_time'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm font-medium">
                                            <?php echo $trip['booked_seats']; ?> seats
                                        </td>
                                        <td class="px-2 md:px-4 py-2">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php echo $trip['status'] == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $trip['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="mt-6 bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h2 class="text-base md:text-lg font-semibold mb-4"><i class="fas fa-bolt mr-2"></i> Quick Actions
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <a href="routes.php"
                            class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition duration-200">
                            <div class="p-2 md:p-3 rounded-full bg-blue-100 text-blue-500">
                                <i class="fas fa-route"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-medium text-sm md:text-base"><i class="fas fa-plus mr-2"></i> Add New
                                    Route</h3>
                                <p class="text-xs md:text-sm text-gray-600"><i class="fas fa-route mr-1"></i> Create a
                                    new journey route</p>
                            </div>
                        </a>

                        <a href="managebus.php"
                            class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition duration-200">
                            <div class="p-2 md:p-3 rounded-full bg-green-100 text-green-500">
                                <i class="fas fa-bus"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-medium text-sm md:text-base"><i class="fas fa-plus mr-2"></i> Add New
                                    Bus</h3>
                                <p class="text-xs md:text-sm text-gray-600"><i class="fas fa-bus mr-1"></i> Register a
                                    new bus in your fleet</p>
                            </div>
                        </a>

                        <a href="x_bookings.php"
                            class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition duration-200">
                            <div class="p-2 md:p-3 rounded-full bg-purple-100 text-purple-500">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-medium text-sm md:text-base"><i class="fas fa-eye mr-2"></i> Check New
                                    Bookings</h3>
                                <p class="text-xs md:text-sm text-gray-600"><i class="fas fa-clipboard-list mr-1"></i>
                                    Review and manage your new bookings</p>
                            </div>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript for mobile menu toggle -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const overlay = document.getElementById('overlay');

        function toggleMenu() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }

        mobileMenuButton.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);

        // Close menu when clicking on a menu item (for mobile)
        const menuItems = sidebar.querySelectorAll('a');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    toggleMenu();
                }
            });
        });

        // Adjust for window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('active');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });
    </script>
</body>

</html>
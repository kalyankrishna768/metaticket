<?php
include 'config.php';
session_start();
$username = $_SESSION['username'] ?? 'default_user';

class Routes
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli("localhost", "root", "", "ticket");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getAllRoutes($search = "", $filter_date = "")
    {
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        $sql = "SELECT id, from_location, from_points, to_location, to_points, 
                   departure_date, departure_time, distance, duration, base_fare, status, bus_id 
                FROM routes 
                WHERE email = ?";
        
        if (!empty($filter_date)) {
            if ($filter_date === 'today') {
                $sql .= " AND departure_date = CURDATE()";
            } else {
                $sql .= " AND departure_date = ?";
            }
        }
        
        if (!empty($search)) {
            $search = $this->conn->real_escape_string($search);
            $sql .= " AND (from_location LIKE '%$search%' OR to_location LIKE '%$search%')";
        }
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($filter_date) && $filter_date !== 'today') {
            $stmt->bind_param("ss", $email, $filter_date);
        } else {
            $stmt->bind_param("s", $email);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getRouteById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM routes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addRoute($from, $from_points, $to, $to_points, $departure_date, $departure_time, $distance, $duration, $fare, $status, $bus_id, $username, $email)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO routes (from_location, from_points, to_location, to_points, 
                           departure_date, departure_time, distance, duration, base_fare, status, bus_id, username, email, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );
        $from_points_json = json_encode($from_points);
        $to_points_json = json_encode($to_points);
        $stmt->bind_param("ssssssdddssss", $from, $from_points_json, $to, $to_points_json, $departure_date, $departure_time, $distance, $duration, $fare, $status, $bus_id, $username, $email);
        return $stmt->execute();
    }

    public function updateRoute($id, $from, $from_points, $to, $to_points, $departure_date, $departure_time, $distance, $duration, $fare, $status, $bus_id)
    {
        $stmt = $this->conn->prepare(
            "UPDATE routes 
             SET from_location=?, from_points=?, to_location=?, to_points=?, 
                 departure_date=?, departure_time=?, distance=?, duration=?, 
                 base_fare=?, status=?, bus_id=?, updated_at=NOW(),
                 username=?, email=? 
             WHERE id=?"
        );
        $from_points_json = json_encode($from_points);
        $to_points_json = json_encode($to_points);
        $username = $_SESSION['username'] ?? 'default_user';
        $email = $_SESSION['email'] ?? 'default@example.com';
        $stmt->bind_param("ssssssdddsissi", $from, $from_points_json, $to, $to_points_json, $departure_date, $departure_time, $distance, $duration, $fare, $status, $bus_id, $username, $email, $id);
        return $stmt->execute();
    }

    public function deleteRoute($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM routes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$routes = new Routes();
$searchQuery = $_GET['search'] ?? "";
$filterDate = $_GET['filter_date'] ?? "";
$allRoutes = $routes->getAllRoutes($searchQuery, $filterDate);

// Handle AJAX request for getting route data
if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    $route = $routes->getRouteById($_GET['id']);
    header('Content-Type: application/json');
    echo json_encode($route);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case "add":
            $from_points = [];
            if (isset($_POST["from_point_name"])) {
                foreach ($_POST["from_point_name"] as $key => $name) {
                    if (!empty($name) && !empty($_POST["from_point_time"][$key])) {
                        $from_points[] = ["name" => $name, "time" => $_POST["from_point_time"][$key]];
                    }
                }
            }
            $to_points = [];
            if (isset($_POST["to_point_name"])) {
                foreach ($_POST["to_point_name"] as $key => $name) {
                    if (!empty($name) && !empty($_POST["to_point_time"][$key])) {
                        $to_points[] = ["name" => $name, "time" => $_POST["to_point_time"][$key]];
                    }
                }
            }
            $username = $_SESSION['username'] ?? 'default_user';
            $email = $_SESSION['email'] ?? 'default@example.com';
            $routes->addRoute(
                $_POST["from_location"],
                $from_points,
                $_POST["to_location"],
                $to_points,
                $_POST["departure_date"],
                $_POST["departure_time"],
                floatval($_POST["distance"]),
                floatval($_POST["duration"]),
                floatval($_POST["base_fare"]),
                $_POST["status"],
                intval($_POST["bus_id"]),
                $username,
                $email
            );
            break;

        case "update":
            $from_points = [];
            if (isset($_POST["from_point_name"])) {
                foreach ($_POST["from_point_name"] as $key => $name) {
                    if (!empty($name) && !empty($_POST["from_point_time"][$key])) {
                        $from_points[] = ["name" => $name, "time" => $_POST["from_point_time"][$key]];
                    }
                }
            }
            $to_points = [];
            if (isset($_POST["to_point_name"])) {
                foreach ($_POST["to_point_name"] as $key => $name) {
                    if (!empty($name) && !empty($_POST["to_point_time"][$key])) {
                        $to_points[] = ["name" => $name, "time" => $_POST["to_point_time"][$key]];
                    }
                }
            }
            $routes->updateRoute(
                $_POST["id"],
                $_POST["from_location"],
                $from_points,
                $_POST["to_location"],
                $to_points,
                $_POST["departure_date"],
                $_POST["departure_time"],
                floatval($_POST["distance"]),
                floatval($_POST["duration"]),
                floatval($_POST["base_fare"]),
                $_POST["status"],
                intval($_POST["bus_id"])
            );
            break;

        case "delete":
            $routes->deleteRoute($_POST["id"]);
            break;
    }
    header("Location: routes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Home - Routes</title>
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

    .search-container {
        position: relative;
        transition: all 0.3s ease-in-out;
    }

    .search-container input {
        transition: all 0.3s ease-in-out;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .search-container input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .search-container button {
        transition: all 0.3s ease-in-out;
    }

    .search-container button:hover {
        background-color: #2563eb;
    }
    </style>
</head>

<body class="bg-gray-50">
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 sidebar-backdrop md:hidden"
        onclick="toggleSidebar()"></div>

    <div class="flex flex-col md:flex-row min-h-screen">
        <div class="md:hidden bg-blue-700 text-white p-4 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-2xl font-bold"><?php echo ucfirst($username); ?></h1>
                <p class="text-sm text-blue-200"><i class="fas fa-route text-blue-600 text-xl mr-2"></i> Routes</p>
            </div>
            <button id="navToggle" class="text-white focus:outline-none" onclick="toggleSidebar()">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <div id="sidebar" class="w-64 bg-blue-700 text-white md:block">
            <div class="p-6">
                <h1 class="text-2xl font-bold"><?php echo ucfirst($username); ?></h1>
                <p class="text-sm text-blue-200"><i class="fas fa-route text-blue-600 text-xl mr-2"></i> Routes</p>
            </div>
            <nav class="mt-4 flex-grow">
                <a href="agency_home.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-tachometer-alt w-5"></i><span class="ml-3">Dashboard</span>
                </a>
                <a href="managebus.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-bus w-5"></i><span class="ml-3">Manage Buses</span>
                </a>
                <a href="routes.php" class="flex items-center px-6 py-3 bg-blue-800">
                    <i class="fas fa-route w-5"></i><span class="ml-3">Routes</span>
                </a>
                <a href="bookings.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-ticket-alt w-5"></i><span class="ml-3">Bookings</span>
                </a>
                <a href="x_bookings.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-clipboard-list w-5"></i><span class="ml-3">New Bookings</span>
                </a>
                <a href="agency_wallet.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-wallet w-5"></i><span class="ml-3">Wallet</span>
                </a>
            </nav>
            <div class="md:hidden p-4 border-t border-blue-800">
                <button onclick="toggleSidebar()" class="flex items-center text-blue-100 hover:text-white">
                    <i class="fas fa-arrow-left mr-2"></i> Close Menu
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-x-hidden overflow-y-auto main-content">
            <header class="bg-white shadow-sm hidden md:block">
                <div class="flex items-center justify-between px-4 md:px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-route text-blue-600 text-xl mr-2"></i>
                        <h1 class="text-xl font-semibold">Routes Management</h1>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2"><?php echo htmlspecialchars($username); ?></span>
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                            <?php echo strtoupper(substr($username, 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 md:p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="search-container w-full md:w-80">
                        <form method="GET" class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-500"></i>
                            </span>
                            <input type="text" name="search" placeholder="Search routes..."
                                value="<?php echo htmlspecialchars($searchQuery); ?>"
                                class="w-full pl-10 pr-12 py-2 border rounded-lg focus:outline-none focus:ring-2 bg-white">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 bg-blue-600 text-white rounded-r-lg px-3">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <button onclick="document.getElementById('addRouteModal').classList.remove('hidden')"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center w-full md:w-auto justify-center">
                        <i class="fas fa-plus mr-2"></i>Add New Route
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                        <p class="text-sm text-gray-500 flex items-center justify-center">
                            <i class="fas fa-map-marked-alt text-blue-500 mr-2"></i>Total Routes
                        </p>
                        <p class="text-lg font-bold text-blue-600"><?php echo count($allRoutes); ?></p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                        <p class="text-sm text-gray-500 flex items-center justify-center">
                            <i class="fas fa-filter text-purple-500 mr-2"></i>Filter Routes
                        </p>
                        <div class="flex justify-center gap-2 mt-1">
                            <button onclick="applyFilter('today')"
                                class="px-2 py-1 text-sm <?php echo $filterDate === 'today' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'; ?> rounded hover:bg-purple-500 hover:text-white">
                                Today
                            </button>
                            <button onclick="document.getElementById('customDateModal').classList.remove('hidden')"
                                class="px-2 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-purple-500 hover:text-white">
                                Custom Date
                            </button>
                            <button
                                class="px-2 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-purple-500 hover:text-white">
                                <a href="routes.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                                    <i class="fas fa-times-circle mr-1"></i> Clear Filters
                                </a>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-700 flex items-center">
                            <i class="fas fa-list-alt text-blue-500 mr-2"></i>Routes List
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-hashtag text-gray-400 mr-1"></i> Route ID
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-exchange-alt text-gray-400 mr-1"></i> From - To
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-bus-alt text-gray-400 mr-1"></i> Bus ID
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-clock text-gray-400 mr-1"></i> Departure
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-road text-gray-400 mr-1"></i> Distance
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-hourglass-half text-gray-400 mr-1"></i> Duration
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-rupee-sign text-gray-400 mr-1"></i> Fare
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-toggle-on text-gray-400 mr-1"></i> Status
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-cog text-gray-400 mr-1"></i> Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($allRoutes)): ?>
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-info-circle mr-2"></i>No routes found.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($allRoutes as $route): ?>

                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        #<?php echo htmlspecialchars($route['id']); ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 flex items-center">
                                            <?php echo htmlspecialchars($route['from_location']); ?>
                                            <i class="fas fa-long-arrow-alt-right mx-1 text-gray-400"></i>
                                            <?php echo htmlspecialchars($route['to_location']); ?>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-4 md:px-6 py-4 whitespace-nowrap">
                                        #<?php echo htmlspecialchars($route['bus_id']); ?>
                                    </td>
                                    <td class="hidden md:table-cell px-4 md:px-6 py-4 whitespace-nowrap">
                                        <?php echo date('M d, Y h:i A', strtotime($route['departure_date'] . ' ' . $route['departure_time'])); ?>
                                    </td>
                                    <td class="hidden md:table-cell px-4 md:px-6 py-4 whitespace-nowrap">
                                        <?php echo htmlspecialchars($route['distance']); ?> km
                                    </td>
                                    <td class="hidden md:table-cell px-4 md:px-6 py-4 whitespace-nowrap">
                                        <?php echo htmlspecialchars($route['duration']); ?> hrs
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap font-medium text-green-600">
                                        <?php echo number_format($route['base_fare'], 2); ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php echo $route['status'] == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <i
                                                class="fas <?php echo $route['status'] == 'Active' ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                                            <?php echo htmlspecialchars($route['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                                        <button onclick="editRoute(<?php echo $route['id']; ?>)"
                                            class="text-blue-600 hover:text-blue-900 mr-2" title="Edit Route">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteRoute(<?php echo $route['id']; ?>)"
                                            class="text-red-600 hover:text-red-900" title="Delete Route">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button onclick="showRouteDetails(<?php echo $route['id']; ?>)"
                                            class="md:hidden text-gray-600 hover:text-gray-900 ml-2"
                                            title="View Details">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="routeDetailsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-black bg-opacity-50"
                        onclick="document.getElementById('routeDetailsModal').classList.add('hidden')"></div>
                    <div class="relative flex items-center justify-center min-h-screen p-4">
                        <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>Route Details
                                </h3>
                                <button type="button"
                                    onclick="document.getElementById('routeDetailsModal').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="routeDetailContent" class="space-y-3"></div>
                            <div class="mt-4 flex justify-end">
                                <button type="button"
                                    onclick="document.getElementById('routeDetailsModal').classList.add('hidden')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center">
                                    <i class="fas fa-times-circle mr-1"></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Date Filter Modal -->
                <div id="customDateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
                    <div class="relative min-h-screen flex items-center justify-center p-4">
                        <div class="relative bg-white rounded-lg max-w-sm w-full p-6 mx-auto">
                            <button onclick="document.getElementById('customDateModal').classList.add('hidden')"
                                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                            <h3 class="text-lg font-bold mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>Select Date
                            </h3>
                            <form id="customDateForm" method="GET" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 flex items-center">
                                        <i class="far fa-calendar-alt text-purple-500 mr-1"></i>Choose Date
                                    </label>
                                    <input type="date" name="filter_date" id="custom_filter_date" required
                                        class="w-full p-2 border rounded-md">
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <button type="button"
                                        onclick="document.getElementById('customDateModal').classList.add('hidden')"
                                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 flex items-center">
                                        <i class="fas fa-ban mr-1"></i>Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center">
                                        <i class="fas fa-check mr-1"></i>Apply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="addRouteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-lg max-w-lg w-full p-6 mx-auto max-h-[90vh] overflow-y-auto">
                <button onclick="document.getElementById('addRouteModal').classList.add('hidden')"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
                <h3 id="modalTitle" class="text-lg font-bold mb-4 flex items-center">
                    <i class="fas fa-route text-blue-500 mr-2"></i>Add New Route
                </h3>
                <form action="routes.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add" id="form_action">
                    <input type="hidden" name="id" id="edit_route_id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>From
                            </label>
                            <input type="text" name="from_location" id="from_location" required
                                class="w-full p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-map-marker-alt text-green-500 mr-1"></i>To
                            </label>
                            <input type="text" name="to_location" id="to_location" required
                                class="w-full p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="far fa-calendar-alt text-blue-500 mr-1"></i>Departure Date
                            </label>
                            <input type="date" name="departure_date" id="departure_date" required
                                class="w-full p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="far fa-clock text-blue-500 mr-1"></i>Departure Time
                            </label>
                            <input type="time" name="departure_time" id="departure_time" required
                                class="w-full p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-road text-blue-500 mr-1"></i>Distance (km)
                            </label>
                            <input type="number" step="0.1" name="distance" id="distance" required
                                class="w-full p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-hourglass-half text-blue-500 mr-1"></i>Duration (hrs)
                            </label>
                            <input type="number" step="0.1" name="duration" id="duration" required
                                class="w-full p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-rupee-sign text-green-500 mr-1"></i>Base Fare (₹)
                            </label>
                            <input type="number" step="1" name="base_fare" id="base_fare" required
                                class="w-full p-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-toggle-on text-blue-500 mr-1"></i>Status
                            </label>
                            <select name="status" id="status" required class="w-full p-2 border rounded-md">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-bus text-blue-500 mr-1"></i>Bus ID
                            </label>
                            <input type="number" name="bus_id" id="bus_id" required
                                class="w-full p-2 border rounded-md">
                        </div>
                    </div>

                    <!-- Boarding Points (from_points) Section -->
                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-map-pin text-orange-500 mr-1"></i>Boarding Points
                            </label>
                            <button type="button" onclick="addBoardingPoint()"
                                class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                <i class="fas fa-plus-circle mr-1"></i>Add Point
                            </button>
                        </div>
                        <div id="boarding-points-container" class="space-y-2">
                            <div class="boarding-point grid grid-cols-2 gap-2">
                                <input type="text" name="from_point_name[]" placeholder="Point Name"
                                    class="p-2 border rounded-md" required>
                                <input type="time" name="from_point_time[]" class="p-2 border rounded-md" required>
                            </div>
                        </div>
                    </div>

                    <!-- Dropping Points (to_points) Section -->
                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-map-pin text-green-500 mr-1"></i>Dropping Points
                            </label>
                            <button type="button" onclick="addDroppingPoint()"
                                class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                <i class="fas fa-plus-circle mr-1"></i>Add Point
                            </button>
                        </div>
                        <div id="dropping-points-container" class="space-y-2">
                            <div class="dropping-point grid grid-cols-2 gap-2">
                                <input type="text" name="to_point_name[]" placeholder="Point Name"
                                    class="p-2 border rounded-md" required>
                                <input type="time" name="to_point_time[]" class="p-2 border rounded-md" required>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" onclick="document.getElementById('addRouteModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 flex items-center">
                            <i class="fas fa-ban mr-1"></i>Cancel
                        </button>
                        <button type="submit" id="submitBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                            <i class="fas fa-plus-circle mr-1"></i>Add Route
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="deleteRouteForm" action="routes.php" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_route_id">
    </form>

    <script>
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

    function deleteRoute(routeId) {
        if (confirm('Are you sure you want to delete this route?')) {
            document.getElementById('delete_route_id').value = routeId;
            document.getElementById('deleteRouteForm').submit();
        }
    }

    function applyFilter(filter) {
        window.location.href = `routes.php?filter_date=${filter}&search=<?php echo urlencode($searchQuery); ?>`;
    }

    function editRoute(routeId) {
        fetch(`routes.php?action=get&id=${routeId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data) {
                    document.getElementById('modalTitle').innerHTML =
                        '<i class="fas fa-edit text-blue-500 mr-2"></i>Edit Route';
                    document.getElementById('form_action').value = 'update';
                    document.getElementById('edit_route_id').value = data.id;
                    document.getElementById('from_location').value = data.from_location;
                    document.getElementById('to_location').value = data.to_location;
                    document.getElementById('departure_date').value = data.departure_date;
                    document.getElementById('departure_time').value = data.departure_time;
                    document.getElementById('distance').value = data.distance;
                    document.getElementById('duration').value = data.duration;
                    document.getElementById('base_fare').value = data.base_fare;
                    document.getElementById('status').value = data.status;
                    document.getElementById('bus_id').value = data.bus_id;

                    // Clear existing points
                    document.getElementById('boarding-points-container').innerHTML = '';
                    document.getElementById('dropping-points-container').innerHTML = '';

                    // Add from_points from JSON data
                    try {
                        const fromPoints = JSON.parse(data.from_points);
                        if (Array.isArray(fromPoints) && fromPoints.length > 0) {
                            fromPoints.forEach(point => {
                                const newPoint = document.createElement('div');
                                newPoint.className = 'boarding-point grid grid-cols-2 gap-2 relative';
                                newPoint.innerHTML = `
                                        <input type="text" name="from_point_name[]" value="${point.name}" placeholder="Point Name" class="p-2 border rounded-md" required>
                                        <div class="flex">
                                            <input type="time" name="from_point_time[]" value="${point.time}" class="p-2 border rounded-md flex-grow" required>
                                            <button type="button" onclick="removePoint(this)" class="ml-2 text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    `;
                                document.getElementById('boarding-points-container').appendChild(newPoint);
                            });
                        } else {
                            addBoardingPoint();
                        }
                    } catch (e) {
                        console.error('Error parsing from_points:', e);
                        addBoardingPoint();
                    }

                    // Add to_points from JSON data
                    try {
                        const toPoints = JSON.parse(data.to_points);
                        if (Array.isArray(toPoints) && toPoints.length > 0) {
                            toPoints.forEach(point => {
                                const newPoint = document.createElement('div');
                                newPoint.className = 'dropping-point grid grid-cols-2 gap-2 relative';
                                newPoint.innerHTML = `
                                        <input type="text" name="to_point_name[]" value="${point.name}" placeholder="Point Name" class="p-2 border rounded-md" required>
                                        <div class="flex">
                                            <input type="time" name="to_point_time[]" value="${point.time}" class="p-2 border rounded-md flex-grow" required>
                                            <button type="button" onclick="removePoint(this)" class="ml-2 text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    `;
                                document.getElementById('dropping-points-container').appendChild(newPoint);
                            });
                        } else {
                            addDroppingPoint();
                        }
                    } catch (e) {
                        console.error('Error parsing to_points:', e);
                        addDroppingPoint();
                    }

                    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save mr-1"></i>Update Route';
                    document.getElementById('addRouteModal').classList.remove('hidden');
                } else {
                    alert('Route not found');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching route data');
            });
    }

    function showRouteDetails(routeId) {
        fetch(`routes.php?action=get&id=${routeId}`)
            .then(response => response.json())
            .then(data => {
                const detailContent = document.getElementById('routeDetailContent');

                let fromPointsHTML = '<div class="col-span-2 my-2"><hr></div>';
                fromPointsHTML +=
                    '<div class="col-span-2 font-medium text-blue-600 flex items-center mb-1"><i class="fas fa-map-pin text-orange-500 mr-1"></i>Boarding Points:</div>';

                try {
                    const fromPoints = JSON.parse(data.from_points);
                    if (Array.isArray(fromPoints) && fromPoints.length > 0) {
                        fromPoints.forEach(point => {
                            fromPointsHTML += `
                                    <div class="text-gray-600 pl-4">• ${point.name}</div>
                                    <div class="font-medium">${point.time}</div>
                                `;
                        });
                    } else {
                        fromPointsHTML +=
                            '<div class="col-span-2 text-gray-500 pl-4">No boarding points specified</div>';
                    }
                } catch (e) {
                    fromPointsHTML +=
                        '<div class="col-span-2 text-gray-500 pl-4">No boarding points specified</div>';
                }

                let toPointsHTML = '<div class="col-span-2 my-2"><hr></div>';
                toPointsHTML +=
                    '<div class="col-span-2 font-medium text-blue-600 flex items-center mb-1"><i class="fas fa-map-pin text-green-500 mr-1"></i>Dropping Points:</div>';

                try {
                    const toPoints = JSON.parse(data.to_points);
                    if (Array.isArray(toPoints) && toPoints.length > 0) {
                        toPoints.forEach(point => {
                            toPointsHTML += `
                                    <div class="text-gray-600 pl-4">• ${point.name}</div>
                                    <div class="font-medium">${point.time}</div>
                                `;
                        });
                    } else {
                        toPointsHTML +=
                            '<div class="col-span-2 text-gray-500 pl-4">No dropping points specified</div>';
                    }
                } catch (e) {
                    toPointsHTML += '<div class="col-span-2 text-gray-500 pl-4">No dropping points specified</div>';
                }

                detailContent.innerHTML = `
                        <div class="grid grid-cols-2 gap-2">
                            <div class="text-gray-600 flex items-center"><i class="fas fa-hashtag text-blue-500 mr-1"></i>Route ID:</div>
                            <div class="font-medium">#${data.id}</div>
                            <div class="text-gray-600 flex items-center"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i>From:</div>
                            <div class="font-medium">${data.from_location}</div>
                            <div class="text-gray-600 flex items-center"><i class="fas fa-map-marker-alt text-green-500 mr-1"></i>To:</div>
                            <div class="font-medium">${data.to_location}</div>
                            <div class="text-gray-600 flex items-center"><i class="fas fa-bus text-blue-500 mr-1"></i>Bus ID:</div>
                            <div class="font-medium">#${data.bus_id}</div>
                            <div class="text-gray-600 flex items-center"><i class="far fa-calendar-alt text-purple-500 mr-1"></i>Departure:</div>
                            <div class="font-medium">${new Date(data.departure_date + ' ' + data.departure_time).toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</div>
                            <div class="text-gray-600 flex items-center"><i class="fas fa-road text-blue-500 mr-1"></i>Distance:</div>
                            <div class="font-medium">${data.distance} km</div>
                            <div class="text-gray-600 flex items-center"><i class="far fa-clock text-orange-500 mr-1"></i>Duration:</div>
                            <div class="font-medium">${data.duration} hrs</div>
                            <div class="text-gray-600 flex items-center"><i class="fas fa-rupee-sign text-green-500 mr-1"></i>Fare:</div>
                            <div class="font-medium">₹${Number(data.base_fare).toFixed(2)}</div>
                            <div class="text-gray-600 flex items-center"><i class="fas fa-toggle-on text-blue-500 mr-1"></i>Status:</div>
                            <div class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                ${data.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    <i class="fas ${data.status === 'Active' ? 'fa-check-circle' : 'fa-times-circle'} mr-1"></i>
                                    ${data.status}
                                </span>
                            </div>
                            ${fromPointsHTML}
                            ${toPointsHTML}
                        </div>
                        <div class="flex justify-between mt-4">
                            <button onclick="editRoute(${routeId})" class="text-blue-600 hover:text-blue-900 flex items-center">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <button onclick="deleteRoute(${routeId})" class="text-red-600 hover:text-red-900 flex items-center">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </div>
                    `;
                document.getElementById('routeDetailsModal').classList.remove('hidden');
            })
            .catch(error => console.error('Error:', error));
    }

    function initSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth >= 768) {
            sidebar.classList.add('active');
        } else {
            sidebar.classList.remove('active');
        }
    }

    function addBoardingPoint() {
        const container = document.getElementById('boarding-points-container');
        const newPoint = document.createElement('div');
        newPoint.className = 'boarding-point grid grid-cols-2 gap-2 relative';
        newPoint.innerHTML = `
                <input type="text" name="from_point_name[]" placeholder="Point Name" class="p-2 border rounded-md" required>
                <div class="flex">
                    <input type="time" name="from_point_time[]" class="p-2 border rounded-md flex-grow" required>
                    <button type="button" onclick="removePoint(this)" class="ml-2 text-red-500 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        container.appendChild(newPoint);
    }

    function addDroppingPoint() {
        const container = document.getElementById('dropping-points-container');
        const newPoint = document.createElement('div');
        newPoint.className = 'dropping-point grid grid-cols-2 gap-2 relative';
        newPoint.innerHTML = `
                <input type="text" name="to_point_name[]" placeholder="Point Name" class="p-2 border rounded-md" required>
                <div class="flex">
                    <input type="time" name="to_point_time[]" class="p-2 border rounded-md flex-grow" required>
                    <button type="button" onclick="removePoint(this)" class="ml-2 text-red-500 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        container.appendChild(newPoint);
    }

    function removePoint(button) {
        const pointDiv = button.closest('.boarding-point, .dropping-point');
        pointDiv.remove();
    }

    document.addEventListener('DOMContentLoaded', function() {
        initSidebar();

        const boardingPointsContainer = document.getElementById('boarding-points-container');
        const droppingPointsContainer = document.getElementById('dropping-points-container');

        if (boardingPointsContainer && boardingPointsContainer.children.length === 0) {
            addBoardingPoint();
        }

        if (droppingPointsContainer && droppingPointsContainer.children.length === 0) {
            addDroppingPoint();
        }
    });

    document.addEventListener('DOMContentLoaded', initSidebar);
    window.addEventListener('resize', initSidebar);
    </script>
</body>

</html>
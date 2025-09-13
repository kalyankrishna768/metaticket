<?php
include 'config.php';
session_start();

// BusManagement class definition
class BusManagement
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli("localhost", "root", "", "ticket");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getAllBuses($search = '')
    {
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

        if (!empty($search)) {
            $search = "%$search%";
            $sql = "SELECT * FROM buses WHERE email = ? AND (bus_number LIKE ? OR type LIKE ? OR status LIKE ?) ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssss", $email, $search, $search, $search);
        } else {
            $sql = "SELECT * FROM buses WHERE email = ? ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $email);
        }

        $stmt->execute();
        return $stmt->get_result();
    }

    public function getBusById($id)
    {
        $sql = "SELECT * FROM buses WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateBus($id, $bus_number, $capacity, $type, $status, $username, $email)
    {
        $sql = "UPDATE buses SET bus_number=?, capacity=?, type=?, status=?, username=?, email=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sissssi", $bus_number, $capacity, $type, $status, $username, $email, $id);
        return $stmt->execute();
    }

    // Add deleteBus method
    public function deleteBus($id)
    {
        // Ensure the bus belongs to the current user
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        $sql = "DELETE FROM buses WHERE id=? AND email=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $id, $email);
        return $stmt->execute();
    }

    // Add addBus method
    public function addBus($bus_number, $capacity, $type, $status, $username, $email)
    {
        $sql = "INSERT INTO buses (bus_number, capacity, type, status, username, email) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sissss", $bus_number, $capacity, $type, $status, $username, $email);
        return $stmt->execute();
    }
}

$busManagement = new BusManagement();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'default_user';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'default@example.com';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Handle AJAX request for getting bus data
if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    $bus = $busManagement->getBusById($_GET['id']);
    header('Content-Type: application/json');
    echo json_encode($bus);
    exit;
}

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        // Handle update
        if ($_POST['action'] === 'update') {
            $busManagement->updateBus(
                $_POST['id'],
                $_POST['bus_number'],
                $_POST['capacity'],
                $_POST['type'],
                $_POST['status'],
                $username,
                $email
            );
            // Redirect to refresh the page
            header("Location: managebus.php");
            exit;
        }

        // Handle delete
        if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $busManagement->deleteBus($_POST['id']);
            // Redirect to refresh the page
            header("Location: managebus.php");
            exit;
        }

        // Handle add
        if ($_POST['action'] === 'add') {
            $busManagement->addBus(
                $_POST['bus_number'],
                $_POST['capacity'],
                $_POST['type'],
                $_POST['status'],
                $username,
                $email
            );
            // Redirect to refresh the page
            header("Location: managebus.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Home - Manage Bus</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    /* Sidebar transition for mobile */
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
    </style>
</head>

<body class="bg-gray-50">
    <!-- Sidebar Backdrop (Mobile only) -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 sidebar-backdrop md:hidden"
        onclick="toggleSidebar()"></div>

    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Mobile Header -->
        <div class="md:hidden bg-blue-700 text-white p-4 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-2xl font-bold"><?php echo ucfirst($_SESSION['username']); ?></h1>
                <p class="text-sm text-blue-200"><i class="fas fa-bus mr-2"></i> Manage Bus</p>
            </div>
            <button id="navToggle" class="text-white focus:outline-none" onclick="toggleSidebar()">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Sidebar - Will slide in from left on mobile -->
        <div id="sidebar" class="w-64 bg-blue-700 text-white md:block">
            <div class="p-6">
                <h1 class="text-2xl font-bold"><?php echo ucfirst($_SESSION['username']); ?></h1>
                <p class="text-sm text-blue-200"><i class="fas fa-bus mr-2"></i> Manage Bus</p>
            </div>

            <nav class="mt-4 flex-grow">
                <a href="agency_home.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>

                <a href="managebus.php" class="flex items-center px-6 py-3 bg-blue-800">
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
                    <i class="fas fa-wallet mr-3"></i>
                    <span class="ml-3">Wallet</span>
                </a>
            </nav>

            <!-- Mobile Only Close Button -->
            <div class="md:hidden p-4 border-t border-blue-800">
                <button onclick="toggleSidebar()" class="flex items-center text-blue-100 hover:text-white">
                    <i class="fas fa-arrow-left mr-2"></i> Close Menu
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto main-content">
            <!-- Top Navigation Bar (desktop only) -->
            <header class="bg-white shadow-sm hidden md:block">
                <div class="flex items-center justify-between px-4 md:px-6 py-4">
                    <div class="flex items-center flex-grow">
                        <form method="GET" action="" class="w-full md:w-64 mx-2 md:mx-4">
                            <div class="relative w-full">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-search text-gray-500"></i>
                                </span>
                                <input type="text" name="search" placeholder="Search buses..."
                                    value="<?php echo htmlspecialchars($searchQuery); ?>"
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2">
                                <button type="submit"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 bg-blue-600 text-white rounded-r-lg px-3">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Mobile Search Bar -->
            <div class="md:hidden p-4 bg-white shadow-sm">
                <form method="GET" action="">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-500"></i>
                        </span>
                        <input type="text" name="search" placeholder="Search buses..."
                            value="<?php echo htmlspecialchars($searchQuery); ?>"
                            class="w-full pl-10 pr-10 py-2 border rounded-lg focus:outline-none focus:ring-2">
                        <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-arrow-right text-blue-500"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bus Management Content -->
            <main class="p-4 md:p-6">
                <!-- Add New Bus Button -->
                <div class="mb-6">
                    <button onclick="document.getElementById('addBusModal').classList.remove('hidden')"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 w-full md:w-auto">
                        <i class="fas fa-plus mr-2"></i>Add New Bus
                    </button>
                </div>

                <!-- Search Results Indicator -->
                <?php if (!empty($searchQuery)): ?>
                <div class="mb-4 flex justify-between items-center">
                    <div class="text-gray-600">
                        <i class="fas fa-search mr-2"></i> Search results for: <span
                            class="font-semibold"><?php echo htmlspecialchars($searchQuery); ?></span>
                    </div>
                    <a href="managebus.php" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times mr-1"></i> Clear search
                    </a>
                </div>
                <?php endif; ?>

                <!-- Bus List Table - Responsive Design -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-bus mr-2"></i> Bus Number
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-users mr-2"></i> Capacity
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-info-circle mr-2"></i> Type
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-signal mr-2"></i> Status
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-tools mr-1"></i> Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                if (!isset($_SESSION['email'])) {
                                    echo "<tr><td colspan='5' class='px-4 md:px-6 py-4 text-center'>Please log in to view your buses</td></tr>";
                                } else {
                                    $buses = $busManagement->getAllBuses($searchQuery);
                                    if ($buses->num_rows > 0) {
                                        while ($bus = $buses->fetch_assoc()) {
                                            echo "<tr class='hover:bg-gray-50'>
                                                <td class='px-4 md:px-6 py-4 text-sm'>{$bus['bus_number']}</td>
                                                <td class='px-4 md:px-6 py-4 text-sm'>{$bus['capacity']}</td>
                                                <td class='hidden md:table-cell px-4 md:px-6 py-4 text-sm'>{$bus['type']}</td>
                                                <td class='px-4 md:px-6 py-4 text-sm'>
                                                    <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full " .
                                                ($bus['status'] == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') .
                                                "'>{$bus['status']}</span>
                                                </td>
                                                <td class='px-4 md:px-6 py-4 text-sm flex gap-2'>
                                                    <button onclick='editBus({$bus['id']})' class='text-blue-600 hover:text-blue-900'>
                                                        <i class='fas fa-edit'></i>
                                                    </button>
                                                    <button onclick='deleteBus({$bus['id']})' class='text-red-600 hover:text-red-900'>
                                                        <i class='fas fa-trash'></i>
                                                    </button>
                                                    <button onclick='showDetails({$bus['id']})' class='md:hidden text-gray-600 hover:text-gray-900'>
                                                        <i class='fas fa-info-circle'></i>
                                                    </button>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='px-4 md:px-6 py-4 text-center'>No buses found</td></tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Bus Modal - Responsive Design -->
                <div id="addBusModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-black bg-opacity-50"
                        onclick="document.getElementById('addBusModal').classList.add('hidden')"></div>
                    <div class="relative flex items-center justify-center min-h-screen p-4">
                        <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900"><i class="fas fa-bus mr-2"></i> Add New
                                    Bus</h3>
                                <button type="button"
                                    onclick="document.getElementById('addBusModal').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="add">
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2"><i
                                            class="fas fa-bus mr-2"></i> Bus Number</label>
                                    <input type="text" name="bus_number" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2"><i
                                            class="fas fa-users mr-2"></i> Capacity</label>
                                    <input type="number" name="capacity" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2"><i
                                            class="fas fa-info-circle mr-2"></i> Type</label>
                                    <select name="type" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="Standard">Standard</option>
                                        <option value="Luxury">Luxury</option>
                                        <option value="AC">AC</option>

                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2"><i
                                            class="fas fa-signal mr-2"></i> Status</label>
                                    <select name="status" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="Active">Active</option>
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="flex justify-end">
                                    <button type="button"
                                        onclick="document.getElementById('addBusModal').classList.add('hidden')"
                                        class="mr-2 px-4 py-2 text-gray-500 hover:text-gray-700">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        <i class="fas fa-plus mr-2"></i> Add Bus
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Bus Modal -->
                <div id="editBusModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-black bg-opacity-50"
                        onclick="document.getElementById('editBusModal').classList.add('hidden')"></div>
                    <div class="relative flex items-center justify-center min-h-screen p-4">
                        <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900"><i class="fas fa-edit mr-2"></i> Edit Bus
                                </h3>
                                <button type="button"
                                    onclick="document.getElementById('editBusModal').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form method="POST" id="editBusForm">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" id="edit_bus_id">
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2"><i
                                            class="fas fa-bus mr-2"></i> Bus Number</label>
                                    <input type="text" name="bus_number" id="edit_bus_number" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2"><i
                                            class="fas fa-users mr-2"></i> Capacity</label>
                                    <input type="number" name="capacity" id="edit_capacity" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2"><i
                                            class="fas fa-info-circle mr-2"></i> Type</label>
                                    <select name="type" id="edit_type" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="Standard">Standard</option>
                                        <option value="Luxury">Luxury</option>
                                        <option value="AC">AC</option>

                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2"><i
                                            class="fas fa-signal mr-2"></i> Status</label>
                                    <select name="status" id="edit_status" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="Active">Active</option>
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="flex justify-end">
                                    <button type="button"
                                        onclick="document.getElementById('editBusModal').classList.add('hidden')"
                                        class="mr-2 px-4 py-2 text-gray-500 hover:text-gray-700">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        <i class="fas fa-save mr-2"></i> Update Bus
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bus Details Modal (for mobile view) -->
                <div id="detailsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-black bg-opacity-50"
                        onclick="document.getElementById('detailsModal').classList.add('hidden')"></div>
                    <div class="relative flex items-center justify-center min-h-screen p-4">
                        <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Bus Details</h3>
                                <button type="button"
                                    onclick="document.getElementById('detailsModal').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="busDetailContent" class="space-y-3">
                                <!-- Details will be filled by JavaScript -->
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="button"
                                    onclick="document.getElementById('detailsModal').classList.add('hidden')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    // Sidebar Toggle Function
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const body = document.body;

        sidebar.classList.toggle('active');
        backdrop.classList.toggle('active');
        body.classList.toggle('sidebar-open');
    }

    // Edit Bus Function
    function editBus(id) {
        // Fetch bus data using AJAX
        fetch(`managebus.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                // Populate the edit form
                document.getElementById('edit_bus_id').value = data.id;
                document.getElementById('edit_bus_number').value = data.bus_number;
                document.getElementById('edit_capacity').value = data.capacity;
                document.getElementById('edit_type').value = data.type;
                document.getElementById('edit_status').value = data.status;

                // Show the edit modal
                document.getElementById('editBusModal').classList.remove('hidden');
            })
            .catch(error => console.error('Error fetching bus data:', error));
    }

    // Delete Bus Function
    function deleteBus(id) {
        if (confirm('Are you sure you want to delete this bus?')) {
            // Create and submit a form to delete the bus
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Show Bus Details Function (for mobile view)
    function showDetails(id) {
        // Fetch bus data using AJAX
        fetch(`managebus.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                // Populate the details modal
                const detailContent = document.getElementById('busDetailContent');
                detailContent.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-bus text-blue-600 mr-2"></i>
                    <span class="font-semibold">Bus Number:</span>
                    <span class="ml-2">${data.bus_number}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    <span class="font-semibold">Capacity:</span>
                    <span class="ml-2">${data.capacity} seats</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    <span class="font-semibold">Type:</span>
                    <span class="ml-2">${data.type}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-signal text-blue-600 mr-2"></i>
                    <span class="font-semibold">Status:</span>
                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${data.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        }">${data.status}</span>
                </div>
            `;

                // Show the details modal
                document.getElementById('detailsModal').classList.remove('hidden');
            })
            .catch(error => console.error('Error fetching bus data:', error));
    }

    // Close modals when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners for closing modals when clicking on backdrop
        const modals = ['addBusModal', 'editBusModal', 'detailsModal'];

        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
                });
            }
        });
    });
    </script>
</body>

</html>
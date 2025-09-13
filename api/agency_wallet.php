<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

class WalletManager
{
    private $conn;
    private $email;

    public function __construct()
    {
        $this->conn = new mysqli("localhost", "root", "", "ticket");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        $this->email = $_SESSION['email'];
    }

    public function getWalletBalance()
    {
        $stmt = $this->conn->prepare("SELECT balance FROM agency_wallet WHERE email = ?");
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['balance'];
        }
        return 0;
    }

    public function getTransactionHistory($filter_date = "")
    {
        $sql = "SELECT * FROM agency_wallet_transactions WHERE email = ?";
        if (!empty($filter_date)) {
            if ($filter_date === 'today') {
                $sql .= " AND DATE(transaction_date) = CURDATE()";
            } else {
                $sql .= " AND DATE(transaction_date) = ?";
            }
        }
        $sql .= " ORDER BY transaction_date DESC LIMIT 10";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($filter_date) && $filter_date !== 'today') {
            $stmt->bind_param("ss", $this->email, $filter_date);
        } else {
            $stmt->bind_param("s", $this->email);
        }
        $stmt->execute();
        return $stmt->get_result();
    }
}

$wallet = new WalletManager();
$balance = $wallet->getWalletBalance();
$filterDate = $_GET['filter_date'] ?? "";
$transactions = $wallet->getTransactionHistory($filterDate);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Home - Wallet</title>
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

    input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }

    button {
        transition: all 0.2s ease;
    }
    </style>
</head>

<body class="bg-gray-50">
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 sidebar-backdrop md:hidden"
        onclick="toggleSidebar()"></div>

    <!-- Add Money Modal -->
    <div id="addMoneyModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-md p-6 relative max-h-[90vh] overflow-y-auto">
            <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-lg font-medium text-gray-900 mb-4"><i class="fas fa-wallet text-blue-600 mr-2"></i>Add Money
                to Wallet</h3>
            <div class="space-y-4">
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500"><i class="fas fa-rupee-sign"></i></span>
                    <input type="number" id="amount" placeholder="Enter amount (₹)"
                        class="w-full pl-8 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex justify-end space-x-2">
                    <button onclick="closeModal()"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button id="processPayment" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-check mr-1"></i> Add to Wallet
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

    <div class="flex flex-col md:flex-row min-h-screen">
        <div class="md:hidden bg-blue-700 text-white p-4 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-xl font-bold"><?php echo ucfirst($_SESSION['username']); ?></h1>
                <p class="text-xs text-blue-200"><i class="fas fa-wallet w-5"></i> Wallet</p>
            </div>
            <button id="navToggle" class="text-white focus:outline-none" onclick="toggleSidebar()">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <div id="sidebar" class="w-64 bg-blue-700 text-white md:block">
            <div class="p-6">
                <h1 class="text-2xl font-bold"><?php echo ucfirst($_SESSION['username']); ?></h1>
                <p class="text-sm text-blue-200"><i class="fas fa-wallet w-5"></i> Wallet</p>
            </div>
            <nav class="mt-4 flex-grow">
                <a href="agency_home.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-tachometer-alt w-5"></i><span class="ml-3">Dashboard</span>
                </a>
                <a href="managebus.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-bus w-5"></i><span class="ml-3">Manage Buses</span>
                </a>
                <a href="routes.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-route w-5"></i><span class="ml-3">Routes</span>
                </a>
                <a href="bookings.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-ticket-alt w-5"></i><span class="ml-3">Bookings</span>
                </a>
                <a href="x_bookings.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-800">
                    <i class="fas fa-clipboard-list w-5"></i><span class="ml-3">New Bookings</span>
                </a>
                <a href="agency_wallet.php" class="flex items-center px-6 py-3 bg-blue-800">
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
                <div class="flex items-center justify-between px-6 py-4">
                    <h1 class="text-xl font-semibold"><i class="fas fa-wallet text-blue-600 mr-2"></i>Wallet Management
                    </h1>
                    <div class="flex items-center">
                        <span class="mr-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 md:p-6">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800"><i
                                    class="fas fa-coins text-yellow-500 mr-2"></i>Available Balance</h2>
                            <p class="text-3xl font-bold text-blue-600 mt-2"><i
                                    class="fas fa-rupee-sign text-2xl mr-1"></i><?php echo number_format($balance, 2); ?>
                            </p>
                        </div>
                        <button onclick="openModal()"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>Add Money
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h2 class="text-lg font-semibold"><i class="fas fa-filter text-purple-500 mr-2"></i>Filters</h2>
                        <div class="flex gap-2">
                            <button onclick="applyFilter('today')"
                                class="px-3 py-1 text-sm <?php echo $filterDate === 'today' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'; ?> rounded hover:bg-purple-500 hover:text-white">
                                Today
                            </button>
                            <button onclick="document.getElementById('customDateModal').classList.remove('hidden')"
                                class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-purple-500 hover:text-white">
                                Custom Date
                            </button>
                            <?php if (!empty($filterDate)): ?>
                            <button onclick="clearFilter()"
                                class="px-3 py-1 text-sm bg-red-200 text-red-700 rounded hover:bg-red-500 hover:text-white">
                                Clear Filter
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold"><i class="fas fa-history text-blue-600 mr-2"></i>Transaction
                            History</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-hashtag mr-1"></i> Transaction ID
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-calendar-alt mr-1"></i> Date
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-file-alt mr-1"></i> Description
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-exchange-alt mr-1"></i> Type
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-rupee-sign mr-1"></i> Amount
                                    </th>
                                    <th
                                        class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-info-circle mr-1"></i> Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        #<?php echo htmlspecialchars($transaction['transaction_id']); ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($transaction['description']); ?>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-4 md:px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $transaction['type'] === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php if ($transaction['type'] === 'credit'): ?>
                                            <i class="fas fa-arrow-down mr-1"></i>
                                            <?php else: ?>
                                            <i class="fas fa-arrow-up mr-1"></i>
                                            <?php endif; ?>
                                            <?php echo ucfirst($transaction['type']); ?>
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 md:px-6 py-4 whitespace-nowrap font-medium 
                                        <?php echo $transaction['type'] === 'credit' ? 'text-green-600' : 'text-red-600'; ?>">
                                        <?php if ($transaction['type'] === 'credit'): ?>
                                        <i class="fas fa-plus-circle mr-1"></i>
                                        <?php else: ?>
                                        <i class="fas fa-minus-circle mr-1"></i>
                                        <?php endif; ?>
                                        ₹<?php echo number_format($transaction['amount'], 2); ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $transaction['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?php if ($transaction['status'] === 'completed'): ?>
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <?php else: ?>
                                            <i class="fas fa-clock mr-1"></i>
                                            <?php endif; ?>
                                            <?php echo ucfirst($transaction['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($transactions->num_rows === 0): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-receipt text-gray-300 text-5xl mb-3"></i>
                        <p class="text-gray-500">No transactions found</p>
                    </div>
                    <?php endif; ?>
                </div>

                <div id="transactionDetailsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-black bg-opacity-50"
                        onclick="document.getElementById('transactionDetailsModal').classList.add('hidden')"></div>
                    <div class="relative flex items-center justify-center min-h-screen p-4">
                        <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900"><i
                                        class="fas fa-info-circle text-blue-600 mr-2"></i>Transaction Details</h3>
                                <button type="button"
                                    onclick="document.getElementById('transactionDetailsModal').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="transactionDetailContent" class="space-y-3">
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="button"
                                    onclick="document.getElementById('transactionDetailsModal').classList.add('hidden')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    <i class="fas fa-times mr-1"></i> Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const body = document.body;

        sidebar.classList.toggle('active');
        backdrop.classList.toggle('active');
        body.classList.toggle('sidebar-open');
    }

    function openModal() {
        document.getElementById('addMoneyModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('addMoneyModal').classList.add('hidden');
        document.getElementById('amount').value = '';
    }

    function applyFilter(filter) {
        window.location.href = `agency_wallet.php?filter_date=${filter}`;
    }

    function clearFilter() {
        window.location.href = 'agency_wallet.php';
    }

    function showTransactionDetails(id) {
        const transactionRows = document.querySelectorAll('tbody tr');
        let transaction = {};

        for (let row of transactionRows) {
            const cells = row.querySelectorAll('td');
            if (cells[0].textContent.replace('#', '') == id) {
                transaction = {
                    id: cells[0].textContent,
                    date: cells[1].textContent.trim(),
                    description: cells[2].textContent.trim(),
                    type: cells[3].querySelector('span')?.textContent.trim() || cells[4].textContent.includes(
                        'text-green') ? 'Credit' : 'Debit',
                    amount: cells[4].textContent.trim(),
                    status: cells[5].querySelector('span').textContent.trim()
                };
                break;
            }
        }

        const detailContent = document.getElementById('transactionDetailContent');
        detailContent.innerHTML = `
                <div class="grid grid-cols-2 gap-2">
                    <div class="text-gray-600"><i class="fas fa-hashtag mr-1"></i> Transaction ID:</div>
                    <div class="font-medium">${transaction.id}</div>
                    <div class="text-gray-600"><i class="fas fa-calendar-alt mr-1"></i> Date:</div>
                    <div class="font-medium">${transaction.date}</div>
                    <div class="text-gray-600"><i class="fas fa-file-alt mr-1"></i> Description:</div>
                    <div class="font-medium">${transaction.description}</div>
                    <div class="text-gray-600"><i class="fas fa-exchange-alt mr-1"></i> Type:</div>
                    <div class="font-medium">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        ${transaction.type === 'Credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${transaction.type === 'Credit' ? '<i class="fas fa-arrow-down mr-1"></i>' : '<i class="fas fa-arrow-up mr-1"></i>'}
                            ${transaction.type}
                        </span>
                    </div>
                    <div class="text-gray-600"><i class="fas fa-rupee-sign mr-1"></i> Amount:</div>
                    <div class="font-medium ${transaction.type === 'Credit' ? 'text-green-600' : 'text-red-600'}">
                        ${transaction.type === 'Credit' ? '<i class="fas fa-plus-circle mr-1"></i>' : '<i class="fas fa-minus-circle mr-1"></i>'}
                        ${transaction.amount}
                    </div>
                    <div class="text-gray-600"><i class="fas fa-info-circle mr-1"></i> Status:</div>
                    <div class="font-medium">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        ${transaction.status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                            ${transaction.status === 'Completed' ? '<i class="fas fa-check-circle mr-1"></i>' : '<i class="fas fa-clock mr-1"></i>'}
                            ${transaction.status}
                        </span>
                    </div>
                </div>
            `;

        document.getElementById('transactionDetailsModal').classList.remove('hidden');
    }

    document.getElementById('processPayment').addEventListener('click', async function() {
        const amountInput = document.getElementById('amount');
        const amount = parseFloat(amountInput.value);

        if (!amount || isNaN(amount) || amount <= 0) {
            alert('Please enter a valid amount greater than 0');
            return;
        }

        try {
            const button = this;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';

            const response = await fetch('process_payment.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Amount added successfully!');
                closeModal();
                window.location.reload();
            } else {
                alert(data.message || 'Failed to add amount. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while processing your request. Please try again.');
        } finally {
            const button = document.getElementById('processPayment');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-check mr-1"></i> Add to Wallet';
        }
    });

    function initSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth >= 768) {
            sidebar.classList.add('active');
        } else {
            sidebar.classList.remove('active');
        }
    }

    document.addEventListener('DOMContentLoaded', initSidebar);
    window.addEventListener('resize', initSidebar);

    document.getElementById('addMoneyModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.getElementById('amount').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('processPayment').click();
        }
    });
    </script>
</body>

</html>
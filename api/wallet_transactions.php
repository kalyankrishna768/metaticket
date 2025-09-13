<?php
session_start();
include 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: newsignin.php');
    exit();
}

$username = $_SESSION['username'];
$user_email = $_SESSION['email'];

// Get filter dates from GET parameters if set
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

try {
    // Get user ID from signup table
    $stmt = $conn->prepare("SELECT id FROM signup WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    // Fetch transactions with date filter if provided
    $query = "SELECT type, amount, transaction_date AS date FROM wallet_transactions WHERE wallet_id = ?";
    if ($start_date && $end_date) {
        $query .= " AND transaction_date BETWEEN ? AND ?";
    }
    $query .= " ORDER BY transaction_date DESC";
    
    $transactions_stmt = $conn->prepare($query);
    
    if ($start_date && $end_date) {
        $transactions_stmt->bind_param("iss", $user_id, $start_date, $end_date);
    } else {
        $transactions_stmt->bind_param("i", $user_id);
    }
    
    $transactions_stmt->execute();
    $transactions_result = $transactions_stmt->get_result();
    $transactions = $transactions_result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    error_log("Transactions Error: " . $e->getMessage());
    die("An error occurred while fetching your transactions.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History | <?php echo htmlspecialchars($username); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
        }
        
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E"),
                linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }
        
        .transaction-hover:hover {
            background: rgba(243, 244, 246, 0.8);
        }

        .date-filter-form {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
    </style>
</head>

<body class="bg-pattern min-h-screen">
    <!-- Navigation Bar -->
    <div class="sticky top-0 bg-white shadow-md z-10">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-3">
                <a></a>
                <div class="text-indigo-700 font-medium">
                    <i class="fas fa-user-circle mr-2"></i>
                    <?php echo htmlspecialchars($username); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="mb-6 text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2">Transaction History</h1>
                <p class="text-gray-600">View all your wallet transactions</p>
            </div>

            <!-- Transactions Card -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history text-indigo-600 mr-2"></i>
                            All Transactions
                        </h3>
                        <!-- Date Filter Form -->
                        <form method="GET" class="date-filter-form">
                            <div class="flex items-center space-x-2">
                                <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date ?? ''); ?>" 
                                    class="border rounded-lg px-3 py-2 text-sm">
                                <span class="text-gray-600">to</span>
                                <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date ?? ''); ?>" 
                                    class="border rounded-lg px-3 py-2 text-sm">
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                                <?php if ($start_date || $end_date): ?>
                                    <a href="?" class="text-indigo-600 hover:text-indigo-800">Clear</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    
                    <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-100">
                        <?php if (empty($transactions)): ?>
                            <div class="p-8 text-center">
                                <div class="bg-gray-50 rounded-full h-20 w-20 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-receipt text-3xl text-gray-400"></i>
                                </div>
                                <h4 class="text-lg font-medium text-gray-700 mb-2">No transactions found</h4>
                                <p class="text-gray-500">
                                    <?php echo ($start_date && $end_date) ? 
                                        "No transactions found for the selected date range." : 
                                        "Your transaction history will appear here once you make transactions."; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <table class="w-full">
                                <thead>
                                    <tr class="header-gradient text-white text-left">
                                        <th class="p-4 rounded-tl-lg">Date</th>
                                        <th class="p-4">Type</th>
                                        <th class="p-4 rounded-tr-lg text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $index => $transaction): ?>
                                        <tr class="transaction-hover border-b <?php echo $index === count($transactions) - 1 ? 'border-0' : ''; ?>">
                                            <td class="p-4 text-gray-700">
                                                <?php 
                                                $date = new DateTime($transaction['date']);
                                                echo $date->format('d M Y, h:i A'); 
                                                ?>
                                            </td>
                                            <td class="p-4">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm 
                                                    <?php echo $transaction['type'] == 'Deposit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                                    <?php if ($transaction['type'] == 'Deposit'): ?>
                                                        <i class="fas fa-arrow-up mr-2"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-arrow-down mr-2"></i>
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($transaction['type']); ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-right font-semibold
                                                <?php echo $transaction['type'] == 'Deposit' ? 'text-green-600' : 'text-red-600'; ?>">
                                                ₹<?php echo number_format(abs($transaction['amount']), 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer Card -->
            <div class="glass-card rounded-xl p-6 text-center mt-6">
                <p class="text-gray-600 text-sm">Need help? Contact our support team</p>
                <div class="flex justify-center mt-3 space-x-4">
                    <a href="contact.php" class="text-indigo-600 hover:text-indigo-800 transition">
                        <i class="fas fa-headset mr-1"></i> Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
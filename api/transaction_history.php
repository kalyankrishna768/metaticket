<?php
session_start();
include 'config.php';

// Authentication check
if (!isset($_SESSION['email'])) {
    header('Location: newsignin.php');
    exit();
}

$email = $_SESSION['email'];
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Fetch paginated transactions with error handling
function get_transaction_history($email, $limit, $offset) {
    global $conn;
    
    $transactions_query = "SELECT amount, type, description, timestamp 
                           FROM transactions 
                           WHERE user_email = ? 
                           ORDER BY timestamp DESC 
                           LIMIT ? OFFSET ?";
    
    $count_query = "SELECT COUNT(*) as total FROM transactions WHERE user_email = ?";
    
    try {
        // Fetch transactions
        $stmt = $conn->prepare($transactions_query);
        $stmt->bind_param("sii", $email, $limit, $offset);
        $stmt->execute();
        $transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Count total transactions
        $count_stmt = $conn->prepare($count_query);
        $count_stmt->bind_param("s", $email);
        $count_stmt->execute();
        $total = $count_stmt->get_result()->fetch_assoc()['total'];
        
        return [
            'transactions' => $transactions,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ];
    } catch (Exception $e) {
        error_log("Transaction fetch error: " . $e->getMessage());
        return [
            'transactions' => [],
            'total' => 0,
            'pages' => 0
        ];
    }
}

// Get transaction data
$transaction_data = get_transaction_history($email, $limit, $offset);
$transactions = $transaction_data['transactions'];
$total_transactions = $transaction_data['total'];
$total_pages = $transaction_data['pages'];

// Calculate transaction summaries
$summary_query = "SELECT 
    SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END) as deposits,
    SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END) as withdrawals,
    COUNT(*) as total_count
FROM transactions 
WHERE user_email = ?";

$summary_stmt = $conn->prepare($summary_query);
$summary_stmt->bind_param("s", $email);
$summary_stmt->execute();
$summary = $summary_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Transaction History</h1>
            <div class="space-x-2">
                <button onclick="filterTransactions('all')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">All</button>
                <button onclick="filterTransactions('deposit')" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Deposits</button>
                <button onclick="filterTransactions('withdrawal')" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Withdrawals</button>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Total Transactions</h3>
                <p class="text-2xl font-bold text-purple-600"><?php echo $summary['total_count']; ?></p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Total Deposits</h3>
                <p class="text-2xl font-bold text-green-600">₹<?php echo number_format($summary['deposits'], 2); ?></p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Total Withdrawals</h3>
                <p class="text-2xl font-bold text-red-600">₹<?php echo number_format($summary['withdrawals'], 2); ?></p>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden rounded-lg">
            <table class="min-w-full" id="transactions-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($transactions as $transaction): ?>
                    <tr class="transaction-row <?php echo $transaction['type']; ?>" data-type="<?php echo $transaction['type']; ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('M d, Y H:i', strtotime($transaction['timestamp'])); ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="<?php 
                                echo $transaction['type'] == 'deposit' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800'; 
                                ?> px-2 py-1 rounded-full text-xs font-medium">
                                <?php echo ucfirst($transaction['type']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <?php echo htmlspecialchars($transaction['description']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right <?php 
                            echo $transaction['type'] == 'deposit' 
                                ? 'text-green-600' 
                                : 'text-red-600'; ?>">
                            ₹<?php echo number_format($transaction['amount'], 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page-1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    <?php endif; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page+1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium"><?php echo (($page-1)*$limit)+1; ?></span>
                            to
                            <span class="font-medium"><?php echo min($page*$limit, $total_transactions); ?></span>
                            of
                            <span class="font-medium"><?php echo $total_transactions; ?></span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page-1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page+1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function filterTransactions(type) {
        const rows = document.querySelectorAll('.transaction-row');
        rows.forEach(row => {
            row.style.display = (type === 'all' || row.dataset.type === type) ? '' : 'none';
        });
    }
    </script>
</body>
</html>
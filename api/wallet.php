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

try {
    // Get user ID from signup table
    $stmt = $conn->prepare("SELECT id FROM signup WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    // Check if wallet exists, create if not
    $stmt = $conn->prepare("SELECT id, balance FROM user_wallets WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $wallet_result = $stmt->get_result();

    if ($wallet_result->num_rows == 0) {
        // Create new wallet if not exists
        $create_stmt = $conn->prepare("INSERT INTO user_wallets (user_id, balance) VALUES (?, 0.00)");
        $create_stmt->bind_param("i", $user_id);
        $create_stmt->execute();
        $wallet_id = $conn->insert_id;
    } else {
        $wallet = $wallet_result->fetch_assoc();
        $wallet_id = $wallet['id'];
        $wallet_balance = $wallet['balance'];
    }

    // Handle fund addition
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_funds'])) {
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);

        if ($amount > 0) {
            // Update wallet balance
            $update_stmt = $conn->prepare("UPDATE user_wallets SET balance = balance + ? WHERE id = ?");
            $update_stmt->bind_param("di", $amount, $wallet_id);
            $update_stmt->execute();

            // Log transaction
            $transaction_stmt = $conn->prepare("INSERT INTO wallet_transactions (wallet_id, type, amount) VALUES (?, 'Deposit', ?)");
            $transaction_stmt->bind_param("id", $wallet_id, $amount);
            $transaction_stmt->execute();
        }
    }

    // Fetch current wallet balance
    $balance_stmt = $conn->prepare("SELECT balance FROM user_wallets WHERE id = ?");
    $balance_stmt->bind_param("i", $wallet_id);
    $balance_stmt->execute();
    $balance_result = $balance_stmt->get_result();
    $wallet_balance = $balance_result->fetch_assoc()['balance'];

    // Fetch recent transactions
    $transactions_stmt = $conn->prepare("SELECT type, amount, transaction_date AS date FROM wallet_transactions WHERE wallet_id = ? ORDER BY transaction_date DESC LIMIT 5");
    $transactions_stmt->bind_param("i", $wallet_id);
    $transactions_stmt->execute();
    $transactions_result = $transactions_stmt->get_result();
    $transactions = $transactions_result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    // Error handling
    error_log("Wallet Error: " . $e->getMessage());
    die("An error occurred while processing your wallet.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wallet | <?php echo htmlspecialchars($username); ?></title>
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
        
        .balance-gradient {
            background: linear-gradient(135deg, rgba(68, 44, 227, 0.9) 0%, rgba(68, 44, 227, 0.9) 100%);
        }
        
        .header-gradient {
            background: linear-gradient(135deg,rgb(113, 102, 235) 0%,rgb(113, 102, 235) 50%,rgb(113, 102, 235) 100%);
        }
        
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E"),
                linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }
        
        .transaction-hover:hover {
            background: rgba(243, 244, 246, 0.8);
        }
        
        .add-funds-button {
            box-shadow: 0 4px 14px rgba(78, 30, 223, 0.4);
        }
        
        .add-funds-button:hover {
            box-shadow: 0 6px 20px rgba(52, 46, 236, 0.6);
        }
        
        .modal-submit-button {
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
        }
        
        .modal-submit-button:hover {
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
        }
        
        /* Animated background */
        .animated-bg {
            background: linear-gradient(-45deg, #EE7752, #E73C7E, #23A6D5,rgb(55, 39, 233));
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        /* Subtle pulse animation for balance */
        .pulse-subtle {
            animation: pulse-subtle 3s ease infinite;
        }
        
        @keyframes pulse-subtle {
            0% {
                box-shadow: 0 0 0 0 rgba(84, 46, 251, 0.2);
            }
            70% {
                box-shadow: 0 0 0 12px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }
    </style>
</head>

<body class="bg-pattern min-h-screen">
    <!-- Navigation Bar -->
    <div class="sticky top-0 bg-white shadow-md z-10">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-3">
                <div></div>
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
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2">My Wallet</h1>
                <p class="text-gray-600">Manage your funds and track your transactions</p>
            </div>

            <!-- Main Content -->
            <div class="glass-card rounded-2xl overflow-hidden mb-6">
                <!-- Balance Card -->
                <div class="balance-gradient p-6 sm:p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800">
                            <path fill="none" stroke="white" stroke-width="2" 
                                d="M769 229L1037 260.9M927 880L731 737 520 660 309 538 40 599 295 764 126.5 879.5 40 599-197 493 102 382-31 229 126.5 79.5-69-63" />
                            <path fill="none" stroke="white" stroke-width="2" 
                                d="M-31 229L237 261 390 382 603 493 308.5 537.5 101.5 381.5M370 905L295 764" />
                            <path fill="none" stroke="white" stroke-width="2" 
                                d="M520 660L578 842 731 737 840 599 603 493 520 660 295 764 309 538 390 382 539 269 769 229 577.5 41.5 370 105 295 -36 126.5 79.5 237 261 102 382 40 599 -69 737 127 880" />
                        </svg>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between items-center relative z-10">
                        <div class="text-center sm:text-left mb-6 sm:mb-0">
                            <h2 class="text-lg font-medium text-white opacity-90 mb-1">Current Balance</h2>
                            <div class="flex items-center justify-center sm:justify-start">
                                <i class="fas fa-wallet text-2xl mr-3 opacity-90"></i>
                                <p class="text-4xl sm:text-5xl font-bold">₹<?php echo number_format($wallet_balance, 2); ?></p>
                            </div>
                        </div>
                        <button onclick="openAddFundsModal()" 
                            class="add-funds-button bg-white text-blue-700 px-6 py-3 rounded-lg hover:bg-blue-50 transition transform hover:scale-105 flex items-center">
                            <i class="fas fa-plus-circle text-xl mr-2"></i>
                            <span class="font-medium">Add Amount</span>
                        </button>
                    </div>
                </div>

                <!-- Transactions Section -->
                <div class="p-6 sm:p-8 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history text-indigo-600 mr-2"></i>
                            Recent Transactions
                        </h3>
                        <!-- You could add a "View All" button here -->
                    </div>
                    
                    <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-100">
                        <?php if (empty($transactions)): ?>
                            <div class="p-8 text-center">
                                <div class="bg-gray-50 rounded-full h-20 w-20 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-receipt text-3xl text-gray-400"></i>
                                </div>
                                <h4 class="text-lg font-medium text-gray-700 mb-2">No transactions yet</h4>
                                <p class="text-gray-500">Your transaction history will appear here once you add funds.</p>
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
            
            <!-- Stats Cards - Optional, adds more visual appeal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div class="glass-card rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-gray-500 text-sm">Last Updated</h4>
                            <p class="font-semibold text-gray-800">
                                <?php 
                                echo !empty($transactions) ? 
                                    (new DateTime($transactions[0]['date']))->format('d M Y, h:i A') : 
                                    'No transactions yet'; 
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="glass-card rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-lg bg-purple-100 flex items-center justify-center mr-4">
                        <i class="fas fa-history text-indigo-600 mr-2"></i>
                        </div>
                        <div>
                            <h4 class="text-gray-500 text-sm"><a href=wallet_transactions.php>All Transactions</a></h4>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Card -->
            <div class="glass-card rounded-xl p-6 text-center">
                <p class="text-gray-600 text-sm">Need help? Contact our support team</p>
                <div class="flex justify-center mt-3 space-x-4">
                    <a href="contact.php" class="text-indigo-600 hover:text-indigo-800 transition">
                        <i class="fas fa-headset mr-1"></i> Support
                    </a>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Add Funds Modal -->
    <div id="add-funds-modal" 
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md transform transition-all shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Add Amount</h2>
                <button type="button" onclick="closeAddFundsModal()" 
                    class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Amount to Add</label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-lg">₹</span>
                        </div>
                        <input type="number" name="amount" step="0.01" min="0.01" 
                            class="block w-full pl-10 pr-12 py-4 text-lg rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                            placeholder="0.00" required>
                    </div>
                    
                    <!-- Quick amount buttons -->
                    <div class="grid grid-cols-3 gap-3 mt-4">
                        <button type="button" onclick="setAmount(500)" 
                            class="py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
                            ₹500
                        </button>
                        <button type="button" onclick="setAmount(1000)" 
                            class="py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
                            ₹1,000
                        </button>
                        <button type="button" onclick="setAmount(2000)" 
                            class="py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
                            ₹2,000
                        </button>
                    </div>
                </div>
                
                <div class="pt-4">
                    <button type="submit" name="add_funds" 
                        class="modal-submit-button w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 rounded-lg text-lg font-medium hover:from-indigo-700 hover:to-purple-700 transition transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddFundsModal() {
            document.getElementById('add-funds-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddFundsModal() {
            document.getElementById('add-funds-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function setAmount(amount) {
            document.querySelector('input[name="amount"]').value = amount;
        }

        // Close modal when clicking outside
        document.getElementById('add-funds-modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeAddFundsModal();
            }
        });
    </script>
</body>

</html>
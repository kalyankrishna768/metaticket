<?php
session_start();
include 'config.php';

// Initialize wallet if not exists for specific user
$user_email = $_SESSION['email'];
$wallet_key = 'wallet_balance' . md5($user_email);

if (!isset($_SESSION[$wallet_key])) {
    $_SESSION[$wallet_key] = 0.00;
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];
$gender = $_SESSION['gender'];
$phonenumber = $_SESSION['phonenumber'];
$address = $_SESSION['address'];
$wallet_balance = $_SESSION[$wallet_key];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $new_phonenumber = filter_input(INPUT_POST, 'phonenumber', FILTER_SANITIZE_STRING);
    $new_gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $new_address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    
    try {
        // Update user profile in the signup table
        $update_stmt = $conn->prepare("UPDATE signup SET username = ?, phonenumber = ?, gender = ?, address = ? WHERE email = ?");
        $update_stmt->bind_param("sssss", $new_username, $new_phonenumber, $new_gender, $new_address, $user_email);
        $update_stmt->execute();
        
        if ($update_stmt->affected_rows > 0 || $conn->affected_rows > 0) {
            // Update session variables
            $_SESSION['username'] = $new_username;
            $_SESSION['gender'] = $new_gender;
            $_SESSION['phonenumber'] = $new_phonenumber;
            $_SESSION['address'] = $new_address;
            
            // Set success message
            $success_message = "Profile updated successfully!";
            
            // Update local variables to display updated info
            $username = $new_username;
            $gender = $new_gender;
            $phonenumber = $new_phonenumber;
            $address = $new_address;
        } else {
            // No changes or error
            $info_message = "No changes were made to your profile.";
        }
    } catch (Exception $e) {
        error_log("Profile Update Error: " . $e->getMessage());
        $error_message = "An error occurred while updating your profile.";
    }
}

try {
    // Get user ID from signup table
    $stmt = $conn->prepare("SELECT id FROM signup WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    // Check wallet
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
        $wallet_balance = 0.00;
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

            $wallet_balance += $amount;
        }
    }
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
    <title>Profile | <?php echo htmlspecialchars($username); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #6366f1;
        --primary-dark: #4f46e5;
        --secondary-color: #f59e0b;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #f3f4f6;
        color: #1f2937;
    }

    .gradient-bg {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    }

    .card {
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    .profile-img {
        border: 4px solid #fff;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .notification-dot {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 10px;
        height: 10px;
        background-color: var(--secondary-color);
        border-radius: 50%;
        border: 2px solid white;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .modal {
        transition: opacity 0.3s ease;
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .profile-info {
            margin-top: 1rem;
        }
    }

    .toast {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 9999;
        max-width: 320px;
        transform: translateX(400px);
        transition: transform 0.5s ease;
    }

    .toast.show {
        transform: translateX(0);
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(99, 102, 241, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
        }
    }

    .back-button {
        position: absolute;
        top: 1.25rem;
        left: 1.25rem;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.3s;
    }

    .back-button:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .action-button {
        transition: all 0.3s ease;
    }

    .action-button:hover {
        transform: translateY(-2px);
    }

    .activity-item {
        border-left: 3px solid var(--primary-color);
    }
    </style>
</head>

<body class="min-h-screen">
    <!-- Toast Notifications -->
    <?php if(isset($success_message)): ?>
    <div id="success-toast" class="toast bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
            <p><?php echo $success_message; ?></p>
        </div>
        <button onclick="closeToast('success-toast')" class="ml-auto text-green-500 hover:text-green-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php endif; ?>

    <?php if(isset($error_message)): ?>
    <div id="error-toast" class="toast bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
            <p><?php echo $error_message; ?></p>
        </div>
        <button onclick="closeToast('error-toast')" class="ml-auto text-red-500 hover:text-red-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- Top Navigation -->
    <nav class="gradient-bg text-white shadow-lg relative">
        <div class="container mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl sm:text-2xl font-bold ml-8 sm:ml-0">My Profile</h1>
                </div>
                <button onclick="toggleEditMode()"
                    class="action-button bg-white text-indigo-600 px-3 py-2 sm:px-4 sm:py-2 rounded-lg shadow hover:bg-gray-100 transition duration-200 flex items-center text-sm sm:text-base">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
                </button>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 sm:px-6 py-6">
        <!-- Profile Header Card -->
        <div class="card bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="gradient-bg px-6 py-6">
                <div class="flex profile-header items-center">
                    <div class="relative">
                        <img class="profile-img w-24 h-24 sm:w-32 sm:h-32 rounded-full object-cover"
                            src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>&background=random"
                            alt="<?php echo htmlspecialchars($username); ?>">
                    </div>
                    <div class="profile-info ml-0 sm:ml-6 mt-4 sm:mt-0">
                        <h2 class="text-xl sm:text-2xl font-bold text-white"><?php echo htmlspecialchars($username); ?>
                        </h2>
                        <p class="text-indigo-100 text-sm sm:text-base"><?php echo htmlspecialchars($email); ?></p>
                        <div class="flex flex-wrap mt-3 gap-2">
                            <span class="bg-indigo-100 bg-opacity-20 text-white text-xs px-2 py-1 rounded-full">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?php echo htmlspecialchars($address ? substr($address, 0, 15) . (strlen($address) > 15 ? '...' : '') : 'No address'); ?>
                            </span>
                            <span class="bg-indigo-100 bg-opacity-20 text-white text-xs px-2 py-1 rounded-full">
                                <i class="fas fa-phone mr-1"></i>
                                <?php echo htmlspecialchars($phonenumber); ?>
                            </span>
                            <span class="bg-indigo-100 bg-opacity-20 text-white text-xs px-2 py-1 rounded-full">
                                <i class="fas fa-venus-mars mr-1"></i>
                                <?php echo htmlspecialchars($gender); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quick Actions Sidebar (existing code) -->
            <div class="lg:col-span-1 order-2 lg:order-1">
                <div class="card bg-white rounded-2xl shadow-md p-5">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bolt text-indigo-500 mr-2"></i>
                        Quick Links
                    </h3>
                    <div class="space-y-3">
                        <a href="wallet.php" class="stat-card flex items-center p-3 bg-indigo-50 rounded-xl">
                            <div class="bg-indigo-100 p-2 rounded-lg">
                                <i class="fas fa-wallet text-indigo-600"></i>
                            </div>
                            <span class="text-gray-700 ml-3">My Wallet</span>
                            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                        </a>
                        <a href="mybookings.php" class="stat-card flex items-center p-3 bg-indigo-50 rounded-xl">
                            <div class="bg-indigo-100 p-2 rounded-lg">
                                <i class="fas fa-ticket-alt text-indigo-600"></i>
                            </div>
                            <span class="text-gray-700 ml-3">My Bookings</span>
                            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                        </a>
                        <a href="myuploads.php" class="stat-card flex items-center p-3 bg-indigo-50 rounded-xl">
                            <div class="bg-indigo-100 p-2 rounded-lg">
                                <i class="fas fa-cloud-upload-alt text-indigo-600"></i>
                            </div>
                            <span class="text-gray-700 ml-3">My Uploads</span>
                            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                        </a>
                        <a href="index.php" class="stat-card flex items-center p-3 bg-red-50 rounded-xl">
                            <div class="bg-red-100 p-2 rounded-lg">
                                <i class="fas fa-sign-out-alt text-red-600"></i>
                            </div>
                            <span class="text-red-700 ml-3">Logout</span>
                            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 order-1 lg:order-2">
                <!-- Profile Details Card -->
                <div class="card bg-white rounded-2xl shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-user text-indigo-500 mr-2"></i>
                        Profile Information
                    </h3>

                    <form id="profile-form" method="POST" class="space-y-6">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="space-y-2">
                                <label class="text-gray-600 font-medium text-sm">Full Name</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" name="username"
                                        value="<?php echo htmlspecialchars($username); ?>"
                                        class="w-full p-3 pl-10 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        disabled>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-gray-600 font-medium text-sm">Phone Number</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" name="phonenumber"
                                        value="<?php echo htmlspecialchars($phonenumber); ?>"
                                        class="w-full p-3 pl-10 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        disabled>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-gray-600 font-medium text-sm">Gender</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                        <i class="fas fa-venus-mars"></i>
                                    </span>
                                    <select name="gender"
                                        class="w-full p-3 pl-10 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none"
                                        disabled>
                                        <option value="Male"
                                            <?php echo strtolower($gender) === 'male' ? 'selected' : ''; ?>>Male
                                        </option>
                                        <option value="Female"
                                            <?php echo strtolower($gender) === 'female' ? 'selected' : ''; ?>>Female
                                        </option>
                                        <option value="Others"
                                            <?php echo strtolower($gender) === 'others' ? 'selected' : ''; ?>>Other
                                        </option>
                                    </select>
                                    <span
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Email Field - Read-only -->
                            <div class="space-y-2">
                                <label class="text-gray-600 font-medium text-sm">Email Address</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" value="<?php echo htmlspecialchars($email); ?>"
                                        class="w-full p-3 pl-10 border border-gray-300 rounded-xl bg-gray-100 cursor-not-allowed"
                                        readonly disabled>
                                </div>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="text-gray-600 font-medium text-sm">Address</label>
                                <div class="relative">
                                    <span class="absolute top-3 left-3 text-gray-500">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <textarea name="address"
                                        class="w-full p-3 pl-10 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        rows="3" disabled><?php echo htmlspecialchars($address); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4 border-t" id="edit-buttons" style="display: none;">
                            <button type="button" onclick="toggleEditMode()"
                                class="px-6 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition duration-200">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleEditMode() {
        const form = document.getElementById('profile-form');
        const inputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
        const editButtons = document.getElementById('edit-buttons');

        inputs.forEach(input => {
            input.disabled = !input.disabled;
        });

        editButtons.style.display = editButtons.style.display === 'none' ? 'flex' : 'none';
    }

    // Check for any success messages and automatically scroll to them
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.querySelector('.bg-green-100');
        if (successMessage) {
            successMessage.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Auto-hide the message after 5 seconds
            setTimeout(function() {
                successMessage.style.transition = 'opacity 1s';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 1000);
            }, 5000);
        }
    });

    function closeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.display = 'none';
        }
    }
    </script>
</body>

</html>
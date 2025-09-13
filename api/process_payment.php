<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['amount']) || !is_numeric($data['amount'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid amount']);
    exit();
}

$amount = floatval($data['amount']);

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
    exit();
}

try {
    $conn = new mysqli("localhost", "root", "", "ticket");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        $email = $_SESSION['email'];
        $transaction_id = 'TXN' . time() . rand(1000, 9999);
        
        // First, check if wallet exists
        $stmt = $conn->prepare("SELECT email FROM agency_wallet WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Create new wallet
            $stmt = $conn->prepare("INSERT INTO agency_wallet (email, balance) VALUES (?, ?)");
            $stmt->bind_param("sd", $email, $amount);
            $stmt->execute();
        } else {
            // Update existing wallet
            $stmt = $conn->prepare("UPDATE agency_wallet SET balance = balance + ? WHERE email = ?");
            $stmt->bind_param("ds", $amount, $email);
            $stmt->execute();
        }

        // Add transaction record
        $stmt = $conn->prepare("INSERT INTO agency_wallet_transactions (transaction_id, email, amount, type, description, status, transaction_date) VALUES (?, ?, ?, 'credit', 'Wallet money addition', 'completed', NOW())");
        $stmt->bind_param("ssd", $transaction_id, $email, $amount);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Amount added successfully',
            'transaction_id' => $transaction_id
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error processing payment: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
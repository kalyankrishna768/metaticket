<?php
include 'config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $username = htmlspecialchars(trim($_POST["fullname"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $gender = htmlspecialchars(trim($_POST["gender"]));
    $address = htmlspecialchars(trim($_POST["address"]));
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirm_password"]);

    // Error message initialization
    $error = "";

    // Input validation
    if (empty($username) || empty($email) || empty($phone) || empty($gender) || empty($address) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $error = "Invalid phone number format. Enter 10-15 digits.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if the email already exists
        $checkEmailStmt = $conn->prepare("SELECT email FROM signup WHERE email = ?");
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailStmt->store_result();

        if ($checkEmailStmt->num_rows > 0) {
            $error = "This email is already registered.";
        } else {
            // Hash passwor

            // Insert data into the database
            $stmt = $conn->prepare("INSERT INTO signup (username, email, phonenumber, gender, address, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $email, $phone, $gender, $address, $password);

            if ($stmt->execute()) {
                // Redirect to a success page
                header("Location: newsignin.php"); // Replace with a success page if needed
                exit();
            } else {
                $error = "Error: Unable to register. Please try again.";
            }

            $stmt->close();
        }

        $checkEmailStmt->close();
    }

    // If there's an error, display it
    if (!empty($error)) {
        echo "<script>alert('$error');</script>";
    }
}

$conn->close();
?>

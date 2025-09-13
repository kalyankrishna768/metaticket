<?php
// Start the session to manage user login
session_start();

// Database connection details
include ("config.php");
// Initialize variables
$email = $password = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email and password from the form
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Check if fields are empty
    if (empty($email) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        // Prepare the SQL query to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM signup WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if the user exists
        if ($result->num_rows == 1) {
            $_SESSION["logged_in"] = true;
            $row = $result->fetch_assoc();
            // Directly compare the password
            if ($password === $row['password']) {
                // Set session variables
                $_SESSION['id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['phonenumber'] = $row['phonenumber'];
                $_SESSION['gender'] = $row['gender'];
                $_SESSION['address'] = $row['address'];
                $_SESSION['user_type'] = $row['user_type'];

                // Redirect based on user type
                $user_type = $row['user_type'];
                if ($user_type == "1") {
                    header("Location: admin_home.php");
                    exit();
                } elseif ($user_type == "2") {
                    header("Location: dashboard.php");
                    exit();
                } elseif ($user_type == "3") {
                    header("Location: agency_home.php");
                    exit();
                } else {
                    echo "Unknown role: $user_type";
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with this email.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url("assets/images/back3.jpg") no-repeat center center fixed;
            background-size: cover;
            font-family: 'Droid Serif', serif;
        }

        #container {
            background: rgba(3, 3, 55, 0.7);
            width: 90%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
        }

        header {
            font-size: 1.5rem;
            color: #d3d3d3;
            margin-bottom: 1rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 1rem;
        }

        .input-group input {
            width: 100%;
            padding: 0.8rem 2.5rem;
            background: transparent;
            border: 2px solid #FF0000;
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
        }

        .input-group .fa-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #FF0000;
        }

        input:focus {
            background: rgba(235, 30, 54, 0.3);
            outline: none;
        }

        input[type="submit"] {
            background: rgba(235, 30, 54, 1);
            border: none;
            width: 100%;
            padding: 0.7rem;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 0.5rem;
            transition: background 0.3s;
        }

        input[type="submit"]:hover {
            background: rgba(235, 30, 54, 0.8);
        }

        p {
            color: white;
            margin-top: 1rem;
        }

        a {
            color: red;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #FF0000;
            font-size: 0.85rem;
            text-align: left;
            margin-top: 0.3rem;
            padding-left: 0.5rem;
        }

        @media (max-width: 480px) {
            #container {
                width: 95%;
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div id="container">
        <header>Sign In</header>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group">
                <i class="fas fa-envelope fa-icon"></i>
                <input type="email" name="email" id="email" placeholder="E-mail" value="<?php echo $email; ?>" required>
                <?php if ($error == "No account found with this email.") { ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php } ?>
            </div>
            <div class="input-group">
                <i class="fas fa-lock fa-icon"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <?php if ($error == "Incorrect password.") { ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php } ?>
            </div>
            <?php if ($error == "Please fill in both fields.") { ?>
                <div class="error-message" style="text-align: center; margin-bottom: 1rem;"><?php echo $error; ?></div>
            <?php } ?>
            <input type="submit" name="submit" id="submit" value="SIGN IN">
            <p>Don't have an account? <a href="newsignup.php"><i class="fas fa-user-plus"></i> Register</a></p>
        </form>
    </div>
</body>

</html>
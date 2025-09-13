<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html {
            height: 100%;
            width: 100%;
        }

        body {
            background: url("assets/images/back4.jpg") no-repeat center center fixed;
            background-size: cover;
            font-family: 'Droid Serif', serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        #container {
            background: rgba(3, 3, 55, 0.5);
            width: 90%;
            max-width: 25rem;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            margin: 1rem;
        }

        header {
            text-align: center;
            line-height: 3rem;
            height: 3rem;
            background: rgba(1, 1, 55, 0.7);
            font-size: 1.4rem;
            color: #d3d3d3;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        header i {
            margin-right: 0.5rem;
        }

        fieldset {
            border: 0;
            text-align: center;
            padding: 0;
            margin: 0;
        }

        .input-group {
            position: relative;
            margin-bottom: 0.5rem;
            width: 90%;
            margin-left: auto;
            margin-right: auto;
        }

        .input-group i {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: #d3d3d3;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"],
        textarea, 
        select {
            width: 100%;
            padding: 0.8rem;
            padding-left: 2.5rem;
            margin: 0.5rem 0;
            background: transparent;
            border: 2px solid #FF0000;
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background: rgba(235, 30, 54, 1);
            border: 0;
            display: block;
            width: 90%;
            margin: 1rem auto;
            color: white;
            padding: 0.8rem;
            cursor: pointer;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background: rgba(255, 50, 74, 1);
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="password"]:focus,
        textarea:focus,
        select:focus {
            outline: 0;
            background: rgba(235, 30, 54, 0.3);
            border-color: #FF0000;
        }

        ::placeholder {
            color: #d3d3d3;
        }

        textarea {
            resize: none;
            height: 5rem;
            padding-left: 2.5rem;
        }

        p {
            margin-top: 1rem;
            color: white;
        }

        a {
            color: red;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            text-decoration: underline;
            color: #ff5555;
        }

        /* Media queries for better responsiveness */
        @media all and (max-width: 480px) {
            #container {
                width: 95%;
                padding: 1rem;
            }
            
            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="password"],
            textarea, 
            select, 
            input[type="submit"] {
                width: 100%;
            }
            
            .input-group {
                width: 100%;
            }
        }

        @media all and (min-width: 481px) and (max-width: 768px) {
            #container {
                width: 80%;
            }
        }
    </style>
</head>

<body>
    <div id="container">
        <header><i class="fas fa-user-plus"></i> Create Your Account</header>
        <form method="post" action="newsignupDB.php">
            <fieldset>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="fullname" id="fullname" placeholder="Full Name" required autofocus>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Email Address" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" id="phone" placeholder="Phone Number" pattern="[0-9]{10,15}" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-venus-mars"></i>
                    <select name="gender" id="gender" required>
                        <option value="" disabled selected>Choose Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <textarea name="address" id="address" placeholder="Your Address" required></textarea>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Create Password" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-check-circle"></i>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                </div>
                
                <input type="submit" name="register" id="register" value="CREATE ACCOUNT">
                <p>Already registered? <a href="newsignin.php"><i class="fas fa-sign-in-alt"></i> Login Here</a></p>
            </fieldset>
        </form>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journey in Comfort - Book Tickets</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #e84118;
            --primary-hover: #ff3b3b;
            --bg-overlay: rgba(0, 0, 0, 0.6);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: url('assets/images/bus_buy1.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .container {
            text-align: center;
            background: var(--bg-overlay);
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            width: 95%;
            max-width: 500px;
            backdrop-filter: blur(10px);
        }

        h1 {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 10px;
        }

        p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 5px rgba(232, 65, 24, 0.5);
        }

        .search-btn {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-3px);
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            font-size: 1.3rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.4);
        }
    </style>
</head>
<body>
    <button class="back-btn" onclick="window.history.back();">←</button>
    <div class="container">
        <h1>Journey in Comfort</h1>
        <p>Book your tickets online and enjoy premium travel.</p>
        <form method="POST" action="search_bus.php">
            <div class="input-group">
                <label for="date"><i class="far fa-calendar"></i> Travel Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="input-group">
                <label for="from"><i class="fas fa-map-marker-alt"></i> From:</label>
                <input type="text" id="from" name="from" placeholder="Enter departure city" required>
            </div>
            <div class="input-group">
                <label for="to"><i class="fas fa-map-pin"></i> To:</label>
                <input type="text" id="to" name="to" placeholder="Enter destination city" required>
            </div>
            <button type="submit" class="search-btn">Search Buses</button>
        </form>
    </div>

    <script>
        // Disable past dates
        let today = new Date().toISOString().split('T')[0];
            document.getElementById("date").setAttribute("min", today);
    </script>
</body>
</html>

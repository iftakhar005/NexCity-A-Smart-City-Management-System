<?php
session_start();
require_once("database.php"); 

$user_id = $_SESSION['user']['user_id']; 

$query = "SELECT service_id, name FROM services";
$result = $conn->query($query);


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $service_id = $_POST['service'];
    $start_date = $_POST['start_date'];

    
    $insert_query = "INSERT INTO subscriptions (user_id, service_id, start_date, end_date, status) 
                     VALUES (?, ?, ?, NULL, 'pending')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iis", $user_id, $service_id, $start_date);

    if ($stmt->execute()) {
        echo "<p>Subscription successful!</p>";
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe to Services</title>
    <style>
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e1e2f, #292940);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .subscribe-container {
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 600px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #4caf50;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 16px;
            margin-bottom: 10px;
            color: #cfcfcf;
        }

        select, input {
            font-size: 16px;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            background: #2a2a3d;
            color: #fff;
        }

        .submit-btn {
            background: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #45a049;
        }

        .back-btn {
            text-align: center;
            margin-top: 20px;
        }

        .back-btn a {
            color: #4caf50;
            text-decoration: none;
            font-size: 14px;
        }

        .back-btn a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="subscribe-container">
        <h1>Subscribe to Services</h1>
        <form action="subscribe_service.php" method="POST">
            <label for="service">Choose a Service</label>
            <select id="service" name="service" required>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['service_id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="">No services available</option>
                <?php endif; ?>
            </select>

            <label for="start-date">Subscription Start Date</label>
            <input type="date" id="start-date" name="start_date" required>

            <button type="submit" class="submit-btn">Subscribe Now</button>
        </form>

        <div class="back-btn">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

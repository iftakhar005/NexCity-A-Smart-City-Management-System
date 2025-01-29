<?php
session_start();
require_once("database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $subscription_id = intval($_POST['subscription_id']);
        $update_query = "UPDATE subscriptions SET status = 'approved' WHERE subscription_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $subscription_id);

        if ($stmt->execute()) {
            echo "Subscription approved successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    if (isset($_POST['delete'])) {
        $subscription_id = intval($_POST['subscription_id']);
        $delete_query = "DELETE FROM subscriptions WHERE subscription_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $subscription_id);

        if ($stmt->execute()) {
            echo "Subscription deleted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch subscription requests
$query = "
    SELECT s.subscription_id, u.username, srv.name AS service_name, s.start_date, s.end_date, s.status
    FROM subscriptions s
    JOIN users u ON s.user_id = u.user_id
    JOIN services srv ON s.service_id = srv.service_id
    ORDER BY s.subscription_id DESC
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching subscription requests: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services</title>
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

        .dashboard-container {
            width: 100%;
            max-width: 1200px;
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 40px;
            position: relative;
        }

        .go-back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .go-back-btn:hover {
            background-color: #388e3c;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #4caf50;
        }

        .service-card {
            background: linear-gradient(145deg, #2a2a3d, #1d1d2e);
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.4);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.6);
        }

        .service-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #4caf50;
        }

        .service-card p {
            font-size: 16px;
            color: #cfcfcf;
        }

        .service-card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #4caf50;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .service-card a:hover {
            background: #45a049;
        }

        .subscription-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .subscription-table th, .subscription-table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ccc;
        }

        .subscription-table th {
            background-color: #2a2a3d;
        }

        .subscription-table tr:nth-child(even) {
            background-color: #333;
        }

        .subscription-table tr:hover {
            background-color: #444;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .action-buttons button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            color: #fff;
            transition: background 0.3s;
        }

        .action-buttons .approve {
            background: #4caf50;
        }

        .action-buttons .approve:hover {
            background: #388e3c;
        }

        .action-buttons .delete {
            background: #f44336;
        }

        .action-buttons .delete:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="admin_dashboard.php" class="go-back-btn">&#8592; Go Back</a>
        <h1>Manage City Services</h1>

        <!-- Service creation card -->
        <div class="service-card">
            <h3>Create a New Service</h3>
            <p>Manage services by adding new ones such as garbage collection, water supply, etc.</p>
            <a href="create_service.php">Create Service</a>
        </div>

        <!-- Subscription requests table -->
        <div class="service-card">
            <h3>View Subscription Requests</h3>
            <p>Here you can view all the subscription requests from citizens.</p>
            <table class="subscription-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Service</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($subscription = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($subscription['subscription_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($subscription['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($subscription['service_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($subscription['start_date']) . "</td>";
                        echo "<td>" . ($subscription['end_date'] ? htmlspecialchars($subscription['end_date']) : 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($subscription['status']) . "</td>";
                        echo "<td>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='subscription_id' value='" . $subscription['subscription_id'] . "'>
                                    <div class='action-buttons'>
                                        <button type='submit' name='approve' class='approve'>Approve</button>
                                        <button type='submit' name='delete' class='delete'>Delete</button>
                                    </div>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    ?>

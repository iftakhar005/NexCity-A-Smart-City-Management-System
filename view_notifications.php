<?php 
session_start(); 

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once("database.php"); 

$user_id = $_SESSION['user']['user_id']; // Assuming user ID is stored in session
$user_role = $_SESSION['user']['selected_role']; // Correct
 // Assuming role ('citizen' or 'doctor') is stored in session

// Determine the dashboard URL based on user role
$dashboard_url = ($user_role === 'doctor') ? "doctor_dashboard.php" : "Dashboard.php";

// Calculate date range for last 2 days
$two_days_ago = date('Y-m-d H:i:s', strtotime('-2 days'));

// SQL query with GROUP BY and a subquery to filter recent notifications for the user
$query = "
    SELECT 
        n.notification_id, 
        n.message, 
        n.created_at,
        nr.status
    FROM notifications n
    JOIN notification_recipients nr ON n.notification_id = nr.notification_id
    WHERE nr.user_id = ? 
    AND n.created_at >= ? 
    AND n.notification_id IN (
        SELECT notification_id
        FROM notification_recipients
        WHERE user_id = ?
    )
    GROUP BY n.notification_id 
    ORDER BY n.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("isi", $user_id, $two_days_ago, $user_id); // Bind parameters for user ID and date range
$stmt->execute();
$result = $stmt->get_result();

// Handle remove notification request
if (isset($_GET['remove_notification'])) {
    $notification_id = $_GET['remove_notification'];

    // Delete from the notification_recipients table
    $remove_query = "DELETE FROM notification_recipients WHERE notification_id = ? AND user_id = ?";
    $remove_stmt = $conn->prepare($remove_query);
    $remove_stmt->bind_param("ii", $notification_id, $user_id);
    if ($remove_stmt->execute()) {
        echo "<script>alert('Notification removed successfully.');</script>";
    } else {
        echo "<script>alert('Error removing notification.');</script>";
    }
    $remove_stmt->close();
}

// Handle mark as read request
if (isset($_GET['mark_read'])) {
    $notification_id = $_GET['mark_read'];

    // Update status to 'read' in the notification_recipients table
    $mark_read_query = "UPDATE notification_recipients SET status = 'read' WHERE notification_id = ? AND user_id = ?";
    $mark_read_stmt = $conn->prepare($mark_read_query);
    $mark_read_stmt->bind_param("ii", $notification_id, $user_id);
    if ($mark_read_stmt->execute()) {
        echo "<script>alert('Notification marked as read.');</script>";
    } else {
        echo "<script>alert('Error marking notification as read.');</script>";
    }
    $mark_read_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .notification {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .notification p {
            margin: 5px 0;
        }

        .notification .timestamp {
            font-size: 12px;
            color: #999;
            position: absolute;
            bottom: 10px;
            right: 15px;
        }

        .notification.unread {
            background: #f1f8ff;
        }

        .notification.read {
            background: #e9ecef;
        }

        .remove-btn, .read-btn {
            background: #ff4b5c;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .remove-btn:hover, .read-btn:hover {
            background-color: #e33a47;
        }

        .read-btn {
            background: #4CAF50; /* Green */
            right: 80px;
        }

        .back-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo $dashboard_url; ?>" class="back-btn">Back to Dashboard</a>
        <h2>Your Notifications</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="notification <?php echo ($row['status'] == 'read') ? 'read' : 'unread'; ?>">
                    <p><strong>Message:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
                    <p><strong>Received on:</strong> <?php echo date("Y-m-d H:i:s", strtotime($row['created_at'])); ?></p>

                    <!-- Remove Button -->
                    <a href="view_notifications.php?remove_notification=<?php echo $row['notification_id']; ?>" class="remove-btn" onclick="return confirm('Are you sure you want to remove this notification?');">Remove</a>
                    
                    <!-- Read Button (only for unread notifications) -->
                    <?php if ($row['status'] == 'unread'): ?>
                        <a href="view_notifications.php?mark_read=<?php echo $row['notification_id']; ?>" class="read-btn">Mark as Read</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No notifications available for the last 2 days.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

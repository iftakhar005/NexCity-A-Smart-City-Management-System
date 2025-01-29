<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['user_id'];


$count_query = "SELECT COUNT(*) AS total_subscriptions FROM subscriptions WHERE user_id = ?";
$count_stmt = mysqli_prepare($conn, $count_query);
mysqli_stmt_bind_param($count_stmt, "i", $user_id);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_row = mysqli_fetch_assoc($count_result);
$total_subscriptions = $count_row['total_subscriptions'];

$search_query = "";
if (isset($_GET['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " AND (s.status LIKE '%$search_term%' OR srv.name LIKE '%$search_term%' OR s.start_date LIKE '%$search_term%')";
}

$query = "SELECT 
                s.subscription_id,
                s.service_id,
                s.start_date,
                s.end_date,
                s.status,
                srv.name AS service_name
            FROM 
                subscriptions s
            JOIN 
                services srv ON s.service_id = srv.service_id
            WHERE 
                s.user_id = ? 
                $search_query";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (isset($_POST['delete'])) {
    $subscription_id = $_POST['subscription_id'];

    $fetch_query = "SELECT start_date, end_date, status FROM subscriptions WHERE subscription_id = ?";
    $fetch_stmt = mysqli_prepare($conn, $fetch_query);
    mysqli_stmt_bind_param($fetch_stmt, "i", $subscription_id);
    mysqli_stmt_execute($fetch_stmt);
    $fetch_result = mysqli_stmt_get_result($fetch_stmt);
    $subscription = mysqli_fetch_assoc($fetch_result);

    $start_date = strtotime($subscription['start_date']);
    $end_date = $subscription['end_date'] ? strtotime($subscription['end_date']) : time(); // Default to current time if end_date is NULL
    $status = $subscription['status'];

    $update_query = '';

    if ($status == 'pending') {
        $update_query = "UPDATE subscriptions SET status = 'canceled', end_date = NULL WHERE subscription_id = ?";
    } elseif ($status == 'approved') {
        $date_diff = ($end_date - $start_date) / (60 * 60 * 24);
        if ($date_diff >= 30) {
            $update_query = "UPDATE subscriptions SET status = 'canceled', end_date = NOW() WHERE subscription_id = ?";
        } else {
            echo "<script>alert('The subscription must be active for at least 30 days before being canceled.');</script>";
            header("Refresh:0");
            exit();
        }
    }

    if ($update_query !== '') {
        $update_stmt = mysqli_prepare($conn, $update_query);
        if ($update_stmt === false) {
            echo "Error preparing query: " . mysqli_error($conn);
            exit();
        }
        mysqli_stmt_bind_param($update_stmt, "i", $subscription_id);
        mysqli_stmt_execute($update_stmt);
        header("Location: view_subscriptions.php");
    } else {
        echo "<script>alert('Unable to cancel the subscription.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Subscriptions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1e1e2f;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #292940;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #4caf50;
            text-align: left;
        }
        table th {
            background: #4caf50;
        }
        .btn-approve, .btn-delete {
            background: #f39c12;
            color: #fff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-approve:hover {
            background: #e67e22;
        }
        .btn-delete {
            background: #e74c3c;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search-bar input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 250px;
        }
        .search-bar button {
            padding: 10px 15px;
            background-color: #4caf50;
            border: none;
            color: white;
            border-radius: 5px;
        }
        .go-back-btn {
            padding: 10px 15px;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }
        .go-back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Subscriptions</h1>
        <h2>Total Subscriptions: <?php echo $total_subscriptions; ?></h2>

        <div class="search-bar">
            <form method="GET" action="view_subscriptions.php">
                <input type="text" name="search" placeholder="Search by service name, status, or start date" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_date']) ? htmlspecialchars($row['end_date']) : 'N/A'; ?></td>
                        <td>
                            <span style="color: <?php echo $row['status'] == 'approved' ? 'green' : 'orange'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <form method="POST" action="view_subscriptions.php" style="display: inline;">
                                    <input type="hidden" name="subscription_id" value="<?php echo $row['subscription_id']; ?>">
                               
                                </form>
                            <?php endif; ?>
                            
                            <form method="POST" action="view_subscriptions_U.php" style="display: inline;">
                                <input type="hidden" name="subscription_id" value="<?php echo $row['subscription_id']; ?>">
                                <button type="submit" name="delete" class="btn-delete" onclick="return confirm('Are you sure you want to delete this subscription?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="go-back-btn">Go Back to Dashboard</a>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>

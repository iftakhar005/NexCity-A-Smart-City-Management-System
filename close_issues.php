<?php
session_start();
require_once("database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $issueId = mysqli_real_escape_string($conn, $_POST['issue_id']);

    // Update query to mark the issue as closed
    $closeQuery = "
        UPDATE issues 
        SET status = 'Closed'
        WHERE issue_id = $issueId AND status = 'Assigned'
    ";

    if (mysqli_query($conn, $closeQuery)) {
        $message = "Issue successfully closed!";
    } else {
        $message = "Error closing issue: " . mysqli_error($conn);
    }
}

$assignedIssuesQuery = "
    SELECT i.issue_id, i.description, d.name AS department, u.username AS assigned_by
    FROM issues i
    JOIN users u ON i.user_id = u.user_id
    JOIN departments d ON i.assigned_department_id = d.department_id
    WHERE i.status = 'Assigned'
    ORDER BY d.name, i.description
";


$assignedIssuesResult = mysqli_query($conn, $assignedIssuesQuery);

if (!$assignedIssuesResult) {
    die("Error fetching issues: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Close Issues</title>
    <style>
        /* Include your CSS here */
        <?php include 'manage_issues_css.css'; ?>
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

        h1, h2 {
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
        .form-container {
            margin-top: 20px;
            text-align: center;
        }

        .form-container form {
            display: inline-block;
            background: linear-gradient(145deg, #2a2a3d, #1d1d2e);
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.4);
            padding: 20px;
        }

        form select, form input {
            margin-bottom: 20px;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: #2a2a3d;
            color: #fff;
            font-size: 14px;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            color: #4caf50;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="manage_issues.php" class="go-back-btn">&#8592; Go Back</a>
        <h1>Close Reported Issues</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="" method="POST">
                <label for="issue_id">Select Issue to Close:</label>
                <select name="issue_id" id="issue_id" required>
                    <?php while ($issue = mysqli_fetch_assoc($assignedIssuesResult)): ?>
                        <option value="<?php echo $issue['issue_id']; ?>">
                            [<?php echo htmlspecialchars($issue['department']); ?>] <?php echo htmlspecialchars($issue['description']); ?> 
                            (Assigned by: <?php echo htmlspecialchars($issue['assigned_by']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Close Issue</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>

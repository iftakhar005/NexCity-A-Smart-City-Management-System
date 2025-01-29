<?php
session_start();

require_once("database.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $issueId = mysqli_real_escape_string($conn, $_POST['issue_id']);
    $assignedTo = mysqli_real_escape_string($conn, $_POST['assigned_to']);

    // Update query to assign the issue
    $assignQuery = "
        UPDATE issues 
        SET assigned_department_id = '$assignedTo', status = 'Assigned'
        WHERE issue_id = $issueId
    ";

    if (mysqli_query($conn, $assignQuery)) {
        $message = "Issue successfully assigned!";
    } else {
        $message = "Error assigning issue: " . mysqli_error($conn);
    }
}

// Fetch unassigned issues
$unassignedIssuesQuery = "
    SELECT i.issue_id, i.description, i.location_id, i.status
    FROM issues i
    WHERE i.status = 'Pending'
    ORDER BY i.reported_date DESC
";

$unassignedIssuesResult = mysqli_query($conn, $unassignedIssuesQuery);

if (!$unassignedIssuesResult) {
    die("Error fetching issues: " . mysqli_error($conn));
}

// Fetch department/team list
$departmentsQuery = "
    SELECT department_id, name 
    FROM departments
";
$departmentsResult = mysqli_query($conn, $departmentsQuery);

if (!$departmentsResult) {
    die("Error fetching departments: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Issues</title>
    <style>
        <?php include 'manage_issues_css.css'; ?>
        /* Styles... */
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="manage_issues.php" class="go-back-btn">&#8592; Go Back</a>
        <h1>Assign Reported Issues</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="" method="POST">
                <!-- Select Issue -->
                <label for="issue_id">Select Issue:</label>
                <select name="issue_id" id="issue_id" required>
                    <?php while ($issue = mysqli_fetch_assoc($unassignedIssuesResult)): ?>
                        <option value="<?php echo $issue['issue_id']; ?>">
                            [<?php echo htmlspecialchars($issue['location_id']); ?>] <?php echo htmlspecialchars($issue['description']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <!-- Select Department -->
                <label for="assigned_to">Assign To (Department/Team):</label>
                <select name="assigned_to" id="assigned_to" required>
                    <?php while ($department = mysqli_fetch_assoc($departmentsResult)): ?>
                        <option value="<?php echo $department['department_id']; ?>">
                            <?php echo htmlspecialchars($department['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Assign Issue</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>

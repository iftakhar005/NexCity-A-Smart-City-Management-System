<?php
session_start();


require_once("database.php");


$query = "
    SELECT 
        issues.issue_id, 
        issues.issue_type, 
        issues.description, 
        issues.reported_date, 
        issues.status, 
        issues.location_id, 
        users.username AS user_name
    FROM 
        issues
    JOIN 
        users ON issues.user_id = users.user_id
    ORDER BY 
        issues.reported_date DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Issues</title>
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

        .container {
            width: 100%;
            max-width: 1200px;
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 40px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #4caf50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: #fff;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #444;
        }

        th {
            background: #4caf50;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #2a2a3d;
        }

        tr:hover {
            background: #444;
        }

        .back-btn {
            margin-top: 20px;
            text-align: center;
        }

        .back-btn a {
            color: #4caf50;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .back-btn a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reported Issues</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Issue ID</th>
                    <th>Reported By</th>
                    <th>Issue Type</th>
                    <th>Description</th>
                    <th>Reported Date</th>
                    <th>Status</th>
                    <th>Location_id</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['issue_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['issue_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['reported_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['location_id']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No issues reported yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="back-btn">
            <a href="manage_issues.php">Back to Manage Issues</a>
        </div>
    </div>
</body>
</html>

<?php

mysqli_close($conn);
?>

<?php
// Include the database connection file
require_once("database.php");

// Fetch all feedback sorted by the highest rating first
$query = "SELECT feedback.id, feedback.feedback_text, feedback.rating, feedback.created_at, feedback.user_id 
          FROM feedback 
          ORDER BY feedback.rating DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Feedbacks</title>
    <style>
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
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #4caf50;
        }
        .feedback-container {
            width: 100%;
            max-width: 1000px;
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: #4caf50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #2c2f3d;
        }
        tr:nth-child(odd) {
            background-color: #1e1e2f;
        }
        tr:hover {
            background-color: #3e3e5e;
        }
        .go-back {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #4caf50;
            font-weight: bold;
        }
        .go-back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="feedback-container">
        <h1>All Feedbacks</h1>
        <table>
            <thead>
                <tr>
                    <th>Feedback ID</th>
                    <th>User ID</th>
                    <th>Feedback</th>
                    <th>Rating</th>
                    <th>Submitted On</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['feedback_text']; ?></td>
                        <td><?php echo $row['rating']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="view_feedback.php" class="go-back">← Go Back to Dashboard</a>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>

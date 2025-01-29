<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['user_id'];

$search_query = "";
if (isset($_GET['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " AND (i.issue_type LIKE '%$search_term%' OR i.status LIKE '%$search_term%')";
}

$query = "SELECT 
                i.issue_id,
                i.issue_type,
                i.description,
                i.reported_date,
                i.status,
                (SELECT d.name FROM departments d WHERE d.department_id = i.assigned_department_id) AS department_name,
                (SELECT a.ward_no FROM address a WHERE a.address_id = i.location_id) AS ward_no,
                (SELECT a.street_no FROM address a WHERE a.address_id = i.location_id) AS street_no,
                (SELECT a.building_no FROM address a WHERE a.address_id = i.location_id) AS building_no
            FROM 
                issues i
            WHERE 
                i.user_id = ? 
                $search_query
            ORDER BY 
                i.reported_date DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reported Issues</title>
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
        .btn-edit, .btn-delete {
            background: #f39c12;
            color: #fff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-edit:hover {
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
        .back-btn {
            background: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .back-btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Reported Issues</h1>

       
        <div class="search-bar">
            <form method="GET" action="view_issues_U.php">
                <input type="text" name="search" placeholder="Search by issue type or status" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Issue Type</th>
                    <th>Description</th>
                    <th>Reported Date</th>
                    <th>Status</th>
                    <th>Assigned Department</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['issue_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['reported_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['ward_no']) . ", " . htmlspecialchars($row['street_no']) . ", Building No: " . htmlspecialchars($row['building_no']); ?></td>
                        <td>
                           
                            <a href="delete_issue.php?issue_id=<?php echo $row['issue_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this issue?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="back-btn">Go Back to Dashboard</a>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>

<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once("database.php");

$user_id = $_SESSION['user']['user_id'];

// Fetch user address
$query_user_address = "
    SELECT a.ward_no, a.street_no, a.building_no
    FROM address a
    JOIN user_address ua ON a.address_id = ua.address_id
    WHERE ua.user_id = ?";
$stmt_user_address = $conn->prepare($query_user_address);
$stmt_user_address->bind_param("i", $user_id);
$stmt_user_address->execute();
$result_user_address = $stmt_user_address->get_result();
$user_address = $result_user_address->fetch_assoc();

if (!$user_address) {
    die("User address not found.");
}

// Initialize search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " AND (LOWER(ei.name) LIKE LOWER('%$search_term%') OR LOWER(ei.type) LIKE LOWER('%$search_term%'))"; // Case-insensitive search
}

// Query for fetching institutions with sorting by proximity
$query_institutions = "
    SELECT 
        ei.name, 
        CONCAT('Ward ', a.ward_no, ', Street ', a.street_no, ', Building ', a.building_no) AS address, 
        ei.contact_number, 
        ei.type,
        ABS(a.ward_no - ?) AS ward_proximity,
        ABS(a.street_no - ?) AS street_proximity,
        ABS(a.building_no - ?) AS building_proximity
    FROM 
        educational_institutions ei
    LEFT JOIN 
        address a ON ei.location_id = a.address_id
    WHERE 1=1 
    $search_query
    GROUP BY 
        ei.institution_id
    ORDER BY 
        ward_proximity ASC, 
        street_proximity ASC, 
        building_proximity ASC
";

$stmt_institutions = $conn->prepare($query_institutions);
if (!empty($search_query)) {
    $stmt_institutions->bind_param("iii", $user_address['ward_no'], $user_address['street_no'], $user_address['building_no']);
} else {
    $stmt_institutions->bind_param("iii", $user_address['ward_no'], $user_address['street_no'], $user_address['building_no']);
}

$stmt_institutions->execute();
$result_institutions = $stmt_institutions->get_result();
$institutions = $result_institutions->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educational Institutions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e1e2f, #292940);
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(25, 25, 35, 0.9);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        h1 {
            text-align: center;
            color: #4caf50;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #444;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background: #4caf50;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #2a2a3d;
        }
        tr:hover {
            background: #333;
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
        .back-link {
            display: inline-block;
            margin: 20px 0;
            text-decoration: none;
            background: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }
        .back-link:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Educational Institutions</h1>

        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by name or type" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <?php if (count($institutions) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($institutions as $institution): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($institution['name']); ?></td>
                            <td><?php echo htmlspecialchars($institution['address'] ?: 'No address available'); ?></td>
                            <td><?php echo htmlspecialchars($institution['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($institution['type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No educational institutions found.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>

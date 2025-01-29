<?php
session_start(); 

require_once("database.php");



$user = $_SESSION['user'];
$user_id = $user['user_id'];

 $query_user_address = "
 SELECT 
     a.ward_no, 
     a.street_no, 
     a.building_no
 FROM 
     address a
 INNER JOIN 
     user_address ua ON a.address_id = ua.address_id
 INNER JOIN 
     users u ON ua.user_id = u.user_id
 WHERE 
     u.user_id = $user_id
";

$user_address_result = $conn->query($query_user_address);
if ($user_address_result->num_rows > 0) {
    $user_address = $user_address_result->fetch_assoc();
    $user_ward = $user_address['ward_no'];
    $user_street = $user_address['street_no'];
    $user_building = $user_address['building_no'];
} else {
    die("User address not found.");
}


$shop_type_filter = isset($_GET['shop_type']) ? $_GET['shop_type'] : '';
$category_filter = isset($_GET['category_filter']) ? $_GET['category_filter'] : '';


$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " AND (LOWER(s.shop_name) LIKE LOWER('%$search_term%') OR LOWER(c.category_name) LIKE LOWER('%$search_term%'))";
}

$query = "
    SELECT 
        s.shop_id, 
        s.shop_name, 
        c.category_name, 
        CONCAT('Ward ', a.ward_no, ', Street ', a.street_no, ', Building ', a.building_no) AS address, 
        COALESCE(s.rating, 'No Rating') AS rating,
        ABS(a.ward_no - $user_ward) AS ward_diff,
        ABS(a.street_no - $user_street) AS street_diff,
        ABS(a.building_no - $user_building) AS building_diff
    FROM 
        shops s
    INNER JOIN 
        address a ON s.address_id = a.address_id
    LEFT JOIN 
        categories c ON s.shop_type = c.category_id
    WHERE 1=1
    $search_query
";


if ($shop_type_filter) {
    $query .= " AND c.category_name LIKE '%$shop_type_filter%'";
}

if ($category_filter) {
    $query .= " AND c.category_name LIKE '%$category_filter%'";
}


$query .= " ORDER BY 
                ABS(a.ward_no - $user_ward) ASC,        -- Prioritize ward number first
                ABS(a.street_no - $user_street) ASC,      -- Then prioritize street number
                ABS(a.building_no - $user_building) ASC     -- Finally prioritize building number
";


$shops_result = $conn->query($query);

if (!$shops_result) {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shops in the City</title>
    <style>
        style>
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

        .shops-container {
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 900px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #4caf50;
        }

        .filter-form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filter-form select, .filter-form button {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #444;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #333;
        }

        table tr:hover {
            background-color: #555;
        }

        h3 {
            margin-top: 30px;
            text-align: center;
            color: #4caf50;
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
    <div class="shops-container">
        <h1>Shops in the City</h1>

        <!-- Filter Form -->
        <div class="filter-form">
            <form method="GET" action="">
                <select name="shop_type">
                    <option value="">-- Select Shop Type --</option>
                    <option value="Grocery">Grocery</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Furniture">Furniture</option>
                </select>

               

                <button type="submit">Apply Filters</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Shop ID</th>
                    <th>Shop Name</th>
                    <th>Category</th>
                    <th>Address</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $shops_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['shop_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['shop_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['rating']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="back-btn">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

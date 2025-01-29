<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once("database.php");

try {

    $user_id = $_SESSION['user']['user_id'];
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

    $ward_no = $user_address['ward_no'];
    $street_no = $user_address['street_no'];
    $building_no = $user_address['building_no'];


    $search_query = "";
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_term = mysqli_real_escape_string($conn, $_GET['search']);
        
        $search_query = " AND (LOWER(r.name) LIKE LOWER('%$search_term%') OR LOWER(r.cuisine_type) LIKE LOWER('%$search_term%'))";
    }

    
    $query = "
        SELECT 
            r.name, 
            CONCAT('Ward ', a.ward_no, ', Street ', a.street_no, ', Building ', a.building_no) AS address, 
            r.contact_number, 
            r.cuisine_type, 
            r.average_cost
        FROM 
            restaurants r
        LEFT JOIN 
            address a ON r.location_id = a.address_id
        WHERE 1=1
        $search_query
        GROUP BY 
            r.restaurant_id
        ORDER BY 
            ABS(a.ward_no - ?) ASC,        
            ABS(a.street_no - ?) ASC,     
            ABS(a.building_no - ?) ASC     
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $ward_no, $street_no, $building_no); 
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurants = $result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    die("Error fetching restaurants: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants</title>
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

        a.back-link {
            display: inline-block;
            margin: 20px 0;
            text-decoration: none;
            background: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        a.back-link:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Restaurants</h1>

        <div class="search-bar">
            <form method="GET" action="view_restaurants.php">
                <input type="text" name="search" placeholder="Search by name or cuisine" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <?php if (count($restaurants) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Cuisine Type</th>
                        <th>Average Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($restaurants as $restaurant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($restaurant['name']); ?></td>
                            <td><?php echo htmlspecialchars($restaurant['address'] ?: 'No address available'); ?></td>
                            <td><?php echo htmlspecialchars($restaurant['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($restaurant['cuisine_type']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($restaurant['average_cost'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No restaurants found.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>


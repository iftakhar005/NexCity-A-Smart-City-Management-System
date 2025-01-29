<?php
session_start();
require_once("database.php"); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $contact_number = $_POST['contact_number'];
        $cuisine_type = $_POST['cuisine_type'];
        $average_cost = $_POST['average_cost'];
        $location_id = $_POST['location_id'];

        $sql = "INSERT INTO restaurants (name, contact_number, cuisine_type, average_cost, location_id) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $contact_number, $cuisine_type, $average_cost, $location_id);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['restaurant_id'];

        $sql = "DELETE FROM restaurants WHERE restaurant_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

$restaurants = $conn->query("SELECT * FROM restaurants");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Restaurants</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e1e2f, #292940);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 800px;
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 20px;
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
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #4caf50;
        }
        form {
            margin-bottom: 20px;
            background: linear-gradient(145deg, #2a2a3d, #1d1d2e);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.4);
        }
        form h3 {
            margin-bottom: 10px;
            color: #4caf50;
        }
        form input, form button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
        }
        form button {
            background: #4caf50;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }
        form button:hover {
            background: #45a049;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            background: linear-gradient(145deg, #2a2a3d, #1d1d2e);
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        ul li form {
            margin: 0;
        }
        ul li button {
            padding: 5px 10px;
            background: #ff4b5c;
            border: none;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }
        ul li button:hover {
            background: #e33a47;
        }
    </style>
</head>
<body>
    
  
    <div class="container">
        <h1>Manage Restaurants</h1>
        <form method="POST">
            <h3>Add Restaurant</h3>
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="contact_number" placeholder="Contact Number">
            <input type="text" name="cuisine_type" placeholder="Cuisine Type">
            <input type="number" step="0.01" name="average_cost" placeholder="Average Cost">
            <input type="number" name="location_id" placeholder="Location ID">
            <button type="submit" name="add">Add Restaurant</button>
        </form>
        <h3>Restaurants List</h3>
        <ul>
            <?php while ($row = $restaurants->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($row['name']); ?>
                    <form method="POST">
                        <input type="hidden" name="restaurant_id" value="<?php echo $row['restaurant_id']; ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
        <a href="admin_dashboard.php" class="go-back">← Go Back to Dashboard</a>
    </div>
</body>
</html>

<?php
session_start();



require_once("database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serviceName = mysqli_real_escape_string($conn, $_POST['service_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $insertQuery = "
        INSERT INTO services (name, description) 
        VALUES ('$serviceName', '$description')
    ";

    if (mysqli_query($conn, $insertQuery)) {
        $message = "Service created successfully!";
    } else {
        $message = "Error creating service: " . mysqli_error($conn);
    }
}

// Fetch existing services grouped by category
$groupedServicesQuery = "
    SELECT name, COUNT(*) AS total_services 
    FROM services 
    GROUP BY name 
    HAVING total_services > 0
";
$servicesResult = mysqli_query($conn, $groupedServicesQuery);

if (!$servicesResult) {
    die("Error fetching services: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Service</title>
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

        .container {
            width: 100%;
            max-width: 800px;
            background: rgba(25, 25, 35, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 40px;
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

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #4caf50;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
            font-weight: bold;
        }

        input, textarea, select {
            margin-bottom: 20px;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: #2a2a3d;
            color: #fff;
            font-size: 14px;
        }

        button {
            padding: 10px 15px;
            background: #4caf50;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #388e3c;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            color: #4caf50;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #2a2a3d;
        }

        tr:nth-child(even) {
            background-color: #333;
        }

        tr:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="manage_services.php" class="go-back-btn">&#8592; Go Back</a>
        <h1>Create a New Service</h1>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="service_name">Service Name:</label>
            <input type="text" name="service_name" id="service_name" required>


            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" required></textarea>

            <button type="submit">Create Service</button>
        </form>

        <div class="table-container">
            <h2>Existing Services by Category</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Total Services</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($servicesResult)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_services']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>

<?php 
session_start(); 



require_once("database.php");


$query = "
    SELECT 
        u.user_id, 
        u.username, 
        u.email, 
        u.phone_number, 
        u.dob, 
        r.role_name AS role, 
        a.ward_no, 
        a.street_no, 
        a.building_no
    FROM 
        users u
    LEFT JOIN 
        user_roles ur ON u.user_id = ur.user_id
    LEFT JOIN 
        roles r ON ur.role_id = r.role_id
    LEFT JOIN 
        user_address ua ON u.user_id = ua.user_id
    LEFT JOIN 
        address a ON ua.address_id = a.address_id
    ORDER BY 
        u.user_id ASC
";


$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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

        .manage-container {
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

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #4caf50;
        }

        .user-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .user-card {
            background: linear-gradient(145deg, #2a2a3d, #1d1d2e);
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.4);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.6);
        }

        .user-card h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #4caf50;
        }

        .user-card p {
            font-size: 16px;
            color: #cfcfcf;
            margin: 5px 0;
        }

        .user-card a {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 20px;
            background: #4caf50;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .user-card a:hover {
            background: #45a049;
        }
    </style>
<body>
    <div class="manage-container">
        <a href="admin_dashboard.php" class="go-back-btn">&#8592; Go Back</a>
        <h1>Manage Users</h1>
        <div class="user-list">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="user-card">
                <h3><?php echo htmlspecialchars($row['username']); ?></h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone_number']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars('Ward No:'.$row['ward_no'] . ' Street No: ' . $row['street_no'] . ' Building No: ' . $row['building_no']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($row['role']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($row['dob']); ?></p>
                
                <a href="delete_user.php?user_id=<?php echo $row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; color: #fff;">No users found.</p>
    <?php endif; ?>
</div>

    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
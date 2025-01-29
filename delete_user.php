<?php
session_start();
require_once("database.php");

// Ensure the user is logged in and has admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['username'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

// Check if user_id is passed via GET and is numeric
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete related records from dependent tables first
        
        // Delete from appointments
        $delete_appointments = $conn->prepare("DELETE FROM appointments WHERE user_id = ?");
        $delete_appointments->bind_param("i", $user_id);
        $delete_appointments->execute();

        // Delete from issues
        $delete_issues = $conn->prepare("DELETE FROM issues WHERE user_id = ?");
        $delete_issues->bind_param("i", $user_id);
        $delete_issues->execute();

        // Delete from notifications
        $delete_notifications = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");
        $delete_notifications->bind_param("i", $user_id);
        $delete_notifications->execute();

        // Delete from patient_medicine
        $delete_patient_medicine = $conn->prepare("DELETE FROM patient_medicine WHERE user_id = ?");
        $delete_patient_medicine->bind_param("i", $user_id);
        $delete_patient_medicine->execute();

        // Delete from patient_test
        $delete_patient_test = $conn->prepare("DELETE FROM patient_test WHERE user_id = ?");
        $delete_patient_test->bind_param("i", $user_id);
        $delete_patient_test->execute();

        // Delete from feedback
        $delete_feedback = $conn->prepare("DELETE FROM feedback WHERE user_id = ?");
        $delete_feedback->bind_param("i", $user_id);
        $delete_feedback->execute();

        // Delete from notifications_recipients
        $delete_notification_recipients = $conn->prepare("DELETE FROM notification_recipients WHERE user_id = ?");
        $delete_notification_recipients->bind_param("i", $user_id);
        $delete_notification_recipients->execute();

        // Delete from services
        $delete_services = $conn->prepare("DELETE FROM services WHERE user_id = ?");
        $delete_services->bind_param("i", $user_id);
        $delete_services->execute();

        // Delete from subscriptions
        $delete_subscriptions = $conn->prepare("DELETE FROM subscriptions WHERE user_id = ?");
        $delete_subscriptions->bind_param("i", $user_id);
        $delete_subscriptions->execute();

        // Delete from user_roles
        $delete_user_roles = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $delete_user_roles->bind_param("i", $user_id);
        $delete_user_roles->execute();

        // Delete from user_address
        $delete_user_address = $conn->prepare("DELETE FROM user_address WHERE user_id = ?");
        $delete_user_address->bind_param("i", $user_id);
        $delete_user_address->execute();

        // Finally, delete the user from the users table
        $delete_user = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $delete_user->bind_param("i", $user_id);
        $delete_user->execute();

        // If all deletions were successful, commit the transaction
        $conn->commit();

        // Redirect to manage users page with a success message
        header("Location: manage_users.php?success=User deleted successfully.");
        exit();

    } catch (Exception $e) {
        // If any query fails, rollback the transaction
        $conn->rollback();

        // Redirect to manage users page with an error message
        header("Location: manage_users.php?error=Failed to delete user: " . $e->getMessage());
        exit();
    }
} else {
    // If user_id is not passed or is invalid, show an error
    echo "Invalid user ID!";
    exit();
}
?>

<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.php");
    exit();
}

include 'db.php';

// Ensure the user is an admin
$stmt = $pdo->prepare("SELECT role_id FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $user_id_to_delete = $_POST['user_id'];

        // Prevent admin from deleting themselves
        if ($user_id_to_delete == $_SESSION['user_id']) {
            $_SESSION['message'] = "You cannot delete your own account.";
            header("Location: admin.php?pageUsers=" . $_POST['pageUsers'] . "&tab=" . $_POST['tab']);
            exit();
        }

        // Delete user query
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = "User deleted successfully.";
        } else {
            $_SESSION['message'] = "Failed to delete user.";
        }
    }

    // Pagination
    $pageUsers = isset($_POST['pageUsers']) ? (int)$_POST['pageUsers'] : 1;
    $tab = isset($_POST['tab']) ? $_POST['tab'] : 'users';

    // Get the total number of users
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // Check if the current page has no records left
    $recordsOnPage = $totalUsers - ($pageUsers - 1) * 8; // Assuming 8 records per page
    if ($recordsOnPage <= 1 && $pageUsers > 1) {
        $pageUsers--; // If the last user is deleted, go back to the previous page
    }

    // Redirect back to the correct page
    header("Location: admin.php?pageUsers=" . $pageUsers . "&tab=" . $tab);
    exit();
}

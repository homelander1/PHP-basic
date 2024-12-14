<?php
ob_clean();
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include 'db.php';

try {
    if (isset($_GET['user_id'])) {
        $userId = $_GET['user_id'];

        $stmt = $pdo->prepare("SELECT u.user_id, u.first_name, u.last_name, u.role_id, r.role_name 
                                FROM users u 
                                JOIN user_roles r ON u.role_id = r.role_id 
                                WHERE u.user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode([
                'status' => 'success',
                'user' => $user
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No user ID provided.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit();

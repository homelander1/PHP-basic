<?php
// Clear any previous output
ob_clean();

session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include 'db.php';

try {
    // Fetch the current user data
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        $stmt = $pdo->prepare("SELECT first_name, last_name, role_id, email FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists
        if ($user) {
            $currentFirstName = $user['first_name'];
            $currentLastName = $user['last_name'];
            $currentRoleId = $user['role_id'];
            $currentEmail = $user['email'];

            // Get the data submitted by the form
            $newFirstName = $_POST['first_name'];
            $newLastName = $_POST['last_name'];
            $newRoleId = $_POST['role'];

            // Check if any changes were made
            $changesMade = false;

            $updateQuery = "UPDATE users SET first_name = :first_name, last_name = :last_name, role_id = :role_id WHERE user_id = :user_id";

            if ($newFirstName !== $currentFirstName || $newLastName !== $currentLastName || $newRoleId != $currentRoleId) {
                $stmtUpdate = $pdo->prepare($updateQuery);
                $stmtUpdate->execute([
                    'first_name' => $newFirstName,
                    'last_name' => $newLastName,
                    'role_id' => $newRoleId,
                    'user_id' => $userId
                ]);
                $changesMade = true;
            }

            // Set JSON
            header('Content-Type: application/json');

            if ($changesMade) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'User updated successfully.',
                    'user' => [
                        'user_id' => $userId,
                        'first_name' => $newFirstName,
                        'last_name' => $newLastName,
                        'role_id' => $newRoleId,
                        'role_name' => $newRoleId == 1 ? 'Admin' : 'Writer',
                        'email' => $currentEmail
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'no_changes', 'message' => 'No changes detected.']);
            }
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
            exit();
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'No user ID provided.']);
        exit();
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit();
}

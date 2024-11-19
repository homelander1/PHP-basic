<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

include 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['article_id'])) {
            echo json_encode(['success' => false, 'message' => 'Article ID is missing.']);
            exit();
        }

        $article_id = $data['article_id'];
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("DELETE FROM articles WHERE article_id = :article_id AND user_id = :user_id");
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Article deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete article.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

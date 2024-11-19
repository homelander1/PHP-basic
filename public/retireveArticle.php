<?php
include 'db.php';


if (isset($_GET['article_id'])) {
    $article_id = $_GET['article_id'];


    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :article_id");
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->execute();
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($article) {
        echo json_encode($article);
    } else {
        echo json_encode(['error' => 'Article not found']);
    }
    exit();
}

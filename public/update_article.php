<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $image_url = null;
    if (!empty($_FILES['image']['name'])) {
        $image_path = './assets/img/articleImg/' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image_url = $image_path;
        } else {
            header("Location: user.php?error=Image upload failed");
            exit();
        }
    } else {
        $stmt = $pdo->prepare("SELECT image_url FROM articles WHERE article_id = :article_id AND user_id = :user_id");
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $image_url = $result['image_url'];
        } else {
            header("Location: user.php?error=Article not found");
            exit();
        }
    }

    $stmt = $pdo->prepare("UPDATE articles 
                           SET title = :title, content = :content, image_url = :image_url 
                           WHERE article_id = :article_id AND user_id = :user_id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':image_url', $image_url);
    $stmt->bindParam(':article_id', $article_id);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        header("Location: user.php?success=Article updated");
        exit();
    } else {
        header("Location: user.php?error=Update failed");
        exit();
    }
}

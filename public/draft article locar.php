<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit an article.");
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$author = $user['first_name'] . ' ' . $user['last_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = $_POST['title'];
    $content = $_POST['content'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $imagePath = './assets/img/articleImg/' . uniqid('', true) . '.' . $imageExtension;

        if (!move_uploaded_file($imageTmpName, $imagePath)) {
            die("Failed to upload image.");
        }
    } else {

        $imagePath = './assets/img/articleImg/balckjacket.jpg';
    }

    $stmt = $pdo->prepare("INSERT INTO articles (title, content, author, image_url, user_id) 
                           VALUES (:title, :content, :author, :image_url, :user_id)");
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':author', $author, PDO::PARAM_STR);
    $stmt->bindParam(':image_url', $imagePath, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {

        echo "Article published successfully!";
    } else {
        echo "Failed to save article.";
    }
}

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

    $publishDate = date('Y-m-d');

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $imageBaseName = pathinfo($imageName, PATHINFO_FILENAME);

        $uploadDir = './assets/img/articleImg/';

        $newImageName = $imageBaseName . '.' . $imageExtension;
        $imagePath = $uploadDir . $newImageName;
        $counter = 1;

        while (file_exists($imagePath)) {

            $newImageName = $imageBaseName . '-' . $counter . '.' . $imageExtension;
            $imagePath = $uploadDir . $newImageName;
            $counter++;
        }

        if (!move_uploaded_file($imageTmpName, $imagePath)) {
            die("Failed to upload image.");
        }
    } else {

        $imagePath = './assets/img/articleImg/default.jpg';
    }


    $stmt = $pdo->prepare("INSERT INTO articles (title, content, author, image_url, user_id, publication_date) 
                           VALUES (:title, :content, :author, :image_url, :user_id, :publication_date)");
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':author', $author, PDO::PARAM_STR);
    $stmt->bindParam(':image_url', $imagePath, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':publication_date', $publishDate, PDO::PARAM_STR);

    if ($stmt->execute()) {
        header("Location: user.php");
        exit();
    } else {
        echo "Failed to save article.";
    }
}

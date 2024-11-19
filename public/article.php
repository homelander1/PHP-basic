<?php
include 'db.php';

$id = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;
$stmt = $pdo->prepare("SELECT title, content, author, publication_date, image_url FROM articles WHERE article_id = :article_id");
$stmt->bindParam(':article_id', $id, PDO::PARAM_INT);
$stmt->execute();
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    echo "Article not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        .article-container {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
        }

        .article-image {
            max-width: 250px;
            margin-right: 20px;
        }

        .article-content {
            flex-grow: 1;
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <h1 class="mb-3"><?= htmlspecialchars($article['title']) ?></h1>
        <p class="text-muted">By <?= htmlspecialchars($article['author']) ?> on <?= htmlspecialchars($article['publication_date']) ?></p>

        <div class="article-container">
            <?php if (!empty($article['image_url'])): ?>
                <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="Article Image" class="article-image">
            <?php endif; ?>

            <div class="article-content">
                <div class="border rounded p-3"><?= nl2br(htmlspecialchars($article['content'])) ?></div>
            </div>
        </div>

        <a href="./equipment.php" class="btn btn-secondary mt-4">Back to Articles</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>
<?php session_start(); ?>
<?php
include 'db.php';

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$totalPages = ceil($totalArticles / $limit);

$stmt = $pdo->prepare("SELECT article_id, title, content, author, publication_date, image_url FROM articles ORDER BY publication_date DESC LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include_once './trimDisplayedText.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/supp.css">
    <link rel="stylesheet" href="./css/styles.css">


    <title>Equipment</title>
</head>

<body>
    <?php include './header.php'; ?>
    <?php include './socialMedia.php'; ?>
    <?php include './followUsForm.php'; ?>
    <?php include './logInSignUpForm.php'; ?>

    <div class="container my-4">
        <h3 class="mb-4">Equipment details</h3>
        <div class="row article-box">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-4 mb-4">
                    <div class="card card-fixed shadow-sm d-flex flex-row">
                        <?php if ($article['image_url']): ?>
                            <div class="image-container">
                                <img src="<?= htmlspecialchars($article['image_url']) ?>" class="fixed-image" alt="<?= htmlspecialchars($article['title']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?= trimText($article['title'], 23) ?></h5>
                            <p class="card-text"><?= trimText($article['content'], 50) ?></p>
                            <p class="card-text1"><small class="text-muted">By <?= htmlspecialchars($article['author']) ?> on <?= htmlspecialchars($article['publication_date']) ?></small></p>
                            <a href="article.php?article_id=<?= $article['article_id'] ?>" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">
                            < </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>"> > </a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php include './footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script src="./js.js"></script>
</body>

</html>
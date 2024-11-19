<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.php");
    exit();
}

$first_name = $_SESSION['first_name'] ?? '';
$last_name = $_SESSION['last_name'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';
include 'db.php';
$stmt = $pdo->prepare("SELECT article_id, title, content,publication_date, image_url FROM articles WHERE user_id = :user_id ORDER BY publication_date DESC");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/supp.css">
    <link rel="stylesheet" href="./css/styles.css">
    <title>Dashboard</title>
</head>

<body>

    <?php include './header.php'; ?>
    <?php
    include_once './trimDisplayedText.php';

    // Pagination 
    $articlesPerPage = 4;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $currentPage = max($currentPage, 1);
    $totalArticles = count($articles);
    $totalPages = ceil($totalArticles / $articlesPerPage);
    $offset = ($currentPage - 1) * $articlesPerPage;

    $articlesForPage = array_slice($articles, $offset, $articlesPerPage);
    ?>

    <div class="container my-4">
        <div class="dashboard-title">
            <div>Welcome, <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>!</div>
            <div class=""><?= date('d M Y') ?></div>
        </div>

        <div class="page-heading my-4">
            <h5>Your Published Articles</h5>

            <!-- New Article Button -->
            <button class="btn btn-primary bnt-size-article" id="openArticleModal">New Article</button>
        </div>

        <ul class="list-group mb-4">
            <?php if (count($articlesForPage) > 0): ?>
                <?php foreach ($articlesForPage as $article): ?>
                    <li class="list-group-item">
                        <?php if ($article['image_url']): ?>
                            <div class="article-image">
                                <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="Article Image" style="max-width: 100px;">
                            </div>
                        <?php endif; ?>
                        <div class="article-text">
                            <div class="article-title">
                                <?= trimText(htmlspecialchars($article['title']), 70) ?>
                            </div>
                            <div class="published-content">
                                <?= nl2br(trimText(htmlspecialchars($article['content']), 400)) ?>
                            </div>
                        </div>

                        <div class="modify-article">
                            <div class="published-date">
                                (<?= date('d M Y', strtotime($article['publication_date'])) ?>)
                            </div>
                            <div class="btn-group" role="group">
                                <button type="button"
                                    class="btn btn-info btn-sm edit-article-btn"
                                    data-id="<?= $article['article_id'] ?>"
                                    data-title="<?= htmlspecialchars($article['title']) ?>"
                                    data-content="<?= htmlspecialchars($article['content']) ?>"
                                    data-image="<?= htmlspecialchars($article['image_url']) ?>">
                                    Edit
                                </button>

                                <button type="button" class="btn btn-danger btn-sm delete-article-btn" data-id="<?= $article['article_id'] ?>">Delete</button>

                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item">You have not published any articles yet.</li>
            <?php endif; ?>
        </ul>


        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $currentPage - 1 ?>">
                            < </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $currentPage + 1 ?>"> > </a></li>
                <?php endif; ?>
            </ul>
        </nav>



        <!-- Modal -->
        <div id="myModalArticle" class="modal">
            <div class="modal-content extended">
                <div class="followUsTitle">Create New Article<div class="close">X</div>
                </div>
                <form method="POST" action="save_article.php" enctype="multipart/form-data">
                    <label for="articleTitle">Title<span class="requiredField">*</span></label>
                    <input type="text" id="articleTitle" name="title" class="formField" placeholder="Article title ..." required>

                    <label for="articleContent">Content<span class="requiredField">*</span></label>
                    <textarea class="formField-area" id="articleContent" name="content" rows="7" placeholder="Article content ..." required></textarea>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <div class="upload-area1">
                            <label for="imageUpload" class="form-label">Upload Image (JPEG/JPG)</label><br>
                            <input type="file" id="imageUpload" name="image" accept="image/jpeg, image/jpg" onchange="previewImage()" />
                        </div>
                        <div class="upload-area2">
                            <img id="imagePreview" src="" alt="Image preview" style="max-width: 100px; margin-top: 10px; display: none;" />
                            <!-- Clear Image Button -->
                            <button type="button" id="clearImageBtn" style="display: none;" onclick="clearImage()"><img src="./assets/img/solar_trash-bin-trash-outline.png"> Remove</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="followUs_subscribe">Publish</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit -->
        <div id="myeditArticleModal" class="modal">
            <div class="modal-content extended">
                <div class="followUsTitle">
                    Edit Article
                    <div class="close" id="close-modal-btn">X</div>
                </div>
                <form id="editArticleForm" method="POST" action="update_article.php" enctype="multipart/form-data">
                    <input type="hidden" id="editArticleId" name="article_id">

                    <label for="editArticleTitle">Title<span class="requiredField">*</span></label>
                    <input type="text" id="editArticleTitle" class="formField" name="title" required>

                    <label for="editArticleContent">Content<span class="requiredField">*</span></label>
                    <textarea class="formField-area" id="editArticleContent" name="content" rows="7" required></textarea>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <div class="upload-area1">
                            <label for="editImageUpload" class="form-label">Upload Image (JPEG/JPG)</label>
                            <input type="file" id="editImageUpload" name="image" accept="image/jpeg, image/jpg">
                        </div>
                        <div class="upload-area2">
                            <img id="editImagePreview" src="" alt="Article Image" style="max-width: 100px; display: none;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script src="./js.js"></script>

</body>

</html>
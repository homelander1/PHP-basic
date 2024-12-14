<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.php");
    exit();
}

// Redirect if not an admin
include 'db.php';
$stmt = $pdo->prepare("SELECT role_id FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

$first_name = $_SESSION['first_name'] ?? '';
$last_name = $_SESSION['last_name'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';


$limit = 8;
$pageUsers = isset($_GET['pageUsers']) ? (int)$_GET['pageUsers'] : 1;
$offsetUsers = ($pageUsers - 1) * $limit;

// Fetch users with pagination and role name
$registeredUsers = $pdo->prepare("
    SELECT u.user_id, u.first_name, u.last_name, u.email, u.role_id, r.role_name 
    FROM users u
    LEFT JOIN user_roles r ON u.role_id = r.role_id
    LIMIT :limit OFFSET :offset
");
$registeredUsers->bindParam(':limit', $limit, PDO::PARAM_INT);
$registeredUsers->bindParam(':offset', $offsetUsers, PDO::PARAM_INT);
$registeredUsers->execute();
$registeredUsers = $registeredUsers->fetchAll(PDO::FETCH_ASSOC);

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPagesUsers = ceil($totalUsers / $limit);


$pageSubscribed = isset($_GET['pageSubscribed']) ? (int)$_GET['pageSubscribed'] : 1;
$offsetSubscribed = ($pageSubscribed - 1) * $limit;

// Fetch subscribed and registered users with pagination
$registeredAndSubscribedUsers = $pdo->prepare("
    SELECT DISTINCT u.user_id, u.first_name, u.last_name, u.email, s.subscription_date 
    FROM users u
    INNER JOIN subscribers s ON u.email = s.email
    LIMIT :limit OFFSET :offset
");
$registeredAndSubscribedUsers->bindParam(':limit', $limit, PDO::PARAM_INT);
$registeredAndSubscribedUsers->bindParam(':offset', $offsetSubscribed, PDO::PARAM_INT);
$registeredAndSubscribedUsers->execute();
$registeredAndSubscribedUsers = $registeredAndSubscribedUsers->fetchAll(PDO::FETCH_ASSOC);

// Get total number of subscribed and registered users
$totalSubscribedUsers = $pdo->query("
    SELECT COUNT(DISTINCT u.user_id)
    FROM users u
    INNER JOIN subscribers s ON u.email = s.email
")->fetchColumn();
$totalPagesSubscribed = ceil($totalSubscribedUsers / $limit);

// Pagination for All Subscribers
$pageSubscribers = isset($_GET['pageSubscribers']) ? (int)$_GET['pageSubscribers'] : 1;
$offsetSubscribers = ($pageSubscribers - 1) * $limit;

// Fetch all subscribers with pagination
$allSubscribers = $pdo->prepare("
    SELECT email, name, subscription_date
    FROM subscribers
    LIMIT :limit OFFSET :offset
");
$allSubscribers->bindParam(':limit', $limit, PDO::PARAM_INT);
$allSubscribers->bindParam(':offset', $offsetSubscribers, PDO::PARAM_INT);
$allSubscribers->execute();
$allSubscribers = $allSubscribers->fetchAll(PDO::FETCH_ASSOC);


$totalSubscribers = $pdo->query("SELECT COUNT(*) FROM subscribers")->fetchColumn();
$totalPagesSubscribers = ceil($totalSubscribers / $limit);

$limitArticles = 4;
// Pagination for articles
$pageArticles = isset($_GET['pageArticles']) ? (int)$_GET['pageArticles'] : 1;
$offsetArticles = ($pageArticles - 1) * $limitArticles;

// Fetch articles with pagination and author details
$articles = $pdo->prepare("
    SELECT 
        a.publication_date, 
        a.article_id, 
        a.title, 
        a.content, 
        a.author, 
        a.image_url, 
        u.first_name AS author_first_name, 
        u.last_name AS author_last_name, 
        u.email AS author_email 
    FROM articles a
    JOIN users u ON a.user_id = u.user_id
    LIMIT :limit OFFSET :offset
");
$articles->bindParam(':limit', $limitArticles, PDO::PARAM_INT);
$articles->bindParam(':offset', $offsetArticles, PDO::PARAM_INT);
$articles->execute();
$articles = $articles->fetchAll(PDO::FETCH_ASSOC);

$totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$totalPagesArticles = ceil($totalArticles / $limitArticles);

// active tab 
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'users';

include_once './trimDisplayedText.php';
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/supp.css">
    <link rel="stylesheet" href="./css/admincss.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="container my-4">

        <div class="dashboard-title">
            <div>Welcome, <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>!</div>
            <div class="date-and-log-out">
                <div class=""><?= date('d M Y') ?></div>
                <a href="logout.php" class="account-btn"><img src="./assets/img/logOUT.png" alt="log out"></a>
            </div>
        </div>

        <div class="container-with-tabs">
            <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= $activeTab == 'users' ? 'active' : '' ?>" href="?tab=users&pageUsers=1">All Users</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= $activeTab == 'subscribed-users' ? 'active' : '' ?>" href="?tab=subscribed-users&pageSubscribed=1">Subscribed and Registered</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= $activeTab == 'subscribers' ? 'active' : '' ?>" href="?tab=subscribers&pageSubscribers=1">All Subscribers</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?= $activeTab == 'articles' ? 'active' : '' ?>" href="?tab=articles&pageArticles=1">Articles</a>
                </li>
            </ul>
        </div>

        <div class="tab-content mt-4 container-with-tabs" id="adminTabsContent">
            <!-- All Users Tab -->
            <div class="tab-pane fade show <?= $activeTab == 'users' ? 'active' : '' ?>" id="users" role="tabpanel">
                <h4>All Registered Users</h4>
                <div class="users-list">
                    <?php foreach ($registeredUsers as $user): ?>
                        <div class="user-item d-flex justify-content-between align-items-center mb-3 p-3 border">
                            <div class="user-name">
                                <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                            </div>
                            <div class="user-email">
                                <?= htmlspecialchars($user['email']) ?>
                            </div>
                            <div class="user-role">
                                <strong><?= htmlspecialchars($user['role_name']) ?></strong> <!-- Display role_name instead of role_id -->
                            </div>
                            <div class="user-action">
                                <!-- Delete Button -->
                                <form action="delete_user.php" method="POST" onsubmit="return confirmDeletion()" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                    <input type="hidden" name="pageUsers" value="<?= isset($_GET['pageUsers']) ? $_GET['pageUsers'] : 1 ?>">
                                    <input type="hidden" name="tab" value="users">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>

                                <!-- Edit Button -->
                                <button class="btn btn-info btn-sm edit-user-btn"
                                    data-id="<?= $user['user_id'] ?>"
                                    data-firstname="<?= htmlspecialchars($user['first_name']) ?>"
                                    data-lastname="<?= htmlspecialchars($user['last_name']) ?>"
                                    data-role="<?= htmlspecialchars($user['role_id']) ?>"
                                    data-email="<?= htmlspecialchars($user['email']) ?>">Edit</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination for Users -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($pageUsers > 1): ?>
                            <li class="page-item"><a class="page-link" href="?tab=users&pageUsers=<?= $pageUsers - 1 ?>"> &lt; </a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPagesUsers; $i++): ?>
                            <li class="page-item <?= $i == $pageUsers ? 'active' : '' ?>">
                                <a class="page-link" href="?tab=users&pageUsers=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pageUsers < $totalPagesUsers): ?>
                            <li class="page-item"><a class="page-link" href="?tab=users&pageUsers=<?= $pageUsers + 1 ?>"> &gt; </a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>

            <!-- Subscribed Users Tab -->
            <div class="tab-pane fade <?= $activeTab == 'subscribed-users' ? 'show active' : '' ?>" id="subscribed-users" role="tabpanel">
                <h4>Subscribed and Registered</h4>
                <div class="subscribed-users-list">
                    <?php foreach ($registeredAndSubscribedUsers as $user): ?>
                        <div class="user-item d-flex justify-content-between align-items-center mb-3 p-3 border">
                            <div class="user-name">
                                <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                            </div>
                            <div class="user-email">
                                <?= htmlspecialchars($user['email']) ?>
                            </div>
                            <div class="subscription-date">
                                <?= htmlspecialchars($user['subscription_date']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination for Subscribed Users -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($pageSubscribed > 1): ?>
                            <li class="page-item"><a class="page-link" href="?tab=subscribed-users&pageSubscribed=<?= $pageSubscribed - 1 ?>"> &lt; </a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPagesSubscribed; $i++): ?>
                            <li class="page-item <?= $i == $pageSubscribed ? 'active' : '' ?>">
                                <a class="page-link" href="?tab=subscribed-users&pageSubscribed=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pageSubscribed < $totalPagesSubscribed): ?>
                            <li class="page-item"><a class="page-link" href="?tab=subscribed-users&pageSubscribed=<?= $pageSubscribed + 1 ?>"> &gt; </a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <!-- All Subscribers Tab -->
            <div class="tab-pane fade <?= $activeTab == 'subscribers' ? 'show active' : '' ?>" id="subscribers" role="tabpanel">
                <h4>All Subscribers</h4>
                <div class="subscribed-users-list">
                    <?php foreach ($allSubscribers as $subscriber): ?>
                        <div class="user-item d-flex justify-content-between align-items-center mb-3 p-3 border">
                            <div class="user-name">
                                <strong><?= htmlspecialchars($subscriber['name']) ?></strong>
                            </div>
                            <div class="user-email">
                                <?= htmlspecialchars($subscriber['email']) ?>
                            </div>
                            <div class="subscription-date">
                                <?= htmlspecialchars($subscriber['subscription_date']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination for All Subscribers -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($pageSubscribers > 1): ?>
                            <li class="page-item"><a class="page-link" href="?tab=subscribers&pageSubscribers=<?= $pageSubscribers - 1 ?>"> &lt; </a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPagesSubscribers; $i++): ?>
                            <li class="page-item <?= $i == $pageSubscribers ? 'active' : '' ?>">
                                <a class="page-link" href="?tab=subscribers&pageSubscribers=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pageSubscribers < $totalPagesSubscribers): ?>
                            <li class="page-item"><a class="page-link" href="?tab=subscribers&pageSubscribers=<?= $pageSubscribers + 1 ?>"> &gt; </a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>


            <!-- Articles Tab -->
            <div class="tab-pane fade <?= $activeTab == 'articles' ? 'show active' : '' ?>" id="articles" role="tabpanel">
                <h4>All Published Articles</h4>
                <ul class="list-group mb-4 admin-article-group">
                    <?php foreach ($articles as $article): ?>
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
                                <div class="published-date admin-published-date">
                                    (<?= date('d M Y', strtotime($article['publication_date'])) ?>)
                                </div>
                                <div class="article-author">
                                    <small>
                                        <i>Author:</i><br><strong><?= htmlspecialchars($article['author_first_name']) ?><br> <?= htmlspecialchars($article['author_last_name']) ?>
                                        </strong>
                                    </small>
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
                </ul>

                <!-- Pagination for Articles -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($pageArticles > 1): ?>
                            <li class="page-item"><a class="page-link" href="?tab=articles&pageArticles=<?= $pageArticles - 1 ?>"> &lt; </a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPagesArticles; $i++): ?>
                            <li class="page-item <?= $i == $pageArticles ? 'active' : '' ?>">
                                <a class="page-link" href="?tab=articles&pageArticles=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pageArticles < $totalPagesArticles): ?>
                            <li class="page-item"><a class="page-link" href="?tab=articles&pageArticles=<?= $pageArticles + 1 ?>"> &gt; </a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="myModalEditUser" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="followUsTitle">Edit User
                <div class="close" onclick="closeModal()">X</div>
            </div>
            <form id="editUserForm" action="update_user.php" method="POST">
                <input type="hidden" name="form_type" value="edit_user">
                <input type="hidden" name="user_id">

                <label>First Name<span class="requiredField">*</span></label>
                <input class="formField" type="text" name="first_name" placeholder="First name" required>

                <label>Last Name<span class="requiredField">*</span></label>
                <input class="formField" type="text" name="last_name" placeholder="Last name" required>

                <label>Role<span class="requiredField">*</span></label>
                <select class="formField" name="role" required>
                    <option value="1">Admin</option>
                    <option value="2">Writer</option>
                </select>

                <button class="followUs_subscribe" type="submit">SAVE CHANGES</button>
            </form>
        </div>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>

    <script src="./edit_user.js"></script>


</body>

</html>
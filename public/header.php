<header>
    <nav>
        <a class="logo" href="index.php">MNTN</a>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="equipment.php">Equipment</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>

            <?php if (basename($_SERVER['PHP_SELF']) == 'user.php'): ?>

                <a href="logout.php" class="account-btn"><img src="./assets/img/logOUT.png" alt="log out"></a>
            <?php else: ?>

                <a href="user.php" class="account-btn">
                    <img src="./assets/img/account.png" alt="Account"></a>
            <?php endif; ?>
        <?php else: ?>

            <button type="button" id="openLoginModal" class="log-reg">Log in</button>
        <?php endif; ?>
    </nav>
</header>
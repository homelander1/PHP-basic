<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/styles.css">
    <title>MNTN</title>
</head>

<body class="gradient-background">


    <div class="wrapper">
        <?php include './header.php'; ?>

        <div class="hero">
            <div class="hero-info">
                <div class="hiking-guide"><img src="/assets/img/Rectangle 2.1.png"></div>
                <h1>Be Prepared For The Mauntains And Beyond!</h1>
                <div class="scrollDown" id="scrollButton"><a href="#"><img src="/assets/img/scrollDown.png" alt="scroll"></a></div>

            </div>
        </div>
    </div>


    <?php include './socialMedia.php'; ?>
    <?php include './followUsForm.php'; ?>
    <?php
    include_once './ContactUs.php';
    include_once './registration.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['form_type']) && $_POST['form_type'] === 'subscribe') {
            processForm($_POST);
        } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'registration') {
            registrationForm($_POST);
        } elseif (isset($_POST['login'])) {
            loginUser($_POST);
        }
    }

    ?>
    <?php include './logInSignUpForm.php'; ?>
    <?php include './index-articles.php'; ?>
    <?php include './footer.php'; ?>


    <script src="./js.js"></script>

</body>

</html>
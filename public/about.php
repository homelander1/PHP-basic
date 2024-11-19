<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/about.css">
    <title>About</title>
</head>

<body>


    <?php include './header.php'; ?>
    <?php include './logInSignUpForm.php'; ?>
    <?php
    include_once './ContactUs.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['form_type']) && $_POST['form_type'] === 'subscribe') {
            processForm($_POST);
        }
    }

    ?>

    <div class="social-block">
        <ul>
            <li><button type="button" id="openFollowUsModal" class="followUs"><img src="/assets/img/Follow us.png"></button></li>
            <li><a href="https://www.instagram.com/accounts/login/" target="_blank"><img src="/assets/img/instagram.png"></a></li>
            <li><a href="https://x.com/i/flow/login" target="_blank"><img src="/assets/img/twitter.png"></a></li>

        </ul>
    </div>


    <?php include './followUsForm.php'; ?>


    <div class="article2">
        <!-- section 01 -->
        <div class="get_started">

            <div class="section_group">
                <div class="section_content2">

                    <h1>About MNTN</h1>
                    <p>We believe hiking is more than just an outdoor activity; it’s a path to self-discovery, strength, and serenity. Each trail tells its own story, and with every climb, every view, and every breath of fresh mountain air, we’re reminded of the incredible resilience within us and the beauty of the world around us. At MNTN, our mission is to empower and inspire everyone to embrace the journey, no matter the terrain or challenge.</p>
                    <p>
                        Founded by mountain enthusiasts and outdoor adventurers, MNTN is built on the belief that the mountains hold lessons, strength, and peace that we can all carry into our daily lives. We’re committed to providing gear, guidance, and support that help you safely and confidently explore new heights. From beginners finding their footing to experienced hikers pushing their limits, we’re here to equip you for every adventure, with a community ready to share in the wonders and challenges of the trail. Together, let's climb further, reach higher, and find new horizons.
                    </p>

                </div>
            </div>
            <img class="main_photo" src="/assets/img/about.png" alt="">
        </div>
    </div>
    <?php include './footer.php'; ?>

    <script src="./js.js"></script>

</body>

</html>
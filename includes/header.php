<?php
require 'config/config.php';
include 'includes/classes/User.php';
include 'includes/classes/Post.php';

if(isset($_SESSION['username'])){
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
}
else {
    header("Location:register.php");
}
?>


<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SocialNetwork</title>
    <!-- Javascript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/main.js"></script>

    <!--  CSS  -->
    <script src="https://kit.fontawesome.com/8b16d5a62c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
    <div class="top_bar">

        <div class="logo">
            <a href="index.php">Social Network</a>
        </div>

        <nav>
            <a href="<?= $userLoggedIn?>">
                <?php echo $user['firstname'] ?>
            </a>
            <a href="index.php">
                <i class="fa fa-home"></i>
            </a>

            <a href="#">
                <i class="fa fa-enveloppe"></i>
            </a>
            <a href="#">
                <i class="fa fa-bell-o"></i>
            </a>
            <a href="request.php">
                <i class="fa fa-users"></i>
            </a>
            <a href="#">
                <i class="fa fa-cog"></i>
            </a>
            <a href="includes/handlers/logout.php">
                <i class="fa fa-sign-out"></i>
            </a>

        </nav>

    </div>

    <div class="wrapper">


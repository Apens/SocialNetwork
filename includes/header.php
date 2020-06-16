<?php
require 'config/config.php';
include 'includes/classes/User.php';
include 'includes/classes/Post.php';
include 'includes/classes/Message.php';

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
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/jcrop_bits.js"></script>
    <script src="assets/js/jquery.Jcrop.js"></script>
    <script src="assets/js/main.js"></script>

    <!--  CSS  -->
    <script src="https://kit.fontawesome.com/8b16d5a62c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/css/jquery.Jcrop.css">
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

            <a href="javascript:void(0);" onclick="getDropdownData(<?= $userLoggedIn?>, 'message')">
                <i class="fa fa-envelope"></i>
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

        <div class="dropdown_data_window">
            <input type="hidden" id="dropdown_data_type" value="">
        </div>

    </div>

    <div class="wrapper">


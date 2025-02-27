<?php
require 'config/config.php';
include 'includes/classes/User.php';
include 'includes/classes/Post.php';
include 'includes/classes/Message.php';
include 'includes/classes/Notification.php';

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
<!--    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>-->
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

        <div class="search">
            <form action="search.php" method="GET" name="search_form">
                <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?= $userLoggedIn ?>')" name="q" placeholder="Chercher..." autocomplete="off" id="search_text_input">
                <div class="button_holder">
                    <img src="https://svgsilh.com/svg_v2/303408.svg" alt="">
                </div>
            </form>
            <div class="search_results">

            </div>
            <div class="search_results_footer_empty">

            </div>
        </div>

        <nav>
            <?php
            //message non-lu
            $messages = new Message($con, $userLoggedIn);
            $num_messages = $messages->getUnreadNumber();


            //notifications non-vu
            $notification = new Notification($con, $userLoggedIn);
            $num_notification = $notification->getUnreadNumber();

            //demandes d'ami non-vu
            $user_obj = new User($con, $userLoggedIn);
            $num_request = $user_obj->getNumberOfFriendRequest();




            ?>

            <a href="<?= $userLoggedIn?>">
                <?php echo $user['firstname'] ?>
            </a>
            <a href="index.php">
                <i class="fa fa-home"></i>
            </a>

            <a href="javascript:void(0);" onclick="getDropdownData('<?= $userLoggedIn; ?>','message')">
                <i class="fa fa-envelope"></i>
                <?php
                if ($num_messages > 0)
                    echo '<span class="notification_badge" id="unread_message">'.$num_messages. '</span>';
                ?>

            </a>
            <a href="javascript:void(0);" onclick="getDropdownData('<?= $userLoggedIn; ?>','notification')">
                <i class="fa fa-bell-o"></i>
                <?php
                if ($num_notification > 0)
                    echo '<span class="notification_badge" id="unread_notification">'.$num_notification. '</span>';
                ?>
            </a>
            <a href="request.php">
                <i class="fa fa-users"></i>
                <?php
                if ($num_request > 0)
                    echo '<span class="notification_badge" id="unread_notification">'.$num_request. '</span>';
                ?>
            </a>
            <a href="#">
                <i class="fa fa-cog"></i>
            </a>
            <a href="includes/handlers/logout.php">
                <i class="fa fa-sign-out"></i>
            </a>

        </nav>

        <div class="dropdown_data_window" style="height: 0px; border:none;"></div>
        <input type="hidden" id="dropdown_data_type" value="">

    </div>

    <script>
        var userLoggedIn = '<?= $userLoggedIn; ?>';

        $(document).ready(function () {

            $('.dropdown_data_window').scroll(function () {
                var inner_height = $('.dropdown_data_window').innerHeight(); // la div contenant les posts
                var scroll_top = $('.dropdown_data_window').scrollTop();
                var page = $('.dropdown_data_window').find('.nextPageDropDownData').val();
                var noMoreData = $('.dropdown_data_window').find('.noMoreDropDownData').val();


                if ((scroll_top + inner_height +.61 >= $('.dropdown_data_window')[0].scrollHeight)&& noMoreData === 'false') {
                    var pageName; //Contient le nom de la page à envoyer à la requete Ajax
                    var type = $('#dropdown_data_type').val();

                    if(type === 'notification')
                        pageName= "ajax_load_notification.php";
                    else if (type === 'message')
                        pageName= 'ajax_load_messages.php';

                    var ajaxReq = $.ajax({
                        url: "includes/handlers/" + pageName, //on rensigne l'url qui traitera les données
                        type: "POST", // On renseigne la methode
                        data: "page="+ page +"&userLoggedIn=" + userLoggedIn, //REQUEST d'ajax
                        cache: false,

                        success: function (response) {
                            $('.dropdown_data_window').find('.nextPageDropDownData').remove();
                            $('.dropdown_data_window').find('.noMoreDropDownData').remove();

                            $('.dropdown_data_window').append(response) //on ajoute d'autres données collectées dans la div

                        }
                    });

                } // Fin IF
                return false;

            }); // Fin (dropdown_data_window).scroll function

        });
    </script>

    <div class="wrapper">



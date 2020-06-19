<?php
require 'config/config.php';
include 'includes/classes/User.php';
include 'includes/classes/Post.php';
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
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
    <style type="text/css">
        *{
            font-size: 12px;
            font-family: Arial, Helvetica, Sans-serif;
        }

    </style>


    <script>
        function toggle() {
            var element= document.getElementById("comment_section");

            if(element.style.display === "block")
                element.style.display = "none";
            else
                element.style.display = "block";
        }
    </script>

    <?php
    // Get id Post
    if (isset($_GET['post_id'])){
        $post_id = $_GET['post_id'];
    }
    $user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id = '$post_id'");
    $row = mysqli_fetch_array($user_query);

    $posted_to = $row['added_by'];
    $user_to = $row['user_to'];

    if (isset($_POST['postComment'.$post_id])){
        $post_body = $_POST['post_body'];
        $post_body = mysqli_escape_string($con, $post_body);
        $date_time_now = date("Y-m-d H:i:s");
        $insert_post = mysqli_query($con,"INSERT INTO comments (post_body, posted_by, posted_to, date_added, removed, post_id ) 
                                                VALUES ('$post_body','$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");
        if ($posted_to != $userLoggedIn){
            var_dump($row);
            var_dump($posted_to);
            $notification = new Notification($con, $userLoggedIn);
            $notification->insertNotification($post_id, $posted_to, "comment" );
        }

        if ( $user_to != 'none' && $user_to != $userLoggedIn) {
            $notification = new Notification($con, $userLoggedIn);
            $notification->insertNotification($post_id, $user_to, "profile_comment" );
        }

        $get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id= '$post_id'");
        $notified_users = [];
        while($row = mysqli_fetch_array($get_commenters)){
            if ($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to
                && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users) ){

                $notification = new Notification($con, $userLoggedIn);
                $notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner" );

                array_push($notified_users, $row['posted_by']);
            }
        }

        echo "<p> Commentaire envoyé ! </p>";

    }


    ?>

    <form action="comment_frame.php?post_id=<?= $post_id; ?>" method="POST" name="postComment<?= $post_id; ?>" id="comment_form">
        <textarea name="post_body"></textarea>
        <input type="submit"name="postComment<?= $post_id; ?>" value="Post">
    </form>

<!--Charge les commentaires-->

    <?php
        $get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
        $count = mysqli_num_rows($get_comments);

        if($count != 0) {

            while ($comment = mysqli_fetch_array($get_comments)){
                $comment_body = $comment['post_body'];
                $posted_to = $comment['posted_to'];
                $posted_by = $comment['posted_by'];
                $date_added = $comment['date_added'];
                $removed = $comment['removed'];


                //Capture du temps
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_added); // date et heure du post
                $end_date = new DateTime($date_time_now);// date et heure actuelle
                $interval = $start_date->diff($end_date);// difference entre les 2 dates précedentes
                if ($interval->y >= 1) {
                    if ($interval == 1)
                        $time_message = "Il y a ".$interval->y. " an"; // "il y a 1 an"
                    else
                        $time_message = "Il y a ".$interval->y. " ans";// "il y a X ans"
                }
                elseif ($interval->m >= 1 ){
                    if($interval->d == 0){
                        $days = "Il y a ";
                    }
                    elseif ($interval->d == 1){
                        $days =  "Il y a ".$interval->d." jour";
                    }
                    else {
                        $days = "Il y a ".$interval->d." jours";
                    }

                    if($interval->m == 1 ){
                        $time_message = $days.$interval->m." mois";
                    }
                    else {
                        $time_message = $days.$interval->m." mois";
                    }

                }
                elseif ($interval->d >= 1 ){
                    if ($interval->d == 1){
                        $time_message =  "Hier";
                    }
                    else {
                        $time_message = "Il y a ".$interval->d." jours";
                    }
                }
                elseif ($interval->h >= 1){
                    if ($interval->h == 1){
                        $time_message =  "Il y a ".$interval->h." heure";
                    }
                    else {
                        $time_message = "Il y a ".$interval->h." heures";
                    }
                }
                elseif ($interval->i >= 1){
                    if ($interval->i == 1){
                        $time_message =  "Il y a ".$interval->i." minute";
                    }
                    else {
                        $time_message = "Il y a ".$interval->i." minutes";
                    }
                }
                else{
                    if ($interval->s < 30){
                        $time_message =  "A l'instant";
                    }
                    else {
                        $time_message = "Il y a ".$interval->s." secondes";
                    }
                }

                $user_obj = new User($con, $posted_by);

                ?>

                <div class="comment_section">
                    <a href=" <?= $posted_by; ?>" target="_parent"> <img src="<?= $user_obj->getProfilePic(); ?>" title="<?= $posted_by; ?>" style="float: left" height="30" alt=""></a>
                    <a href=" <?= $posted_by; ?>" target="_parent"> <b><?= $user_obj->getFirstAndLastname() ?></b> </a>
                    &nbsp;&nbsp;&nbsp;&nbsp; <?= $time_message. "<br>" .$comment_body; ?>
                    <hr>
                </div>

                <?php
            }
        }
        else {
            echo "<div class='comment_section'>
                        <p style='text-align: center'>
                            <br><br>Pas de commentaires !
                        </p>
                  </div>";
        }
    ?>


</body>
</html>



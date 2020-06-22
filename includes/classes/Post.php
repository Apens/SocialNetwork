<?php


class Post
{
    private $user_obj;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function submitPost($body, $user_to)
    {
        $body = strip_tags($body);
        $body = mysqli_real_escape_string($this->con, $body);

        //Ces 2 lignes permettent de garder le format du message
        $body = str_replace('\r\n', '\n',$body); // \r\n = retour chariot (entrée à la ligne ); \n = nouvelle ligne
        $body = nl2br($body); // Remplace les nouvelles lignes par des <br>

        $chek_empty = preg_replace('/\s+/', '',$body); // supprime les espaces

        if($chek_empty != ""){

            // date et heure du post
            $date_added = date("Y-m-d H:i:s");
            // Get Username
            $added_by= $this->user_obj->getUsername();
        }

        // Si l'utilisateur est sur son propre profile, user_to est "none"
        if($user_to == $added_by){
            $user_to = "none";
        }

        // insert post
        $query = mysqli_query($this->con, "INSERT INTO posts (body, added_by, user_to, date_added, user_closed, deleted, likes) VALUES ('$body','$added_by', '$user_to', '$date_added', 'no', 'no', '0')");
        $returned_id= mysqli_insert_id($this->con);

         //Notification
        if ($user_to != 'none') {
            $notification = new Notification($this->con, $added_by);
            $notification->insertNotification($returned_id, $user_to, "profile_post" );

        }

        //Mise a jour du nombre de post d'utilisateur
        $num_posts = $this->user_obj->getNumPosts();
        $num_posts++;
        $update_query = mysqli_query($this->con, "UPDATE users SET num_post='$num_posts'WHERE username='$added_by'");

        return ;
    }

    public function loadPostsFriends($data, $limit)
    {
        $page = $data['page'];
        $userLoggedIn= $this->user_obj->getUsername();

        if($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;


        $str = ""; //Le string à retourner
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

        if(mysqli_num_rows($data_query) > 0){

            $num_iteration = 0; // nombre de resultat retournée
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                // Preparation du string "user_to" pour que le post soit inclus meme s'il n'a pas été posté a un utilisateur
                if($row['user_to'] == "none"){
                    $user_to = "";
                }
                else {
                    $user_to_obj = new User($this->con, $row['user_to']);
                    $user_to_name = $user_to_obj->getFirstAndLastname();
                    $user_to = " à <a href='".$row['user_to']."'>" . $user_to_name . "</a>";
                }

                //On verifie que le compte de l'utilisateur ayant posté ne soit pas cloturé
                $added_by_obj = new User($this->con, $row['added_by']);
                if($added_by_obj->isClosed()){
                    continue;
                }

                $user_logged_obj = new User($this->con, $userLoggedIn);
                if ($user_logged_obj->isFriend($added_by)){

                    if($num_iteration++ < $start)
                        continue;

                    // Une fois les 10 posts chargé break
                    if($count > $limit){
                        break;
                    }
                    else{
                        $count++;
                    }

                    if($userLoggedIn == $added_by)
                        $delete_button = "<button class= 'delete_button btn-danger' id= 'post$id'>X</button>";
                    else
                        $delete_button = "";

                    $user_detail_query = mysqli_query($this->con, "SELECT firstname, lastname, profile_pic FROM users WHERE username = '$added_by'");
                    $user_row = mysqli_fetch_array($user_detail_query);
                    $firstname = $user_row['firstname'];
                    $lastname = $user_row['lastname'];
                    $profile_pic = $user_row['profile_pic'];


                    ?>
                    <script>
                        function toggle<?= $id; ?>() {

                            // var event:Event ;

                            var target = $(event.target);
                            if (!target.is("a")){

                                var element= document.getElementById("toggleComment<?= $id; ?>");

                                if(element.style.display === "block")
                                    element.style.display = "none";
                                else
                                    element.style.display = "block";
                            }
                        }

                    </script>
                    <?php

                    $comment_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
                    $comment_check_num = mysqli_num_rows($comment_check);

                    //Capture du temps
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time); // date et heure du post
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

                    $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                        
                        <div class= 'post_profile_pic'>
                            
                            <img src='$profile_pic' width='50'>
                        </div>
                        
                        <div class='posted_by' style='color: #ACACAC;'>
                            <a href='$added_by'>$firstname $lastname</a>
                            &nbsp;$user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                            $delete_button
                        
                        </div>
                        <div id='post_body'>
                            $body
                            <br>
                            <br>
                            <br>
                        </div>
                        
                        <div class='newsfeedPostOptions'>
                            Commentaires($comment_check_num)&nbsp;&nbsp;&nbsp;&nbsp;
                            <iframe src='like.php?post_id= $id' frameborder='0' scrolling='no'></iframe>
                        </div>
                        
                    </div>
                    <div class='post_comment' id='toggleComment$id' style='display: none;'>
                        <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                    </div>
                    <hr>";
                }
                ?>
                <script>
                    $(document).ready(function () {
                        $('#post<?= $id; ?>').on('click', function () {
                            bootbox.confirm("ètes vous sur de vouloir supprimer ce post ?", function (result) {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?= $id; ?>", {result:result});

                                if(result)
                                    location.reload()
                            });
                        });
                    });
                </script>
                <?php

            }


            if ($count > $limit)
                $str .= "<input type='hidden' class='nextPage' value='".($page + 1)."'>
                         <input type='hidden' class='noMorePosts' value='false'>";
            else
                $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'> Plus d'autres Posts</p>";

        }// fin if data_query rows


        echo $str;
    }

    public function loadProfilePosts($data, $limit)
    {
        $page = $data['page'];
        $profileUser= $data['profileUsername'];
        $userLoggedIn= $this->user_obj->getUsername();

        if($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;


        $str = ""; //Le string à retourner
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by = '$profileUser' AND user_to = 'none') OR user_to = '$profileUser') ORDER BY id DESC");

        if(mysqli_num_rows($data_query) > 0){

            $num_iteration = 0; // nombre de resultat retournée
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];


                    if($num_iteration++ < $start)
                        continue;

                    // Une fois les 10 posts chargé break
                    if($count > $limit){
                        break;
                    }
                    else{
                        $count++;
                    }

                    if($userLoggedIn == $added_by)
                        $delete_button = "<button class= 'delete_button btn-danger' id= 'post$id'>X</button>";
                    else
                        $delete_button = "";

                    $user_detail_query = mysqli_query($this->con, "SELECT firstname, lastname, profile_pic FROM users WHERE username = '$added_by'");
                    $user_row = mysqli_fetch_array($user_detail_query);
                    $firstname = $user_row['firstname'];
                    $lastname = $user_row['lastname'];
                    $profile_pic = $user_row['profile_pic'];


                    ?>
                    <script>
                        function toggle<?= $id; ?>() {

                            // var event:Event ;

                            var target = $(event.target);
                            if (!target.is("a")){

                                var element= document.getElementById("toggleComment<?= $id; ?>");

                                if(element.style.display === "block")
                                    element.style.display = "none";
                                else
                                    element.style.display = "block";
                            }
                        }

                    </script>
                    <?php

                    $comment_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
                    $comment_check_num = mysqli_num_rows($comment_check);

                    //Capture du temps
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time); // date et heure du post
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

                    $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                        
                        <div class= 'post_profile_pic'>
                            
                            <img src='$profile_pic' width='50'>
                        </div>
                        
                        <div class='posted_by' style='color: #ACACAC;'>
                            <a href='$added_by'>$firstname $lastname</a>
                            &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                            $delete_button
                        
                        </div>
                        <div id='post_body'>
                            $body
                            <br>
                            <br>
                            <br>
                        </div>
                        
                        <div class='newsfeedPostOptions'>
                            Commentaires($comment_check_num)&nbsp;&nbsp;&nbsp;&nbsp;
                            <iframe src='like.php?post_id= $id' frameborder='0' scrolling='no'></iframe>
                        </div>
                        
                    </div>
                    <div class='post_comment' id='toggleComment$id' style='display: none;'>
                        <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                    </div>
                    <hr>";

                ?>
                <script>
                    $(document).ready(function () {
                        $('#post<?= $id; ?>').on('click', function () {
                            bootbox.confirm("ètes vous sur de vouloir supprimer ce post ?", function (result) {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?= $id; ?>", {result:result});

                                if(result)
                                    location.reload()
                            });
                        });
                    });
                </script>
                <?php

            }


            if ($count > $limit)
                $str .= "<input type='hidden' class='nextPage' value='".($page + 1)."'>
                         <input type='hidden' class='noMorePosts' value='false'>";
            else
                $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'> Plus d'autres Posts</p>";

        }// fin if data_query rows


        echo $str;
    }

    public function getSinglePost($post_id){

        $userLoggedIn= $this->user_obj->getUsername();

        $opened_query= mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to = '$userLoggedIn' AND link LIKE '%=$post_id'");


        $str = ""; //Le string à retourner
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id= '$post_id'");

        if(mysqli_num_rows($data_query) > 0){

                $row = mysqli_fetch_array($data_query);
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                // Preparation du string "user_to" pour que le post soit inclus meme s'il n'a pas été posté a un utilisateur
                if($row['user_to'] == "none"){
                    $user_to = "";
                }
                else {
                    $user_to_obj = new User($this->con, $row['user_to']);
                    $user_to_name = $user_to_obj->getFirstAndLastname();
                    $user_to = " à <a href='".$row['user_to']."'>" . $user_to_name . "</a>";
                }

                //On verifie que le compte de l'utilisateur ayant posté ne soit pas cloturé
                $added_by_obj = new User($this->con, $row['added_by']);
                if($added_by_obj->isClosed()){
                    return;
                }

                $user_logged_obj = new User($this->con, $userLoggedIn);
                if ($user_logged_obj->isFriend($added_by)){


                    if($userLoggedIn == $added_by)
                        $delete_button = "<button class= 'delete_button btn-danger' id= 'post$id'>X</button>";
                    else
                        $delete_button = "";

                    $user_detail_query = mysqli_query($this->con, "SELECT firstname, lastname, profile_pic FROM users WHERE username = '$added_by'");
                    $user_row = mysqli_fetch_array($user_detail_query);
                    $firstname = $user_row['firstname'];
                    $lastname = $user_row['lastname'];
                    $profile_pic = $user_row['profile_pic'];


                    ?>
                    <script>
                        function toggle<?= $id; ?>() {

                            // var event:Event ;

                            var target = $(event.target);
                            if (!target.is("a")){

                                var element= document.getElementById("toggleComment<?= $id; ?>");

                                if(element.style.display === "block")
                                    element.style.display = "none";
                                else
                                    element.style.display = "block";
                            }
                        }

                    </script>
                    <?php

                    $comment_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
                    $comment_check_num = mysqli_num_rows($comment_check);

                    //Capture du temps
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time); // date et heure du post
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

                    $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                        
                        <div class= 'post_profile_pic'>
                            
                            <img src='$profile_pic' width='50'>
                        </div>
                        
                        <div class='posted_by' style='color: #ACACAC;'>
                            <a href='$added_by'>$firstname $lastname</a>
                            &nbsp;$user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                            $delete_button
                        
                        </div>
                        <div id='post_body'>
                            $body
                            <br>
                            <br>
                            <br>
                        </div>
                        
                        <div class='newsfeedPostOptions'>
                            Commentaires($comment_check_num)&nbsp;&nbsp;&nbsp;&nbsp;
                            <iframe src='like.php?post_id= $id' frameborder='0' scrolling='no'></iframe>
                        </div>
                        
                    </div>
                    <div class='post_comment' id='toggleComment$id' style='display: none;'>
                        <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                    </div>
                    <hr>";

                ?>
                <script>
                    $(document).ready(function () {
                        $('#post<?= $id; ?>').on('click', function () {
                            bootbox.confirm("ètes vous sur de vouloir supprimer ce post ?", function (result) {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?= $id; ?>", {result:result});

                                if(result)
                                    location.reload()
                            });
                        });
                    });
                </script>
                <?php
                }
                else {
                    echo "Vous ne pouvez voir ce post, vous n'etes pas l'ami de cette personne";
                    return;
                }
        }// fin if data_query rows
        else {
            echo "<p>Post introuvable, le liens est peut etre cassé</p>";
            return;
        }
        echo $str;
    }
}
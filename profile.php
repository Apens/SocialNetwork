<?php
include 'includes/header.php';


if (isset($_GET['profile_username'])){
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username ='$username'");
    $user_array = mysqli_fetch_array($user_details_query);

    $num_friends = substr_count($user_array['friend_array'], ",")-1;
}
if (isset($_POST['remove_friend'])){
    $user = new User($con, $userLoggedIn);
    $user->removeFriend($username);
}
if (isset($_POST['add_friend'])){
    $user = new User($con, $userLoggedIn);
    $user->sendRequest($username);
}
if (isset($_POST['respond_request'])){
    header("Location: request.php");
}


?>

        <style>
            .wrapper {
                margin-left: 0;
                padding-left: 0;
            }
        </style>

        <div class="profile_left">
            <img src="<?= $user_array['profile_pic']; ?>" alt="">

            <div class="profile_info">
                <p>
                    <?= "Posts: ".$user_array['num_post']; ?>
                </p>
                <p>
                    <?= "likes: ".$user_array['num_likes']; ?>
                </p>
                <p>
                    <?= "Friends: ".$num_friends; ?>
                </p>
            </div>
            <form action="<?= $username;?>" method="POST">
                <?php
                    $profile_user_obj = new User($con, $username);

                    if ($profile_user_obj->isClosed()){
                        header("Location: user_closed.php");
                    }

                    $logged_in_user_obj = new User($con, $userLoggedIn);
                    if($userLoggedIn != $username){

                        if($logged_in_user_obj->isFriend($username)){
                            echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
                        }
                        elseif ($logged_in_user_obj->didReceiveRequest($username)) {
                            echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
                        }
                        elseif ($logged_in_user_obj->didSendRequest($username)) {
                            echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
                        }
                        else {
                            echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
                        }

                    }
                ?>
            </form>
            <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something">

            <?php
            if($userLoggedIn != $username) {
                echo '<div class="profile_info_bottom">';
                echo $logged_in_user_obj->getMutualFriends($username). "&nbsp;Amis en commun";
                echo '</div>';
            }
            ?>
        </div> <!-- /profile left -->

        <div class="profile_main_column column">

            <ul class="nav nav-tabs" role="tablist" id="profiletabs">
                <li class="nav-item">
                    <a class="nav-link active" href="newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Flux d'actualité</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about_div" aria-controls="about_div" role="tab" data-toggle="tab">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="message_div" aria-controls="message_div" role="tab" data-toggle="tab">Messages</a>
                </li>
            </ul>

            <div class="tab_content">
                <div role="tabpanel" class="tab-pane fade show active" id="newsfeed_div">
                    <div class="posts_area"></div>
                    <img id="loading" src="assets/images/icons/loading.gif" alt="">
                </div>

                <div role="tabpanel" class="tab-pane fade" id="about_div">

                </div>

                <div role="tabpanel" class="tab-pane fade" id="message_div">
                    <?php
                    $message_obj = new Message($con, $userLoggedIn);

                        echo "<h4><a href='".$username."'>". $profile_user_obj->getFirstAndLastname() ."</a> et toi </h4><hr><br>";
                        echo "<div class='loaded_messages' id='scroll_messages'>";
                        echo $message_obj->getMessage($username);
                        echo "</div>";
                    ?>

                    <div class="messages_post">
                        <form action="" method="POST">
                             <textarea name='message_body' id='message_textarea' placeholder='Ecris ton message...'></textarea>
                             <input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
                        </form>
                    </div>

                    <script>
                        //todo: finir l'implantation des tabs ( https://getbootstrap.com/docs/4.0/components/navs/#tabs )
                        // $.('#profiletabs a').on('click', function (e){
                        //     // e.preventDefault();
                        //     // $(this).tab('show');
                        // })

                        let div = document.getElementById('scroll_messages');
                        if (div !== null){
                            div.scrollTop = div.scrollHeight;
                        }


                    </script>
                </div>
            </div>

        </div> <!-- fin main_column -->

        <!-- Modal -->
        <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ecrire un truc !</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Ceci apparaitra sur le profil de l'utilisateur ainsi que son flux d'actualité</p>

                        <form action="" method="POST" class="profile_post">
                            <div class="form-group">
                                <textarea name="post_body" id="" cols="30" rows="10" class="form-control"></textarea>
                                <input type="hidden" name="user_from" value="<?= $userLoggedIn ?>">
                                <input type="hidden" name="user_to" value="<?= $username ?>">

                            </div>
                        </form>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Envoyer</button>
                    </div>
                </div>
            </div>
        </div> <!-- /Modal -->

        <script>
            var userLoggedIn = '<?= $userLoggedIn; ?>';
            var profileUsername = '<?= $username; ?>'

            $(document).ready(function () {
                $('#loading').show();

                // Requête initiale ajax pour charger les premiers post
                $.ajax({
                    url: "includes/handlers/ajax_load_profile_post.php", //on rensigne l'url qui traitera les données
                    type: "POST", // On renseigne la methode
                    data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                    cache: false,

                    success: function (data) {
                        $('#loading').hide();
                        $('.posts_area').html(data) //on envoie les données collectées dans la div

                    }
                });

                $(window).scroll(function () {
                    var height = $('.posts_area').height(); // la div contenant les posts
                    var scroll_top = $(this).scrollTop();
                    var page = $('.posts_area').find('.nextPage').val();
                    var noMorePosts = $('.posts_area').find('.noMorePosts').val();

                    if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight)&& noMorePosts == 'false') {
                        $('#loading').show();

                        var ajaxReq = $.ajax({
                            url: "includes/handlers/ajax_load_profile_post.php", //on rensigne l'url qui traitera les données
                            type: "POST", // On renseigne la methode
                            data: "page="+ page +"&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername, //REQUEST d'ajax
                            cache: false,

                            success: function (response) {
                                $('.posts_area').find('.nextPage').remove();
                                $('.posts_area').find('.noMorePosts').remove();

                                $('#loading').hide();
                                $('.posts_area').append(response) //on ajoute d'autres données collectées dans la div

                            }
                        });

                    } // Fin IF
                    return false;

                }); // Fin (window).scroll function

            });
        </script>

    </div> <!-- wrapper -->

</body>
</html>
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

<!--            --><?//= var_dump($user_array); ?>
        </div> <!-- /profile left -->

        <div class="main_column column">
            <?= $username ?>

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

    </div> <!-- wrapper -->

</body>
</html>
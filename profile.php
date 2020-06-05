<?php
include 'includes/header.php';
include 'includes/classes/User.php';
include 'includes/classes/Post.php';

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

<!--            --><?//= var_dump($user_array); ?>
        </div> <!-- /profile left -->

        <div class="main_column column">
            <?= $username ?>
        </div> <!-- fin main_column -->

    </div> <!-- wrapper -->

</body>
</html>
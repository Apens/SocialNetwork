<?php
include ("includes/header.php");

$message_obj = new Message($con, $userLoggedIn);

if (isset($_GET['u']))
    $user_to = $_GET['u'];
else{
    $user_to = $message_obj->getMostRecentUser();
    if ($user_to == false)
        $user_to = 'new';
}


if ($user_to != 'new')
    $user_to_obj = new User($con, $user_to);
?>
<div class="user_details column">
    <a href="<?= $userLoggedIn ?>"> <img src="<?php echo $user['profile_pic'] ?>"> </a>

    <div class="user_details_left_right">
        <a href="<?= $userLoggedIn ?>">
            <?php
            echo $user['firstname'] . " " . $user['lastname'];
            ?>
        </a>
        <br>

        <?php
        echo "Post: ".$user['num_post']."<br>";
        echo "Likes: ".$user['num_likes'];
        ?>
    </div>
</div> <!-- fin user_details -->
<div class="main_column column" id="main_column">
    <?php
    if ($user_to != "new")
        echo "<h4><a href='$user_to'>". $user_to_obj->getFirstAndLastname() ."</a> et toi </h4><hr><br>";
    ?>
</div>
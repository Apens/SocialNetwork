<?php
include ("includes/header.php");

if(isset($_GET['id'])){
    $id = $_GET['id'];
}
else {
    $id = 0;
}
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
    <div class="post_area">
        <?php
            $post = new Post($con, $userLoggedIn);
            $post->getSinglePost($id);
        ?>
    </div>
</div>
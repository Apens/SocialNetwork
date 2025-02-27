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

if (isset($_POST['post_message'])){

    if(isset($_POST['message_body'])){
        $body = mysqli_real_escape_string($con,$_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
    }

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
    <?php
    if ($user_to != "new"){
        echo "<h4><a href='$user_to'>". $user_to_obj->getFirstAndLastname() ."</a> et toi </h4><hr><br>";
        echo "<div class='loaded_messages' id='scroll_messages'>";
        echo $message_obj->getMessage($user_to);
        echo "</div>";
    }
    else{
        echo "<h4>New Message</h4>";
    }
    ?>

    <div class="messages_post">
        <form action="" method="POST">
            <?php
            if ($user_to == "new"){
                echo "A qui voulez vous envoyer un message<br><br>";
            ?>
                A : <input type='text'onkeyup='getUser(this.value, "<?= $userLoggedIn ?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'>
            <?php
                echo  "<div class='results' ></div>";
            } else {
                echo "<br> <textarea name='message_body' id='message_textarea' placeholder='Ecris ton message...'></textarea>";
                echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'> ";
            }
            ?>

        </form>
    </div>

    <script>
        let div = document.getElementById('scroll_messages');
        if (div !== null){
            div.scrollTop = div.scrollHeight;
        }

    </script>
</div>
<div class="user_details column" id="conversations">
    <h4>Conversation</h4>

    <div class="loaded_conversation">
        <?= $message_obj->getConvos(); ?>
    </div>
    <br>
    <a href="messages.php?u=new">Nouveau message</a>

</div>
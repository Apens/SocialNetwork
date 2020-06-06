<?php
include 'includes/header.php';
?>

<div class="main_column column" id="main_column">
    <h4>Friend Request</h4>

    <?php
    $query = mysqli_query($con, "SELECT * FROM friend_request WHERE user_to = '$userLoggedIn'");
    if(mysqli_num_rows($query)== 0)
        echo "Vous n'avez pas d'invitation";
    else {
        while ($row = mysqli_fetch_array($query)) {
            $user_from = $row['user_from'];
            $user_from_obj = new User($con, $user_from);

            echo $user_from_obj->getFirstAndLastname()." vous a envoyé une demande";

            $user_from_friend_array = $user_from_obj->getFriendArray();

            if (isset($_POST['accept_request'.$user_from])) {
                $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array,'$user_from,') WHERE username = '$userLoggedIn' ");
                $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array,'$userLoggedIn,') WHERE username = '$user_from' ");

                $delete_query = mysqli_query($con, "DELETE FROM friend_request WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
                echo "Vous etes maintenant amis !";
                header("Location: request.php");
            }

            if (isset($_POST['ignore_request'.$user_from])) {
                $delete_query = mysqli_query($con, "DELETE FROM friend_request WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
                echo "Requete ignoré";
                header("Location: request.php");

            }
            ?>
            <form action="request.php" method="POST">
                <input type="submit" name="accept_request<?= $user_from; ?>" class="success" id="accept_button" value="Accepter">
                <input type="submit" name="ignore_request<?= $user_from; ?>" class="danger" id="ignore_button" value="Ignorer">

            </form>
            <?php

        }
    }
    ?>


</div>

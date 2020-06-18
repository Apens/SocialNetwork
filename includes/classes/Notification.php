<?php


class Notification
{
    private $user_obj;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function getUnreadNumber(){
        $userLoggedIn= $this->user_obj->getUsername();
        $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed='no' AND user_to='$userLoggedIn'");
        return mysqli_num_rows($query);
    }

    public function insertNotification($post_id, $user_to, $type){

        $userLoggedIn= $this->user_obj->getUsername();
        $userLoggedInName = $this->user_obj->getFirstAndLastname();

        $date_time = date("Y-m-d H:i:s");

        switch ($type){
            case 'comment':
                $message= $userLoggedInName ." a commenté ton post";
                break;
            case 'like' :
                $message= $userLoggedInName ." a aimé ton post";
                break;
            case 'profile_post' :
                $message= $userLoggedInName ." a posté sur ton profil";
                break;
            case 'comment_non_owner' :
                $message= $userLoggedInName ." a commenté un post que tu as commenté";
                break;
            case 'profile_comment' :
                $message= $userLoggedInName ." a commenté un post sur ton profil";
                break;

        }

        $link = "post.php?id=". $post_id;

        $insert_query= mysqli_query($this->con, "INSERT INTO notifications(user_to, user_from, link, date_time, opened, viewed) VALUES ('$user_to','$userLoggedIn','$message','$link','$date_time','no', 'no')");
    }

}
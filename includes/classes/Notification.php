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

    public function getNotifications($data, $limit){


        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";


        if($page == 1)
            $start= 0;
        else
            $start = ($page -1) * $limit;

        $set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed= 'yes' WHERE user_to = '$userLoggedIn'");

        $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to = '$userLoggedIn' ORDER BY id DESC ");

        if (mysqli_num_rows($query) == 0) {

            return "Tu n'as pas de notification";
        }

        $num_itaration = 0; //nombre des messages checké
        $count= 1; // Nombre des messages posté

        while ($row = mysqli_fetch_array($query)) {

            if ($num_itaration++ < $start)
                continue;

            if ($count > $limit)
                break;
            else
                $count++;

            $user_from = $row['user_from'];

            $user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE username= '$user_from'");
            $user_data = mysqli_fetch_array($user_data_query);


            //Capture du temps
            $date_time_now = date("Y-m-d H:i:s");
            $start_date = new DateTime($row['date_time']); // date et heure du post
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


            $opened= $row['opened'];
            $style = (isset($row['opened']) && $row['opened'] == 'no')? "background-color: #DDEDFF;" : "";


            $return_string .= "<a href='". $row['link'] ."'>
                            <div class='resultDisplay resultDisplayNotification' style='".$style."'>
                                <div class='notificationProfilePic'>
                                    <img src='".$user_data['profile_pic']."' alt=''>
                                </div>
                                <p class='timestamp_smaller' id='grey'>".$time_message."</p>".$row['message']."
                            </div>
                           </a> ";

        }

        // Si des post on été chargé
        if ($count > $limit)
            return $return_string.= "<input type='hidden' class='nextPageDropDownData' value='".($page +1) ."'>
                                     <input type='hidden' class='noMoreDropDownData' value='false'>";
        else
            return $return_string.= "<input type='hidden' class='noMoreDropDownData' value='true'> <p style='text-align: center'>Plus de notifications à charger</p>";
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

        $insert_query= mysqli_query($this->con, "INSERT INTO notifications(user_to, user_from, link, message, date_time, opened, viewed) VALUES ('$user_to','$userLoggedIn','$link', '$message' ,'$date_time','no','no')");

    }

}
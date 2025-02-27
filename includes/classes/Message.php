<?php


class Message
{
    private $user_obj;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function getMostRecentUser(){
        $userLoggedIn = $this->user_obj->getUsername();

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages 
                                                 WHERE user_to ='$userLoggedIn' OR user_from = '$userLoggedIn'
                                                 ORDER BY id DESC LIMIT 1");

        if (mysqli_num_rows($query)== 0)
            return false;

        $row = mysqli_fetch_array($query);
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];

        if($user_to != $userLoggedIn)
            return $user_to;
        else
            return $user_from;
    }

    public function sendMessage($user_to, $body, $date){
        if ($body != ""){
            $userLoggedIn = $this->user_obj->getUsername();
            $query = mysqli_query($this->con, "INSERT INTO messages(user_to, user_from, body, date, opened, viewed, deleted) VALUES('$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no') ");
        }
    }

    public function getMessage($otherUser){
        $userLoggedIn = $this->user_obj->getUsername();
        $data = "";

        $query= mysqli_query($this->con, "UPDATE messages SET opened = 'yes' WHERE user_to= '$userLoggedIn' AND user_from='$otherUser' ");


        $get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to = '$userLoggedIn' AND user_from= '$otherUser') OR (user_from = '$userLoggedIn' AND user_to= '$otherUser')");

        while($row = mysqli_fetch_array($get_messages_query)){
            $user_to = $row['user_to'];
            $user_from = $row['user_from'];
            $body = $row['body'];

            $div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='green'>": "<div class='message' id='blue'>";
            $data = $data. $div_top . $body . "</div><br><br>";
        }
        return $data;
    }

    public function getLatestMessage($userLoggedIn, $user2){
        $details_array = [];

        $query = mysqli_query($this->con, "SELECT body, user_to, date FROM messages WHERE (user_to='$userLoggedIn' AND user_from ='$user2') OR user_to='$user2' AND user_from ='$userLoggedIn' ORDER BY id DESC LIMIT 1");

        $row = mysqli_fetch_array($query);
        $sent_by = ($row['user_to'] == $userLoggedIn)? "ils ont dit: " : "Tu as dis: ";
        $date_time = $row['date'];

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

        array_push($details_array, $sent_by);
        array_push($details_array, $row['body']);
        array_push($details_array, $time_message);

        return $details_array;
    }

    public function getConvos()
    {
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";
        $convos = [];

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to = '$userLoggedIn' OR user_from ='$userLoggedIn' ORDER BY id DESC ");

        while ($row = mysqli_fetch_array($query)) {
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if (!in_array($user_to_push, $convos)) {
                array_push($convos, $user_to_push);
            }
        }

        foreach ($convos as $username) {
            $user_found_obj = new User($this->con, $username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

            $dots = (strlen($latest_message_details[1] >= 12)) ? "..." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;


        $return_string .= "<a href='messages.php?u=$username'><div class='user_found_messages'>
                                <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius:5px; margin-right: 5px;'>
                                " . $user_found_obj->getFirstAndLastname() . "
                                <span class='timestamp_smaller' id='grey'>" . $latest_message_details[2] . "</span>
                                <p id='grey' style='margin: 0'>" . $latest_message_details[0] . $split . "</p>
                            </div>
                           </a> ";

        }
        return $return_string;
    }

    public function getConvosDropdown($data, $limit) {

        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";
        $convos = [];


        if($page == 1)
            $start= 0;
        else
            $start = ($page -1) * $limit;

        $set_viewed_query = mysqli_query($this->con, "UPDATE messages SET viewed= 'yes' WHERE user_to = '$userLoggedIn'");

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to = '$userLoggedIn' OR user_from ='$userLoggedIn' ORDER BY id DESC ");

        while ($row = mysqli_fetch_array($query)) {
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if (!in_array($user_to_push, $convos)) {
                array_push($convos, $user_to_push);
            }
        }

        $num_itaration = 0; //nombre des messages checké
        $count= 1; // Nombre des messages posté

        foreach ($convos as $username) {

            if ($num_itaration++ < $start)
                continue;

            if ($count > $limit)
                break;
            else
                $count++;

            $is_unread_query = mysqli_query($this->con, "SELECT opened FROM messages WHERE user_to= '$userLoggedIn' AND user_from = '$username' ORDER BY id DESC");
            $row = mysqli_fetch_array($is_unread_query);
            $style = (isset($row['opened']) && $row['opened'] == 'no')? "background-color: #DDEDFF;" : "";

            $user_found_obj = new User($this->con, $username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

            $dots = (strlen($latest_message_details[1] >= 12)) ? "..." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;


            $return_string .= "<a href='messages.php?u=$username'>
                            <div class='user_found_messages'style='".$style ."'>
                                <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius:5px; margin-right: 5px;'>
                                " . $user_found_obj->getFirstAndLastname() . "
                                <span class='timestamp_smaller' id='grey'>" . $latest_message_details[2] . "</span>
                                <p id='grey' style='margin: 0'>" . $latest_message_details[0] . $split . "</p>
                            </div>
                           </a> ";

        }

        // Si des post on été chargé
        if ($count > $limit)
            return $return_string.= "<input type='hidden' class='nextPageDropDownData' value='".($page +1) ."'>
                                     <input type='hidden' class='noMoreDropDownData' value='false'>";
        else
            return $return_string.= "<input type='hidden' class='noMoreDropDownData' value='true'> <p style='text-align: center'>Plus de message à charger</p>";
    }

    public function getUnreadNumber(){
        $userLoggedIn= $this->user_obj->getUsername();
        $query = mysqli_query($this->con, "SELECT * FROM messages WHERE viewed='no' AND user_to='$userLoggedIn'");
        return mysqli_num_rows($query);
    }


}
<?php
include ("../../config/config.php");
include ("../../includes/classes/User.php");


$query= $_POST['query'];
$userLoggedIn= $_POST['userLoggedIn'];
$limit= 8;

$names = explode(" ", $query);

// si la requete contient "_", l'utilisateur fait une recherche sur l'username

if (strpos($query, '_') !== false)
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed = 'no' LIMIT 8");

//S'il y a 2 mots, l'utilisateur fait une recherche avec le prenom et le nom
elseif (count($names) == 2)
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (firstname LIKE '$names[0]%' AND lastname LIKE '$names[1]%')AND user_closed = 'no' LIMIT 8");

//S'il n'y a qu'1 seul mot dans la requete on cherche le prenom ou le nom
else
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (firstname LIKE '$names[0]%' OR lastname LIKE '$names[0]%')AND user_closed = 'no' LIMIT 8");


if ($query != ""){
    while ($row = mysqli_fetch_array($usersReturnedQuery)){
        $user = new User($con, $userLoggedIn);

        if ($row['username'] != $userLoggedIn)
            $mutual_friend = $user->getMutualFriends($row['username'])." amis en commun";
        else
            $mutual_friend = "";

        echo "<div class='resultDisplay'> 
                    <a href=' ".$row['username'] ." ' style='color: #1485BD'>
                        <div class='liveSearchProfilePic'>
                            <img src='".$row['profile_pic']."'>
                        </div>
                        
                        <div class='liveSearchText'>
                            ".$row['firstname']."&nbsp".$row['lastname']."
                            <p>".$row['username']."</p>
                            <p id='grey'>".$mutual_friend."</p>
                        </div>
                    </a>
              </div>";
    }
}
<?php
include ("includes/header.php");

if (isset($_GET['q'])){
    $query = $_GET['q'];
}else{
    $query= "";
}

if (isset($_GET['type'])){
    $type=$_GET['type'];
}else{
    $type = "name";
}
?>
<div class="main_column column" id="main_column">
    <?php
        if ($query == "")
            echo "tu dois taper un truc à chercher";
        else{

// si la requete contient "_", l'utilisateur fait une recherche sur l'username

            if ($type = "username")
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed = 'no' LIMIT 8");
            else {

                $names = explode(" ", $query);

                if (count($names) == 3)
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (firstname LIKE '$names[0]%' AND lastname LIKE '$names[2]%')AND user_closed = 'no'");

                //S'il n'y a qu'1 seul mot dans la requete on cherche le prenom ou le nom
                else  if (count($names) == 2)
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (firstname LIKE '$names[0]%' AND lastname LIKE '$names[1]%')AND user_closed = 'no'");
                else
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (firstname LIKE '$names[0]%' OR lastname LIKE '$names[0]%')AND user_closed = 'no'");
            }
            //on verifie que des resultats on été trouvé
            if (mysqli_num_rows($usersReturnedQuery) == 0)
                echo "il n'y a personne portant le nom". $type . "ou".$query;
            else
                echo  mysqli_num_rows($usersReturnedQuery)." résultats trouvés: <br> <br>";


            echo "<p id='grey'>tu cherches ça ? : </p> ";
            echo "<a href='search.php?q=' ".$query."&type=name'>Names</a>, <a href='search.php?q=' ".$query."&type=username'>Usernames</a> <br> <br> <hr>";

            while ($row = mysqli_fetch_array($usersReturnedQuery)){
                $user_obj = new User($con, $user['username']);

                $button = "";
                $mutual_friends = "";

                if ($user['username'] != $row['username']){

                }
            }

        }
    ?>

</div>

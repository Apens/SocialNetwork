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

//S'il y a 2 mots, l'utilisateur fait une recherche avec le prenom et le nom

        }
    ?>

</div>

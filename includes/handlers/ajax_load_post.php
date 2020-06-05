<?php
include ("../../config/config.php");
include ("../classes/User.php");
include ("../classes/Post.php");


$limit = 10; //nombre de posts à charger


$post = new Post($con, $_REQUEST['userLoggedIn']); // on récupère les données de la request de l'appel ajax dans la super global $_REQUEST
$post->loadPostsFriends($_REQUEST, $limit);


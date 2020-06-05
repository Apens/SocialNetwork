<?php
    ob_start(); // Active l'output buffering
    session_start();

    $timezone = date_default_timezone_set("Europe/Paris");

    $con = mysqli_connect('localhost', 'root','','social');

    if(mysqli_connect_error()) {
        echo "Erreure de connexion:".mysqli_connect_error();
    }
?>

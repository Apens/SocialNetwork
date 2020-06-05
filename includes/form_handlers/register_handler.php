<?php
    // on déclare les variables dont on aura besoin pour éviter les erreures
    $fname = ""; //Prénom
    $lname = ""; //Nom
    $username= "";
    $email= ""; //Courriel
    $email2= ""; //Courriel2
    $password= ""; // Mot de passe
    $password2= ""; // Mot de passe2
    $date = ""; // Jour d'inscription
    $error_array = []; // Stocker les erreures rencontrer

    if(isset($_POST['register_button'])){ // on verifie la presence de données dans le formulaire
        // Si le formulaire contient des données on les traites

        //Prénom
        $fname = strip_tags($_POST['reg_fname']); // strip_tags retire les tag de code (ce code retire les tags html)
        $fname = str_replace(' ','',$fname); // retire les espaces superflue/involontaires
        $fname = ucfirst(strtolower($fname)); // met la premiere lettre contenue dans la variable en majuscule
        $_SESSION['reg_fname']= $fname;

        //Prénom
        $lname = strip_tags($_POST['reg_lname']); // strip_tags retire les tag de code (ce code retire les tags html)
        $lname = str_replace(' ','',$lname); // retire les espaces superflue/involontaires
        $lname = ucfirst(strtolower($lname)); // met la premiere lettre contenue dans la variable en majuscule
        $_SESSION['reg_lname']= $lname;

        //Email
        $email = strip_tags($_POST['reg_email']); // strip_tags retire les tag de code (ce code retire les tags html)
        $email = str_replace(' ','',$email); // retire les espaces superflue/involontaires
        $email = ucfirst(strtolower($email)); // met la premiere lettre contenue dans la variable en majuscule
        $_SESSION['reg_email']= $email;

        //Email2
        $email2 = strip_tags($_POST['reg_email2']); // strip_tags retire les tag de code (ce code retire les tags html)
        $email2 = str_replace(' ','',$email2); // retire les espaces superflue/involontaires
        $email2 = ucfirst(strtolower($email2)); // met la premiere lettre contenue dans la variable en majuscule
        $_SESSION['reg_email2']= $email2;

        //Password
        $password = strip_tags($_POST['reg_password']); // strip_tags retire les tag de code (ce code retire les tags html)
        $password2 = strip_tags($_POST['reg_password2']); // strip_tags retire les tag de code (ce code retire les tags html)

        //Date
        $date = date("Y-m-d");

        if ($email == $email2){
            // on verifie le bon format de l'adresse mail
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){

                $email= filter_var($email, FILTER_VALIDATE_EMAIL);

                //On verifie l'existence de l'email dans la bdd
                $e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");

                // on retourne le nombre d'occurence de l'email
                $num_rows = mysqli_num_rows($e_check);

                if($num_rows > 0) {
                    array_push($error_array,"Cet email est déjà utilisé");
                }

            }
            else {
                array_push($error_array,"Format de l'email incorrecte");
            }

        }
        else {
            array_push($error_array, "Les emails ne correspondent pas");
        }

        if(strlen($fname) > 25 || strlen($fname)< 2 ){
            array_push($error_array, "Votre prénom doit contenir entre 2 et 25 caractères");
        }
        if(strlen($lname) > 25 || strlen($lname)< 2 ){
            array_push($error_array,"Votre nom doit contenir entre 2 et 25 caractères");
        }

        if($password != $password2){
            array_push($error_array, "Vos mots de passe ne correspondent pas");
        }
        else{
            if(preg_match('/[^A-Za-z0-9]/', $password)){
                array_push($error_array, "Votre mot de passe ne peux contenir que des nombre ou des lettres");
            }
        }

        if(strlen($password )> 30 || strlen($password)< 8){
            array_push($error_array, "Votre mot de passe doit contenir entre 8 et 30 caractères");
        }

        if(empty($error_array)){
            $password = md5($password); //encrypte le mdp de l'utilisateur avant enregsistrement en bdd

            // generer l'username par concatenation du fname et du lname
            $username = strtolower($fname."_".$lname);
            $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");

            $i = 0;
            //on verifie si l'username existe, si oui on ajoute 1 à celui-ci
            while (mysqli_num_rows($check_username_query) !=0) {
                $i++; //incrémente i de 1
                $username= $username ."_".$i;
                //on verifie à nouveau s'il existe d'autres occurences du nom
                $check_username_query= mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
            }

            // attribution d'image de profil
            $rand = rand(1,2); // un rand 2

            if($rand == 1 )
                $profile_pic = "assets/images/profile_pics/defaults/head_alizarin.png";
            elseif ($rand== 2)
                $profile_pic = "assets/images/profile_pics/defaults/head_wet_asphalt.png";

            $query = mysqli_query($con, "INSERT INTO users (firstname, lastname, username, email, password, signup_date, profile_pic, num_post, num_likes, user_closed, friend_array ) VALUE ('$fname', '$lname', '$username', '$email', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");

            array_push($error_array, "Tu es bien enregistré, tu peux te connecter");

            // on vide la session
            $_SESSION['reg_fname']= "";
            $_SESSION['reg_lname']= "";
            $_SESSION['reg_email']= "";
            $_SESSION['reg_email2']= "";
        }

    }
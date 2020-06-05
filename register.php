<?php
    require 'config/config.php';
    require 'includes/form_handlers/register_handler.php';
    require 'includes/form_handlers/login_handler.php';

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bienvenue chez SocialTest</title>

    <link rel="stylesheet" href="assets/css/register_style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="assets/js/register.js"></script>
</head>
<body>
    <?php
        if(isset($_POST['register_button'])){

            echo '
            <script>
            $(document).ready(function() {
              $("#first").hide();
              $("#second").show();
            });
            </script>
            ';
        }
    ?>
    <div class="wrapper">

        <div class="login_box">

            <div class="login_header">
                <h1>Social Network</h1>
            </div>
            <br>
            <div id="first">

                <form action="register.php" method="POST">
                    <input type="email" name="log_email" placeholder="Courriel" value="<?php
                    if (isset($_SESSION['log_email'])){
                        echo $_SESSION['log_email'];
                    }?>" required>
                    <br>
                    <input type="password" name="log_password" placeholder="Mot de passe">
                    <br>
                    <input type="submit" name="login_button" value="login">
                    <?php if(in_array("Email ou mot de passe incorrecte",$error_array))
                        echo "Email ou mot de passe incorrecte <br>"; ?>
                    <br>
                    <a href="#" id="signup" class="signup">Pas encore de compte ? Enregistrez-vous !</a>
                </form>
            </div>
            <div id="second">
                <form action="register.php" method="post">
                    <input type="text" name="reg_fname" placeholder="Prénom" value="<?php
                    if (isset($_SESSION['reg_fname'])){
                        echo $_SESSION['reg_fname'];
                    }
                    ?>" required>
                    <br>
                    <?php if(in_array("Votre prénom doit contenir entre 2 et 25 caractères",$error_array))
                        echo "Votre prénom doit contenir entre 2 et 25 caractères<br>"; ?>

                    <input type="text" name="reg_lname" placeholder="Nom" value="<?php
                    if (isset($_SESSION['reg_lname'])){
                        echo $_SESSION['reg_lname'];
                    }
                    ?>" required>
                    <br>
                    <?php if(in_array("Votre nom doit contenir entre 2 et 25 caractères",$error_array))
                        echo "Votre nom doit contenir entre 2 et 25 caractères<br>"; ?>

                    <input type="email" name="reg_email" placeholder="Email" value="<?php
                    if (isset($_SESSION['reg_email'])){
                        echo $_SESSION['reg_email'];
                    }
                    ?>" required>
                    <br>
                    <input type="email" name="reg_email2" placeholder="Confirmer Email" value="<?php
                    if (isset($_SESSION['reg_email2'])){
                        echo $_SESSION['reg_email2'];
                    }
                    ?>" required>
                    <br>
                    <?php if(in_array("Cet email est déjà utilisé",$error_array))
                        echo "Cet email est déjà utilisé<br>";
                    else if(in_array("Format de l'email incorrecte",$error_array))
                        echo "Format de l'email incorrecte<br>";
                    else if(in_array("Les emails ne correspondent pas",$error_array))
                        echo "Les emails ne correspondent pas<br>"; ?>


                    <input type="password" name="reg_password" placeholder="Mot de passe" required>
                    <br>
                    <input type="password" name="reg_password2" placeholder="Confirmer Mot de passe" required>
                    <br>
                    <?php if(in_array("Vos mots de passe ne correspondent pas",$error_array))
                        echo "Vos mots de passe ne correspondent pas<br>";
                    else if(in_array("Votre mot de passe ne peux contenir que des nombre ou des lettres",$error_array))
                        echo "Votre mot de passe ne peux contenir que des nombre ou des lettres<br>";
                    else if(in_array("Votre mot de passe doit contenir entre 8 et 30 caractères",$error_array))
                        echo "Votre mot de passe doit contenir entre 8 et 30 caractères<br>"; ?>
                    <input type="submit" name="register_button" value="Register">
                    <br>
                    <?php if(in_array("Tu es bien enregistré, tu peux te connecter",$error_array))
                        echo "<span style='color: #14C800'> Tu es bien enregistré, tu peux te connecter </span> <br>"; ?>

                    <a href="#" id="signin" class="signin">Vous avez déjà un compte ? connectez-vous !</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

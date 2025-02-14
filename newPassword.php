<?php
session_start();
function create_captcha($taille = 5){
    $caractere = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?/&$%";
    $captcha_code = substr(str_shuffle($caractere), 0, $taille );
    return $captcha_code;
}

if (isset($_SESSION['erreurmdp'])) {
    echo $_SESSION['erreurmdp'];
    unset ($_SESSION['erreurmdp']);
}
?>


<?php
// Récupérer le token depuis l'URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    // Rediriger ou afficher un message d'erreur si le token est absent
    die('Token manquant.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="newpassword.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
<style>
        #caracteres.invalid,
    #maj.invalid,
    #min.invalid,
    #chiffre.invalid,
    #special.invalid,
    #password-match.invalid {
        color: red;
        font-weight: bold;
    }


    
    #caracteres.valid,
    #maj.valid,
    #min.valid,
    #chiffre.valid,
    #special.valid,
    #password-match.valid {
        color: green;
        font-weight: bold;
    }

    li {
        list-style: none;
        text-align: center;
    }

    </style>

    <div class="login-container">
        <div class="form-container">
            <h2 class="titre"><i class='bx bxs-lock-alt' ></i>&nbsp;Réinitialiser le mot de passe&nbsp;<i class='bx bxs-lock-alt' ></i></h2>
            <?php echo '<form action="new_password.php?token=' . urlencode($token) . '" method="POST">';?>
                
                <label for="new_password">Nouveau mot de passe : </label>
                <div class="newpassword">
                <input type="password" id="password" name="password" required>
                </div>
                <div id="force_mdp"></div>
                <ul id="password-criteria">
            <li id="caracteres" class="invalid">Au moins 8 caractères</li>
            <li id="maj" class="invalid"> Une majuscule</li>
            <li id="min" class="invalid">Une minuscule</li>
            <li id="chiffre" class="invalid">Un chiffre</li>
            <li id="special" class="invalid">Un caractère spécial</li>
        </ul>
                <label for="confirm-password">Confirmer le mot de passe : </label>
                <div class="confirmPassword">
                <input type="password" id="confirm-password" name="confirm-password" required>
                </div>
                <div id="confirmermdp"></div>
                <div class="envoyer">
                <input type="submit" value="Réinitialiser">
                </div>
            </form>
        </div>
        <p id="error-msg"></p>
    </div>
    <script src="Js/inscription.js"></script>
</body>
</html>
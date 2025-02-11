<?php
session_start();
function create_captcha($taille = 5){
    $caractere = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?/&$%";
    $captcha_code = substr(str_shuffle($caractere), 0, $taille );
    return $captcha_code;
}

$captcha_code = create_captcha();
$_SESSION['captcha'] = $captcha_code;
include 'header.php';


if (isset($_SESSION['erreurmdp'])) {
    echo $_SESSION['erreurmdp'];
    unset ($_SESSION['erreurmdp']);
}
if (isset($_SESSION['erreurcaptcha'])) {
    echo $_SESSION['erreurcaptcha'];
    unset ($_SESSION['erreurcaptcha']);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link rel="stylesheet" href="signup.css">
</head>

<body>
    <div class="signup-container">
        <div class="fond-img">
            <img src="img/signup.avif" alt="login">
        </div>
        <div class="titre-signup">
            <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px"> <i class='bx bx-user'></i> &nbsp;Inscription <i class='bx bx-user'></i></h2>

            <form action="signup.php" method="POST" >
                <label for="nom">Nom d'utilisateur :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>

                <div id="force_mdp"></div>

                <label for="confirm-password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
                <label for="capchat">Veuillez remplir le Captcha:</label>
                <div class="captcha">
                    <?php
                    $catpcha_array = str_split($_SESSION['captcha']);
                    foreach ($catpcha_array as $char) {
                        echo "<span>" . $char . "</span>";
                    }
                    ?>
                </div>
                    <input type="text" id="captcha" name="captcha" required>
                    <input type="submit" value="S'inscrire">
            </form>
        </div>
        <p id="error-msg"></p>
    </div>
    <script src="Js/inscription.js"></script>
</body>

</html>
<?php include 'footer.php'; ?>
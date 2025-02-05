<?php
session_start();
  $captcha = random_int(10000,99999);
  $_SESSION['captcha'] = $captcha;
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

            <form action="signup.php" method="POST" onsubmit="return validateForm()">
                <label for="nom">Nom d'utilisateur :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm-password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
                <label  for="capchat">Veuillez remplir le Captcha:</label>
                <div class="captcha">
                <?php echo $_SESSION ['captcha']?>
                </div>
                <label for="captcha">
                    <input type="text" id="captcha" name="captcha" required>
                <input type="submit" value="S'inscrire">
            </form>
        </div>
        <p id="error-msg"></p>
    </div>

    <script>
        function validateForm() {
            var mdp = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;
            var errorMsg = document.getElementById("error-msg");

            if (mdp !== confirmPassword) {
                errorMsg.textContent = "Les mots de passe ne correspondent pas.";
                errorMsg.style.color = "red";
                return false;
            }
            return true;
        }
    </script>
<!-- <?php
include 'footer.php'
?> -->

</html>
</body>

</html>
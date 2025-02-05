<?php
session_start();
  
  $captcha = random_int(10000,99999);
  $_SESSION['captcha'] = $captcha;
?>



<form action="test2.php" method="POST" onsubmit="return validateForm()">
                <label for="nom">Nom d'utilisateur :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm-password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
                <label for="capchat">Veuillez remplir le Captcha:</label>
                <?php echo $_SESSION ['captcha']?>
                <label for="captcha">
                    <input type="text" id="captcha" name="captcha" required>
                <input type="submit" value="S'inscrire">
            </form>

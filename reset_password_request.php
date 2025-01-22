<?php
require_once("connexion.php");

date_default_timezone_set('Europe/Paris');
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = htmlspecialchars(trim($_POST['email']));

    try{
        $connexion = getConnexion();
        $stmt = $connexion->prepare("DELETE FROM password_resets WHERE expires_at < NOW() - INTERVAL 1 MINUTE");
        $stmt ->execute();

        $stmt = $connexion->prepare("SELECT * FROM clients WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('15 minutes'));

            $connexion->prepare("INSERT INTO password_resets(email, token, expires_at, created_at) VALUES (:email, :token, :expires_at, NOW())" )
        ->execute(['email' => $email, 'token' => $token, 'expires_at' => $expiry]);

        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/reset_password.php?token=$token";

        $subject = "?UTF-8?B?" . base64_encode("Reinitialisation de votre mot de passe") . "?=";
        $message = "
        <html>
        <head>
        <title>Reinitialisation de votre mot de passe</title>
        </head>
        <body>
        <p>Bonjour,</p>
        <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
        <p><a href='$resetLink' style='color: blue; text-decoration: underline;'>Réinitialiser mon mot de passe</a></p>
        <p>Ce lien expirera dans 15 minutes.</p>
        <p>Si  vous n'avez pas  demandé cette réinitialisation, ignorez cet email.</p>
        </body>
        </html>   
        ";
        $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset= UTF-8\r\n";
        $headers .= "Content-transfer-Encoding: 8bit\r\n"; 

        if(mail($email, $subject, $message, $headers)){
            echo"<p style='color: green;'>Un lien de réinitialisation a été envoyé à votre adresse email.</p>";
        }else{
            echo"<p style='color: red;'>Erreur lors de l'envoi de l'email.</p>";
        }
        }else{
            echo"<p style=color: red;'>Aucun compte  trouvé pour cet email.</p>";
        }

    }catch (PDOException $e){
        echo "Erreur : " . $e->getMessage();

    }
}


?>


<!DOCTYPE html>
<html>

<head>
    <title>Mot de passe oublié</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <?php
    include 'header.php';
    ?>

    <div class="login-container">
        <div class="form-container">
            <h2>Réinitialisation du mot de passe</h2>
            <form action="reset_password_request.php" method="post">
                <label for="email">Entrez votre email :</label>
                <input type="email" id="email" name="email" required>
                <button type="submit">Envoyer</button>
            </form>
        </div>
    </div>

</body>

</html>
<?php
include 'footer.php';
?>
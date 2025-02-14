<?php
// if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once("connexion.php");
    if (!isset($_GET['token'])) {
        echo "<p style='color: red;'>Le token est introuvable dans l'URL.</p>";
        exit;
    }

    $token = htmlspecialchars(trim($_GET['token']));
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);

    echo "Le token est : " . htmlspecialchars($token);

    if($new_password !== $confirm_password){
        echo"<p style='color: red;'>Le mot de passe ne correspond pas.</p>";
        exit;
    }

    if(strlen($new_password) < 8 ){
        echo"<p style='color: red;'>Le mot de passe  doit contenir au moins 8 caractères.</p>";
        exit;
    }

        $connexion = getConnexion();
        $stmt = $connexion->prepare("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()");
        $stmt ->bindParam(':token', $token);
        $stmt->execute();
    

        if($stmt->rowCount() > 0){
            $email = $stmt->fetchColumn();
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $connexion->prepare("UPDATE clients SET mdp = :mdp WHERE email = :email")->execute(['mdp' => $hashed_password, 'email' => $email]);
            $connexion->prepare("DELETE FROM password_resets WHERE token = :token ")
            ->execute(['token' => $token]);
        }
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Votre mot de passe a été réinitialisé avec succès !</p>";
        } else {
            echo "<p style='color: red;'>Une erreur est survenue lors de la réinitialisation de votre mot de passe. Veuillez réessayer.</p>";
        }


header("Location: login.php");
?>



    



<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once("connexion.php");

    $token = htmlspecialchars(trim($_POST['token']));
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);


    if($new_password !== $confirm_password){
        echo"<p style='color: red;'>Le mot de passe ne correspond pas.</p>";
        exit;
    }

    if(strlen($new_password) < 8 ){
        echo"<p style='color: red;'>Le mot de passe  doit contenir au moins 8 caractères.</p>";
        exit;
    }

    try{
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

            header('Location: login.php');
            exit;
        }else{
            echo"<p style='color: red;'>Le lien de réinitialisation est invalide ou expiré.</p>";
        }

    }catch(PDOException $e){
        echo "Erreur : " . $e->getMessage();
    }
}else if (isset($_GET['token'])){
    $token = htmlspecialchars($_GET['token']);
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
            <h2>Réinitialiser le mot de passe</h2>
            <form action="reset_password.php" method="post">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
                <label for="new_password">Nouveau mot de passe : </label>
                <input type="password" id="new_password" name="new_password" required>
                <label for="confirm_password">Confirmer le mot de passe : </label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <button type="submit">Réinitialiser</button>
            </form>
        </div>
    </div>

</body>

</html>
<?php
include 'footer.php';
?>
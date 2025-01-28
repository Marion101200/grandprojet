<?php
// Récupérer le token depuis l'URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    // Rediriger ou afficher un message d'erreur si le token est absent
    die('Token manquant.');
}
echo "Le token est : " . htmlspecialchars($token);
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

    <div class="login-container">
        <div class="form-container">
            <h2>Réinitialiser le mot de passe</h2>
            <?php echo '<form action="new_password.php?token=' . urlencode($token) . '" method="POST">';?>
                
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
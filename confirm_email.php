
<?php

include "connexion.php";
$connexion = getConnexion();
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$token = $_GET['token'];
$stmt = $connexion->prepare("SELECT token, date_token FROM clients WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $clients = $stmt->fetch(PDO::FETCH_ASSOC);

$current_time = new DateTime();
$token_creation_time = new DateTime($clients['date_token']);
$interval = $token_creation_time->diff($current_time);
if ($interval->i >= 1 || $interval->h > 0){
    echo("<p style='color: red;'>Le lien a expiré..</p>");
}
else {
$update_token= $connexion->prepare('UPDATE clients SET etat_token = 1 WHERE token = :token');
$update_token->execute(['token' => $token]);
echo '<p>Votre email à été vérifier vous pouvez fermer cette page</p>'; 
}
?>
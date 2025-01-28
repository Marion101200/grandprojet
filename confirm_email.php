<p>Votre email à été vérifier vous pouvez fermer cette page</p>
<?php

include "connexion.php";
$connexion = getConnexion();
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$token = $_GET['token'];
$update_token= $connexion->prepare('UPDATE clients SET etat_token = 1 WHERE token = :token');
$update_token->execute(['token' => $token]);
?>
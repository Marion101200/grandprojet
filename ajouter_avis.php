<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="jeux.css">
  <title>Panier</title>
</head>

<body>
<?php

require_once("connexion.php");
$connexion = getConnexion();
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
date_default_timezone_set('Europe/Paris');



if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $jeux_titre = $_POST['jeux_titre'];
  $nom = $_POST['nom'];
  $commentaire = $_POST['commentaire'];
  $note = intval($_POST['note']);


  if($note < 1 || $note > 5){
      echo "La note doit être comprise entre 1 et 5.";
      exit;
  }


  $stmt = $connexion->prepare("INSERT INTO avis (jeux_titre, nom, commentaire, note) VALUES (?, ?, ?, ?)");
  $stmt->execute([$jeux_titre, $nom, $commentaire, $note]);

  header("Location: accueil.php");
  exit;
}
?>

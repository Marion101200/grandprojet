<?php

include 'pdo.php';

require_once("connexion.php");

$connexion = getConnexion();

$bd = $connexion->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);



foreach($bd AS $bdResult){
    $sql = $connexion->query("SELECT * FROM {$bdResult}");
    $stockresult = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($stockresult);
}
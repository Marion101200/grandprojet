<?php

include 'pdo.php';

require_once("connexion.php");

$connexion = getConnexion();

$bd = $connexion->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);


$data = [];

foreach($bd AS $bdResult){
    $sql = $connexion->query("SELECT * FROM {$bdResult}");
    $data[$bdResult] = $sql->fetchAll(PDO::FETCH_ASSOC);
    // $stockresult = $sql->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($data);
exit;
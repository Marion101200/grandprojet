<?php
require_once("connexion.php");
$connexion = getConnexion();

$stmt = $connexion->prepare("SELECT titre FROM jeux WHERE titre LIKE :search");
$search = isset($_GET['search']) ? $_GET['search'] : '';
$stmt->execute(['search' => '%' . $search . '%']); 
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
?>


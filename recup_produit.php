<?php
header('Content-Type: application/json'); // Assure un bon encodage JSON
require_once("connexion.php");
$connexion = getConnexion();

$stmt = $connexion->prepare("SELECT titre FROM jeux WHERE titre LIKE :search");
$search = isset($_GET['search']) ? $_GET['search'] : '';
$stmt->execute(['search' => '%' . $search . '%']); 
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Décoder les entités HTML pour éviter les erreurs d'affichage
foreach ($results as &$row) {
    $row['titre'] = html_entity_decode($row['titre']);
}

echo json_encode($results);
exit;


?>


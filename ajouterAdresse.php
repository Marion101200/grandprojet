<!DOCTYPE html>
<html>

<head>
    <title>Paiement</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="ajouterAdresse.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>


<body>
<?php
session_start(); 
include 'pdo.php';
require_once("connexion.php");

if (!isset($_SESSION['id_client'])) {
    echo "Erreur : ID client non défini.";
    exit;
}
$connexion = getConnexion();
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$id = $_SESSION['id_client'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['adresse'])) {
    $adresse = trim($_POST['adresse']);

    // Vérifier si l'adresse existe déjà pour éviter les doublons
    $sql = $connexion->prepare("SELECT COUNT(*) FROM adresse WHERE id_clients = :id_client AND adresse = :adresse");
    $sql->execute(['id_client' => $id, 'adresse' => $adresse]);
    $count = $sql->fetchColumn();

    if ($count == 0) { // Si l'adresse n'existe pas encore, on l'ajoute
        $sql = $connexion->prepare("INSERT INTO adresse (id_clients, adresse) VALUES (:id_client, :adresse)");
        $sql->execute([
            'id_client' => $id,
            'adresse' => $adresse
        ]);
    }    header('Location: finaliser_panier.php'); // Remplacez 'confirmation.php' par la page où vous voulez rediriger
    exit;
}
include 'header.php';
?>
<h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px;text-align:center;"> 
    <i class='bx bxs-credit-card-alt'></i> &nbsp;Nouvelle adresse &nbsp; <i class='bx bxs-credit-card-alt'></i>
</h2>
<form method="POST" action="finaliser_panier.php">
        <div class="adresse-container">
            <label for="adresse">Ajouter une nouvelle adresse :</label>
            <div class="adresse-input-group">
                <input type="text" id="adresse" name="adresse" required>
                <button type="submit" id="button-ajouter">Ajouter</button>
            </div>
        </div>
    </form>
    <?php include 'footer.php'; ?>


</body>

</html
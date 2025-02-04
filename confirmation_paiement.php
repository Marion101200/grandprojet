<?php
include 'header.php';
include 'pdo.php';
?>

<?php

try {
    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $connexion->prepare("INSERT INTO commande (id_clients, montant, adresse) VALUES (:id_clients, :montant, :adresse)");
    $stmt->bindParam(':id_clients', $id_clients);
    $stmt->bindParam(':montant', $montant);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->execute();

    $stmt = $connexion->prepare("INSERT INTO details_commande (id_commande, adresse) VALUES (:id_commande, :adresse)");
    $stmt->bindParam(':id_commande', $id_commande);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->execute();
}catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;

}

?>



<!DOCTYPE html>
<html>

<head>
    <title>Confirmation de paiement</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="confirmation_paiement.css" rel="stylesheet">
</head>
<div class="confirmation_paiement">
<p><i class='bx bxs-check-square'></i>&nbsp; Votre paiement à été validé vous serez livrez dans les plus bref délais&nbsp;<i class='bx bxs-check-square'></i></p>
</div>
<body>

</body>

</html>
<?php include 'footer.php'; ?>
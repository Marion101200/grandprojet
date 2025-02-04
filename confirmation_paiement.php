<?php
session_start();
include 'pdo.php';
include 'header.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "Votre panier est vide.";
        exit;
    }

    $stmt = $connexion->prepare("INSERT INTO commande (id_clients, montant, adresse) VALUES (:id_clients, :montant, :adresse)");
    $total = 0;


    foreach ($_SESSION['cart'] as $jeux_id => $quantite) {
        $query = $connexion->prepare("SELECT prix FROM jeux WHERE id = ?");
        $query->execute([$jeux_id]);
        $jeu = $query->fetch(PDO::FETCH_ASSOC);
        if ($jeu) {
            $total += $jeu['prix'] * $quantite;
        }
    }

    $stmt->bindParam(':total', $total, ':id_clients', $client, ':montant', $montant, ':adresse', $adresse);
    $stmt->execute();
    $id_commande = $connexion->lastInsertId();


    $stmt_detail = $connexion->prepare("INSERT INTO details_commande (id_commande, adresse) VALUES (:id_commande, :adresse)");

    foreach ($_SESSION['cart'] as $jeux_id => $quantite) {
        $query = $connexion->prepare("SELECT prix FROM jeux WHERE id = ?");
        $query->execute([$jeux_id]);
        $jeu = $query->fetch(PDO::FETCH_ASSOC);

        if ($jeu) {
            $stmt_detail->execute([
                ':id_commande' => $id_commande,
                ':adresse' => $adresse,
            ]);
        }
    }


    unset($_SESSION['cart']);
    echo "Commande validée avec succès !";
    header("Location: confirmation.php");
    exit;
} catch (PDOException $e) {
    echo "Erreur lors de la validation de la commande : " . $e->getMessage();
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
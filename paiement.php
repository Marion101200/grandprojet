<?php
session_start();
include 'pdo.php';

try {
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "Votre panier est vide.";
        exit;
    }

    $stmt = $connexion->prepare("INSERT INTO commandes (date_commande, total) VALUES (NOW(), :total)");
    $total = 0;

    foreach ($_SESSION['cart'] as $jeux_id => $quantite) {
        $query = $connexion->prepare("SELECT prix FROM jeux WHERE id = ?");
        $query->execute([$jeux_id]);
        $jeu = $query->fetch(PDO::FETCH_ASSOC);
        if ($jeu) {
            $total += $jeu['prix'] * $quantite;
        }
    }

    $stmt->bindParam(':total', $total);
    $stmt->execute();
    $commande_id = $connexion->lastInsertId();


    $stmt_detail = $connexion->prepare("INSERT INTO details_commandes (commande_id, jeux_id, quantite, prix_unitaire) VALUES (:commande_id, :jeux_id, :quantite, :prix_unitaire)");

    foreach ($_SESSION['cart'] as $jeux_id => $quantite) {
        $query = $connexion->prepare("SELECT prix FROM jeux WHERE id = ?");
        $query->execute([$jeux_id]);
        $jeu = $query->fetch(PDO::FETCH_ASSOC);

        if ($jeu) {
            $stmt_detail->execute([
                ':commande_id' => $commande_id,
                ':jeux_id' => $jeux_id,
                ':quantite' => $quantite,
                ':prix_unitaire' => $jeu['prix']
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

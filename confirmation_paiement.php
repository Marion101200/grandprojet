<?php
session_start();
include 'pdo.php';
include 'header.php';

if (!isset($_SESSION['id_clients'])) {
    echo "Erreur : utilisateur non connecté.";
    exit;
}

try {
    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "Votre panier est vide.";
        exit;
    }

    $id_clients = $_SESSION['id_clients'];
    $adresse = isset($_SESSION['adresse']) ? $_SESSION['adresse'] : 'Adresse non fournie';
    $total = 0;

    // Calcul du montant total de la commande
    foreach ($_SESSION['cart'] as $jeux_id => $quantite) {
        $query = $connexion->prepare("SELECT prix FROM jeux WHERE id = ?");
        $query->execute([$jeux_id]);
        $jeu = $query->fetch(PDO::FETCH_ASSOC);
        if ($jeu) {
            $total += $jeu['prix'] * $quantite;
        }
    }

    // Insérer la commande
    $stmt = $connexion->prepare("INSERT INTO commande (id_clients, montant, adresse) VALUES (:id_clients, :montant, :adresse)");
    $stmt->bindParam(':id_clients', $id_clients);
    $stmt->bindParam(':montant', $total);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->execute();

    // Récupérer l'ID de la commande insérée
    $id_commande = $connexion->lastInsertId();

    // Insérer les détails de la commande
    $stmt_detail = $connexion->prepare("INSERT INTO details_commande (id_commande, id_jeux, quantite) VALUES (:id_commande, :id_jeux, :quantite)");

    foreach ($_SESSION['cart'] as $jeux_id => $quantite) {
        $stmt_detail->execute([
            ':id_commande' => $id_commande,
            ':id_jeux' => $jeux_id,
            ':quantite' => $quantite,
        ]);
    }

    // Vider le panier après la commande
    unset($_SESSION['cart']);

    echo "Commande validée avec succès !";
    header("Location: confirmation.php");
    exit;
} catch (PDOException $e) {
    echo "Erreur lors de la validation de la commande : " . $e->getMessage();
}

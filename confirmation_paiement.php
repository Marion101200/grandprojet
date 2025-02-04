<?php
session_start();
include 'pdo.php';
include 'header.php';

if (!isset($_SESSION['id_client'])) {
    echo "Erreur : utilisateur non connecté.";
    exit;
}

try {
    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (empty($_SESSION['cart'])) {
        echo "Votre panier est vide.";
        exit;
    }

    $id_client = $_SESSION['id_client'];
    $adresse = $_SESSION['adresse'] ?? 'Adresse non fournie';
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

    try {
        // Insérer la commande
        $stmt = $connexion->prepare("INSERT INTO commande (id_client, montant, adresse) VALUES (:id_client, :montant, :adresse)");
        $stmt->execute([
            ':id_client' => $id_client,
            ':montant' => $total,
            ':adresse' => $adresse
        ]);

        // Récupérer l'ID de la commande insérée
        $id_commande = $connexion->lastInsertId();

        // Insérer les détails de la commande
        $stmt_detail = $connexion->prepare("INSERT INTO details_commande (id_commande, id_jeux, quantite) VALUES (:id_commande, :id_jeux, :quantite)");

        foreach ($_SESSION['cart'] as $jeux_id => $quantite) {
            $stmt_detail->execute([
                ':id_commande' => $id_commande,
                ':id_jeux' => $jeux_id,
                ':quantite' => $quantite
            ]);
        }

        unset($_SESSION['cart']);

        echo "Commande validée avec succès !";
        header("Location: confirmation.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de l'enregistrement de la commande : " . $e->getMessage();
    }
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}

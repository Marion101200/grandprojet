<?php

session_start();
include 'pdo.php';

if (!isset($_SESSION['id_client'])) {
    echo "Erreur : ID client non défini.";
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

    $total = 0;
    $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : ''; // Récupération de l'adresse

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
        // Insérer la commande dans la table 'commande'
        $stmt = $connexion->prepare("INSERT INTO commande (id_clients, montant, adresse) VALUES (:id_clients, :montant, :adresse)");
        $stmt->execute([
            ':id_clients' => $_SESSION['id_client'],
            ':montant' => $total,
            ':adresse' => $adresse,  // Ajout de l'adresse récupérée
        ]);

        // Récupérer l'ID de la commande insérée
        $id_commande = $connexion->lastInsertId();

        // Insérer les détails de la commande
        $stmt_detail = $connexion->prepare("INSERT INTO details_commande (id_commande, id_jeu, quantite) VALUES (:id_commande, :id_jeu, :quantite)");

        foreach ($_SESSION['cart'] as $jeux_id => $quantite) {
            $stmt_detail->execute([
                ':id_commande' => $id_commande,
                ':id_jeu' => $jeux_id,
                ':quantite' => $quantite
            ]);
        }

        // Vider le panier après validation de la commande
        unset($_SESSION['cart']);

        echo "Commande validée avec succès !";
        header("Location: historique_commandes.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de l'enregistrement de la commande : " . $e->getMessage();
    }
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}
include 'header.php';
?>
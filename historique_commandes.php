<?php
session_start();
include 'header.php';
include 'pdo.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Historique de commande</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="historique_commandes.css" rel="stylesheet">
</head>

<body>
    <div class="historique">
        <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px;">
            <i class='bx bxs-credit-card-alt'></i>&nbsp;Historique de paiement&nbsp;<i class='bx bxs-credit-card-alt'></i>
        </h2>

        <?php
        // Vérifier que le client est connecté
        if (!isset($_SESSION['id_client'])) {
            echo "<p>Vous devez être connecté pour consulter votre historique de commandes.</p>";
            exit;
        }

        try {
            require_once("connexion.php");
            $connexion = getConnexion();
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Récupérer les commandes du client connecté
            $stmt = $connexion->prepare("SELECT id, montant, date_commande FROM commande WHERE id_clients = :id_client ORDER BY date_commande DESC");
            $stmt->execute([':id_client' => $_SESSION['id_client']]);
            $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($commandes && count($commandes) > 0) {
                echo "<table>";
                echo "<tr>
                        <th>ID Commande</th>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Détails</th>
                      </tr>";

                // Affichage de chaque commande
                foreach ($commandes as $commande) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($commande['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($commande['date_commande']) . "</td>";
                    echo "<td>" . htmlspecialchars($commande['montant']) . " €</td>";
                    // Lien vers une page qui affichera les détails de la commande
                    echo "<td><a href='details_commande.php?id=" . urlencode($commande['id']) . "'>Voir détails</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Aucune commande trouvée.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Erreur lors de la récupération des commandes : " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>
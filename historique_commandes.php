<?php
session_start();
include 'header.php';
include 'pdo.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Historique de commandes</title>
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
        if (!isset($_SESSION['id_client'])) {
            echo "<p>Vous devez être connecté pour consulter votre historique de commandes.</p>";
            exit;
        }

        require_once("connexion.php");
        $connexion = getConnexion();
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT c.id_commande, cl.nom, cl.email, a.adresse, c.montant,
                   j.titre AS titre_jeu, j.id AS id_jeu
            FROM commande c
            JOIN clients cl ON c.id_clients = cl.id
            JOIN details_commande dc ON c.id_commande = dc.id_commande
            JOIN jeux j ON dc.id_jeu = j.id
            LEFT JOIN adresse a ON dc.id_adresse = a.id
            WHERE c.id_clients = :id_client";

        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(':id_client', $_SESSION['id_client'], PDO::PARAM_INT);
        $stmt->execute();
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <table>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Adresse</th>
                <th>Jeu</th>
                <th>Montant</th>
                <th>Actions</th>
            </tr>

            <?php if (count($commandes) > 0): ?>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['nom']) ?></td>
                        <td><?= htmlspecialchars($commande['email']) ?></td>
                        <td><?= htmlspecialchars($commande['adresse'] ?? 'Non spécifiée') ?></td>
                        <td><?= htmlspecialchars($commande['titre_jeu']) ?></td>
                        <td><?= htmlspecialchars($commande['montant']) ?> €</td>
                        <td>
                            <?php
                            // Vérifier si l'utilisateur a déjà laissé un avis sur ce jeu
                            $stmtAvis = $connexion->prepare("SELECT * FROM avis WHERE jeux_titre = ? AND nom = ?");
                            $stmtAvis->execute([$commande['titre_jeu'], $commande['nom']]);
                            $avis = $stmtAvis->fetch(PDO::FETCH_ASSOC);
                            ?>

                            <?php if (!$avis): ?>
                                <!-- Ajouter un avis -->
                                <form method="get" action="fiche_jeux.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $commande['id_jeu'] ?>">
                                    <button type="submit">📝 Ajouter un avis</button>
                                </form>
                            <?php else: ?>
                                <!-- Modifier l’avis -->
                                <form method="get" action="fiche_jeux.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $commande['id_jeu'] ?>">
                                    <input type="hidden" name="edit" value="<?= $avis['id'] ?>">
                                    <button type="submit">✏ Modifier</button>
                                </form>

                                <!-- Supprimer l’avis -->
                                <form method="post" action="fiche_jeux.php" style="display:inline;" onsubmit="return confirm('Supprimer cet avis ?');">
                                    <input type="hidden" name="id" value="<?= $commande['id_jeu'] ?>">
                                    <input type="hidden" name="supprimer_avis_id" value="<?= $avis['id'] ?>">
                                    <button type="submit">🗑 Supprimer</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">Aucune commande trouvée.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>
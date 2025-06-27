<?php
// Activer le rapport d'erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'pdo.php';
include 'ajouter_avis.php';
require_once("connexion.php");
$connexion = getConnexion();
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Europe/Paris');

$avis = [];

if (isset($_GET['id']) || isset($_POST['id'])) {
    $jeux_id = isset($_GET['id']) ? (int) $_GET['id'] : (int) $_POST['id'];

    $stmt = $connexion->prepare("SELECT * FROM jeux WHERE id = :id");
    $stmt->execute(['id' => $jeux_id]);
    $jeux = $stmt->fetch(PDO::FETCH_ASSOC);
    // Si l'utilisateur veut modifier un avis existant
    $avis_a_modifier = null;
    if (isset($_GET['edit']) && is_numeric($_GET['edit']) && isset($_SESSION['nom'])) {
        $stmt = $connexion->prepare("SELECT * FROM avis WHERE id = ? AND nom = ?");
        $stmt->execute([$_GET['edit'], $_SESSION['nom']]);
        $avis_a_modifier = $stmt->fetch(PDO::FETCH_ASSOC);
    }


    if (!$jeux) {
        echo "<p>Erreur : Jeu introuvable.</p>";
        exit;
    }
}

// Récupération des avis
$avisStmt = $connexion->prepare("SELECT * FROM avis WHERE jeux_titre = ? ORDER BY date_ajout DESC");
$avisStmt->execute([$jeux['titre']]);
$avis = $avisStmt->fetchAll();

if (isset($_SESSION['email'])) {
    $stmt = $connexion->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$_SESSION['email']]);
    $clients = $stmt->fetch(PDO::FETCH_ASSOC);
}

$moyenneStmt = $connexion->prepare("SELECT AVG(note) as moyenne FROM avis WHERE jeux_titre = ?");
$moyenneStmt->execute([$jeux['titre']]);
$moyenne = $moyenneStmt->fetch(PDO::FETCH_ASSOC)['moyenne'];
$moyenne = $moyenne !== null ? round($moyenne, 1) : 0;

include 'header.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($jeux['titre']); ?></title>
    <link rel="stylesheet" href="fiche_jeux.css">
</head>

<body>
    <div class="container">
        <div class="jeux-item">
            <img class="images_jeux" src="<?= htmlspecialchars($jeux['images']); ?>" alt="<?= htmlspecialchars($jeux['titre']); ?>">
            <h1><?= htmlspecialchars($jeux['titre']); ?></h1>
            <p class="description">
                <strong>Catégorie : </strong>
            <h6 class="details"><?= htmlspecialchars($jeux['categorie']); ?></h6>
            <strong>Description : </strong>
            <h6 class="details"><?= htmlspecialchars($jeux['description']); ?></h6>
            <strong>Date de sortie : </strong>
            <h6 class="details"><?= htmlspecialchars($jeux['date']); ?></h6>
            </p>
            <div class="prix_ajout">
                <h3 class="prix"><?= htmlspecialchars($jeux['prix']); ?> €</h3>

                <form class="favoris" method="post">
                    <input type="hidden" name="jeux_id" value="<?= htmlspecialchars($jeux['id']); ?>">
                    <p class="stock">
                        <?php if ($jeux['stock'] > 0): ?>
                            <strong>En stock :</strong> <?= $jeux['stock']; ?> exemplaire(s)
                        <?php else: ?>
                            <strong style="color:red;">Rupture de stock</strong>
                        <?php endif; ?>
                    </p>
                    <?php if ($jeux['stock'] > 0): ?>
                        <button class="ajout-panier" type="submit" name="add-to-cart">Ajouter au panier</button>
                    <?php else: ?>
                        <button class="ajout-panier" type="button" disabled style="background-color: gray;">Indisponible</button>
                    <?php endif; ?>

                    <input type="hidden" name="favori_id" value="<?= htmlspecialchars($jeux['id']); ?>">
                    <button class="ajout-favori" type="submit" name="add-to-favorites">❤️</button>
                </form>
            </div>
        </div>
    </div>

    <a href="panier.php" class="see_cart"><i class='bx bxs-cart'></i>&nbsp;Voir le panier&nbsp;<i class='bx bxs-cart'></i></a>

    <h2><i class='bx bxs-star'></i>&nbsp;Avis des utilisateurs&nbsp;<i class='bx bxs-star'></i></h2>

    <div class="moyenne-avis">
        <strong>Note moyenne : <?= $moyenne; ?>/5</strong><br>
        <span>
            <?php
            $fullStars = floor($moyenne);
            $halfStar = ($moyenne - $fullStars) >= 0.5 ? 1 : 0;
            $emptyStars = 5 - ($fullStars + $halfStar);
            echo str_repeat('⭐', $fullStars);
            if ($halfStar) echo '⭐️';
            echo str_repeat('☆', $emptyStars);
            ?>
        </span>
    </div>

    <h2 class="donner_avis"><i class='bx bxs-message-dots'></i>&nbsp;Donnez votre avis&nbsp;<i class='bx bxs-message-dots'></i></h2>

    <?php if (isset($_SESSION['nom'])): ?>
        <form id="commentaire" method="post">
            <input type="hidden" name="id" value="<?= $jeux['id']; ?>"> <!-- ✅ Ajout important -->
            <input type="hidden" name="jeux_titre" value="<?= htmlspecialchars($jeux['titre']); ?>">
            <input type="hidden" name="nom" value="<?= htmlspecialchars($clients['nom']); ?>">
            <input type="hidden" name="note" id="note" value="<?= isset($avis_a_modifier) ? $avis_a_modifier['note'] : 0 ?>">

            <label><i class='bx bxs-message-dots'></i>&nbsp;Commentaire&nbsp;<i class='bx bxs-message-dots'></i></label>
            <textarea name="commentaire" required><?= isset($avis_a_modifier) ? htmlspecialchars($avis_a_modifier['commentaire']) : '' ?></textarea>
            <?php if (isset($avis_a_modifier)): ?>
                <input type="hidden" name="modifier_avis_id" value="<?= $avis_a_modifier['id'] ?>">
            <?php endif; ?>


            <div class="star-rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star" data-value="<?= $i; ?>">★</span>
                <?php endfor; ?>
            </div>

            <button id="button_avis" type="submit">Envoyer</button>
        </form>
    <?php else: ?>
        <p class="avis_connexion"><a href="login.php"><i class='bx bx-user'></i>&nbsp;Connectez-vous</a> pour laisser un avis.&nbsp;<i class='bx bx-user'></i></p>
    <?php endif; ?>

    <h2><i class='bx bxs-star'></i>&nbsp;Avis des utilisateurs&nbsp;<i class='bx bxs-star'></i></h2>
    <?php foreach ($avis as $a): ?>
        <div class="avis">
            <strong><?= htmlspecialchars($a['nom']); ?></strong><br>
            <span><?= str_repeat('⭐', $a['note']); ?></span>
            <p><?= nl2br(htmlspecialchars($a['commentaire'])); ?></p>
            <small>Posté le <?= $a['date_ajout']; ?></small>
        </div>
    <?php endforeach; ?>

    <div class="retour-accueil">
        <a href="jeux.php"><i class='bx bxs-invader'></i>&nbsp;Retour à la liste de jeux&nbsp;<i class='bx bxs-invader'></i></a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll(".star");
            const noteInput = document.getElementById("note"); // <-- Ajoute cette ligne !

            const selectedNote = parseInt(noteInput.value);
            if (!isNaN(selectedNote)) {
                for (let i = 0; i < selectedNote; i++) {
                    stars[i].classList.add("selected");
                }
            }

            stars.forEach(star => {
                star.addEventListener("click", function() {
                    let value = this.getAttribute("data-value");
                    noteInput.value = value;
                    stars.forEach(s => s.classList.remove("selected"));
                    for (let i = 0; i < value; i++) {
                        stars[i].classList.add("selected");
                    }
                });
            });
        });
    </script>
</body>

</html>

<?php include 'footer.php'; ?>
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

// Initialisation de la variable avis
$avis = [];

// Récupération des détails du jeu
if (isset($_GET['id'])) {
    $jeux_id = (int) $_GET['id'];
    require_once("connexion.php");
    $connexion = getConnexion();

    $stmt = $connexion->prepare("SELECT * FROM jeux WHERE id = :id");
    $stmt->execute(['id' => $jeux_id]);
    $jeux = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$jeux) {
        echo "<p>Erreur : Jeu introuvable.</p>";
        exit;
    }
}

// Enregistrement des avis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['nom']) && !empty($_POST['commentaire']) && isset($_POST['note'])) {
    $nom = $_SESSION['nom'];
    $jeux_titre = $jeux['titre'];
    $commentaire = trim($_POST['commentaire']);
    $note = (int) $_POST['note'];
    $date_ajout = date("Y-m-d H:i:s");

    if ($note >= 1 && $note <= 5) {
        try {
            $stmt = $connexion->prepare("INSERT INTO avis (jeux_titre, nom, commentaire, note, date_ajout) VALUES (:jeux_titre, :nom, :commentaire, :note, :date_ajout)");
            $stmt->execute([
                'jeux_titre' => $jeux_titre,
                'nom' => $nom,
                'commentaire' => $commentaire,
                'note' => $note,
                'date_ajout' => $date_ajout
            ]);
        } catch (PDOException $e) {
            echo "<p>Erreur lors de l'ajout de l'avis : " . $e->getMessage() . "</p>";
        }
    }
}

// Récupération des avis
$avisStmt = $connexion->prepare("SELECT * FROM avis WHERE jeux_titre = ? ORDER BY date_ajout DESC");
$avisStmt->execute([$jeux['titre']]);
$avis = $avisStmt->fetchAll();

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $connexion->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $clients = $stmt->fetch(PDO::FETCH_ASSOC);
}
$moyenneStmt = $connexion->prepare("SELECT AVG(note) as moyenne FROM avis WHERE jeux_titre = ?");
$moyenneStmt->execute([$jeux['titre']]);
$moyenne = $moyenneStmt->fetch(PDO::FETCH_ASSOC)['moyenne'];
$moyenne = $moyenne !== null ? round($moyenne, 1) : 0;
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($jeux['titre']); ?></title>
    <link rel="stylesheet" href="fiche_jeux.css"> <!-- Votre CSS -->
</head>

<body>
    <div class="container">
        <div class="jeux-item">
            <img class="images_jeux" src="<?php echo htmlspecialchars($jeux['images']); ?>" alt="<?php echo htmlspecialchars($jeux['titre']); ?>">
            <h1><?= htmlspecialchars($jeux['titre']); ?></h1>
            <p class="description">
                <strong>Catégorie : </strong>
            <h6 class="details"><?php echo htmlspecialchars($jeux['categorie']); ?></h6>
            <strong>Description : </strong>
            <h6 class="details"><?php echo htmlspecialchars($jeux['description']); ?></h6>
            <strong>Date de sortie : </strong>
            <h6 class="details"><?php echo htmlspecialchars($jeux['date']); ?></h6>
            </p>
            <div class="prix_ajout">
                <h3 class="prix"><?php echo htmlspecialchars($jeux['prix']); ?> €</h3>

                <form class="favoris" method="post">
                    <input type="hidden" name="jeux_id" value="<?php echo htmlspecialchars($jeux['id']); ?>">
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

                    <input type="hidden" name="favori_id" value="<?php echo htmlspecialchars($jeux['id']); ?>">
                    <button class="ajout-favori" type="submit" name="add-to-favorites">
                        ❤️
                    </button>
                </form>
            </div>
        </div>
    </div>
    <a href="panier.php" class="see_cart"><i class='bx bxs-cart'></i>&nbsp;Voir le panier&nbsp;<i class='bx bxs-cart'></i></a>

    <h2><i class='bx bxs-star'></i>&nbsp;Avis des utilisateurs &nbsp;<i class='bx bxs-star'></i></h2>

    <div class="moyenne-avis">
        <strong>Note moyenne : <?= $moyenne; ?>/5</strong><br>
        <span>
            <?php
            $fullStars = floor($moyenne); // Étoiles pleines
            $halfStar = ($moyenne - $fullStars) >= 0.5 ? 1 : 0; // Étoile demi pleine
            $emptyStars = 5 - ($fullStars + $halfStar); // Étoiles vides

            echo str_repeat('⭐', $fullStars); // Affichage des étoiles pleines
            if ($halfStar) echo '⭐️'; // Affichage de l'étoile demi pleine
            echo str_repeat('☆', $emptyStars); // Affichage des étoiles vides
            ?>
        </span>
    </div>

    <h2 class="donner_avis"><i class='bx bxs-message-dots'></i>&nbsp;Donnez votre avis&nbsp;<i class='bx bxs-message-dots'></i></h2>
    <?php if (isset($_SESSION['nom'])): ?>
        <form id="commentaire" method="post">
            <input type="hidden" name="jeux_titre" value="<?php echo $jeux['titre']; ?>">
            <input type="hidden" name="nom" value="<?php echo $clients['nom']; ?>">
            <input type="hidden" name="note" id="note" value="0">
            <label><i class='bx bxs-message-dots'></i>&nbsp;Commentaire: &nbsp;<i class='bx bxs-message-dots'></i></label>

            <textarea name="commentaire" required></textarea>
            <div class="star-rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star" data-value="<?= $i; ?>">★</span>

                <?php endfor; ?>
            </div>
            <button id="button_avis" type="submit">Envoyer</button>
        </form>

    <?php else: ?>
        <p class="avis_connexion"><a class="avis_connexion" href="login.php"><i class='bx bx-user'></i>&nbsp;Connectez-vous</a> pour laisser un avis.&nbsp;<i class='bx bx-user'></i></p>
    <?php endif; ?>

    <h2><i class='bx bxs-star'></i>&nbsp;Avis des utilisateurs &nbsp;<i class='bx bxs-star'></i></h2>
    <?php foreach ($avis as $a): ?>
        <div class="avis">
            <strong><?= htmlspecialchars($a['nom']); ?></strong> <br>
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
            const noteInput = document.getElementById("note");

            stars.forEach(star => {
                star.addEventListener("click", function() {
                    let value = this.getAttribute("data-value");
                    noteInput.value = value;


                    stars.forEach(s => s.classList.remove("selected"));
                    for (let i = 0; i < value; i++) {
                        stars[i].classList.add("selected");
                    }
                })
            })
        });
    </script>
</body>

</html>
<?php include 'footer.php'; ?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">

<head>
  <title>Aventure</title>
  <link href="aventure.css" rel="stylesheet">
</head>

<body>
  <?php include 'header.php'; ?>
  <?php
try {
    require_once("connexion.php"); // Inclure le fichier de connexion à la base de données

    // Connexion à la base de données
    $connexion = getConnexion();

    // Préparer la requête pour récupérer les jeux de catégorie "aventure"
    $stmt = $connexion->prepare("SELECT * FROM jeux WHERE categorie = :categorie");
    $categorie = "Soulslike"; // Spécifier la catégorie recherchée
    $stmt->bindParam(':categorie', $categorie, PDO::PARAM_STR);

    // Exécuter la requête
    $stmt->execute();
} catch (PDOException $e) {
    // Gestion des erreurs
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
// Vérification et ajout au panier
if (isset($_POST['add-to-cart'])) {
$jeux_id = $_POST['jeux_id'];

// Initialiser le panier si nécessaire
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Ajout du jeu au panier
if (isset($_SESSION['cart'][$jeux_id])) {
  $_SESSION['cart'][$jeux_id]++; // Incrémenter la quantité si le jeu est déjà dans le panier
} else {
  $_SESSION['cart'][$jeux_id] = 1; // Ajouter le jeu avec une quantité de 1
}

// Rediriger vers la page du panier après ajout
// Ajouter un script JavaScript pour afficher un message de confirmation
echo "<script>
  if (confirm('Le jeu a été ajouté au panier. Voulez-vous aller sur votre panier ?')) {
    window.location.href = 'panier.php';
  } else {
    window.location.href = 'jeux.php';
  }
</script>";
exit;
}
if (isset($_POST['add-to-favorites'])) {
  if (!isset($_SESSION['nom'])) {
    echo "<script>
            alert('Vous devez être connecté pour ajouter des jeux à vos favoris.');
            window.location.href = 'login.php';
        </script>";
    exit;
  }

  $favori_id = (int)$_POST['favori_id'];

  try {
    require_once("connexion.php");
    $connexion = getConnexion();

    // Récupérer l'ID du client connecté
    $stmt = $connexion->prepare("SELECT id FROM clients WHERE nom = :nom");
    $stmt->execute(['nom' => $_SESSION['nom']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
      echo "<script>alert('Utilisateur non trouvé.');</script>";
      exit;
    }

    $clients_id = $client['id'];

    // Vérifier si le jeu est déjà dans les favoris
    $stmt = $connexion->prepare("SELECT * FROM user_favorites WHERE clients_id = :clients_id AND jeux_id = :jeux_id");
    $stmt->execute(['clients_id' => $clients_id, 'jeux_id' => $favori_id]);

    if ($stmt->rowCount() == 0) {
      // Ajouter le jeu aux favoris
      $stmt = $connexion->prepare("INSERT INTO user_favorites (clients_id, jeux_id) VALUES (:clients_id, :jeux_id)");
      $stmt->execute(['clients_id' => $clients_id, 'jeux_id' => $favori_id]);

      echo "<script>
                alert('Le jeu a été ajouté à vos favoris.');
                window.location.href = 'favoris.php';
            </script>";
      exit;
    } else {
      echo "<script>alert('Ce jeu est déjà dans vos favoris.');</script>";
    }
  } catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
  }
}
?>
<?php
try {
    require_once("connexion.php");
    $connexion = getConnexion();

    $stmt = $connexion->prepare("SELECT * FROM jeux WHERE categorie = :categorie");
    $categorie = "Soulslike";
    $stmt->bindParam(':categorie', $categorie, PDO::PARAM_STR);
    $stmt->execute();

    $jeu = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($jeu && count($jeu) > 0): ?>
        <!-- Liste des jeux -->
        <div class="jeux-listes">
        <?php foreach ($jeu as $jeux):
          $moyenneStmt = $connexion->prepare("SELECT AVG(note) as moyenne FROM avis WHERE jeux_titre = ?");
          $moyenneStmt->execute([$jeux['titre']]);
          $moyenne = $moyenneStmt->fetch(PDO::FETCH_ASSOC)['moyenne'];
          $moyenne = $moyenne !== null ? round($moyenne, 1) : 0; // Arrondir à 1 chiffre après la virgule
  
  
  
          $testStmt = $connexion->prepare("SELECT note FROM avis WHERE jeux_titre = ?");
          $testStmt->execute([$jeux['titre']]);
          $allNotes = $testStmt->fetchAll(PDO::FETCH_COLUMN);?>
            <div class="jeux-item">
                <img class="images_jeux"
                    src="<?php echo htmlspecialchars($jeux['images']); ?>"
                    alt="<?php echo htmlspecialchars($jeux['titre']); ?>"
                    onerror="this.onerror=null; this.src='img/logo.jpg';">
                <div class="element_jeux">
                    <h2 class="produit_id">
                        <a href="fiche_jeux.php?id=<?php echo htmlspecialchars($jeux['id']); ?>">
                            <?php echo htmlspecialchars($jeux['titre']); ?>
                        </a>
                    </h2>
                    <p class="description">
                        <strong>Catégorie : </strong><h6 class="details"><?php echo htmlspecialchars($jeux['categorie']); ?></h6>
                        <strong>Description : </strong><h6 class="details"><?php echo htmlspecialchars($jeux['description']); ?></h6>
                        <strong>Date de sortie : </strong><h6 class="details"><?php echo htmlspecialchars($jeux['date']); ?></h6>
                    </p>
                    <div class="prix_ajout">
                        <h3 class="prix"><?php echo htmlspecialchars($jeux['prix']); ?> €</h3>
                        <div class="moyenne-avis">
                        <strong>Note moyenne : <?= htmlspecialchars($moyenne); ?>/5</strong><br>

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
                        <form method="post" class="favoris">
                            <input type="hidden" name="jeux_id" value="<?php echo htmlspecialchars($jeux['id']); ?>">
                            <button class="ajout-panier" type="submit" name="add-to-cart">Ajouter au panier</button>
                            <input type="hidden" name="favori_id" value="<?= htmlspecialchars($jeux['id']) ?>">
              <button class="ajout-favori" type="submit" name="add-to-favorites">❤️</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucun jeu trouvé dans cette catégorie.</p>
    <?php endif;
} catch (PDOException $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
?>
<a href="panier.php" class="see_cart">
    <i class='bx bxs-cart'></i>&nbsp;Voir le panier&nbsp;<i class='bx bxs-cart'></i>
</a>

<script>
        document.addEventListener('DOMContentLoaded', function() {
  const stars = document.querySelectorAll(".star");
  const noteInput = document.getElementById("note_min_input");
  const noteDisplay = document.getElementById("note_min");

  stars.forEach(star => {
    star.addEventListener("click", function() {
      let value = this.getAttribute("data-value");
      noteInput.value = value;
      noteDisplay.textContent = value;

      stars.forEach(s => s.classList.remove("selected"));
      for (let i = 0; i < value; i++) {
        stars[i].classList.add("selected");
      }

      // Soumettre automatiquement le formulaire après sélection
      debounceSubmit();
    });
  });
});
      maxSlider.addEventListener('input', function() {
        maxPrixLabel.textContent = maxSlider.value;
        debounceSubmit();
      });

        </script>

  <script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll(".star"); // Sélectionner les étoiles
    const noteInput = document.getElementById("note_min_input"); // Le champ caché pour la note
    const noteDisplay = document.getElementById("note_min"); // Afficher la note sélectionnée

    stars.forEach(star => {
        star.addEventListener("click", function() {
            let value = this.getAttribute("data-value"); // Obtenir la valeur de la note (1-5)
            noteInput.value = value;  // Mettre la valeur dans le champ caché
            noteDisplay.textContent = `Note sélectionnée : ${value} étoiles`;  // Afficher la note

            // Ajoutez ou supprimez la classe 'selected' sur les étoiles
            stars.forEach(s => s.classList.remove("selected"));
            for (let i = 0; i < value; i++) {
                stars[i].classList.add("selected");
            }

            // Soumettre automatiquement le formulaire après sélection de la note exacte
            document.getElementById('filterForm').submit();
        });
    });
});






  </script> 
  </body>
</html>
<?php
include 'footer.php';
?>
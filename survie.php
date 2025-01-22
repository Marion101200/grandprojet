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
    $categorie = "Survie"; // Spécifier la catégorie recherchée
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
?>
<?php
try {
    require_once("connexion.php");
    $connexion = getConnexion();

    $stmt = $connexion->prepare("SELECT * FROM jeux WHERE categorie = :categorie");
    $categorie = "Survie";
    $stmt->bindParam(':categorie', $categorie, PDO::PARAM_STR);
    $stmt->execute();

    $jeu = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($jeu && count($jeu) > 0): ?>
        <!-- Liste des jeux -->
        <div class="jeux-listes">
        <?php foreach ($jeu as $jeux): ?>
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
                        <form method="post">
                            <input type="hidden" name="jeux_id" value="<?php echo htmlspecialchars($jeux['id']); ?>">
                            <button class="ajout-panier" type="submit" name="add-to-cart">Ajouter au panier</button>
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


  </body>
</html>
<?php
include 'footer.php';
?>
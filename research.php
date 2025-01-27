<?php
// Activer le rapport d'erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'header.php';
include 'pdo.php';

// Démarrer la session si elle n'est pas déjà active
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Gestion de la recherche
if (isset($_GET['query'])) {
  $search = htmlspecialchars($_GET['query']);

  require_once("connexion.php");
  $connexion = getConnexion();

  $stmt = $connexion->prepare("SELECT * FROM jeux WHERE titre LIKE :query");
  $stmt->execute(['query' => '%' . $search . '%']);
  $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Ajout au panier
if (isset($_POST['add-to-cart'])) {
  $jeux_id = (int)$_POST['jeux_id'];

  if (isset($_SESSION['cart'][$jeux_id])) {
    $_SESSION['cart'][$jeux_id]++; // Incrémenter la quantité
  } else {
    $_SESSION['cart'][$jeux_id] = 1; // Ajouter avec une quantité de 1
  }

  // Redirection ou confirmation
  echo "<script>
        alert('Le jeu a été ajouté au panier.');
        window.location.href = 'panier.php';
    </script>";
  exit;
}

// Ajout aux favoris
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


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Résultats de recherche</title>
  <link rel="stylesheet" href="research.css"> <!-- Votre fichier CSS -->
</head>

<body>
  <h1 class="recherche"><i class='bx bxs-search'></i>&nbsp;Résultats de recherche pour "<?= htmlspecialchars($search) ?>"&nbsp;<i class='bx bxs-search'></i></h1>
  <div class="jeux-listes">
    <?php if (!empty($resultats)): ?>
      <?php foreach ($resultats as $jeux): ?>
        <div class="jeux-item">
          <img class="images_jeux" src="<?php echo htmlspecialchars($jeux['images']); ?>" alt="<?php echo htmlspecialchars($jeux['titre']); ?>">
          <h2 class="produit_id">
            <a href="fiche_jeux.php?id=<?php echo htmlspecialchars($jeux['id']); ?>">
              <?php echo htmlspecialchars($jeux['titre']); ?>
            </a>
          </h2>
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
              <input type="hidden" name="jeux_id" value="<?= htmlspecialchars($jeux['id']) ?>">
              <button class="ajout-panier" type="submit" name="add-to-cart">Ajouter au panier</button>
              <input type="hidden" name="favori_id" value="<?= htmlspecialchars($jeux['id']) ?>">
              <button class="ajout-favori" type="submit" name="add-to-favorites">❤️</button>
            </form>
          </div>
        </div>

      <?php endforeach; ?>
  </div>
<?php else: ?>
    <div class="not_found">
      <p><i class='bx bx-error'></i>&nbsp;Aucun résultat trouvé pour votre recherche.&nbsp;<i class='bx bx-error'></i></p>
    </div>
  <?php endif; ?>

  <a href="panier.php" class="see_cart"><i class='bx bxs-cart'></i>&nbsp;Voir le panier&nbsp;<i class='bx bxs-cart'></i></a>
  <div class="retour-accueil">
    <a href="accueil.php"><i class='bx bxs-invader'></i>&nbsp;Retour à la page d'accueil&nbsp;<i class='bx bxs-invader'></i></a>
  </div>
</body>
</html>
<?php include 'footer.php'; ?>
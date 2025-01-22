<?php
// Activer le rapport d'erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'header.php';
include 'pdo.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Récupération des détails du jeu
if (isset($_GET['id'])) {
    $jeux_id = (int) $_GET['id'];
    require_once("connexion.php");
    $connexion = getConnexion();

    $stmt = $connexion->prepare("SELECT * FROM jeux WHERE id = :id");
    $stmt->execute(['id' => $jeux_id]);
    $jeux = $stmt->fetch(PDO::FETCH_ASSOC);
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
        $_SESSION['cart'][$jeux_id]++;
    } else {
        $_SESSION['cart'][$jeux_id] = 1;
    }

    echo "<script>
        if (confirm('Le jeu a été ajouté au panier. Voulez-vous aller sur votre panier ?')) {
            window.location.href = 'panier.php';
        } else {
            window.location.href = 'jeux.php';
        }
    </script>";
    exit;
}

// Vérification et ajout aux favoris
if (isset($_POST['add-to-favorites'])) {
    if (!isset($_SESSION['nom'])) {
        echo "<script>
            alert('Vous devez être connecté pour ajouter des jeux à vos favoris.');
            window.location.href = 'login.php';
        </script>";
        exit;
    }

    $favori_id = (int)$_POST['favori_id'];

    // Connexion à la base de données
    try {
        require_once("connexion.php");
        $connexion = getConnexion();

        // Récupérer l'ID du client en fonction du nom
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
                if (confirm('Le jeu a été ajouté à vos favoris ! Voulez-vous aller sur vos favoris ?')) {
                    window.location.href = 'favoris.php';
                } else {
                    window.location.href = 'jeux.php';
                }
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
          <button class="ajout-panier" type="submit" name="add-to-cart">Ajouter au panier</button>
          <input type="hidden" name="favori_id" value="<?php echo htmlspecialchars($jeux['id']); ?>">
          <button class="ajout-favori" type="submit" name="add-to-favorites">
            ❤️
          </button>
        </form>
      </div>
    </div>
  </div>
  <a href="panier.php" class="see_cart"><i class='bx bxs-cart'></i>&nbsp;Voir le panier&nbsp;<i class='bx bxs-cart'></i></a>
  <div class="retour-accueil">
  <a href="accueil.php"><i class='bx bxs-invader'></i>&nbsp;Retour à la liste de jeux&nbsp;<i class='bx bxs-invader'></i></a>
</div>
</body>

</html>
<?php include 'footer.php'; ?>

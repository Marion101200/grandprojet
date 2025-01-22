<?php
// Assurez-vous de démarrer la session en haut de votre fichier

// Activer le rapport d'erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'header.php';
include 'pdo.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

// Effacer tout le panier
if (isset($_POST['clear_cart'])) {
  unset($_SESSION['cart']);
  header("Location: panier.php");
  exit;
}

// Ajouter un jeu au panier avec la gestion des quantités
if (isset($_POST['add_to_cart'])) {
  $jeux_id = $_POST['jeux_id'];

  // Si le panier n'existe pas, initialiser un tableau
  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  // Vérifier si le jeu est déjà dans le panier
  if (isset($_SESSION['cart'][$jeux_id])) {
    $_SESSION['cart'][$jeux_id]++; // Incrémenter la quantité
  } else {
    $_SESSION['cart'][$jeux_id] = 1; // Ajouter avec une quantité de 1
  }

  // Débogage : Afficher le contenu de la session
  echo "<pre>";
  var_dump($_SESSION['cart']);
  echo "</pre>";
  header("Location: panier.php");
  exit;
}

// Retirer un jeu du panier (supprimer complètement un article)
if (isset($_POST['remove_item'])) {
  $item_id = $_POST['item_id'];
  if (isset($_SESSION['cart'][$item_id])) {
  $_SESSION['cart'][$item_id]--; // Supprimer l'élément du panier
  }
  if($_SESSION['cart'][$item_id]<=0){
    unset($_SESSION['cart'][$item_id]);
  }
  header("Location: panier.php");
  exit;
}

try {
  require_once("connexion.php");
  $connexion = getConnexion();
  $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if (!empty($cart_items)) {
    // Vérification de la structure des clés du panier
    $item_ids = array_keys($cart_items);

    // Vérifier s'il y a bien des IDs à récupérer
    if (count($item_ids) > 0) {
      // Construire les placeholders dynamiques
      $placeholders = str_repeat('?,', count($item_ids) - 1) . '?';

      // Préparer la requête SQL
      $query = $connexion->prepare("SELECT * FROM jeux WHERE id IN ($placeholders)");

      // Exécuter la requête avec les IDs du panier
      $query->execute($item_ids);

      // Récupérer les jeux dans le panier
      $jeux_in_cart = $query->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $jeux_in_cart = [];
    }
  } else {
    $jeux_in_cart = [];
  }
} catch (PDOException $e) {
  echo "Erreur de connexion : " . $e->getMessage();
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Panier</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="panier.css" rel="stylesheet">
</head>

<body>
  <h1 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px"> <i class='bx bxs-cart'></i> &nbsp;Votre Panier &nbsp; <i class='bx bxs-cart'></i></h1>
<div class="panier-content">
  <?php if (!empty($jeux_in_cart)): ?>
    <table>
      <thead>
        <tr>
          <th>Image</th>
          <th>Titre</th>
          <th>Prix Unitaire</th>
          <th>Quantité</th>
          <th>Total</th>
          <th id="actions">Actions</th>
        </tr>
      </thead>
      <tbody>
        
        <?php foreach ($jeux_in_cart as $jeux): ?>
          <tr>
            <td><img src="<?php echo htmlspecialchars($jeux['images']); ?>" alt="<?php echo htmlspecialchars($jeux['titre']); ?>" width="150px"></td>
            <td>
              <h2><?php echo htmlspecialchars($jeux['titre']); ?></h2>
            </td>
            <td>€<?php echo htmlspecialchars($jeux['prix']); ?></td>
            <td><?php echo $cart_items[$jeux['id']]; ?> <!-- Afficher la quantité actuelle --></td>
            <td>€<?php echo number_format(floatval($jeux['prix']) * intval($cart_items[$jeux['id']]), 2); ?> <!-- Prix total par jeu --></td>
            <td>
              <!-- Formulaire pour ajouter un autre jeu identique au panier -->
              <form method="post" style="display: inline;">
                <input type="hidden" name="jeux_id" value="<?php echo $jeux['id']; ?>">
                <button type="submit" name="add_to_cart">Ajouter</button>
              </form>

              <!-- Formulaire pour supprimer le jeu du panier -->
              <form method="post" style="display: inline;">
                <input type="hidden" name="item_id" value="<?php echo $jeux['id']; ?>">
                <button type="submit" name="remove_item">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php $total += floatval($jeux['prix']) * intval($cart_items[$jeux['id']]); ?>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="total">
      <p id="prix">Total:<?php echo number_format($total, 2); ?> €</p>
      <form method="get" action="https://buy.stripe.com/test_cN25lj5lK8g251e8ww">
        <button type="submit" name="finaliser_cart">Finaliser le Panier</button>
      </form>
      <form method="post">
        <button type="submit" name="clear_cart">Vider le panier</button>
      </form>
    <?php else: ?>
      <p class="panier-vide"><i class='bx bxs-cart'></i>&nbsp;Votre panier est vide. &nbsp;<i class='bx bxs-cart'></i></p>
    <?php endif; ?>
    <a id="return" href="jeux.php"><i class='bx bxs-invader'></i>&nbsp;Retourner à la liste de jeux&nbsp;<i class='bx bxs-invader'></i></a>
    </div>
</div>
    <?php include 'footer.php'; ?>
</body>

</html>
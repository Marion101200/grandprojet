<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="jeux.css">
  <title>Jeux</title>
</head>

<body>
  <?php
  session_start();
  ?>

  <?php
  include 'pdo.php';
  // include 'ajouter_avis.php';

  
  try {
    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $minNote = isset($_GET['note_min']) ? $_GET['note_min'] : null;



    // Récupérer les valeurs min et max de prix
    $prixQuery = $connexion->query("SELECT MIN(prix) AS min_prix, MAX(prix) AS max_prix FROM jeux");
    $prixResult = $prixQuery->fetch(PDO::FETCH_ASSOC);
    $minPrixGlobal = $prixResult['min_prix'] ?? 10;
    $maxPrixGlobal = $prixResult['max_prix'] ?? 80;

    // Valeurs initiales des sliders
    $minPrix = $_GET['prix_min'] ?? $minPrixGlobal;
    $maxPrix = $_GET['prix_max'] ?? $maxPrixGlobal;

    $noteSelectionnee = isset($_GET['note']) ? (int)$_GET['note'] : null;
    $sql = "SELECT j.id, j.titre, j.categorie, j.prix, j.images, j.description, j.date, 
    COALESCE(AVG(a.note), 0) AS moyenne_note
    FROM jeux j 
    LEFT JOIN avis a ON j.titre = a.jeux_titre
    WHERE j.prix BETWEEN :minPrix AND :maxPrix";
    

// Ajout des filtres de catégories dynamiquement
$filterConditions = [];
$categories = [
  'RPG',
  'Aventure',
  'Horreur',
  'Survie',
  'Soulslike',
  'Action',
  'Historique'
];
foreach ($categories as $category) {
if (isset($_GET["filter_$category"])) {
    $filterConditions[] = "categorie = :cat_$category";
    $params[":cat_$category"] = $category;
}
}

if (!empty($filterConditions)) {
$sql .= " AND (" . implode(" OR ", $filterConditions) . ")";
}

$sql .= " GROUP BY j.id, j.titre, j.categorie, j.prix, j.images, j.description, j.date";

if ($minNote !== null) {
  $sql .= " HAVING moyenne_note >= :minNote";
  $params[':minNote'] = $minNote;
} 
if ($noteSelectionnee !== null) {
  $sql .= " AND moyenne_note = :noteSelectionnee";
  $params[':noteSelectionnee'] = $noteSelectionnee;
} 



// Ajouter le paramètre de note minimale
$params = [
    ':minPrix' => $minPrix,
    ':maxPrix' => $maxPrix,
    ':minNote' => $minNote,
    ':noteSelectionnee' => $noteSelectionnee  // Assurez-vous que 'minNote' est bien défini en PHP
];

    // Filtres des catégories
    $categories = [
      'RPG',
      'Aventure',
      'Horreur',
      'Survie',
      'Soulslike',
      'Action',
      'Historique'
    ];
    $filterConditions = [];
    foreach ($categories as $category) {
      if (isset($_GET["filter_$category"])) {
        $filterConditions[] = "categorie = :cat_$category";
        $params[":cat_$category"] = $category;
      }
    }

    // Ajout des conditions des catégories
// Ajouter les conditions des catégories
// Ajouter les conditions des catégories
if (!empty($filterConditions)) {
  $sql .= " AND (" . implode(" OR ", $filterConditions) . ")";
} else {
  // Assurez-vous que si aucun filtre n'est sélectionné, tous les jeux sont affichés
  $sql .= "";
} // Affiche tous les paramètres GET pour vérifier les filtres


    // Ajout des conditions de prix
    $sql .= " AND prix BETWEEN :minPrix AND :maxPrix";

    // Préparation et exécution de la requête
    $query = $connexion->prepare($sql);
    $query->execute($params);
    $jeu = $query->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
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

  // Vérification et ajout aux favoris
  if (isset($_POST['add-to-favorites'])) {
    if (!isset($_SESSION['nom'])) {
      echo "<script>alert('Vous devez être connecté pour ajouter des jeux à vos favoris.');
      window.location.href = 'login.php'</script>";
    } else {
      $favori_id = (int)$_POST['favori_id'];
      $nom_utilisateur = $_SESSION['nom']; // Nom de l'utilisateur connecté

      try {
        require_once("connexion.php");
        $connexion = getConnexion();

        // Récupérer l'ID du client en fonction du nom
        $stmt = $connexion->prepare("SELECT id FROM clients WHERE nom = :nom");
        $stmt->execute(['nom' => $nom_utilisateur]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$client) {
          echo "<script>alert('Utilisateur non trouvé.');</script>";
          exit;
        }

        $clients_id = $client['id']; // ID du client

        // Vérifier si le jeu est déjà dans les favoris
        $stmt = $connexion->prepare("SELECT * FROM user_favorites WHERE clients_id = :clients_id AND jeux_id = :jeux_id");
        $stmt->execute(['clients_id' => $clients_id, 'jeux_id' => $favori_id]);

        if ($stmt->rowCount() == 0) {
          // Ajouter le jeu aux favoris dans la base de données
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
  }
  include 'header.php';
  ?>

  <h1 class="titre_jeux" style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px">
    <i class='bx bx-game'></i> &nbsp;Les jeux que nous proposons ! &nbsp;<i class='bx bx-game'></i>
  </h1>
  <div class="filter_game">
    <!-- Formulaire des filtres -->
    <form method="get" id="filterForm">
      <h1 class="titre_filter"><i class='bx bx-game'></i> &nbsp;FILTRES : &nbsp;<i class='bx bx-game'></i></h1>
      <div class="line"></div>
      <!-- Filtres des catégories -->
      <div class="categorie_game">
        <h1 class="categorie_titre"><i class='bx bx-game'></i>&nbsp;Catégories :&nbsp;<i class='bx bx-game'></i></h1>
        <label class="categorie_jeux">
          <?php foreach ($categories as $category): ?>
            <div class="categorie_item">
              <input type="checkbox" name="filter_<?php echo $category; ?>"
                <?php echo isset($_GET["filter_$category"]) ? 'checked' : ''; ?>>
              <?php echo $category; ?>
            </div>
          <?php endforeach; ?>
        </label>
      </div>
      <div class="line"></div>
      <div class="prix_game">
        <h1 class="prix_item"><i class='bx bx-money'></i>&nbsp;Prix :&nbsp;<i class='bx bx-money'></i></h1>
        <!-- Sliders de prix -->
        <div class="prix_slider">
          <div class="min">
            Min :

            <input type="range"
              name="prix_min"
              min="<?php echo $minPrixGlobal; ?>"
              max="<?php echo $maxPrixGlobal; ?>"
              value="<?php echo htmlspecialchars($minPrix); ?>"
              step="1"
              id="minPrix">
          </div>
          <br>
          <div class="max">
            Max :

            <input type="range"
              name="prix_max"
              min="<?php echo $minPrixGlobal; ?>"
              max="<?php echo $maxPrixGlobal; ?>"
              value="<?php echo htmlspecialchars($maxPrix); ?>"
              step="1"
              id="maxPrix">
          </div>
        </div>

        <!-- Affichage des valeurs -->
        <div class="price_values">
          <p>
          <div class="euro"><span id="prix_min"><?php echo htmlspecialchars($minPrix); ?> </span>€</div>
          <div class="line2"></div>
          <div class="euro"><span id="prix_max"><?php echo htmlspecialchars($maxPrix); ?></span> €</div>
          </p>
        </div>
<div class="line"></div>
        <div class="note_game">
  <h1 class="note_item"><i class='bx bxs-star'></i>&nbsp;Notes:&nbsp;<i class='bx bxs-star'></i></h1>
  <div class="star-rating">
    <span class="star" data-value="1">★</span>
    <span class="star" data-value="2">★</span>
    <span class="star" data-value="3">★</span>
    <span class="star" data-value="4">★</span>
    <span class="star" data-value="5">★</span>
  </div>
  <input type="hidden" name="note_min" id="note_min_input" value="<?php echo htmlspecialchars($minNote); ?>">
  <p class="noteMin">Note minimale: &nbsp; <span id="note_min"><?php echo htmlspecialchars($minNote); ?></span>/5</p>
</div>
      </div>

      <!-- Bouton de réinitialisation des filtres -->
<button type="reset" class="reset-button" onclick="resetFilters()">Réinitialiser les filtres</button>

    </form>

    <!-- Liste des jeux -->
    <div class="jeux-listes">
      <?php foreach ($jeu as $jeux): {
          $moyenneStmt = $connexion->prepare("SELECT AVG(note) as moyenne FROM avis WHERE jeux_titre = ?");
          $moyenneStmt->execute([$jeux['titre']]);
          $moyenne = $moyenneStmt->fetch(PDO::FETCH_ASSOC)['moyenne'];
          $moyenne = round($moyenne, 1); // Arrondir à 1 chiffre après la virgule



          $testStmt = $connexion->prepare("SELECT note FROM avis WHERE jeux_titre = ?");
          $testStmt->execute([$jeux['titre']]);
          $allNotes = $testStmt->fetchAll(PDO::FETCH_COLUMN);
        } ?>
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
              <strong>Catégorie : </strong>
            <h6 class="details"><?php echo htmlspecialchars($jeux['categorie']); ?></h6>
            <strong>Description : </strong>
            <h6 class="details"><?php echo htmlspecialchars($jeux['description']); ?></h6>
            <strong>Date de sortie : </strong>
            <h6 class="details"><?php echo htmlspecialchars($jeux['date']); ?></h6>
            <div class="prix_ajout">
              <h3 class="prix"><?php echo htmlspecialchars($jeux['prix']); ?> €</h3>
              </p>


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
      <?php endforeach; ?>
    </div>
  </div>
  <a href="panier.php" class="see_cart">
    <i class='bx bxs-cart'></i>&nbsp;Voir le panier&nbsp;<i class='bx bxs-cart'></i>
  </a>

  <?php include 'footer.php'; ?>

  <script>
    const minSlider = document.getElementById('minPrix');
    const maxSlider = document.getElementById('maxPrix');
    const minPrixLabel = document.getElementById('prix_min');
    const maxPrixLabel = document.getElementById('prix_max');
    const filterForm = document.getElementById('filterForm');

    // Mise à jour dynamique des sliders
    if (minSlider && maxSlider) {
      minSlider.addEventListener('input', function() {
        minPrixLabel.textContent = minSlider.value;
        debounceSubmit();
      });
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
    }

    // Gestion des checkboxes
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', () => filterForm.submit());
    });

    // Fonction de debounce pour éviter les soumissions multiples
    let timer;

    function debounceSubmit() {
      clearTimeout(timer);
      timer = setTimeout(() => filterForm.submit(), 300);
    }

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
  <script>
function resetFilters() {
  // Réinitialiser les valeurs des sliders
  document.getElementById('minPrix').value = <?php echo $minPrixGlobal; ?>;
  document.getElementById('maxPrix').value = <?php echo $maxPrixGlobal; ?>;
  document.getElementById('prix_min').textContent = <?php echo $minPrixGlobal; ?>;
  document.getElementById('prix_max').textContent = <?php echo $maxPrixGlobal; ?>;

  // Réinitialiser les étoiles
  const stars = document.querySelectorAll(".star");
  stars.forEach(star => star.classList.remove("selected"));
  document.getElementById('note_min').textContent = 0;

  // Réinitialiser le champ caché pour la note
  document.getElementById('note_min_input').value = 0;

  // Désélectionner les catégories
  const checkboxes = document.querySelectorAll('input[type="checkbox"]');
  checkboxes.forEach(checkbox => checkbox.checked = false);

  // Soumettre le formulaire pour réinitialiser la page
  document.getElementById('filterForm').submit();
}



    </script>
</body>

</html>
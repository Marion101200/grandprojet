<!DOCTYPE html>
<html>

<head>
  <title>Accueil</title>
  <meta charset="utf-8">
  <link href="AccueilLogin.css" rel="stylesheet">

</head>

<body>
<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>
  <?php
  include 'pdo.php';
  ?>
  <div class="header-container">

    <?php
       if (!isset($_SESSION['nom'])){
        include 'headerAccueil.php';
  
    } else{
       include 'header.php';
    }
    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['nom'])) {
      echo " <h2 class='bienvenue'> <i class='bx bx-game' ></i>Bon retour parmis nous, " . $_SESSION['nom'] . "!<i class='bx bx-game' ></i> </h2>";
    }

    // Si l'utilisateur clique sur "Se déconnecter"
    if (isset($_POST['logout'])) {
      session_destroy();
      header("Location: logout.php");
      exit();
    }
    // Connexion à la base de données (assure-toi que $connexion est bien défini)
$stmt = $connexion->prepare("SELECT DISTINCT categorie FROM jeux"); // DISTINCT pour éviter les doublons
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si des catégories existent avant de créer les groupes
$chunks = !empty($categories) ? array_chunk($categories, 4) : [];

    ?>
  </div>
  <div class="welcome"><i class='bx bx-game'></i>&nbsp; <span id="dynamic-text">Bienvenue à l'univers des jeux !</span> &nbsp;<i class='bx bx-game'></i></div>

  <div class="carousel">
    <div class="slides">
        <?php
        require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Connexion à la base de données

        // Sélection des 3 jeux spécifiques (avec leur titre ou leur ID)
        $stmt = $connexion->prepare("SELECT id, titre, images FROM jeux WHERE titre IN ('Shadow of the Tomb Raider', 'Baldur\'s Gate 3', 'Assassin\'s Creed Odyssey')");
        $stmt->execute();
        $jeux = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($jeux as $jeu) {
          $imagePath =  htmlspecialchars($jeu['images']);
          echo '<a href="fiche_jeux.php?id=' . $jeu['id'] . '">
                  <img class="slide" src="' . $imagePath . '" alt="' . htmlspecialchars($jeu['titre']) . '">
                </a>';
      }
        ?>
    </div>
    <button class="prev" onclick="prevSlide()">&#10096;</button>
    <button class="next" onclick="nextSlide()">&#10097;</button>
</div>


<p class="nouveautés">Les nouveautés</p>
<div class="nouveautes-container">
    <?php
    include 'pdo.php';
    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Connexion à la base de données

    // Sélectionner les jeux récents, triés par date d'ajout, pour afficher les nouveaux jeux
    $stmt = $connexion->prepare("SELECT id, titre, images FROM jeux ORDER BY date_ajout DESC LIMIT 3");  // Tu peux ajuster le LIMIT pour le nombre d'images à afficher
    $stmt->execute();
    $jeux = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($jeux as $jeu) {
        $imagePath = htmlspecialchars($jeu['images']);
        echo '<a href="fiche_jeux.php?id=' . $jeu['id'] . '" class="jeu-card">
                <img class="jeu-image" src="' . $imagePath . '" alt="' . htmlspecialchars($jeu['titre']) . '">
              </a>';
    }
    ?>
</div>
<p class="catégories">Les catégories</p>
<div class="carousel-containerCategories">
    <button class="carousel-buttonCategories prev" onclick="moveCarouselCategories(-1)">❮</button>
    <div class="carousel-wrapperCategories">
        <?php if (!empty($chunks)): ?>
            <?php foreach ($chunks as $chunk): ?>
                <div class="carousel-section">
                    <?php foreach ($chunk as $jeux): ?>
                      <?php
// Associer les catégories aux fichiers spécifiques
$categoryPages = [
    'Survie' => 'survie.php',
    'Horreur' => 'horreur.php',
    'RPG' => 'rpg.php',
    'Action' => 'action.php',
    'Historique' => 'historique.php',
    'Aventure' => 'aventure.php',
    "Soulslike" => 'soulslike.php',
    // Ajoute d'autres catégories si besoin
];

$categorie = $jeux['categorie'];
$page = isset($categoryPages[$categorie]) ? $categoryPages[$categorie] : 'categorie.php';

?>

<a href="<?= $page ?>?categorie=<?= urlencode($categorie) ?>" class="category-card">
    <?= htmlspecialchars($categorie) ?>
</a>

                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune catégorie disponible.</p>
        <?php endif; ?>
    </div>
    <button class="carousel-buttonCategories next" onclick="moveCarouselCategories(1)">❯</button>
</div>


<script src="accueil.js"></script>
  <script>
$(document).ready(function() {
    let inputRecherche = $('input[name="query"]');

    inputRecherche.on('input', function() {
        var valeur_saisie = $(this).val();
        if (valeur_saisie.length > 1) {
            $.ajax({
                url: 'recup_produit.php',
                method: 'GET',
                dataType: 'json',
                data: { search: valeur_saisie },
                success: function(data) {

                    $('#datalist').empty(); // On vide la liste avant d’ajouter de nouvelles options
                    
                    let titresAjoutes = new Set();
                    $.each(data, function(index, produit) {
                        if (!titresAjoutes.has(produit.titre)) {
                            $('#datalist').append(
                                $('<option>', { value: produit.titre })
                            );
                            titresAjoutes.add(produit.titre);
                        }
                    });
                }
            });
        }
    });

    // Vider la datalist une fois qu'un jeu est sélectionné
    inputRecherche.on('change', function() {
        setTimeout(() => {
            $('#datalist').empty(); // Efface la liste après la sélection
        }, 50); // Petit délai pour éviter la suppression immédiate avant la sélection
    });
});


document.addEventListener("DOMContentLoaded", function() {
    let messages = [
        "Bienvenue à l'univers des jeux !",
        "Prêt pour une aventure épique ?",
        "Quel jeu vas-tu explorer aujourd'hui ?",
        "Découvre les dernières nouveautés gaming !",
        "Rejoins-nous pour une expérience unique !"
    ];
    
    let index = 0;
    let textElement = document.getElementById("dynamic-text");

    setInterval(() => {
        textElement.style.opacity = 0; // Début du fondu
        setTimeout(() => {
            index = (index + 1) % messages.length;
            textElement.textContent = messages[index]; // Change le texte
            textElement.style.opacity = 1; // Fin du fondu
        }, 300); // Attente plus courte avant de changer le texte
    }, 2500); // Changement toutes les 2.5 secondes
});


  </script> 
  
  <script>
let currentIndexCategories = 0;
const categorySections = document.querySelectorAll('.carousel-section');
const totalPagesCategories = categorySections.length;

function moveCarouselCategories(direction) {
    currentIndexCategories += direction;

    // Empêcher de dépasser les limites
    if (currentIndexCategories < 0) {
        currentIndexCategories = totalPagesCategories - 1;
    } else if (currentIndexCategories >= totalPagesCategories) {
        currentIndexCategories = 0;
    }

    document.querySelector('.carousel-wrapperCategories').style.transform =
        `translateX(-${currentIndexCategories * 100}%)`;
}


  </script>

</body>

</html>
<br>
<?php
include 'footer.php';
?>
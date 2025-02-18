<!DOCTYPE html>
<html>

<head>
  <title>Accueil</title>
  <meta charset="utf-8">
  <link href="accueil.css" rel="stylesheet">
  <link rel="stylesheet" href="accueil.css">

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
    ?>
  </div>
  <div class="welcome"><i class='bx bx-game'></i>&nbsp; <span id="dynamic-text">Bienvenue à l'univers des jeux !</span> &nbsp;<i class='bx bx-game'></i></div>

  <form class="navbar-form navbar-left" action="research.php" method="get">
        <div class="form-group">
        <input type="search" name="query" id="products" class="form-control" placeholder="Rechercher..." required list="datalist">
<datalist id="datalist"></datalist>
        </div>
        <div class="buttonResearch">
        <button type="submit" class="btn-default"><i class='bx bx-search-alt-2'></i>
        </button>
        </div>
      </form>
      <div class="carousel">
    <div class="slides">
        <?php
        include 'pdo.php';
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
  

</body>

</html>
<br>
<?php
include 'footer.php';
?>
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
    ?>
  </div>
  <div class="welcome"><i class='bx bx-game'></i>&nbsp; <span id="dynamic-text">Bienvenue à l'univers des jeux !</span> &nbsp;<i class='bx bx-game'></i></div>

  <div class="carousel">
    <div class="slides">
      <a href=""><img class="slide" src="img/shadow of the tomb raider.avif" alt="img1"></a>
      <a href=""><img class="slide" src="img/baldur's gate 3.avif" alt="img2"></a>
      <a href=""><img class="slide" src="img/assassin's creed odyssey.jpg" alt="img3"></a>
    </div>
    <button class="prev" onclick="prevSlide()">&#10096;</button>
    <button class="next" onclick="nextSlide()">&#10097;</i></button>
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
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
  include 'header.php';
  include 'pdo.php';
  ?>
  <div class="header-container">

    <?php
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
  <div class="welcome"> <i class='bx bx-game'></i>&nbsp; BIENVENUE A L'UNIVERS DES JEUX ! &nbsp; <i class='bx bx-game'></i></div>
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

</body>

</html>
<br>
<?php
include 'footer.php';
?>
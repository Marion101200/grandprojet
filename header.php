<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>  
<html>  
<head>  
<title>Accueil</title>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="header.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> 
</head>  
<body> 
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="accueil.php"> <i class='bx bx-game'></i> &nbsp;L'UNIVERS DES JEUX &nbsp; <i class='bx bx-game'></i></a>
    </div>
    <ul class="nav navbar-nav">
      <li><a href="accueil.php">ACCUEIL</a></li>
      <li><a href="jeux.php">JEUX</a></li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">CATEGORIES
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
        <li><a href="aventure.php">Aventure</a></li>
            <li><a href="rpg.php">RPG</a></li>
            <li><a href="historique.php">Historique</a></li>
            <li><a href="soulslike.php">Soulslike</a></li>
            <li><a href="horreur.php">Horreur</a></li>
            <li><a href="survie.php">Survie</a></li>
            <li><a href="action.php">Action</a></li>
        </ul>
      </li>
      <li><a href="contact.php">CONTACT</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <li><a href="panier.php">Panier &nbsp; <span class="glyphicon glyphicon-shopping-cart"></span></a></li>
        <?php
        if(isset($_SESSION['email'])){
          echo '<li><a href="favoris.php"><i class=\'bx bxs-heart\' ></i></a></li>';
          echo '<li><a href="historique_commandes.php">Historique</a></li>';
          echo "<li><a href=\"compte.php\">Compte</li>";
          echo "<li><a href=\"logout.php\">Logout &nbsp; <span class=\"glyphicon glyphicon-log-in\"></span></a></li>";
        } else{
          echo "<li><a href=\"login.php\">Login &nbsp; <span class=\"glyphicon glyphicon-log-in\"></span></a></li>";
        }
        ?>
    </ul>
    <form class="navbar-form navbar-left" action="research.php" method="get">
      <div class="form-group">
        <input type="search" name="query" class="form-control" placeholder="rechercher..." required style="width: 320px">
      </div>
      <button type="submit" class="btn btn-default"><i class='bx bx-search-alt-2' ></i>
      </button>
    </form>
  </div>
  </div>
</nav>
</body>
</html>
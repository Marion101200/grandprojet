<!DOCTYPE html>  
<html>  
<head>  
<title>Ceci est un titre</title>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> 
</head>  
<body> 
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">L'UNIVERS DES JEUX</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="projet.php">ACCUEIL</a></li>
      <li><a href="#">JEUX</a></li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">CATEGORIES
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="#">Aventure</a></li>
          <li><a href="#">Sport</a></li>
          <li><a href="#">Horreur</a></li>
          <li><a href="#">Puzzle</a></li>
        </ul>
      </li>
      <li><a href="#">MEILLEUR VENTE</a></li>
      <li><a href="contact.php">CONTACT</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <li><a href="panier.php"><span class="glyphicon glyphicon-shopping-cart"></span>Panier</a></li>
      <li><a href="signup.php"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
      <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
    </ul>
    <form class="navbar-form navbar-left" action="/action_page.php">
      <div class="form-group">
        <input type="text" class="form-control" placeholder="rechercher">
      </div>
      <button type="submit" class="btn btn-default">🔍</button>
    </form>
  </div>
  </div>
</nav> 
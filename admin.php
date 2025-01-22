<?php
session_start();
?>
<!DOCTYPE html>  
<html>  
<head>  
<title>Connexion admin</title>
<meta charset="utf-8"> 
<link rel="stylesheet" href="admin.css">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>  
<body>
  <?php
  require_once("connexion.php");

  if (isset($_POST['submit'])) {
    $administrateur = $_POST['login'] ?? ''; // Le nom doit correspondre au champ du formulaire
    $mdp = $_POST['password'] ?? '';

    if (!$administrateur || !$mdp) {
      echo "<p style='color: red;'>Tous les champs doivent être remplis.</p>";
    } else {
      // Connexion à la base de données
      $connexion = getConnexion();

      // Requête pour vérifier l'utilisateur
      $query = $connexion->prepare("SELECT * FROM administrateur WHERE email = ?");
      $query->execute([$administrateur]);
      $administrateurData = $query->fetch(PDO::FETCH_ASSOC);

      // Vérification du mot de passe
      if ($administrateurData && password_verify($mdp, $administrateurData['mdp'])) {
        // Créer une session pour l'utilisateur
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $administrateurData['email'];
        $_SESSION['administrateur'] = $administrateurData['email'];

        // Redirection vers le tableau de bord
        header("Location: admin_dashbord.php");
        exit();
      } else {
        echo "<p style='color: red;'>Nom d'utilisateur ou mot de passe invalide.</p>";
      }
    }
  }
  ?>
  <div class="login">
    <div class="fond-img">
      <img src="img/login1.png" alt="login">
    </div>
    <div class="formulaire">
      <div class="titre">
        <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px"> <i class='bx bx-user'></i> &nbsp;Admin &nbsp; <i class='bx bx-user'></i></h2>
      </div>
      <!-- Assurez-vous que le formulaire soumet vers la même page pour le traitement -->
      <form action="" method="post">
        <label for="login">Email: </label>
        <input type="text" id="login" name="login" required>
        <br>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <a href="inscription_admin.php">Inscrivez-vous ici</a>
        <br>
        <input type="submit" name="submit" value="Se connecter">
      </form>
    </div>
  </div>

</body>
</html>

<!DOCTYPE html>
<html>

<head>
  <title>Login In</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="login.css">
</head>

<body>
  <?php
  include 'header.php';
  ?>
  <?php
  require_once("connexion.php");

  if (isset($_POST['submit'])) {
    $login = $_POST['login'] ?? '';
    $mdp = $_POST['password'] ?? '';

    if (!$login || !$mdp) {
      echo "<p style='color: red;'>Tous les champs doivent être remplis.</p>";
      exit();
    }

    $connexion = getConnexion();

    $query = $connexion->prepare("SELECT * FROM clients WHERE nom = ? OR email = ?");
    $query->execute([$login, $login]);
    $clients = $query->fetch(PDO::FETCH_ASSOC);

  
    echo "<p>" . htmlspecialchars($clients['mdp']) . "</p>";
    echo "<p><strong>Mot de passe saisi :</strong> " . htmlspecialchars($mdp) . "</p>";
if (password_verify($mdp, $clients['mdp'])) {
                    // Connexion réussie
                    $_SESSION['loggedin'] = true;
                    $_SESSION['nom'] = $clients['nom'];
                    $_SESSION['email'] = $clients['email'];

                    // header("Location: accueil.php");
                    exit();
                } else {
                    echo "<p style='color: red;'>Le mot de passe est incorrect.</p>";
                }
            } else {
                echo "<p style='color: red;'>Aucun utilisateur trouvé avec ce nom ou email.</p>";
            }
    
  ?>
  <div class="login">
    <div class="fond-img">
      <img src="img/login1.png" alt="login">
    </div>
    <div class="formulaire">
      <div class="titre">
        <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px"> <i class='bx bx-user'></i> &nbsp;Login &nbsp;<i class='bx bx-user'></i></h2>
      </div>
      <form action="login.php" method="post">
        <label for="nom">Nom ou Email: </label>
        <input type="text" id="login" name="login" required>
        <br>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <a href="signup.html"><i class='bx bx-user'></i>&nbsp;Inscrivez-vous ici&nbsp;<i class='bx bx-user'></i></a>
        <br>
        <a href="reset_password_request.php" class="forgot"><i class='bx bx-game'></i>&nbsp;Oublie de mot de passe&nbsp;<i class='bx bx-game'></i></a>
        <input type="submit" name="submit" value="Se connecter">
      </form>
    </div>
  </div>

</body>

</html>
<?php
include 'footer.php';
?>
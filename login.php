<!DOCTYPE html>
<html>

<head>
  <title>Login In</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="login.css">
</head>

<body>
  <?php
   if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

  require_once("connexion.php");

  if (isset($_POST['submit'])) {
    $login = $_POST['login'] ?? '';
    $mdp = $_POST['password'] ?? '';

    if (!$login || !$mdp) {
      echo "<p style='color: red;'>Tous les champs doivent être remplis.</p>";
      exit();
    }

    $connexion = getConnexion();

    $query = $connexion->prepare("SELECT * FROM clients WHERE email = ? ");
    $query->execute([$login]);
    $clients = $query->fetch(PDO::FETCH_ASSOC);
    $_SESSION['nom'] = $clients['nom'];

    if ($clients === false) {
      echo "<p style='color: red;'>Erreur : aucun utilisateur trouvé.</p>";
      exit();
    }

    if ($clients['etat_token'] == 0) {
      echo "<p style='color: red;'>Votre compte n'est pas activé. Veuillez vérifier vos emails pour l'activer.</p>";
      exit();
    }



    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (password_verify($mdp, $clients['mdp'])) {
        // Connexion réussie
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $clients['email'];
        $_SESSION['nom'] = $clients['nom'];
        $_SESSION['id_client'] = $clients['id'];

        header("Location: accueilLogin.php");
        exit();
      } else {
        echo "<p style='color: red; font-size: 25px; display: flex; justify-content: center;'>Le mot de passe est incorrect.</p>";
      }
    } else {
      echo "<p style='color: red;'>Aucun utilisateur trouvé avec cet email.</p>";
    }
  }
  include 'header.php';
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
        <label for="nom">Email: </label>
        <input type="text" id="login" name="login" required>
        <br>
        <label for="password">Mot de passe:</label>
        <div class="toggle">
        <input type="password" id="password" name="password" required>
        <button type="button" id="togglePassword">👁️</button>
        </div>
        <br>
        <a href="conn.php"><i class='bx bx-user'></i>&nbsp;Inscrivez-vous ici&nbsp;<i class='bx bx-user'></i></a>
        <br>
        <a href="resetPassword.html" class="forgot"><i class='bx bx-game'></i>&nbsp;Oublie de mot de passe&nbsp;<i class='bx bx-game'></i></a>
        <input type="submit" name="submit" value="Se connecter">
      </form>
    </div>
  </div>


  <script>
  document.getElementById("togglePassword").addEventListener("click", function() {
    let passwordField = document.getElementById("password");
    if (passwordField.type === "password") {
      passwordField.type = "text";
      this.textContent = "🙈"; // Icône pour cacher le mot de passe
    } else {
      passwordField.type = "password";
      this.textContent = "👁️"; // Icône pour afficher le mot de passe
    }
  });
</script>
</body>

</html>
<?php
include 'footer.php';
?>
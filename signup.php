<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up</title>
  <link rel="stylesheet" href="signup.css">
</head>

<body>
  <?php
  include 'header.php';
  ?>
  <div class="signup-container">
  <div class="fond-img">
      <img src="img/signup.avif" alt="login">
    </div>
    <div class="titre-signup">
        <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px"> <i class='bx bx-user'></i> &nbsp;Inscription <i class='bx bx-user'></i></h2>
    
        <form action="" method="POST" onsubmit="return validateForm()">
            <label for="nom">Nom d'utilisateur :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm-password">Confirmer le mot de passe :</label>
            <input type="password" id="confirm-password" name="confirm-password" required>

            <input type="submit" value="S'inscrire">
        </form>
    </div>
        <p id="error-msg"></p>
    </div>

    <script>  
        function validateForm() {
            var mdp = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;
            var errorMsg = document.getElementById("error-msg");

            if (mdp !== confirmPassword) {
                errorMsg.textContent = "Les mots de passe ne correspondent pas.";
                errorMsg.style.color = "red";
                return false; 
            }
            return true;
        }
    </script>

<?php

try {
    require_once("connexion.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
        $email = htmlspecialchars(trim($_POST['email'] ?? ''));
        $mdp = htmlspecialchars(trim($_POST['password'] ?? ''));
        $confirmPassword = htmlspecialchars(trim($_POST['confirm-password'] ?? ''));

        if (!$nom || !$email || !$mdp || !$confirmPassword) {
            echo "<p style='color: red;'>Tous les champs doivent être remplis.</p>";
            exit();
        }

        if ($mdp !== $confirmPassword) {
            echo "<p style='color: red;'>Les mots de passe ne correspondent pas.</p>";
            exit();
        }

        $connexion = getConnexion();

        $stmt = $connexion->prepare("SELECT * FROM clients WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<p style='color: red;'>Cet email est déjà utilisé.</p>";
        } else {
            $hashedmdp = password_hash($mdp, PASSWORD_DEFAULT);

            $stmt = $connexion->prepare("INSERT INTO clients (nom, email, mdp) VALUES (:nom, :email, :mdp)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mdp', $hashedmdp);

            if ($stmt->execute()) {
                $_SESSION['nom'] = $nom;
                header("Location: accueil.php");
                exit();
            } else {
                echo "<p style='color: red;'>Une erreur est survenue lors de l'inscription.</p>";
            }
        }
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
?>
</html>
</body>

</html>
<?php 
  include 'footer.php';
  ?>
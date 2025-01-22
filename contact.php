<!DOCTYPE html>
<html>
<meta charset="utf-8">

<head>
  <title>Contact</title>
  <link href="contact.css" rel="stylesheet">
</head>

<body>
  <?php include 'header.php'; ?>
  <?php
try {
    require_once("connexion.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $message = htmlspecialchars(trim($_POST['message'] ?? ''));
        $email = htmlspecialchars(trim($_POST['email'] ?? ''));

        // Vérification des champs vides
        if (!$message || !$email) {
            echo "<p style='color: red;'>Tous les champs doivent être remplis.</p>";
            exit();
        }

        // Connexion à la base de données
        $connexion = getConnexion();

        // Vérifier si l'email existe déjà
        $stmt = $connexion->prepare("SELECT * FROM contact WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Insérer le message et l'email dans la base de données
        $stmt = $connexion->prepare("INSERT INTO contact (message, email) VALUES (:message, :email)");
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            echo "<p style='color: green; text-align: center; font-size: 30px;'>Votre message a été envoyé avec succès.</p>";
        } else {
            echo "<p style='color: red;'>Une erreur s'est produite lors de l'envoi de votre message.</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
?>

<div class="contact">
<div class="fond-img">
      <img src="img/DALL·E 2025-01-03 11.44.22 - A visually engaging 'Contact Us' image for a video game website. The design features a futuristic and vibrant theme with neon lighting, blending eleme.webp" alt="login">
    </div>
  <div class="formulaire">
    <div class="titre">
      <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px"> <i class='bx bxs-message-detail'></i> &nbsp;Nous contacter ! &nbsp;<i class='bx bxs-message-detail'></i></h2>
    </div>
    <form action="contact.php" method="post">
      <label for="nom">Email: </label>
      <input type="email" id="email" name="email" required>
      <br>
      <label for="message">Message: </label>
      <textarea id="message" name="message" rows="10" cols="50" placeholder="Écrivez votre message ici..."></textarea><br>
      <br>
      <input type="submit" name="submit" value="Envoyer">
    </form>
  </div>
</div>
</div>
</body>
</html>
<?php
include 'footer.php';
?>
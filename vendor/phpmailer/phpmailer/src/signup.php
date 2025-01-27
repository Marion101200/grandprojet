<?php

try {

    require_once("../../../../connexion.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
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
 catch (PDOException $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$mail = new PHPMailer(true);
$mail->isSMTP();
                    $mail->Host = 'smtp.mail.hotmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'marion-trouve@hotmail.com';
                    $mail->Password = 'Plumeetoscar11.'; // À sécuriser avec des variables d'environnement
                    $mail->setFrom('marion-trouve@hotmail.com', 'ecom INSTA');
                    $mail->addAddress($email, htmlspecialchars("$nom"));
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

?>

<?php
// include 'footer.php';
?>
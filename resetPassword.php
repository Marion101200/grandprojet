<?php

include "connexion.php";



require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = htmlspecialchars($_POST['email']);

    $connexion = getConnexion();
    $stmt = $connexion->prepare("SELECT * FROM clients WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>Cet email n'existe pas dans nos bases.</p>";
        
        exit;
    }
    $token = bin2hex(random_bytes(16));
    $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));
    $etat_du_ticket = 1;

// Insertion du token et de l'heure d'expiration dans la table
$insertStmt = $connexion->prepare("INSERT INTO password_resets (email, token, expires_at, etat_du_ticket) VALUES (:email, :token, :expires_at, :etat_du_ticket)");
$insertStmt->bindParam(':token', $token);
$insertStmt->bindParam(':expires_at', $expires_at);
$insertStmt->bindParam(':email', $email);
$insertStmt->bindParam(':etat_du_ticket', $etat_du_ticket);

// Exécution de la requête
$insertStmt->execute();
}
try {
    // Connexion sécurisée à la base de données
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Configuration et envoi de l'e-mail de confirmation
    $mail = new PHPMailer(true);

    $mail->isSMTP(); // Utilisation du protocole SMTP
    $mail->Host = 'smtp.mail.yahoo.com'; // Serveur SMTP de Yahoo
    $mail->SMTPAuth = true; // Activer l'authentification SMTP
    $mail->Username = 'kouicicontact@yahoo.com'; // Adresse e-mail Yahoo
    $mail->Password = 'ndvmyqlrsnmeecxw'; // Clé de sécurité Yahoo
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Sécurisation avec STARTTLS
    $mail->Port = 587; // Port SMTP pour STARTTLS

    // Configuration de l'expéditeur et du destinataire
    $mail->setFrom('kouicicontact@yahoo.com', 'ecom INSTA');
    $mail->addAddress($email);

    // Configuration du contenu de l'e-mail
    $mail->isHTML(true); // Format HTML
    $mail->CharSet = 'UTF-8'; // Encodage UTF-8
    $mail->Subject = 'Demande de changement de mot de passe';
    $mail->Body = "Bonjour, <br>Vous avez demandé une réinitialisation de votre mot de passe sur ecom INSTA. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe : <br><a href='https://localhost/grandprojet-main/grandprojet/resetPassword.php?token=" . urlencode($token) . "'>Réinitialiser mon mot de passe</a>";

    $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    // Tentative d'envoi de l'e-mail
    if ($mail->send()) {
        error_log("E-mail de vérification envoyé avec succès à $email.");
    } else {
        error_log("Erreur lors de l'envoi de l'e-mail: {$mail->ErrorInfo}");
    }
} catch (Exception $e) {
    // Gestion des erreurs liées à l'e-mail
    error_log("Erreur lors de la configuration ou de l'envoi de l'e-mail : {$e->getMessage()}");
}

header("Location:resetPassword.html");

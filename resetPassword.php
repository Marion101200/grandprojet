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
                    $mail->Body = "Demande de réinitialisation de mot de passe sur ecom INSTA. Voici le lien pour changer le mot de passe.";

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
?>

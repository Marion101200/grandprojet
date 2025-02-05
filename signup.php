<?php
session_start();

include "connexion.php";

require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $mdp = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirm-password']);
    $captchapost = htmlspecialchars($_POST['captcha']);

    // Vérification si les mots de passe correspondent
    if ($mdp != $confirmPassword) {
        $_SESSION['erreurmdp'] = "<p style='color: red;'>Les mots de passe ne correspondent pas</p>";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    if ($captchapost != $_SESSION['captcha']) {
        $_SESSION['erreurcaptcha'] = "<p style='color: red;'>Le captcha est ne corespond pas, veuillez recommencer.</p>";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    try {
        // Connexion sécurisée à la base de données
        $connexion = getConnexion();
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérification si l'e-mail existe déjà
        $stmt = $connexion->prepare("SELECT * FROM clients WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<p style='color: red;'>Cet email est déjà utilisé.</p>";
        } else {
            // Hachage sécurisé du mot de passe
            $hashedPassword = password_hash($mdp, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(16));

            // Insérer les données de l'utilisateur dans la base
            $stmt = $connexion->prepare("INSERT INTO clients (nom, email, mdp, token) VALUES (:nom, :email, :mdp, :token)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mdp', $hashedPassword);


            if ($stmt->execute()) {

                // Configuration et envoi de l'e-mail de confirmation
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP(); // Utilisation du protocole SMTP
                    $mail->Host = 'smtp.mail.yahoo.com'; // Serveur SMTP de Yahoo
                    $mail->SMTPAuth = true; // Activer l'authentification SMTP
                    $mail->Username = 'kouicicontact@yahoo.com'; // Adresse e-mail Yahoo
                    $mail->Password = 'ndvmyqlrsnmeecxw'; // Clé de sécurité Yahoo
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Sécurisation avec STARTTLS
                    $mail->Port = 587; // Port SMTP pour STARTTLS

                    // Configuration de l'expéditeur et du destinataire
                    $mail->setFrom('kouicicontact@yahoo.com', 'ecom INSTA');
                    $mail->addAddress($email, htmlspecialchars("$nom"));

                    // Configuration du contenu de l'e-mail
                    $mail->isHTML(true); // Format HTML
                    $mail->CharSet = 'UTF-8'; // Encodage UTF-8
                    $mail->Subject = 'Inscription réussie';
                    $mail->Body = "Bienvenue, " . htmlspecialchars($nom) . " sur ecom INSTA. Votre compte a été créé avec succès. <br><a href='http://localhost/grandprojet-main/grandprojet/confirm_email.php?token=" . urlencode($token) . "'>Confirmer votre adresse mail";

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

                // Redirection vers la page de connexion après inscription réussie
                header("Location: login.php");
                exit();
            } else {
                echo "<p style='color: red;'>Erreur lors de l'inscription</p>";
            }
        }
    } catch (PDOException $e) {
        // Gestion des erreurs liées à la base de données
        echo "Erreur : " . $e->getMessage();
    }

    // Fermeture de la connexion à la base
    $conn = null;
    header("Location: " . $_SERVER['HTTP_REFERER']);
}

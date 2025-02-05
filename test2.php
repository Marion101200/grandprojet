<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nom= htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $mdp = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirm-password']);
    $captchapost = htmlspecialchars($_POST['captcha']);

    if($captchapost != $_SESSION['captcha']) {
        echo 'Le captcha ne correspond pas, veuillez recommencer.';
    } exit;
}
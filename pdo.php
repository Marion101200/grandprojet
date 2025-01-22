<?php
try {
    // on se connecte
    $connexion = new PDO("mysql:host=localhost;dbname=jeuxvideos;charset=utf8", "root", "");
    

} catch (PDOException $e) {
    // gestion des erreurs de connexion
    printf("Connexion impossible : %s\n", $e->getMessage());
    exit(); // on sort
}

<?php
function getConnexion(){
    $db = new PDO("mysql:host=localhost;dbname=jeux vidéos;charset=utf8", "root", "");
    return $db;
}
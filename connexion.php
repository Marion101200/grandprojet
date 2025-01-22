<?php
function getConnexion(){
    $db = new PDO("mysql:host=localhost;dbname=jeuxvideos;charset=utf8", "root", "");
    return $db;
}

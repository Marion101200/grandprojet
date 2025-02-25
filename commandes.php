<!DOCTYPE html>
<html>

<head>
    <title>Paiement</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="paiement.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>


<body>

<?php

include 'header.php';
include 'pdo.php';
require_once("connexion.php");

if (!isset($_SESSION['id_client'])) {
    echo "Erreur : ID client non défini.";
    exit;
}
$adresse['id_adresse'];
var_dump($adresse['id_adresse']);


$connexion = getConnexion();
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_SESSION['id_client']; 
$cart_items = $_SESSION['cart'];
$total_prix = $_SESSION['total_prix'];


try {
    $sql = $connexion->prepare("INSERT INTO commande (id_clients, montant) VALUES (:id_clients, :montant)");
    $sql->execute([
        'id_clients' => $id,
        'montant' => $total_prix
        
    ]);
    $id_commande = $connexion->lastInsertId();



    $sqlDetails = $connexion->prepare("INSERT INTO details_commande (id_commande, id_jeu, id_adresse) VALUES (:id_commande, :id_jeu, :id_adresse)");

    foreach ($_SESSION['cart'] as $id_jeu) {
        $sqlDetails->execute([
            'id_commande' => $id_commande,
            'id_jeu' => $id_jeu,
            'id_adresse' => $id_adresse
        ]);
    }
} catch (Exception $e) {
    echo (['error' => $e->getMessage()]);
}


//   $sqlDetails->execute([
//     'id_jeu' => $id_jeu,
//     'id_commande' => $id_commande,
//   ]);

// } catch (Exception $e) {
//     echo json_encode(['error' => $e->getMessage()]);
// }
?>
</body>
</html>
<?php include 'footer.php'; ?>
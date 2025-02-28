<!DOCTYPE html>
<html>

<head>
    <title>Paiement</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="commandes.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>


<body>

    <?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected-adresse'])) {
        $_SESSION['idAdresse'] = $_POST['selected-adresse'];
    }

    $idadresse = $_SESSION['idAdresse'];

    include 'header.php';
    include 'pdo.php';
    require_once("connexion.php");

    if (!isset($_SESSION['id_client'])) {
        echo "Erreur : ID client non défini.";
        exit;
    }



    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_SESSION['id_client'];
    $cart = $_SESSION['cart'];
    $total_prix = $_SESSION['total_prix'];

    try {
        $sql = $connexion->prepare("INSERT INTO commande (id_clients, montant) VALUES (:id_clients, :montant)");
        $sql->execute([
            'id_clients' => $id,
            'montant' => $total_prix

        ]);
        $id_commande = $connexion->lastInsertId();



        $sqlDetails = $connexion->prepare("INSERT INTO details_commande (id_commande, id_jeu, id_adresse) VALUES (:id_commande, :id_jeu, :idadresse)");

        foreach ($cart as $id_jeu => $quantite) {
            // $id_jeu = is_array($id_jeu) ? $id_jeu[0] : $id_jeu; 

            $sqlDetails->execute([
                'id_commande' => $id_commande,
                'id_jeu' => $id_jeu,
                'idadresse' => $idadresse,
            ]);
        }
    } catch (Exception $e) {
        echo (['error' => $e->getMessage()]);
    }
    ?>

    <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px;text-align:center;position: absolute;
    top: 30%;
    left: 50%;
    translate: -50% -50%;">
        <i class='bx bxs-credit-card-alt'></i> &nbsp;Paiement et commande réussi &nbsp; <i class='bx bxs-credit-card-alt'></i>
        <a href="historique_commandes.php" class="seehistorique"><i class='bx bx-right-arrow-circle'></i>&nbsp;Voir mes historiques de commandes</a>
    </h2>
</body>

</html>
<?php include 'footer.php'; ?>
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

    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['adresse'])) {
        $adresse = trim($_POST['adresse']);
        if ($count == 0) { // Si l'adresse n'existe pas encore, on l'ajoute
            $sql = $connexion->prepare("INSERT INTO adresse (id_clients, adresse) VALUES (:id_client, :adresse)");
            $sql->execute([
                'id_client' => $id,
                'adresse' => $adresse
            ]);
        }
    }
?>
    <form method="POST">
    <div class="adresse-container">
        <label for="adresse">Ajouter une nouvelle adresse :</label>
        <div class="adresse-input-group">
            <input type="text" id="adresse" name="adresse" required>
            <button type="submit" id="button-ajouter">Ajouter</button>
        </div>
    </div>
    </form>
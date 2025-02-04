<?php
include 'header.php';
include 'pdo.php';
?>



<!DOCTYPE html>
<html>

<head>
    <title>Paiement</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="paiement.css" rel="stylesheet">
</head>

<body>
    <div class="titre_paiement">
        <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px;"> <i class='bx bxs-credit-card-alt'></i> &nbsp;Paiement &nbsp; <i class='bx bxs-credit-card-alt'></i></h2>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>Prix Unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            </tbody>



                <form action="confirmation_paiement.php" method="POST">
                    <label for="nom">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="adresse">Adresse de livraison:</label>
                    <input type="text" id="adresse" name="adresse" required>

                    <label for="numéro_de_carte bancaire">Numéro de carte bancaire:</label>
                    <input type="number" id="bancaire" name="bancaire" required>

                    <label for="date_expiration_carte_bancaire">Dâte expiration de la carte bancaire:</label>
                    <input type="month" id="date_bancaire" name="date_bancaire" required>

                    <label for="cryptogramme">Cryptogramme:</label>
                    <input type="number" id="cryptograme" name="cryptogramme" required>

                    <label for="nom_carte_bancaire">Titulaire de la carte:</label>
                    <input type="text" id="nom_carte_bancaire" name="nom_carte_bancaire" required>

                    <input type="submit" value="PAYER">
                </form>
    </div>

</body>

</html>
<?php include 'footer.php'; ?>
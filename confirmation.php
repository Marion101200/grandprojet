<?php

$host = 'localhost';
$dbname = 'jeuxvideos';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}


$sql = "SELECT c.id_commande, cl.nom, cl.email, c.montant 
        FROM commande c
        JOIN clients cl ON c.id_clients = cl.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Commandes</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>Historique des Commandes</h2>
    <table>
        <tr>
            <th>ID Commande</th>
            <th>Nom du Client</th>
            <th>Email</th>
            <th>Montant</th>
        </tr>
        <?php foreach ($commandes as $commande) : ?>
            <tr>
                <td><?= htmlspecialchars($commande['id_commande']) ?></td>
                <td><?= htmlspecialchars($commande['nom']) ?></td>
                <td><?= htmlspecialchars($commande['email']) ?></td>
                <td><?= htmlspecialchars($commande['montant']) ?> €</td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
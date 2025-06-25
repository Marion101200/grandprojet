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
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



if (!isset($_SESSION['id_client'])) {
    echo "Erreur : ID client non défini.";
    exit;
}

require_once("connexion.php");
$connexion = getConnexion();
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    

$id = $_SESSION['id_client'];

// Gérer la soumission du formulaire d'adresse et choix livraison/transporteur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Enregistrer adresse choisie
    if (isset($_POST['selected-adresse'])) {
        $_SESSION['idAdresse'] = $_POST['selected-adresse'];
    }

    if (!empty($_POST['mode_livraison'])) {
        $_SESSION['mode_livraison'] = $_POST['mode_livraison'];
    
        $stmt = $connexion->prepare("INSERT INTO mode_livraison (id_clients, livraison) VALUES (:id_clients, :livraison)");
        $stmt->execute([
            'id_clients' => $id,
            'livraison' => $_SESSION['mode_livraison'],
        ]);
    }

    // Enregistrer adresse si nouvelle adresse remplie
    if (!empty($_POST['adresse'])) {
        $adresse = trim($_POST['adresse']);

        $sql = $connexion->prepare("SELECT COUNT(*) FROM adresse WHERE id_clients = :id_client AND adresse = :adresse");
        $sql->execute(['id_client' => $id, 'adresse' => $adresse]);
        $count = $sql->fetchColumn();

        if ($count == 0) {
            $sql = $connexion->prepare("INSERT INTO adresse (id_clients, adresse) VALUES (:id_client, :adresse)");
            $sql->execute([
                'id_client' => $id,
                'adresse' => $adresse
            ]);
        }
    }

    if (!empty($_POST['transporteur'])) {
        $_SESSION['transporteur'] = $_POST['transporteur'];
    
        $stmt = $connexion->prepare("INSERT INTO transporteur (id_clients, transporteur) VALUES (:id_client, :transporteur)");
        $stmt->execute([
            'id_client' => $id,
            'transporteur' => $_SESSION['transporteur'],
        ]);
    }


// Récupérer les adresses du client pour le formulaire
$sql = $connexion->prepare("SELECT DISTINCT id, adresse FROM adresse WHERE id_clients = :id_client");
$sql->execute(['id_client' => $id]);
$adresses = $sql->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px;text-align:center;"> 
    <i class='bx bxs-credit-card-alt'></i> &nbsp;Paiement &nbsp; <i class='bx bxs-credit-card-alt'></i>
</h2>

<div class="payment-container">
    <form method="POST" action="">
        <div class="select-container">
            <label for="selected-adresse">Sélectionner une adresse :</label>
            <select id="selected-adresse" name="selected-adresse" required>
                <?php foreach ($adresses as $adresse) : ?>
                    <option value="<?= htmlspecialchars($adresse['id']) ?>"
                        <?php if (isset($_SESSION['idAdresse']) && $_SESSION['idAdresse'] == $adresse['id']) : ?>
                            selected
                        <?php endif; ?>
                    >
                        <?= htmlspecialchars($adresse['adresse']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a href="ajouterAdresse.php" id="newadresse">Ajouter une nouvelle adresse</a>
            <button type="submit" id="button-valider-adresse">Valider mon adresse</button>
        </div>

        <div class="select-container">
            <label for="mode-livraison">Mode de livraison :</label>
            <select id="mode-livraison" name="mode_livraison" required>
                <option value="domicile" <?= (isset($_SESSION['mode_livraison']) && $_SESSION['mode_livraison'] === 'domicile') ? 'selected' : '' ?>>À domicile</option>
                <option value="relais" <?= (isset($_SESSION['mode_livraison']) && $_SESSION['mode_livraison'] === 'relais') ? 'selected' : '' ?>>Point relais</option>
            </select>
        </div>

        <div class="select-container">
            <label for="transporteur">Choix du transporteur :</label>
            <select id="transporteur" name="transporteur" required>
                <option value="laposte" <?= (isset($_SESSION['transporteur']) && $_SESSION['transporteur'] === 'laposte') ? 'selected' : '' ?>>La Poste</option>
                <option value="chronopost" <?= (isset($_SESSION['transporteur']) && $_SESSION['transporteur'] === 'chronopost') ? 'selected' : '' ?>>Chronopost</option>
                <option value="mondialrelay" <?= (isset($_SESSION['transporteur']) && $_SESSION['transporteur'] === 'mondialrelay') ? 'selected' : '' ?>>Mondial Relay</option>
                <option value="colissimo" <?= (isset($_SESSION['transporteur']) && $_SESSION['transporteur'] === 'colissimo') ? 'selected' : '' ?>>Colissimo</option>
            </select>
        </div>
    </form>

    <form id="payment-form" method="POST">
        <div id="card-element"></div>
        <div id="payment-result"></div>
        <button type="submit" id="button-payer">Payer</button>
    </form>
</div>

<script>
    const stripe = Stripe('pk_test_51QDpTaFohOKPT3SHLePEYKmV0KmSSEwZCJUhHg52iHHXaD2Wtd1m7lGVdNpOKaMSJa15MPw8lUXz1Q8SaekWgcHM00HDPO8Fic');
    const elements = stripe.elements();
    const card = elements.create('card', {
        style: {
            base: {
                fontSize: '18px',
                padding: '10px',
                color: '#333',
                '::placeholder': {
                    color: '#888'
                }
            }
        }
    });
    card.mount('#card-element');

    document.getElementById('payment-form').addEventListener('submit', async (event) => {
        event.preventDefault();

        const adresse = document.getElementById('selected-adresse').value;
        const modeLivraison = document.getElementById('mode-livraison').value;
        const transporteur = document.getElementById('transporteur').value;

        const response = await fetch('payement.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                adresse: adresse,
                mode_livraison: modeLivraison,
                transporteur: transporteur
            })
        });

        const result = await response.json();

        if (result.error) {
            document.getElementById('payment-result').innerText = 'Erreur : ' + result.error;
            return;
        }

        document.getElementById('payment-result').innerText = 'Paiement réussi!';
        window.location.href = "commandes.php";
    });
</script>

<?php
// Envoi email confirmation après POST (paiement)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = $connexion->prepare("SELECT email FROM clients WHERE id = :id_client");
    $sql->execute(['id_client' => $id]);
    $client = $sql->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        $email = $client['email'];
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.mail.yahoo.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kouicicontact@yahoo.com';
            $mail->Password = 'ndvmyqlrsnmeecxw';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('kouicicontact@yahoo.com', 'Commandes');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de votre commande';
            $mail->Body = '<h2>Votre commande a été confirmée !</h2>
            <p>Mode de livraison : ' . htmlspecialchars($_SESSION['mode_livraison']) . '</p>
            <p>Transporteur : ' . htmlspecialchars($_SESSION['transporteur']) . '</p>';

            $mail->send();
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
        }
    }
}
?>

<?php include 'footer.php'; ?>
</body>
</html>

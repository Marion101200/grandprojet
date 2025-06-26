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

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['id_client'])) {
        echo "Erreur : ID client non défini.";
        exit;
    }

    require_once("connexion.php");
    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $id = $_SESSION['id_client'];

    // Gestion des infos adresse et livraison
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['payer'])) {
        if (isset($_POST['selected-adresse'])) {
            $_SESSION['idAdresse'] = $_POST['selected-adresse'];
        }

        if (!empty($_POST['mode_livraison'])) {
            $_SESSION['mode_livraison'] = $_POST['mode_livraison'];
            $stmt = $connexion->prepare("INSERT INTO mode_livraison (id_clients, livraison) VALUES (:id_clients, :livraison)");
            $stmt->execute(['id_clients' => $id, 'livraison' => $_SESSION['mode_livraison']]);
        }

        if (!empty($_POST['adresse'])) {
            $adresse = trim($_POST['adresse']);
            $sql = $connexion->prepare("SELECT COUNT(*) FROM adresse WHERE id_clients = :id_client AND adresse = :adresse");
            $sql->execute(['id_client' => $id, 'adresse' => $adresse]);
            if ($sql->fetchColumn() == 0) {
                $sql = $connexion->prepare("INSERT INTO adresse (id_clients, adresse) VALUES (:id_client, :adresse)");
                $sql->execute(['id_client' => $id, 'adresse' => $adresse]);
            }
        }

        if (!empty($_POST['transporteur'])) {
            $_SESSION['transporteur'] = $_POST['transporteur'];
            $stmt = $connexion->prepare("INSERT INTO transporteur (id_clients, transporteur) VALUES (:id_client, :transporteur)");
            $stmt->execute(['id_client' => $id, 'transporteur' => $_SESSION['transporteur']]);
        }
    }

    // Liste des adresses
    $sql = $connexion->prepare("SELECT DISTINCT id, adresse FROM adresse WHERE id_clients = :id_client");
    $sql->execute(['id_client' => $id]);
    $adresses = $sql->fetchAll(PDO::FETCH_ASSOC);

    // Paiement confirmé
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payer'])) {
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            // Vérification du stock
            foreach ($_SESSION['cart'] as $id_jeu => $quantite) {
                $stmt = $connexion->prepare("SELECT stock FROM jeux WHERE id = ?");
                $stmt->execute([$id_jeu]);
                $stock = $stmt->fetchColumn();
                if ($quantite > $stock) {
                    die("Stock insuffisant pour le produit ID $id_jeu.");
                }
            }

            // Décrémenter stock
            foreach ($_SESSION['cart'] as $id_jeu => $quantite) {
                $stmt = $connexion->prepare("UPDATE jeux SET stock = stock - :quantite WHERE id = :id");
                $stmt->execute(['quantite' => $quantite, 'id' => $id_jeu]);
            }
            // Créer la commande
            $date_commande = date('Y-m-d H:i:s');
            $total = 0;
            foreach ($_SESSION['cart'] as $id_jeu => $quantite) {
                $stmt = $connexion->prepare("SELECT prix FROM jeux WHERE id = ?");
                $stmt->execute([$id_jeu]);
                $prix = $stmt->fetchColumn();
                $total += $prix * $quantite;
            }

            $stmt = $connexion->prepare("INSERT INTO commande (id_clients, montant) 
                             VALUES (:id_clients, :montant)");
            $stmt->execute([
                'id_clients' => $id,
                'montant' => $total
            ]);

            $id_commande = $connexion->lastInsertId(); // Récupère l'ID de la commande créée

            // Ajouter les détails de commande
            foreach ($_SESSION['cart'] as $id_jeu => $quantite) {
                $stmt = $connexion->prepare("SELECT prix FROM jeux WHERE id = ?");
                $stmt->execute([$id_jeu]);
                $prix = $stmt->fetchColumn();

                $stmt = $connexion->prepare("INSERT INTO details_commande (id_commande, id_jeu, quantite, id_adresse) 
                                 VALUES (:id_commande, :id_jeu, :quantite, :id_adresse)");
                $stmt->execute([
                    'id_commande' => $id_commande,
                    'id_jeu' => $id_jeu,
                    'quantite' => $quantite,
                    'id_adresse' => $_SESSION['idAdresse']
                ]);
            }


            // Vider panier
            unset($_SESSION['cart']);

            // Email de confirmation
            $sql = $connexion->prepare("SELECT email FROM clients WHERE id = :id_client");
            $sql->execute(['id_client' => $id]);
            $client = $sql->fetch(PDO::FETCH_ASSOC);

            if ($client) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.mail.yahoo.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'kouicicontact@yahoo.com';
                    $mail->Password = 'ndvmyqlrsnmeecxw'; // à remplacer par une vraie app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('kouicicontact@yahoo.com', 'Commandes');
                    $mail->addAddress($client['email']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Confirmation de votre commande';
                    $mail->Body = '<h2>Votre commande a bien été confirmée !</h2>
                <p>Mode de livraison : ' . htmlspecialchars($_SESSION['mode_livraison']) . '</p>
                <p>Transporteur : ' . htmlspecialchars($_SESSION['transporteur']) . '</p>';
                    $mail->send();
                } catch (Exception $e) {
                    echo "Erreur d'envoi mail : " . $mail->ErrorInfo;
                }
            }


            exit;
        } else {
            echo "<p style='color:red;'>Erreur : panier vide.</p>";
        }
    }
    ?>

    <h2 style="text-align:center; font-size: 50px; color: rgb(181, 3, 3); margin-bottom: 60px;">
        <i class='bx bxs-credit-card-alt'></i> Paiement <i class='bx bxs-credit-card-alt'></i>
    </h2>

    <div class="payment-container">
        <!-- Formulaire Adresse / Livraison -->
        <form method="POST" action="">
            <div class="select-container">
                <label for="selected-adresse">Adresse :</label>
                <select id="selected-adresse" name="selected-adresse" required>
                    <?php foreach ($adresses as $adresse): ?>
                        <option value="<?= htmlspecialchars($adresse['id']) ?>" <?= (isset($_SESSION['idAdresse']) && $_SESSION['idAdresse'] == $adresse['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($adresse['adresse']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <a href="ajouterAdresse.php">Ajouter une nouvelle adresse</a>
                <button type="submit">Valider</button>
            </div>

            <div class="select-container">
                <label for="mode-livraison">Mode de livraison :</label>
                <select name="mode_livraison" required>
                    <option value="domicile" <?= ($_SESSION['mode_livraison'] ?? '') === 'domicile' ? 'selected' : '' ?>>À domicile</option>
                    <option value="relais" <?= ($_SESSION['mode_livraison'] ?? '') === 'relais' ? 'selected' : '' ?>>Point relais</option>
                </select>
            </div>

            <div class="select-container">
                <label for="transporteur">Transporteur :</label>
                <select name="transporteur" required>
                    <option value="laposte" <?= ($_SESSION['transporteur'] ?? '') === 'laposte' ? 'selected' : '' ?>>La Poste</option>
                    <option value="chronopost" <?= ($_SESSION['transporteur'] ?? '') === 'chronopost' ? 'selected' : '' ?>>Chronopost</option>
                    <option value="mondialrelay" <?= ($_SESSION['transporteur'] ?? '') === 'mondialrelay' ? 'selected' : '' ?>>Mondial Relay</option>
                    <option value="colissimo" <?= ($_SESSION['transporteur'] ?? '') === 'colissimo' ? 'selected' : '' ?>>Colissimo</option>
                </select>
            </div>
        </form>

        <!-- Formulaire Stripe -->
        <form id="payment-form" method="POST">
            <div id="card-element" style="margin-bottom: 20px;"></div>
            <div id="payment-result" style="color: red; margin-bottom: 20px;"></div>
            <input type="hidden" name="payer" value="1">
            <button type="submit" id="button-payer">Payer</button>
        </form>
    </div>

    <script>
        const stripe = Stripe('pk_test_51QDpTaFohOKPT3SHLePEYKmV0KmSSEwZCJUhHg52iHHXaD2Wtd1m7lGVdNpOKaMSJa15MPw8lUXz1Q8SaekWgcHM00HDPO8Fic');
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');

        document.getElementById('payment-form').addEventListener('submit', async function(event) {
            event.preventDefault();

            const {
                paymentMethod,
                error
            } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
            });

            if (error) {
                document.getElementById('payment-result').textContent = 'Erreur : ' + error.message;
            } else {
                document.getElementById('payment-result').textContent = 'Paiement réussi (test)';
                // Simule envoi vers le backend
                event.target.submit(); // soumet le formulaire PHP
            }
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>
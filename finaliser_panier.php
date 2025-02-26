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
    session_start();
    include 'header.php';
    include 'pdo.php';
    require_once("connexion.php");

    if (!isset($_SESSION['id_client'])) {
        echo "Erreur : ID client non défini.";
        exit;
    }

    $connexion = getConnexion();
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_SESSION['id_client']; // Récupération de l'ID client

    // Récupérer les adresses uniques pour ce client
    $sql = $connexion->prepare("SELECT DISTINCT adresse, id FROM adresse WHERE id_clients = :id_client");
    $sql->execute(['id_client' => $id]);
    $adresses = $sql->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px;text-align:center;">
        <i class='bx bxs-credit-card-alt'></i> &nbsp;Paiement &nbsp; <i class='bx bxs-credit-card-alt'></i>
    </h2>

    <!-- Formulaire pour ajouter une nouvelle adresse -->
    <form method="POST">
        <div class="adresse-container">
            <label for="adresse">Ajouter une nouvelle adresse :</label>
            <div class="adresse-input-group">
                <input type="text" id="adresse" name="adresse" required>
                <button type="submit" id="button-ajouter">Ajouter</button>
            </div>
        </div>
    </form>
    <!-- Liste déroulante pour sélectionner une adresse existante -->
    <form id="payment-form" method="POST">
        <div>
            <div>

            </div>
        </div>
        <div class="select-container">
            <label for="selected-adresse">Sélectionner une adresse :</label>
            <select id="selected-adresse" name="selected-adresse" required>
                <?php foreach ($adresses as $adresse) : ?>
                    <option value="<?= htmlspecialchars($adresse['id']) ?>">
                    <?php $_SESSION['id_adresse'] = ($adresse['id']);?>
                        <?= htmlspecialchars($adresse['adresse']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
var_dump($_SESSION['id_adresse']);
?>
        </div>

        <div>
            <div>

            </div>
        </div>
        <div>
            <div>

            </div>
        </div>
        <div id="card-element"></div>
        <div id="payment-result"></div>
        <input type="hidden">
        <button type="submit" id="button-payer">Payer</button>
        </input>
    </form>

    <script>
        const stripe = Stripe('pk_test_51QDpTaFohOKPT3SHLePEYKmV0KmSSEwZCJUhHg52iHHXaD2Wtd1m7lGVdNpOKaMSJa15MPw8lUXz1Q8SaekWgcHM00HDPO8Fic');
        const elements = stripe.elements();
        const card = elements.create('card', {
            style: {
                base: {
                    fontSize: '18px', // Augmenter la taille du texte
                    padding: '10px', // Ajouter du padding
                    color: '#333', // Changer la couleur du texte
                    '::placeholder': {
                        color: '#888' // Couleur du placeholder
                    }
                }
            }
        });
        card.mount('#card-element');


        document.getElementById('payment-form').addEventListener('submit', async (event) => {
            event.preventDefault();

            const adresse = document.getElementById('selected-adresse').value;

            const response = await fetch('payement.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    adresse: adresse
                }) // Envoi de l'adresse sélectionnée
            });

            const {
                clientSecret,
                error
            } = await response.json();

            if (error) {
                document.getElementById('payment-result').innerText = 'Erreur : ' + error;
                return;
            }

            const result = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card
                },
                shipping: {
                    name: "Client",
                    address: {
                        line1: adresse
                    }
                }
            });

            document.getElementById('payment-result').innerText = result.error ?
                'Erreur : ' + result.error.message :
                'Paiement réussi!';
            window.location.href = "commandes.php";
        });
    </script>
    <?php include 'footer.php'; ?>


</body>

</html>
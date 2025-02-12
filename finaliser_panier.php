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
    ?>
    <h2 style="color: rgb(181, 3, 3); margin-bottom: 60px; font-size: 50px;text-align:center;"> <i class='bx bxs-credit-card-alt'></i> &nbsp;Paiement &nbsp; <i class='bx bxs-credit-card-alt'></i></h2>

    <form id="payment-form">
        <div id="card-element"></div>
        <div id="payment-result"></div>
        <button type="submit">Payer</button>
    </form>

    <script>
        const stripe = Stripe('pk_test_51QDpTaFohOKPT3SHLePEYKmV0KmSSEwZCJUhHg52iHHXaD2Wtd1m7lGVdNpOKaMSJa15MPw8lUXz1Q8SaekWgcHM00HDPO8Fic');
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');

        document.getElementById('payment-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const response = await fetch('payement.php', {
                method: 'POST'
            });
            const {
                clientSecret
            } = await response.json();
            const result = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card
                },
            });
            document.getElementById('payment-result').innerText = result.error ? 'Erreur : ' + result.error.message : 'Paiement réussi!';
        });
    </script>

</body>

</html>
<?php include 'footer.php'; ?>
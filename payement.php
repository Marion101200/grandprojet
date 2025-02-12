<?php
require 'vendor/autoload.php';
$total_price = $_SESSION['total_price'];


\Stripe\Stripe::setApiKey('sk_test_51QDpTaFohOKPT3SHP25LuNE9IlSaLTUWYlNEYppRQVWdaDZyQ3QTmOvAqSYjgnrkE9izsHLUhw9dpnQH0FWSbACw00lzBF8qwn');

$paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => $total_price * 100,
    'currency' => 'usd',
]);

try {

    echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
} catch (\Stripe\Exception\ApiErrorException $e) {

    echo json_encode(['error' => $e->getMessage()]);
}

?>
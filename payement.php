<?php
require 'vendor/autoload.php';


\Stripe\Stripe::setApiKey('sk_test_51QDpTaFohOKPT3SHP25LuNE9IlSaLTUWYlNEYppRQVWdaDZyQ3QTmOvAqSYjgnrkE9izsHLUhw9dpnQH0FWSbACw00lzBF8qwn');

$paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => 2000,
    'currency' => 'usd',
]);

try {

    echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
} catch (\Stripe\Exception\ApiErrorException $e) {

    echo json_encode(['error' => $e->getMessage()]);
}

?>
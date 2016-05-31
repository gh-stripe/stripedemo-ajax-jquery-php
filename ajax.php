<?php
// This is the line to load Stripe PHP bindings with composer.
// If you want more details or use the manual installation
// refer to this page https://github.com/stripe/stripe-php#composer
require_once('../vendor/autoload.php');

// Put your secret API key here. It can be found
// at https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey('sk_test_xxxyyyzzz');

$token = $_POST['stripeToken'];
$email = $_POST['stripeEmail'];

try {

  $charge = \Stripe\Charge::create(array(
    "amount" => 2000,
    "currency" => "usd",
    "source" => $token,
    "description" => "Charge for " . $email
	));

  // Display the message you want
  print('Charge successful!<br>');
  print('Charge id: ' . $charge->id . '<br>');
  if($charge->livemode){
    $dashLink = 'https://dashboard.stripe.com/payments/' . $charge->id;
  } else {
    $dashLink = 'https://dashboard.stripe.com/test/payments/' . $charge->id;
  }
  print('You can view it in your dashboard <a href="' . $dashLink . '" target="_blank">here</a>');

} catch(\Stripe\Error\Card $e) {
  // The card can't be charged for some reason
  printError($e);
} catch (\Stripe\Error\RateLimit $e) {
  // Too many requests made to the API too quickly
  printError($e);
} catch (\Stripe\Error\InvalidRequest $e) {
  // Invalid parameters were supplied to Stripe's API
  printError($e);
} catch (\Stripe\Error\Authentication $e) {
  // Authentication with Stripe's API failed
  // (maybe you changed API keys recently)
  printError($e);
} catch (\Stripe\Error\ApiConnection $e) {
  // Network communication with Stripe failed
  printError($e);
} catch (\Stripe\Error\Base $e) {
  // Display a very generic error to the user, and maybe send
  // yourself an email
  printError($e);
} catch (Exception $e) {
  // Something else happened, completely unrelated to Stripe
  printError($e);
}

// Helper function to display errors back in the html page
function printError($error) {
  $body = $error->getJsonBody();
  $err  = $body['error'];

  print('An error happened in the server side script<br>');
  print('Status is: ' . $error->getHttpStatus() . '<br>');
  print('Type is: ' . $err['type'] . '<br>');
  print('Code is: ' . $err['code'] . '<br>');
  print('Param is: ' . $err['param'] . '<br>');
  print('Message is: ' . $err['message'] . '<br>');
}
?>
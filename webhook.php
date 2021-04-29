<?php

include("database/db.php");
require_once "setup/init.php";
// Set  your secret key. Remember to switch to your live secret key in production.
// See your keys here: https://dashboard.stripe.com/apikeys
\Stripe\Stripe::setApiKey('sk_test_51IidfrCuTOTS51rEPNfjvHaLT6dk15sYYrxEtC0FQU2IZOIo0HrLZjUidEZi5vIuSDZLgY1CQHWbIdwPksCyDfX500DyvL02nT');

// If you are testing your webhook locally with the Stripe CLI you
// can find the endpoint's secret by running `stripe listen`
// Otherwise, find your endpoint's secret in your webhook settings in the Developer Dashboard
$endpoint_secret = 'whsec_WSuOXPhiFW6p7cmsqxbzH0QXBNlb1zc7';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit(); 
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

$id = $event->data->object->id;
$amount = $event->data->object->amount_captured;
$currency = $event->data->object->currency;
$c_email = $event->data->object->receipt_email;
$status = $event->data->object->status;


// Handle the event
switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
        handlePaymentIntentSucceeded($paymentIntent);
        break;
    case 'charge.succeeded':
        $stmt = $con->prepare("INSERT INTO online_payment (pay_id, customer_email, amount, currency, status) VALUES (?, ?, ?, ? ,?)");
        $stmt-> bind_param("sdsss", $id, $c_email, $amount, $currency, $status);
        $stmt->execute();
        if(!$stmt){
            echo "
                <script>alert('An error occured')</script>;
            ";
        }
        $stmt->close();
        $con->close();
            break;

    case 'charge.failed':
        $stmt = $con->prepare("INSERT INTO failed_online_payment (pay_id, customer_email, amount, currency, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $id, $c_email, $amount, $currency, $status);
        $stmt->execute();
        if(!$stmt){
            echo "
            <script>alert('An error occured')</script>;
        ";
        }
        $stmt->close();
        $con->close();
            break;
      
    // ... handle other event types
    default:
        echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);
?>
<?php
require_once("../conekta-php/lib/Conekta.php");

Conekta\Conekta::setApiKey("key_qh1pKvcoUL4qs6rrZDRpNg");
Conekta\Conekta::setLocale('es');

$options = [
    'options' => [
        'default' => 0, // value to return if the filter fails
        'min_range' => 10000,
        'max_range' => 100000000
    ]
];

$conektaTokenId = $_POST['conektaTokenId'];
$fullName = $_POST['fullName'];
$phoneNumber = $_POST['phoneNumber'];
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT, $options);
$currency = $_POST['currency'];
$rfc = $_POST['rfc'];

$length = 17;
// The following should retrieve the date down to your desired resolution.
// If you want a daily code, retrieve only the date-specific parts
// For hourly resolution, retrieve the date and hour, but no minute parts
$date = new DateTime();
$today = $date->getTimestamp();
$out = substr(hash('md5', $today.$conektaTokenId), 0, $length); // Hash it
$idOrder = 'ord_'.$out;

$valid_order = [
    "currency" => $currency,
    'description' => 'Donativo: '.$quantity,
    "id" => $idOrder,
    'line_items' => [
        [
            'name'        => 'Donativo '.$quantity,
            'unit_price'  => $quantity,
            'quantity'    => 1
        ]
    ],
    'customer_info' => [
        'name'  => $fullName,
        'phone' => $phoneNumber,
        'email' => $email
    ],
    'charges' => [
        [
            'payment_method' => [
                'token_id' => "tok_test_visa_4242",
                "type" => "card",
                "created_at" => $today
            ]
        ]
            ],
    'metadata' => [
        'rfc' => $rfc
    ]
];
try {
    $order = \Conekta\Order::create($valid_order);
} catch (\Conekta\ProccessingError $error){
    //echo '<span class="card-errors alert-danger">'.$error->getMessage().'</span>';
    $json = json_encode($error, JSON_PRETTY_PRINT);
    printf("%s", $json);
} catch (\Conekta\ParameterValidationError $error){
    //echo '<span class="card-errors alert-danger">'.$error->getMessage().'</span>';
    $json = json_encode($error, JSON_PRETTY_PRINT);
    printf("%s", $json);
} catch (\Conekta\Handler $error){
    //echo '<span class="card-errors alert-danger">'.$error->getMessage().'</span>';
    $json = json_encode($error, JSON_PRETTY_PRINT);
    printf("%s", $json);
}

if($order->payment_status === 'paid'){
    $json = json_encode($order, JSON_PRETTY_PRINT);
    printf("%s", $json);
    /* echo '<span class="card-errors alert-success">';
    echo 'Acaba de donar $'. $order->line_items[0]->unit_price/100 .' '. $order->currency; 
    echo '<br>Muchas gracias</span>'; */
}

//echo "<br>ID: ". $order->id;
//echo "Status: ". $order->payment_status;
//echo "<br>$". $order->amount/100 . $order->currency;
//echo "<br>Order: ";
/* echo $order->line_items[0]->quantity .
      "-". $order->line_items[0]->name .
      "- $". $order->line_items[0]->unit_price/100; */
//echo "<br>Payment info";
/* echo "<br>Card info: " .
      "- ". $order->charges[0]->payment_method->name .
      "- ". $order->charges[0]->payment_method->last4 .
      "- ". $order->charges[0]->payment_method->brand .
      "- ". $order->charges[0]->payment_method->type; */

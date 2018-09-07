<?php
include 'checkout-php-api/autoload.php';

use  com\checkout;
$apiClient = new checkout\ApiClient('sk_XXXXXXXXX');

// Create a visa checkout service instance
$visaCheckoutService = $apiClient->visaCheckoutService();

try {
    /**  @var ResponseModels\VisaCheckoutCardToken  $visaCheckoutResponse **/
    $visaCheckoutRequestModel = new checkout\ApiServices\VisaCheckout\RequestModels\VisaCheckoutCardTokenCreate();
    $publicKey = 'pk_XXXXXXXXX';

    $visaCheckoutRequestModel->setCallId('XXXXXXXXXXXXXXX');
    $visaCheckoutRequestModel->setIncludeBinData(true);

    /** Visa Checkout requires the use of the public key **/
    $visaCheckoutResponse = $visaCheckoutService->createVisaCheckoutCardToken($visaCheckoutRequestModel, $publicKey);

}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getErrorMessage(), "\n";
    echo 'Caught exception Error Code: ',  $e->getErrorCode(), "\n";
    echo 'Caught exception Event id: ',  $e->getEventId(), "\n";
}
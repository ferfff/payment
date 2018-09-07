<?php
namespace phpBasic;

include '../vdp-php/helpers/VisaAPIClient2.php';

use VDP;

//'6127063499378433701';
$callId = filter_var($_POST['callid'], FILTER_SANITIZE_STRING);
if (!$callId) {
$json = json_encode(['status' => 401, 'body' => ''], JSON_PRETTY_PRINT);
	printf("%s", $json);
	die();
}

$conf = parse_ini_file ( "../vdp-php/configuration.ini", true );
$visaAPIClient = new VDP\VisaAPIClient;

$baseUrl = "wallet-services-web/";
$resourcePath = "payment/data/{callId}";
$resourcePath = str_replace("{callId}",$callId,$resourcePath);
$queryString = "apikey=".$conf ['VDP'] ['apiKey'];
$response = $visaAPIClient->doXPayTokenCall ( 'get', $baseUrl, $resourcePath, $queryString, 'Get Payment Information Test', '');

$json = json_encode($response, JSON_PRETTY_PRINT);
printf("%s", $json);
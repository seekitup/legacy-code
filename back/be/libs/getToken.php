<?php
// You can use this code in the examples folder
require_once('vendor/autoload.php');
// Or you can put them in to the main folder
// require_once(__DIR__ . '/vendor/autoload.php');

// Remember to get the authorization code
// Make your call in the front of your application like this
// http://auth.mercadolibre.com.ar/authorization?response_type=code&client_id=$APP_ID&redirect_uri=$YOUR_URL 
// http://auth.mercadolibre.com.ar/authorization?response_type=code&client_id=4920434465783045&redirect_uri=https://enterprise-its.com/tests/ML/redirect.php
// Once the user is redirected to your callback url you'll
// receive in the query string, a parameter named code.
// You'll need this for the next part of the process.

$apiInstance = new Meli\Api\OAuth20Api(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
//$grant_type = 'authorization_code'; // Or 'refresh_token' if you need get one new token
$grant_type = 'refresh_token';
$client_id = '4920434465783045'; // Your client_id
$client_secret = '4knGU4r2lwsPpRCJYkYbYygf0nvfecAQ'; // Your client_secret
$redirect_uri = 'https://enterprise-its.com/tests/ML/redirect.php'; // Your redirect_uri
$code = 'TG-607a0446a3b73600098eef50-186187231'; // The parameter CODE who was received in the query.
$refresh_token = 'TG-607a046e1df4010007a9a6a1-186187231'; // Your refresh_token

try {
    $result = $apiInstance->getToken($grant_type, $client_id, $client_secret, $redirect_uri, $code, $refresh_token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling OAuth20Api->getToken: ', $e->getMessage(), PHP_EOL;
}

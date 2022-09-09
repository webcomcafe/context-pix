<?php

require "../vendor/autoload.php";

date_default_timezone_set('America/Sao_Paulo');

// Credenciais
$psp = (new Webcomcafe\Pix\Psp\Bradesco)
    ->setClientId('54d5sd5sds4d45s')
    ->setClientSecret('6656sdsds6sd6')
    ->setCertificate('/usr/local/apache2/certs/dev/cert.pem', '');

// Homologação
$psp->setAsTest(true);

// recuperando token salvo
$accessTokenFile = __DIR__.'/access_token';
//$psp->setAuthorizationToken(file_get_contents($accessTokenFile));

$sdk = new Webcomcafe\Pix\SDK($psp);

$sdk->on('after.auth', function($token) use ($accessTokenFile) {
    // Salvando token
    file_put_contents($accessTokenFile, $token);
});

$sdk->seAsGlobal();

\Webcomcafe\Pix\Facades\Cob::create([
    ':txid' => '7978c0c97ea847e78e8849634473c1f1',
    'loc' => ['id'=>7768],
]);

//echo '<hr>';
//\Webcomcafe\Pix\Facades\Webhook::remove([
//    ':chave' => '71cdf9ba-c695-4e3c-b010-abb521a3f1be',
//]);
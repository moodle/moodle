<?php

require_once(dirname(dirname(__FILE__)).'/libextinc/OAuth.php');

$oauthconfig = \SimpleSAML\Configuration::getConfig('module_oauth.php');

if (!$oauthconfig->getBoolean('getUserInfo.enable', false)) {
    throw new \Exception(
        'Get user info endpoint is disabled. This endpoint can be enabled in the module_oauth.php configuration file.'
    );
}

$store = new \SimpleSAML\Module\oauth\OAuthStore();
$server = new \SimpleSAML\Module\oauth\OAuthServer($store);

$hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
$plaintext_method = new OAuthSignatureMethod_PLAINTEXT();

$server->add_signature_method($hmac_method);
$server->add_signature_method($plaintext_method);

$req = OAuthRequest::from_request();
list($consumer, $token) = $server->verify_request($req);

$data = $store->getAuthorizedData($token->key);

echo json_encode($data);

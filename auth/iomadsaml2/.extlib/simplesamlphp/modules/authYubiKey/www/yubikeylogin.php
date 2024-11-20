<?php

/**
 * This page shows a username/password login form, and passes information from it
 * to the \SimpleSAML\Module\core\Auth\UserPassBase class, which is a generic class for
 * username/password authentication.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

if (!array_key_exists('AuthState', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing AuthState parameter.');
}
$authStateId = $_REQUEST['AuthState'];

$globalConfig = \SimpleSAML\Configuration::getInstance();
$t = new \SimpleSAML\XHTML\Template($globalConfig, 'authYubiKey:yubikeylogin.php');
$translator = $t->getTranslator();

$errorCode = [];
if (array_key_exists('otp', $_REQUEST)) {
    // attempt to log in
    $errorCode = \SimpleSAML\Module\authYubiKey\Auth\Source\YubiKey::handleLogin($authStateId, $_REQUEST['otp']) ?: $errorCode;
    $errorCodes = \SimpleSAML\Error\ErrorCodes::getAllErrorCodeMessages();
    if (array_key_exists($errorCode, $errorCodes['title'])) {
        $t->data['errorTitle'] = $errorCodes['title'][$errorCode];
    }
    if (array_key_exists($errorCode, $errorCodes['desc'])) {
        $t->data['errorDesc'] = $errorCodes['desc'][$errorCode];
    }
}

$t->data['header'] = $translator->t('{authYubiKey:yubikey:header}');
$t->data['autofocus'] = 'otp';
$t->data['errorCode'] = $errorCode;
$t->data['stateParams'] = ['AuthState' => $authStateId];
$t->data['logoUrl'] = \SimpleSAML\Module::getModuleURL('authYubiKey/resources/logo.jpg');
$t->data['devicepicUrl'] = \SimpleSAML\Module::getModuleURL('authYubiKey/resources/yubikey.jpg');
$t->show();

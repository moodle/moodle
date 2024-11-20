<?php

/**
 * This script warns a user that his/her certificate is about to expire.
 *
 * @package SimpleSAMLphp
 */

\SimpleSAML\Logger::info('AuthX509 - Showing expiry warning to user');

if (!array_key_exists('StateId', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing required StateId query parameter.');
}
$id = $_REQUEST['StateId'];
$state = \SimpleSAML\Auth\State::loadState($id, 'warning:expire');

if (is_null($state)) {
    throw new \SimpleSAML\Error\NoState();
} else if (array_key_exists('proceed', $_REQUEST)) {
    // The user has pressed the proceed-button
    \SimpleSAML\Auth\ProcessingChain::resumeProcessing($state);
}

$globalConfig = \SimpleSAML\Configuration::getInstance();

$t = new \SimpleSAML\XHTML\Template($globalConfig, 'authX509:X509warning.php');
$t->data['target'] = \SimpleSAML\Module::getModuleURL('authX509/expirywarning.php');
$t->data['data'] = ['StateId' => $id];
$t->data['daysleft'] = $state['daysleft'];
$t->data['renewurl'] = $state['renewurl'];
$t->data['errorcodes'] = \SimpleSAML\Error\ErrorCodes::getAllErrorCodeMessages();
$t->show();

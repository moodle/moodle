<?php

/**
 * about2expire.php
 *
 * @package SimpleSAMLphp
 */

\SimpleSAML\Logger::info('expirycheck - User has been warned that NetID is near to expirational date.');

if (!array_key_exists('StateId', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing required StateId query parameter.');
}
$state = \SimpleSAML\Auth\State::loadState($_REQUEST['StateId'], 'expirywarning:expired');

$globalConfig = \SimpleSAML\Configuration::getInstance();

$t = new \SimpleSAML\XHTML\Template($globalConfig, 'expirycheck:expired.php');
$t->data['expireOnDate'] = $state['expireOnDate'];
$t->data['netId'] = $state['netId'];
$t->show();

<?php

/**
 * @author Mathias Meisfjordskar, University of Oslo.
 *         <mathias.meisfjordskar@usit.uio.no>
 * @package SimpleSAMLphp
 */

$params = [
    'secure' => false,
    'httponly' => true,
];
\SimpleSAML\Utils\HTTP::setCookie('NEGOTIATE_AUTOLOGIN_DISABLE_PERMANENT', null, $params, false);

$globalConfig = \SimpleSAML\Configuration::getInstance();
$session = \SimpleSAML\Session::getSessionFromRequest();
$session->setData('negotiate:disable', 'session', false, 86400); // 24*60*60=86400
$t = new \SimpleSAML\XHTML\Template($globalConfig, 'negotiate:enable.php');
$t->data['url'] = \SimpleSAML\Module::getModuleURL('negotiate/disable.php');
$t->show();

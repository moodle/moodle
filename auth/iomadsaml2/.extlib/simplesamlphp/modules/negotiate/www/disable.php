<?php

/**
 * @author Mathias Meisfjordskar, University of Oslo.
 *         <mathias.meisfjordskar@usit.uio.no>
 * @package SimpleSAMLphp
 */

$params = [
    'expire' => (mktime(0, 0, 0, 1, 1, 2038)),
    'secure' => false,
    'httponly' => true,
];
\SimpleSAML\Utils\HTTP::setCookie('NEGOTIATE_AUTOLOGIN_DISABLE_PERMANENT', 'True', $params, false);

$globalConfig = \SimpleSAML\Configuration::getInstance();
$session = \SimpleSAML\Session::getSessionFromRequest();
$session->setData('negotiate:disable', 'session', false, 86400); //24*60*60=86400
$t = new \SimpleSAML\XHTML\Template($globalConfig, 'negotiate:disable.php');
$t->data['url'] = \SimpleSAML\Module::getModuleURL('negotiate/enable.php');
$t->show();

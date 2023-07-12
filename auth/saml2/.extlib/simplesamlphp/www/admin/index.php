<?php

require_once('../_include.php');

\SimpleSAML\Utils\HTTP::redirectTrustedURL(\SimpleSAML\Module::getModuleURL('admin/'));

// Load SimpleSAMLphp configuration
$config = \SimpleSAML\Configuration::getInstance();
$session = \SimpleSAML\Session::getSessionFromRequest();

// Check if valid local session exists..
\SimpleSAML\Utils\Auth::requireAdmin();

$adminpages = [
    'hostnames.php' => 'Diagnostics on hostname, port and protocol',
    'phpinfo.php' => 'PHP info',
    '../module.php/sanitycheck/index.php' => 'Sanity check of your SimpleSAMLphp setup',
    'sandbox.php' => 'Sandbox for testing changes to layout and css',
];

$logouturl = \SimpleSAML\Utils\Auth::getAdminLogoutURL();

$template = new \SimpleSAML\XHTML\Template($config, 'index.php');

$template->data['pagetitle'] = 'Admin';
$template->data['adminpages'] = $adminpages;
$template->data['remaining']  = $session->getAuthData('admin', 'Expire') - time();
$template->data['valid'] = 'na';
$template->data['logouturl'] = $logouturl;

$template->show();

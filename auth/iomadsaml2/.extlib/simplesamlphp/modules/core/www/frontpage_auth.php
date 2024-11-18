<?php

// Load SimpleSAMLphp configuration
$config = \SimpleSAML\Configuration::getInstance();
$session = \SimpleSAML\Session::getSessionFromRequest();

// Check if valid local session exists
if ($config->getBoolean('admin.protectindexpage', false)) {
    \SimpleSAML\Utils\Auth::requireAdmin();
}
$loginurl = \SimpleSAML\Utils\Auth::getAdminLoginURL();
$isadmin = \SimpleSAML\Utils\Auth::isAdmin();
$logouturl = \SimpleSAML\Utils\Auth::getAdminLogoutURL();

$links = [];
$links_welcome = [];
$links_config = [];
$links_auth = [];
$links_federation = [];

$links_auth[] = [
    'href' => 'authenticate.php',
    'text' => '{core:frontpage:authtest}',
];

$allLinks = [
    'links' => &$links,
    'welcome' => &$links_welcome,
    'config' => &$links_config,
    'auth' => &$links_auth,
    'federation' => &$links_federation,
];
\SimpleSAML\Module::callHooks('frontpage', $allLinks);

$t = new \SimpleSAML\XHTML\Template($config, 'core:frontpage_auth.tpl.php');
$t->data['pageid'] = 'frontpage_auth';
$t->data['isadmin'] = $isadmin;
$t->data['loginurl'] = $loginurl;
$t->data['logouturl'] = $logouturl;

$t->data['header'] = $t->getTranslator()->t('{core:frontpage:page_title}');
$t->data['links'] = $links;
$t->data['links_welcome'] = $links_welcome;
$t->data['links_config'] = $links_config;
$t->data['links_auth'] = $links_auth;
$t->data['links_federation'] = $links_federation;

$t->show();

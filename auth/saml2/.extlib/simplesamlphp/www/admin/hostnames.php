<?php

require_once('../_include.php');

// Load SimpleSAMLphp configuration
$config = \SimpleSAML\Configuration::getInstance();
$session = \SimpleSAML\Session::getSessionFromRequest();

// Check if valid local session exists..
\SimpleSAML\Utils\Auth::requireAdmin();

$attributes = [];

$attributes['HTTP_HOST'] = [$_SERVER['HTTP_HOST']];
$attributes['HTTPS'] = isset($_SERVER['HTTPS']) ? [$_SERVER['HTTPS']] : [];
$attributes['SERVER_PROTOCOL'] = [$_SERVER['SERVER_PROTOCOL']];
$attributes['SERVER_PORT'] = [$_SERVER['SERVER_PORT']];

$attributes['getBaseURL()'] = [\SimpleSAML\Utils\HTTP::getBaseURL()];
$attributes['getSelfHost()'] = [\SimpleSAML\Utils\HTTP::getSelfHost()];
$attributes['getSelfHostWithNonStandardPort()'] = [\SimpleSAML\Utils\HTTP::getSelfHostWithNonStandardPort()];
$attributes['selfURLhost()'] = [\SimpleSAML\Utils\HTTP::getSelfURLHost()];
$attributes['selfURLNoQuery()'] = [\SimpleSAML\Utils\HTTP::getSelfURLNoQuery()];
$attributes['getSelfHostWithPath()'] = [\SimpleSAML\Utils\HTTP::getSelfHostWithPath()];
$attributes['getFirstPathElement()'] = [\SimpleSAML\Utils\HTTP::getFirstPathElement()];
$attributes['selfURL()'] = [\SimpleSAML\Utils\HTTP::getSelfURL()];

$template = new \SimpleSAML\XHTML\Template($config, 'hostnames.php');

$template->data['remaining']  = $session->getAuthData('admin', 'Expire') - time();
$template->data['attributes'] = $attributes;
$template->data['valid'] = 'na';
$template->data['logout'] = null;

$template->show();

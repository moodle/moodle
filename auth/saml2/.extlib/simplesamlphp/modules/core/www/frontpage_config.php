<?php

// Load SimpleSAMLphp configuration
$config = \SimpleSAML\Configuration::getInstance();
$session = \SimpleSAML\Session::getSessionFromRequest();

// Check if valid local session exists.
if ($config->getBoolean('admin.protectindexpage', false)) {
    \SimpleSAML\Utils\Auth::requireAdmin();
}
$loginurl = \SimpleSAML\Utils\Auth::getAdminLoginURL();
$isadmin = \SimpleSAML\Utils\Auth::isAdmin();
$logouturl = \SimpleSAML\Utils\Auth::getAdminLogoutURL();

$warnings = [];

if (!\SimpleSAML\Utils\HTTP::isHTTPS()) {
    $warnings[] = '{core:frontpage:warnings_https}';
}

if ($config->getValue('secretsalt') === 'defaultsecretsalt') {
    $warnings[] = '{core:frontpage:warnings_secretsalt}';
}

if (extension_loaded('suhosin')) {
    $suhosinLength = ini_get('suhosin.get.max_value_length');
    if (empty($suhosinLength) || (int) $suhosinLength < 2048) {
        $warnings[] = '{core:frontpage:warnings_suhosin_url_length}';
    }
}

$links = [];
$links_welcome = [];
$links_config = [];
$links_auth = [];
$links_federation = [];

$links_config[] = [
    'href' => \SimpleSAML\Utils\HTTP::getBaseURL() . 'admin/hostnames.php',
    'text' => '{core:frontpage:link_diagnostics}'
];

$links_config[] = [
    'href' => \SimpleSAML\Utils\HTTP::getBaseURL() . 'admin/phpinfo.php',
    'text' => '{core:frontpage:link_phpinfo}'
];

$allLinks = [
    'links'      => &$links,
    'welcome'    => &$links_welcome,
    'config'     => &$links_config,
    'auth'       => &$links_auth,
    'federation' => &$links_federation,
];
\SimpleSAML\Module::callHooks('frontpage', $allLinks);
\SimpleSAML\Logger::debug('The "frontpage" hook has been deprecated for the configuration page. Implement the ' .
    '"configpage" hook instead.');

// Check for updates. Store the remote result in the session so we
// don't need to fetch it on every access to this page.
$current = $config->getVersion();
if ($config->getBoolean('admin.checkforupdates', true) && $current !== 'master') {
    if (!function_exists('curl_init')) {
        $warnings[] = '{core:frontpage:warnings_curlmissing}';
    } else {
        $latest = $session->getData("core:latest_simplesamlphp_version", "version");

        if (!$latest) {
            $api_url = 'https://api.github.com/repos/simplesamlphp/simplesamlphp/releases';
            $ch = curl_init($api_url . '/latest');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'SimpleSAMLphp');
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            curl_setopt($ch, CURLOPT_PROXY, $config->getString('proxy', null));
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $config->getValue('proxy.auth', null));
            $response = curl_exec($ch);

            if (curl_getinfo($ch, CURLINFO_RESPONSE_CODE) === 200) {
                /** @psalm-suppress InvalidScalarArgument */
                $latest = json_decode(strval($response), true);
                $session->setData("core:latest_simplesamlphp_version", "version", $latest);
            }
            curl_close($ch);
        }

        if ($latest && version_compare($current, ltrim($latest['tag_name'], 'v'), 'lt')) {
            $outdated = true;
            $warnings[] = '{core:frontpage:warnings_outdated}';
        }
    }
}

$enablematrix = [
    'saml20idp' => $config->getBoolean('enable.saml20-idp', false),
    'shib13idp' => $config->getBoolean('enable.shib13-idp', false),
];


$functionchecks = [
    'time'             => ['required', 'Date/Time Extension'],
    'hash'             => ['required', 'Hashing function'],
    'gzinflate'        => ['required', 'ZLib'],
    'openssl_sign'     => ['required', 'OpenSSL'],
    'dom_import_simplexml' => ['required', 'XML DOM'],
    'preg_match'       => ['required', 'RegEx support'],
    'json_decode'      => ['required', 'JSON support'],
    'class_implements' => ['required', 'Standard PHP Library (SPL)'],
    'mb_strlen'        => ['required', 'Multibyte String Extension'],
    'curl_init' => ['optional', 'cURL (required if automatic version checks are used, also by some modules.'],
    'session_start'  => ['optional', 'Session Extension (required if PHP sessions are used)'],
    'pdo_drivers'    => ['optional', 'PDO Extension (required if a database backend is used)'],
];
if (\SimpleSAML\Module::isModuleEnabled('ldap')) {
    $functionchecks['ldap_bind'] = ['optional', 'LDAP Extension (required if an LDAP backend is used)'];
}
if (\SimpleSAML\Module::isModuleEnabled('radius')) {
    $functionchecks['radius_auth_open'] = ['optional', 'Radius Extension (required if a Radius backend is used)'];
}

$funcmatrix = [];
$funcmatrix[] = [
    'required' => 'required',
    'descr' => 'PHP Version >= 7.1. You run: ' . phpversion(),
    'enabled' => version_compare(phpversion(), '7.1', '>=')
];
foreach ($functionchecks as $func => $descr) {
    $funcmatrix[] = ['descr' => $descr[1], 'required' => $descr[0], 'enabled' => function_exists($func)];
}

$funcmatrix[] = [
    'required' => 'optional',
    'descr' => 'predis/predis (required if the redis data store is used)',
    'enabled' => class_exists('\Predis\Client'),
];

$funcmatrix[] = [
    'required' => 'optional',
    'descr' => 'Memcache or Memcached Extension (required if a Memcached backend is used)',
    'enabled' => class_exists('Memcache') || class_exists('Memcached'),
];

// Some basic configuration checks

if ($config->getString('technicalcontact_email', 'na@example.org') === 'na@example.org') {
    $mail_ok = false;
} else {
    $mail_ok = true;
}
$funcmatrix[] = [
    'required' => 'recommended',
    'descr' => 'technicalcontact_email option set',
    'enabled' => $mail_ok
];
if ($config->getString('auth.adminpassword', '123') === '123') {
    $password_ok = false;
} else {
    $password_ok = true;
}
$funcmatrix[] = [
    'required' => 'required',
    'descr' => 'auth.adminpassword option set',
    'enabled' => $password_ok
];

$t = new \SimpleSAML\XHTML\Template($config, 'core:frontpage_config.tpl.php');
$translator = $t->getTranslator();
$t->data['pageid'] = 'frontpage_config';
$t->data['header'] = '{core:frontpage:page_title}';
$t->data['isadmin'] = $isadmin;
$t->data['loginurl'] = $loginurl;
$t->data['logouturl'] = $logouturl;

$t->data['warnings'] = $warnings;


$t->data['links'] = $links;
$t->data['links_welcome'] = $links_welcome;
$t->data['links_config'] = $links_config;
$t->data['links_auth'] = $links_auth;
$t->data['links_federation'] = $links_federation;


$t->data['enablematrix'] = $enablematrix;
$t->data['funcmatrix'] = $funcmatrix;
$t->data['requiredmap'] = [
    'recommended' => $translator->noop('{core:frontpage:recommended}'),
    'required' => $translator->noop('{core:frontpage:required}'),
    'optional' => $translator->noop('{core:frontpage:optional}'),
];
$t->data['version'] = $config->getVersion();
$t->data['directory'] = dirname(dirname(dirname(dirname(__FILE__))));

$t->show();

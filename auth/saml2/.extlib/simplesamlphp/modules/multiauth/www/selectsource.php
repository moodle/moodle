<?php

/**
 * This page shows a list of authentication sources. When the user selects
 * one of them if pass this information to the
 * \SimpleSAML\Module\multiauth\Auth\Source\MultiAuth class and call the
 * delegateAuthentication method on it.
 *
 * @author Lorenzo Gil, Yaco Sistemas S.L.
 * @package SimpleSAMLphp
 */

// Retrieve the authentication state
if (!array_key_exists('AuthState', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing AuthState parameter.');
}
$authStateId = $_REQUEST['AuthState'];

/** @var array $state */
$state = \SimpleSAML\Auth\State::loadState($authStateId, \SimpleSAML\Module\multiauth\Auth\Source\MultiAuth::STAGEID);

if (array_key_exists("\SimpleSAML\Auth\Source.id", $state)) {
    $authId = $state["\SimpleSAML\Auth\Source.id"];
    /** @var \SimpleSAML\Module\multiauth\Auth\Source\MultiAuth $as */
    $as = \SimpleSAML\Auth\Source::getById($authId);
} else {
    $as = null;
}

$source = null;
if (array_key_exists('source', $_REQUEST)) {
    $source = $_REQUEST['source'];
} else {
    foreach ($_REQUEST as $k => $v) {
        $k = explode('-', $k, 2);
        if (count($k) === 2 && $k[0] === 'src') {
            $source = base64_decode($k[1]);
        }
    }
}
if ($source !== null) {
    if ($as !== null) {
        $as->setPreviousSource($source);
    }
    \SimpleSAML\Module\multiauth\Auth\Source\MultiAuth::delegateAuthentication($source, $state);
}

if (array_key_exists('multiauth:preselect', $state)) {
    $source = $state['multiauth:preselect'];
    \SimpleSAML\Module\multiauth\Auth\Source\MultiAuth::delegateAuthentication($source, $state);
}

$globalConfig = \SimpleSAML\Configuration::getInstance();
$t = new \SimpleSAML\XHTML\Template($globalConfig, 'multiauth:selectsource.tpl.php');

$defaultLanguage = $globalConfig->getString('language.default', 'en');
$language = $t->getTranslator()->getLanguage()->getLanguage();

$sources = $state[\SimpleSAML\Module\multiauth\Auth\Source\MultiAuth::SOURCESID];
foreach ($sources as $key => $source) {
    $sources[$key]['source64'] = base64_encode($sources[$key]['source']);
    if (isset($sources[$key]['text'][$language])) {
        $sources[$key]['text'] = $sources[$key]['text'][$language];
    } else {
        $sources[$key]['text'] = $sources[$key]['text'][$defaultLanguage];
    }

    if (isset($sources[$key]['help'][$language])) {
        $sources[$key]['help'] = $sources[$key]['help'][$language];
    } else {
        $sources[$key]['help'] = $sources[$key]['help'][$defaultLanguage];
    }
}

$t->data['authstate'] = $authStateId;
$t->data['sources'] = $sources;
$t->data['selfUrl'] = $_SERVER['PHP_SELF'];

if ($as !== null) {
    $t->data['preferred'] = $as->getPreviousSource();
} else {
    $t->data['preferred'] = null;
}
$t->show();
exit();

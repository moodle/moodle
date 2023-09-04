<?php

/**
 * Show a warning to an user about the SP requesting SSO a short time after
 * doing it previously.
 *
 * @package SimpleSAMLphp
 */

if (!array_key_exists('StateId', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing required StateId query parameter.');
}
$id = $_REQUEST['StateId'];

/** @var array $state */
$state = \SimpleSAML\Auth\State::loadState($id, 'core:short_sso_interval');

$session = \SimpleSAML\Session::getSessionFromRequest();

if (array_key_exists('continue', $_REQUEST)) {
    // The user has pressed the continue/retry-button
    \SimpleSAML\Auth\ProcessingChain::resumeProcessing($state);
}

$globalConfig = \SimpleSAML\Configuration::getInstance();
$t = new \SimpleSAML\XHTML\Template($globalConfig, 'core:short_sso_interval.tpl.php');
$translator = $t->getTranslator();
$t->data['target'] = \SimpleSAML\Module::getModuleURL('core/short_sso_interval.php');
$t->data['params'] = ['StateId' => $id];
$t->data['trackId'] = $session->getTrackID();
$t->data['header'] = $translator->t('{core:short_sso_interval:warning_header}');
$t->data['autofocus'] = 'contbutton';
$t->show();

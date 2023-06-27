<?php

/**
 * Show a 403 Forbidden page when an attribute violates a cardinality rule
 *
 * @package SimpleSAMLphp
 */

if (!array_key_exists('StateId', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing required StateId query parameter.');
}
$id = $_REQUEST['StateId'];
/** @var array $state */
$state = \SimpleSAML\Auth\State::loadState($id, 'core:cardinality');
$session = \SimpleSAML\Session::getSessionFromRequest();

\SimpleSAML\Logger::stats('core:cardinality:error ' . $state['Destination']['entityid']
    . ' ' . $state['saml:sp:IdP'] . ' ' . implode(',', array_keys($state['core:cardinality:errorAttributes'])));

$globalConfig = \SimpleSAML\Configuration::getInstance();
$t = new \SimpleSAML\XHTML\Template($globalConfig, 'core:cardinality_error.tpl.php');
$t->data['cardinalityErrorAttributes'] = $state['core:cardinality:errorAttributes'];
if (isset($state['Source']['auth'])) {
    $t->data['LogoutURL'] = \SimpleSAML\Module::getModuleURL(
        'core/authenticate.php',
        ['as' => $state['Source']['auth']]
    ) . "&logout";
}
header('HTTP/1.0 403 Forbidden');
$t->show();

<?php

require_once('../../_include.php');

$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();

$config = \SimpleSAML\Configuration::getInstance();
if (!$config->getBoolean('enable.saml20-idp', false) || !\SimpleSAML\Module::isModuleEnabled('saml')) {
    throw new \SimpleSAML\Error\Error('NOACCESS', null, 403);
}

$idpEntityId = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
$idp = \SimpleSAML\IdP::getById('saml2:' . $idpEntityId);

\SimpleSAML\Logger::info('SAML2.0 - IdP.initSLO: Accessing SAML 2.0 IdP endpoint init Single Logout');

if (!isset($_GET['RelayState'])) {
    throw new \SimpleSAML\Error\Error('NORELAYSTATE');
}

$idp->doLogoutRedirect(\SimpleSAML\Utils\HTTP::checkURLAllowed((string) $_GET['RelayState']));
assert(false);

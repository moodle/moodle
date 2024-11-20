<?php

/**
 * This SAML 2.0 endpoint can receive incoming LogoutRequests. It will also send LogoutResponses,
 * and LogoutRequests and also receive LogoutResponses. It is implemeting SLO at the SAML 2.0 IdP.
 *
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */

require_once('../../_include.php');

\SimpleSAML\Logger::info('SAML2.0 - IdP.SingleLogoutService: Accessing SAML 2.0 IdP endpoint SingleLogoutService');

$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();

$config = \SimpleSAML\Configuration::getInstance();
if (!$config->getBoolean('enable.saml20-idp', false) || !\SimpleSAML\Module::isModuleEnabled('saml')) {
    throw new \SimpleSAML\Error\Error('NOACCESS', null, 403);
}

$idpEntityId = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
$idp = \SimpleSAML\IdP::getById('saml2:' . $idpEntityId);

if (isset($_REQUEST['ReturnTo'])) {
    $idp->doLogoutRedirect(\SimpleSAML\Utils\HTTP::checkURLAllowed((string) $_REQUEST['ReturnTo']));
} else {
    try {
        \SimpleSAML\Module\saml\IdP\SAML2::receiveLogoutMessage($idp);
    } catch (\Exception $e) {
        // TODO: look for a specific exception
        /*
         * This is dirty. Instead of checking the message of the exception, \SAML2\Binding::getCurrentBinding() should
         * throw an specific exception when the binding is unknown, and we should capture that here
         */
        if ($e->getMessage() === 'Unable to find the current binding.') {
            throw new \SimpleSAML\Error\Error('SLOSERVICEPARAMS', $e, 400);
        } else {
            throw $e; // do not ignore other exceptions!
        }
    }
}
assert(false);

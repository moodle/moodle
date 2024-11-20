<?php

/**
 * ADFS PRP IDP protocol support for SimpleSAMLphp.
 *
 * @author Hans Zandbelt, SURFnet bv, <hans.zandbelt@surfnet.nl>
 * @package SimpleSAMLphp
 */

\SimpleSAML\Logger::info('ADFS - IdP.prp: Accessing ADFS IdP endpoint prp');

$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();
$idpEntityId = $metadata->getMetaDataCurrentEntityID('adfs-idp-hosted');
$idp = \SimpleSAML\IdP::getById('adfs:'.$idpEntityId);

if (isset($_GET['wa'])) {
    if ($_GET['wa'] === 'wsignout1.0') {
        \SimpleSAML\Module\adfs\IdP\ADFS::receiveLogoutMessage($idp);
    } elseif ($_GET['wa'] === 'wsignin1.0') {
        \SimpleSAML\Module\adfs\IdP\ADFS::receiveAuthnRequest($idp);
    }
    throw new \Exception("Code should never be reached");
} elseif (isset($_GET['assocId'])) {
    // logout response from ADFS SP
    $assocId = $_GET['assocId']; // Association ID of the SP that sent the logout response
    $relayState = $_GET['relayState']; // Data that was sent in the logout request to the SP. Can be null
    $logoutError = null; // null on success, or an instance of a \SimpleSAML\Error\Exception on failure.
    $idp->handleLogoutResponse($assocId, $relayState, $logoutError);
}

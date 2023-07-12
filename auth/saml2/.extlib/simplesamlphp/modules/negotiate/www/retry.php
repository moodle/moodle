<?php

/**
 *
 * @author Mathias Meisfjordskar, University of Oslo.
 *         <mathias.meisfjordskar@usit.uio.no>
 * @package SimpleSAMLphp
 *
 */

$state = \SimpleSAML\Auth\State::loadState(
    $_REQUEST['AuthState'],
    \SimpleSAML\Module\negotiate\Auth\Source\Negotiate::STAGEID
);

$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();
$idpid = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted', 'metaindex');
$idpmeta = $metadata->getMetaData($idpid, 'saml20-idp-hosted');

if (isset($idpmeta['auth'])) {
    $source = \SimpleSAML\Auth\Source::getById($idpmeta['auth']);
    if ($source === null) {
        throw new \SimpleSAML\Error\BadRequest('Invalid AuthId "' . $idpmeta['auth'] . '" - not found.');
    }

    $session = \SimpleSAML\Session::getSessionFromRequest();
    $session->setData('negotiate:disable', 'session', false, 86400); //24*60*60=86400
    \SimpleSAML\Logger::debug('Negotiate(retry) - session enabled, retrying.');
    $source->authenticate($state);
    assert(false);
} else {
    \SimpleSAML\Logger::error('Negotiate - retry - no "auth" parameter found in IdP metadata.');
    assert(false);
}

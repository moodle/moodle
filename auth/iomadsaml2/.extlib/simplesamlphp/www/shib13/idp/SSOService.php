<?php

/**
 * The SSOService is part of the Shibboleth 1.3 IdP code, and it receives incoming Authentication Requests
 * from a Shibboleth 1.3 SP, parses, and process it, and then authenticates the user and sends the user back
 * to the SP with an Authentication Response.
 *
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */

require_once '../../_include.php';

\SimpleSAML\Logger::info('Shib1.3 - IdP.SSOService: Accessing Shibboleth 1.3 IdP endpoint SSOService');
\SimpleSAML\Logger::notice('SAML1 support is deprecated and will be removed in SimpleSAMLphp 2.0');

$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();
$idpEntityId = $metadata->getMetaDataCurrentEntityID('shib13-idp-hosted');
$idp = \SimpleSAML\IdP::getById('saml1:'.$idpEntityId);
\SimpleSAML\Module\saml\IdP\SAML1::receiveAuthnRequest($idp);

assert(false);

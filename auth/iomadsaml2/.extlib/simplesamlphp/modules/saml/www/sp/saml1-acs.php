<?php

use SimpleSAML\Bindings\Shib13\Artifact;

if (!array_key_exists('SAMLResponse', $_REQUEST) && !array_key_exists('SAMLart', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing SAMLResponse or SAMLart parameter.');
}

if (!array_key_exists('TARGET', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing TARGET parameter.');
}

if (!array_key_exists('PATH_INFO', $_SERVER)) {
    throw new \SimpleSAML\Error\BadRequest('Missing authentication source ID in assertion consumer service URL');
}

$sourceId = $_SERVER['PATH_INFO'];
$end = strpos($sourceId, '/', 1);
if ($end === false) {
    $end = strlen($sourceId);
}
$sourceId = substr($sourceId, 1, $end - 1);

/** @var \SimpleSAML\Module\saml\Auth\Source\SP $source */
$source = \SimpleSAML\Auth\Source::getById($sourceId, '\SimpleSAML\Module\saml\Auth\Source\SP');

SimpleSAML\Logger::debug('Received SAML1 response');

$target = (string) $_REQUEST['TARGET'];

if (preg_match('@^https?://@i', $target)) {
    // Unsolicited response
    $state = [
        'saml:sp:isUnsolicited' => true,
        'saml:sp:AuthId' => $sourceId,
        'saml:sp:RelayState' => \SimpleSAML\Utils\HTTP::checkURLAllowed($target),
    ];
    $deprecated_extra = '';
} else {
    /** @var array $state  State can never be null without a third argument */
    $state = \SimpleSAML\Auth\State::loadState($_REQUEST['TARGET'], 'saml:sp:sso');

    // Check that the authentication source is correct
    assert(array_key_exists('saml:sp:AuthId', $state));
    if ($state['saml:sp:AuthId'] !== $sourceId) {
        throw new \SimpleSAML\Error\Exception(
            'The authentication source id in the URL does not match the authentication source which sent the request.'
        );
    }

    assert(isset($state['saml:idp']));
    $deprecated_extra = " IdP: {$state['saml:idp']}";
}

SimpleSAML\Logger::notice('SAML1 support is deprecated and will be removed in SimpleSAMLphp 2.0'. $deprecated_extra);

$spMetadata = $source->getMetadata();

if (array_key_exists('SAMLart', $_REQUEST)) {
    if (!isset($state['saml:idp'])) {
        // Unsolicited response
        throw new \SimpleSAML\Error\Exception(
            'IdP initiated authentication not supported with the SAML 1.1 SAMLart protocol.'
        );
    }
    $idpMetadata = $source->getIdPMetadata($state['saml:idp']);

    $responseXML = Artifact::receive($spMetadata, $idpMetadata);
    $isValidated = true; /* Artifact binding validated with ssl certificate. */
} elseif (array_key_exists('SAMLResponse', $_REQUEST)) {
    $responseXML = $_REQUEST['SAMLResponse'];
    $responseXML = base64_decode($responseXML);
    $isValidated = false; /* Must check signature on response. */
} else {
    throw new \SimpleSAML\Error\BadRequest('Missing SAMLResponse or SAMLart parameter.');
}

$response = new \SimpleSAML\XML\Shib13\AuthnResponse();
$response->setXML($responseXML);

$response->setMessageValidated($isValidated);
$response->validate();

$responseIssuer = $response->getIssuer();
$attributes = $response->getAttributes();

if (isset($state['saml:idp']) && $responseIssuer !== $state['saml:idp']) {
    throw new \SimpleSAML\Error\Exception('The issuer of the response wasn\'t the destination of the request.');
}

$logoutState = [
    'saml:logout:Type' => 'saml1'
];
$state['LogoutState'] = $logoutState;

$state['saml:sp:NameID'] = $response->getNameID();

$source->handleResponse($state, $responseIssuer, $attributes);
assert(false);

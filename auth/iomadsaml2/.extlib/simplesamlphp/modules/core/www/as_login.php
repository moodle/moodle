<?php

/**
 * Endpoint for logging in with an authentication source.
 *
 * @package SimpleSAMLphp
 */

if (!isset($_REQUEST['ReturnTo'])) {
    throw new \SimpleSAML\Error\BadRequest('Missing ReturnTo parameter.');
}

if (!isset($_REQUEST['AuthId'])) {
    throw new \SimpleSAML\Error\BadRequest('Missing AuthId parameter.');
}

/*
 * Setting up the options for the requireAuth() call later..
 */
$options = [
    'ReturnTo' => \SimpleSAML\Utils\HTTP::checkURLAllowed($_REQUEST['ReturnTo']),
];

/*
 * Allows a saml:idp query string parameter specify the IdP entity ID to be used
 * as used by the DiscoJuice embedded client.
 */
if (!empty($_REQUEST['saml:idp'])) {
    $options['saml:idp'] = $_REQUEST['saml:idp'];
}

$as = new \SimpleSAML\Auth\Simple($_REQUEST['AuthId']);
$as->requireAuth($options);

\SimpleSAML\Utils\HTTP::redirectTrustedURL($options['ReturnTo']);

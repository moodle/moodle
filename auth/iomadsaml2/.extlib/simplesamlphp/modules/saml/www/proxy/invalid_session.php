<?php

/**
 * This file will handle the case of a user with an existing session that's not valid for a specific Service Provider,
 * since the authenticating IdP is not in the list of IdPs allowed by the SP.
 *
 * @author Jaime Pérez Crespo, UNINETT AS <jaime.perez@uninett.no>
 *
 * @package SimpleSAMLphp
 */

// retrieve the authentication state
if (!array_key_exists('AuthState', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing mandatory parameter: AuthState');
}

try {
    // try to get the state
    /** @var array $state  State can never be null without a third argument */
    $state = \SimpleSAML\Auth\State::loadState($_REQUEST['AuthState'], 'saml:proxy:invalid_idp');
} catch (\Exception $e) {
    // the user probably hit the back button after starting the logout, try to recover the state with another stage
    /** @var array $state  State can never be null without a third argument */
    $state = \SimpleSAML\Auth\State::loadState($_REQUEST['AuthState'], 'core:Logout:afterbridge');

    // success! Try to continue with reauthentication, since we no longer have a valid session here
    $idp = \SimpleSAML\IdP::getById($state['core:IdP']);
    \SimpleSAML\Module\saml\Auth\Source\SP::reauthPostLogout($idp, $state);
}

if (isset($_POST['cancel'])) {
    // the user does not want to logout, cancel login
    \SimpleSAML\Auth\State::throwException(
        $state,
        new \SimpleSAML\Module\saml\Error\NoAvailableIDP(
            \SAML2\Constants::STATUS_RESPONDER,
            'User refused to reauthenticate with any of the IdPs requested.'
        )
    );
}

if (isset($_POST['continue'])) {
    /** @var \SimpleSAML\Module\saml\Auth\Source\SP $as */
    $as = \SimpleSAML\Auth\Source::getById($state['saml:sp:AuthId'], '\SimpleSAML\Module\saml\Auth\Source\SP');

    // log the user out before being able to login again
    $as->reauthLogout($state);
}

$cfg = \SimpleSAML\Configuration::getInstance();
$template = new \SimpleSAML\XHTML\Template($cfg, 'saml:proxy/invalid_session.tpl.php');
$translator = $template->getTranslator();
$template->data['AuthState'] = (string) $_REQUEST['AuthState'];

// get the name of the IdP
$idpmdcfg = $state['saml:sp:IdPMetadata'];
/** @var \SimpleSAML\Configuration $idpmdcfg */
$idpmd = $idpmdcfg->toArray();
if (array_key_exists('name', $idpmd)) {
    $template->data['idp_name'] = $translator->getPreferredTranslation($idpmd['name']);
} elseif (array_key_exists('OrganizationDisplayName', $idpmd)) {
    $template->data['idp_name'] = $translator->getPreferredTranslation($idpmd['OrganizationDisplayName']);
} else {
    $template->data['idp_name'] = $idpmd['entityid'];
}

// get the name of the SP
$spmd = $state['SPMetadata'];
if (array_key_exists('name', $spmd)) {
    $template->data['sp_name'] = $translator->getPreferredTranslation($spmd['name']);
} elseif (array_key_exists('OrganizationDisplayName', $spmd)) {
    $template->data['sp_name'] = $translator->getPreferredTranslation($spmd['OrganizationDisplayName']);
} else {
    $template->data['sp_name'] = $spmd['entityid'];
}

$template->show();

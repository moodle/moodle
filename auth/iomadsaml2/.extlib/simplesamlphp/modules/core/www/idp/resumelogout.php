<?php

if (!isset($_REQUEST['id'])) {
    throw new \SimpleSAML\Error\BadRequest('Missing id-parameter.');
}

/** @psalm-var array $state */
$state = \SimpleSAML\Auth\State::loadState($_REQUEST['id'], 'core:Logout:afterbridge');
$idp = \SimpleSAML\IdP::getByState($state);

$assocId = $state['core:TerminatedAssocId'];

$handler = $idp->getLogoutHandler();
$handler->startLogout($state, $assocId);

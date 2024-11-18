<?php
/**
 * This is the handler for logout started from the consent page.
 *
 * @package SimpleSAMLphp
 */

if (!array_key_exists('StateId', $_GET)) {
    throw new \SimpleSAML\Error\BadRequest('Missing required StateId query parameter.');
}
$state = \SimpleSAML\Auth\State::loadState($_GET['StateId'], 'consent:request');

$state['Responder'] = ['\SimpleSAML\Module\consent\Logout', 'postLogout'];

$idp = \SimpleSAML\IdP::getByState($state);
$idp->handleLogoutRequest($state, null);
throw new \Exception('Should never happen');

<?php

/**
 * Provide a URL for the module to statically link to.
 *
 * @author Mathias Meisfjordskar, University of Oslo.
 *         <mathias.meisfjordskar@usit.uio.no>
 * @package SimpleSAMLphp
 */

$state = \SimpleSAML\Auth\State::loadState(
    $_REQUEST['AuthState'],
    \SimpleSAML\Module\negotiate\Auth\Source\Negotiate::STAGEID
);
\SimpleSAML\Logger::debug('backend - fallback: ' . $state['LogoutState']['negotiate:backend']);

\SimpleSAML\Module\negotiate\Auth\Source\Negotiate::fallBack($state);

exit;

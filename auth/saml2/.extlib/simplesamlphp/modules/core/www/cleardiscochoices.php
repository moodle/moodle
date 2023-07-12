<?php

require_once('_include.php');

/**
 * This page clears the user's IdP discovery choices.
 */

// The base path for cookies. This should be the installation directory for SimpleSAMLphp.
$config = \SimpleSAML\Configuration::getInstance();
$cookiePath = $config->getBasePath();

// We delete all cookies which starts with 'idpdisco_'
foreach ($_COOKIE as $cookieName => $value) {
    if (substr($cookieName, 0, 9) !== 'idpdisco_') {
        // Not a idpdisco cookie.
        continue;
    }

    /* Delete the cookie. We delete it once without the secure flag and once with the secure flag. This
     * ensures that the cookie will be deleted in any case.
     */
    \SimpleSAML\Utils\HTTP::setCookie($cookieName, null, ['path' => $cookiePath, 'httponly' => false], false);
}

// Find where we should go now.
if (array_key_exists('ReturnTo', $_REQUEST)) {
    $returnTo = \SimpleSAML\Utils\HTTP::checkURLAllowed($_REQUEST['ReturnTo']);
} else {
    // Return to the front page if no other destination is given. This is the same as the base cookie path.
    $returnTo = $cookiePath;
}

// Redirect to destination.
\SimpleSAML\Utils\HTTP::redirectTrustedURL($returnTo);

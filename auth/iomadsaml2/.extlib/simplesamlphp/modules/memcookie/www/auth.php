<?php

use SimpleSAML\Utils;
use SimpleSAML\Module\memcookie\AuthMemCookie;

/**
 * This file implements an script which can be used to authenticate users with Auth MemCookie.
 * See: https://zenprojects.github.io/Apache-Authmemcookie-Module/
 *
 * The configuration for this script is stored in config/authmemcookie.php.
 *
 * The file extra/auth_memcookie.conf contains an example of how Auth Memcookie can be configured
 * to use SimpleSAMLphp.
 */

// load SimpleSAMLphp configuration
$ssp_cf = \SimpleSAML\Configuration::getInstance();

// load Auth MemCookie configuration
$amc_cf = AuthMemCookie::getInstance();

$sourceId = $amc_cf->getAuthSource();
$s = new SimpleSAML\Auth\Simple($sourceId);

// check if the user is authorized. We attempt to authenticate the user if not
$s->requireAuth();

// generate session id and save it in a cookie
$sessionID = Utils\Random::generateID();
$cookieName = $amc_cf->getCookieName();
Utils\HTTP::setCookie($cookieName, $sessionID);

// generate the authentication information
$attributes = $s->getAttributes();

$authData = [];

// username
$usernameAttr = $amc_cf->getUsernameAttr();
if (!array_key_exists($usernameAttr, $attributes)) {
    throw new \SimpleSAML\Error\Exception(
        "The user doesn't have an attribute named '".$usernameAttr.
        "'. This attribute is expected to contain the username."
    );
}
$authData['UserName'] = $attributes[$usernameAttr];

// groups
$groupsAttr = $amc_cf->getGroupsAttr();
if ($groupsAttr !== null) {
    if (!array_key_exists($groupsAttr, $attributes)) {
        throw new \SimpleSAML\Error\Exception(
            "The user doesn't have an attribute named '".$groupsAttr.
            "'. This attribute is expected to contain the groups the user is a member of."
        );
    }
    $authData['Groups'] = $attributes[$groupsAttr];
} else {
    $authData['Groups'] = [];
}

$authData['RemoteIP'] = $_SERVER['REMOTE_ADDR'];

foreach ($attributes as $n => $v) {
    $authData['ATTR_'.$n] = $v;
}

// store the authentication data in the memcache server
$data = '';
foreach ($authData as $n => $v) {
    if (is_array($v)) {
        $v = implode(':', $v);
    }
    $data .= $n.'='.$v."\r\n";
}

$memcache = $amc_cf->getMemcache();
$expirationTime = $s->getAuthData('Expire');
if ($memcache instanceof \Memcached) {
    $memcache->set($sessionID, $data, $expirationTime);
} else {
    $memcache->set($sessionID, $data, 0, $expirationTime);
}

// register logout handler
$session = \SimpleSAML\Session::getSessionFromRequest();
$session->registerLogoutHandler($sourceId, '\SimpleSAML\Module\memcookie\AuthMemCookie', 'logoutHandler');

// redirect the user back to this page to signal that the login is completed
Utils\HTTP::redirectTrustedURL(Utils\HTTP::getSelfURL());

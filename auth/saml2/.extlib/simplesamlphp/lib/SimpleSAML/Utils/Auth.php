<?php

declare(strict_types=1);

namespace SimpleSAML\Utils;

use SimpleSAML\Auth as Authentication;
use SimpleSAML\Error;
use SimpleSAML\Module;
use SimpleSAML\Session;

/**
 * Auth-related utility methods.
 *
 * @package SimpleSAMLphp
 */
class Auth
{
    /**
     * Retrieve an admin login URL.
     *
     * @param string|NULL $returnTo The URL the user should arrive on after admin authentication. Defaults to null.
     *
     * @return string A URL which can be used for admin authentication.
     * @throws \InvalidArgumentException If $returnTo is neither a string nor null.
     */
    public static function getAdminLoginURL($returnTo = null)
    {
        if (!(is_string($returnTo) || is_null($returnTo))) {
            throw new \InvalidArgumentException('Invalid input parameters.');
        }

        if ($returnTo === null) {
            $returnTo = HTTP::getSelfURL();
        }

        return Module::getModuleURL('core/login-admin.php', ['ReturnTo' => $returnTo]);
    }


    /**
     * Retrieve an admin logout URL.
     *
     * @param string|NULL $returnTo The URL the user should arrive on after admin authentication. Defaults to null.
     *
     * @return string A URL which can be used for logging out.
     * @throws \InvalidArgumentException If $returnTo is neither a string nor null.
     */
    public static function getAdminLogoutURL($returnTo = null)
    {
        if (!(is_string($returnTo) || is_null($returnTo))) {
            throw new \InvalidArgumentException('Invalid input parameters.');
        }

        $as = new Authentication\Simple('admin');
        return $as->getLogoutURL($returnTo = null);
    }


    /**
     * Check whether the current user is admin.
     *
     * @return boolean True if the current user is an admin user, false otherwise.
     *
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     */
    public static function isAdmin()
    {
        $session = Session::getSessionFromRequest();
        return $session->isValid('admin') || $session->isValid('login-admin');
    }

    /**
     * Require admin access to the current page.
     *
     * This is a helper function for limiting a page to those with administrative access. It will redirect the user to
     * a login page if the current user doesn't have admin access.
     *
     * @return void This function will only return if the user is admin.
     * @throws \SimpleSAML\Error\Exception If no "admin" authentication source was configured.
     *
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     */
    public static function requireAdmin()
    {
        if (self::isAdmin()) {
            return;
        }

        // not authenticated as admin user, start authentication
        if (Authentication\Source::getById('admin') !== null) {
            $as = new Authentication\Simple('admin');
            $as->login();
        } else {
            throw new Error\Exception(
                'Cannot find "admin" auth source, and admin privileges are required.'
            );
        }
    }
}

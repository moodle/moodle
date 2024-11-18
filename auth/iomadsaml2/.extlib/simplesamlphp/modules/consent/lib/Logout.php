<?php

namespace SimpleSAML\Module\consent;

/**
 * Class defining the logout completed handler for the consent page.
 *
 * @package SimpleSAMLphp
 */

class Logout
{
    /**
     * @param \SimpleSAML\IdP $idp
     * @param array $state
     * @return void
     */
    public static function postLogout(\SimpleSAML\IdP $idp, array $state)
    {
        $url = \SimpleSAML\Module::getModuleURL('consent/logout_completed.php');
        \SimpleSAML\Utils\HTTP::redirectTrustedURL($url);
    }
}

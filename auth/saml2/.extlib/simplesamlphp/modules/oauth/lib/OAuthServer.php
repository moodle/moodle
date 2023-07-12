<?php

namespace SimpleSAML\Module\oauth;

require_once(dirname(dirname(__FILE__)).'/libextinc/OAuth.php');

/**
 * OAuth Provider implementation..
 *
 * @author Andreas Åkre Solberg, <andreas.solberg@uninett.no>, UNINETT AS.
 * @package SimpleSAMLphp
 */
class OAuthServer extends \OAuthServer
{
    /**
     * @param \OAuthDataStore $store
     */
    public function __construct($store)
    {
        parent::__construct($store);
    }


    /**
     * @return array
     */
    public function get_signature_methods()
    {
        return $this->signature_methods;
    }
}

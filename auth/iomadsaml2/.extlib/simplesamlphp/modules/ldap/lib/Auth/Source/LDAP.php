<?php

namespace SimpleSAML\Module\ldap\Auth\Source;

/**
 * LDAP authentication source.
 *
 * See the ldap-entry in config-templates/authsources.php for information about
 * configuration of this authentication source.
 *
 * This class is based on www/auth/login.php.
 *
 * @package SimpleSAMLphp
 */

class LDAP extends \SimpleSAML\Module\core\Auth\UserPassBase
{
    /**
     * A LDAP configuration object.
     */
    private $ldapConfig;


    /**
     * Constructor for this authentication source.
     *
     * @param array $info  Information about this authentication source.
     * @param array $config  Configuration.
     */
    public function __construct($info, $config)
    {
        assert(is_array($info));
        assert(is_array($config));

        // Call the parent constructor first, as required by the interface
        parent::__construct($info, $config);

        $this->ldapConfig = new \SimpleSAML\Module\ldap\ConfigHelper(
            $config,
            'Authentication source '.var_export($this->authId, true)
        );
    }


    /**
     * Attempt to log in using the given username and password.
     *
     * @param string $username  The username the user wrote.
     * @param string $password  The password the user wrote.
     * param array $sasl_arg  Associative array of SASL options
     * @return array  Associative array with the users attributes.
     */
    protected function login($username, $password, array $sasl_args = null)
    {
        assert(is_string($username));
        assert(is_string($password));

        return $this->ldapConfig->login($username, $password, $sasl_args);
    }
}

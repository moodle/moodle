<?php

namespace SimpleSAML\Module\radius\Auth\Source;

/**
 * RADIUS authentication source.
 *
 * This class is based on www/auth/login-radius.php.
 *
 * @package SimpleSAMLphp
 */
class Radius extends \SimpleSAML\Module\core\Auth\UserPassBase
{
    /**
     * @var array The list of radius servers to use.
     */
    private $servers;

    /**
     * @var string The hostname of the radius server.
     */
    private $hostname;

    /**
     * @var int The port of the radius server.
     */
    private $port;

    /**
     * @var string The secret used when communicating with the radius server.
     */
    private $secret;

    /**
     * @var int The timeout for contacting the radius server.
     */
    private $timeout;

    /**
     * @var int The number of retries which should be attempted.
     */
    private $retries;

    /**
     * Var string The realm to be added to the entered username.
     */
    private $realm;

    /**
     * @var string|null The attribute name where the username should be stored.
     */
    private $usernameAttribute = null;

    /**
     * @var string|null The vendor for the RADIUS attributes we are interrested in.
     */
    private $vendor = null;

    /**
     * @var string The vendor-specific attribute for the RADIUS attributes we are
     *     interrested in.
     */
    private $vendorType;

    /**
     * @var string|null The NAS-Identifier that should be set in Access-Request packets.
     */
    private $nasIdentifier = null;


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

        // Parse configuration.
        $cfg = \SimpleSAML\Configuration::loadFromArray(
            $config,
            'Authentication source ' . var_export($this->authId, true)
        );

        $this->servers = $cfg->getArray('servers', []);
        // For backwards compatibility
        if (empty($this->servers)) {
            $this->hostname = $cfg->getString('hostname');
            $this->port = $cfg->getIntegerRange('port', 1, 65535, 1812);
            $this->secret = $cfg->getString('secret');
            $this->servers[] = [
                'hostname' => $this->hostname,
                'port' => $this->port,
                'secret' => $this->secret
            ];
        }
        $this->timeout = $cfg->getInteger('timeout', 5);
        $this->retries = $cfg->getInteger('retries', 3);
        $this->realm = $cfg->getString('realm', null);
        $this->usernameAttribute = $cfg->getString('username_attribute', null);
        $this->nasIdentifier = $cfg->getString(
            'nas_identifier',
            \SimpleSAML\Utils\HTTP::getSelfHost()
        );

        $this->vendor = $cfg->getInteger('attribute_vendor', null);
        if ($this->vendor !== null) {
            $this->vendorType = $cfg->getInteger('attribute_vendor_type');
        }
    }


    /**
     * Attempt to log in using the given username and password.
     *
     * @param string $username  The username the user wrote.
     * @param string $password  The password the user wrote.
     * @return array[] Associative array with the user's attributes.
     */
    protected function login($username, $password)
    {
        assert(is_string($username));
        assert(is_string($password));

        $radius = radius_auth_open();
        if (!is_resource($radius)) {
            throw new \Exception("Insufficient memory available to create handle.");
        }

        // Try to add all radius servers, trigger a failure if no one works
        $success = false;
        foreach ($this->servers as $server) {
            if (!isset($server['port'])) {
                $server['port'] = 1812;
            }
            if (
                !radius_add_server(
                    $radius,
                    $server['hostname'],
                    $server['port'],
                    $server['secret'],
                    $this->timeout,
                    $this->retries
                )
            ) {
                \SimpleSAML\Logger::info(
                    "Could not add radius server: " . radius_strerror($radius)
                );
                continue;
            }
            $success = true;
        }
        if (!$success) {
            throw new \Exception('Error adding radius servers, no servers available');
        }

        if (!radius_create_request($radius, \RADIUS_ACCESS_REQUEST)) {
            throw new \Exception(
                'Error creating radius request: ' . radius_strerror($radius)
            );
        }

        if ($this->realm === null) {
            radius_put_attr($radius, \RADIUS_USER_NAME, $username);
        } else {
            radius_put_attr($radius, \RADIUS_USER_NAME, $username . '@' . $this->realm);
        }
        radius_put_attr($radius, \RADIUS_USER_PASSWORD, $password);

        if ($this->nasIdentifier !== null) {
            radius_put_attr($radius, \RADIUS_NAS_IDENTIFIER, $this->nasIdentifier);
        }

        $res = radius_send_request($radius);
        if ($res != \RADIUS_ACCESS_ACCEPT) {
            switch ($res) {
                case \RADIUS_ACCESS_REJECT:
                    // Invalid username or password
                    throw new \SimpleSAML\Error\Error('WRONGUSERPASS');
                case \RADIUS_ACCESS_CHALLENGE:
                    throw new \Exception('Radius authentication error: Challenge requested, but not supported.');
                default:
                    throw new \Exception(
                        'Error during radius authentication: ' . radius_strerror($radius)
                    );
            }
        }

        // If we get this far, we have a valid login

        $attributes = [];
        $usernameAttribute = $this->usernameAttribute;

        if ($usernameAttribute !== null) {
            $attributes[$usernameAttribute] = [$username];
        }

        if ($this->vendor === null) {
            /*
             * We aren't interested in any vendor-specific attributes. We are
             * therefore done now.
             */
            return $attributes;
        }

        // get AAI attribute sets. Contributed by Stefan Winter, (c) RESTENA
        while ($resa = radius_get_attr($radius)) {
            if (!is_array($resa)) {
                throw new \Exception(
                    'Error getting radius attributes: ' . radius_strerror($radius)
                );
            }

            // Use the received user name
            if ($resa['attr'] === \RADIUS_USER_NAME && $usernameAttribute !== null) {
                $attributes[$usernameAttribute] = [$resa['data']];
                continue;
            }

            if ($resa['attr'] !== \RADIUS_VENDOR_SPECIFIC) {
                continue;
            }

            $resv = radius_get_vendor_attr($resa['data']);
            if ($resv === false) {
                throw new \Exception(
                    'Error getting vendor specific attribute: ' . radius_strerror($radius)
                );
            }

            $vendor = $resv['vendor'];
            $attrv = $resv['attr'];
            $datav = $resv['data'];

            if ($vendor != $this->vendor || $attrv != $this->vendorType) {
                continue;
            }

            $attrib_name = strtok($datav, '=');
            $attrib_value = strtok('=');

            // if the attribute name is already in result set, add another value
            if (array_key_exists($attrib_name, $attributes)) {
                $attributes[$attrib_name][] = $attrib_value;
            } else {
                $attributes[$attrib_name] = [$attrib_value];
            }
        }
        // end of contribution

        return $attributes;
    }
}

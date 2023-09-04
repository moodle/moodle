<?php

namespace SimpleSAML\Module\ldap\Auth;

use SimpleSAML\Error;
use SimpleSAML\Logger;

/**
 * Constants defining possible errors
 */

define('ERR_INTERNAL', 1);
define('ERR_NO_USER', 2);
define('ERR_WRONG_PW', 3);
define('ERR_AS_DATA_INCONSIST', 4);
define('ERR_AS_INTERNAL', 5);
define('ERR_AS_ATTRIBUTE', 6);

// not defined in earlier PHP versions
if (!defined('LDAP_OPT_DIAGNOSTIC_MESSAGE')) {
    define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);
}

/**
 * The LDAP class holds helper functions to access an LDAP database.
 *
 * @author Andreas Aakre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @author Anders Lund, UNINETT AS. <anders.lund@uninett.no>
 * @package SimpleSAMLphp
 */

class Ldap
{
    /**
     * LDAP link identifier.
     *
     * @var resource
     */
    protected $ldap;

    /**
     * LDAP user: authz_id if SASL is in use, binding dn otherwise
     *
     * @var string|null
     */
    protected $authz_id = null;

    /**
     * Timeout value, in seconds.
     *
     * @var int
     */
    protected $timeout = 0;

    /**
     * Private constructor restricts instantiation to getInstance().
     *
     * @param string $hostname
     * @param bool $enable_tls
     * @param bool $debug
     * @param int $timeout
     * @param int $port
     * @param bool $referrals
     * @psalm-suppress NullArgument
     */
    public function __construct(
        $hostname,
        $enable_tls = true,
        $debug = false,
        $timeout = 0,
        $port = 389,
        $referrals = true
    ) {
        // Debug
        Logger::debug('Library - LDAP __construct(): Setup LDAP with '.
            'host=\''.$hostname.
            '\', tls='.var_export($enable_tls, true).
            ', debug='.var_export($debug, true).
            ', timeout='.var_export($timeout, true).
            ', referrals='.var_export($referrals, true));

        /*
         * Set debug level before calling connect. Note that this passes
         * NULL to ldap_set_option, which is an undocumented feature.
         *
         * OpenLDAP 2.x.x or Netscape Directory SDK x.x needed for this option.
         */
        if ($debug && !ldap_set_option(null, LDAP_OPT_DEBUG_LEVEL, 7)) {
            Logger::warning('Library - LDAP __construct(): Unable to set debug level (LDAP_OPT_DEBUG_LEVEL) to 7');
        }

        /*
         * Prepare a connection for to this LDAP server. Note that this function
         * doesn't actually connect to the server.
         */
        $resource = @ldap_connect($hostname, $port);
        if ($resource === false) {
            throw $this->makeException(
                'Library - LDAP __construct(): Unable to connect to \''.$hostname.'\'',
                ERR_INTERNAL
            );
        }
        $this->ldap = $resource;

        // Enable LDAP protocol version 3
        if (!@ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3)) {
            throw $this->makeException(
                'Library - LDAP __construct(): Failed to set LDAP Protocol version (LDAP_OPT_PROTOCOL_VERSION) to 3',
                ERR_INTERNAL
            );
        }

        // Set referral option
        if (!@ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, $referrals)) {
            throw $this->makeException(
                'Library - LDAP __construct(): Failed to set LDAP Referrals (LDAP_OPT_REFERRALS) to '.$referrals,
                ERR_INTERNAL
            );
        }

        // Set timeouts, if supported
        // (OpenLDAP 2.x.x or Netscape Directory SDK x.x needed)
        $this->timeout = $timeout;
        if ($timeout > 0) {
            if (!@ldap_set_option($this->ldap, LDAP_OPT_NETWORK_TIMEOUT, $timeout)) {
                Logger::warning(
                    'Library - LDAP __construct(): Unable to set timeouts (LDAP_OPT_NETWORK_TIMEOUT) to '.$timeout
                );
            }
            if (!@ldap_set_option($this->ldap, LDAP_OPT_TIMELIMIT, $timeout)) {
                Logger::warning(
                    'Library - LDAP __construct(): Unable to set timeouts (LDAP_OPT_TIMELIMIT) to '.$timeout
                );
            }
        }

        // Enable TLS, if needed
        if (stripos($hostname, "ldaps:") === false && $enable_tls) {
            if (!@ldap_start_tls($this->ldap)) {
                throw $this->makeException('Library - LDAP __construct():'.
                    ' Unable to force TLS', ERR_INTERNAL);
            }
        }
    }


    /**
     * Convenience method to create an LDAPException as well as log the
     * description.
     *
     * @param string $description The exception's description
     * @param int|null $type The exception's type
     * @return \Exception
     */
    private function makeException($description, $type = null)
    {
        $errNo = @ldap_errno($this->ldap);

        // Decide exception type and return
        if ($type !== null) {
            if ($errNo !== 0) {
                // Only log real LDAP errors; not success
                Logger::error($description.'; cause: \''.ldap_error($this->ldap).'\' (0x'.dechex($errNo).')');
            } else {
                Logger::error($description);
            }

            switch ($type) {
                case ERR_INTERNAL:// 1 - ExInternal
                    return new Error\Exception($description, $errNo);
                case ERR_NO_USER:// 2 - ExUserNotFound
                    return new Error\UserNotFound($description, $errNo);
                case ERR_WRONG_PW:// 3 - ExInvalidCredential
                    return new Error\InvalidCredential($description, $errNo);
                case ERR_AS_DATA_INCONSIST:// 4 - ExAsDataInconsist
                    return new Error\AuthSource('ldap', $description);
                case ERR_AS_INTERNAL:// 5 - ExAsInternal
                    return new Error\AuthSource('ldap', $description);
            }
        } else {
            if ($errNo !== 0) {
                $description .= '; cause: \''.ldap_error($this->ldap).'\' (0x'.dechex($errNo).')';
                if (@ldap_get_option($this->ldap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extendedError)
                    && !empty($extendedError)
                ) {
                    $description .= '; additional: \''.$extendedError.'\'';
                }
            }
            switch ($errNo) {
                case 0x20://LDAP_NO_SUCH_OBJECT
                    Logger::warning($description);
                    return new Error\UserNotFound($description, $errNo);
                case 0x31://LDAP_INVALID_CREDENTIALS
                    Logger::info($description);
                    return new Error\InvalidCredential($description, $errNo);
                case -1://NO_SERVER_CONNECTION
                    Logger::error($description);
                    return new Error\AuthSource('ldap', $description);
                default:
                    Logger::error($description);
                    return new Error\AuthSource('ldap', $description);
            }
        }
        return new \Exception('Unknown LDAP error.');
    }


    /**
     * Search for DN from a single base.
     *
     * @param string $base
     * Indication of root of subtree to search
     * @param string|array $attribute
     * The attribute name(s) to search for.
     * @param string $value
     * The attribute value to search for.
     * Additional search filter
     * @param string|null $searchFilter
     * The scope of the search
     * @param string $scope
     * @return string
     * The DN of the resulting found element.
     * @throws Error\Exception if:
     * - Attribute parameter is wrong type
     * @throws Error\AuthSource if:
     * - Not able to connect to LDAP server
     * - False search result
     * - Count return false
     * - Searche found more than one result
     * - Failed to get first entry from result
     * - Failed to get DN for entry
     * @throws Error\UserNotFound if:
     * - Zero entries were found
     * @psalm-suppress TypeDoesNotContainType
     */
    private function search($base, $attribute, $value, $searchFilter = null, $scope = "subtree")
    {
        // Create the search filter
        /** @var array $attribute */
        $attribute = self::escape_filter_value($attribute, false);

        /** @var string $value */
        $value = self::escape_filter_value($value, true);

        $filter = '';
        foreach ($attribute as $attr) {
            $filter .= '('.$attr.'='.$value.')';
        }
        $filter = '(|'.$filter.')';

        // Append LDAP filters if defined
        if ($searchFilter !== null) {
            $filter = "(&".$filter."".$searchFilter.")";
        }

        // Search using generated filter
        Logger::debug('Library - LDAP search(): Searching base ('.$scope.') \''.$base.'\' for \''.$filter.'\'');
        if ($scope === 'base') {
            $result = @ldap_read($this->ldap, $base, $filter, [], 0, 0, $this->timeout, LDAP_DEREF_NEVER);
        } elseif ($scope === 'onelevel') {
            $result = @ldap_list($this->ldap, $base, $filter, [], 0, 0, $this->timeout, LDAP_DEREF_NEVER);
        } else {
            $result = @ldap_search($this->ldap, $base, $filter, [], 0, 0, $this->timeout, LDAP_DEREF_NEVER);
        }

        if ($result === false) {
            throw $this->makeException(
                'Library - LDAP search(): Failed search on base \''.$base.'\' for \''.$filter.'\''
            );
        }

        // Sanity checks on search results
        $count = @ldap_count_entries($this->ldap, $result);
        if ($count === false) {
            throw $this->makeException('Library - LDAP search(): Failed to get number of entries returned');
        } elseif ($count > 1) {
            // More than one entry is found. External error
            throw $this->makeException(
                'Library - LDAP search(): Found '.$count.' entries searching base \''.$base.'\' for \''.$filter.'\'',
                ERR_AS_DATA_INCONSIST
            );
        } elseif ($count === 0) {
            // No entry is fond => wrong username is given (or not registered in the catalogue). User error
            throw $this->makeException(
                'Library - LDAP search(): Found no entries searching base \''.$base.'\' for \''.$filter.'\'',
                ERR_NO_USER
            );
        }


        // Resolve the DN from the search result
        $entry = @ldap_first_entry($this->ldap, $result);
        if ($entry === false) {
            throw $this->makeException(
                'Library - LDAP search(): Unable to retrieve result after searching base \''.
                    $base.'\' for \''.$filter.'\''
            );
        }
        $dn = @ldap_get_dn($this->ldap, $entry);
        if ($dn === false) {
            throw $this->makeException(
                'Library - LDAP search(): Unable to get DN after searching base \''.$base.'\' for \''.$filter.'\''
            );
        }
        return $dn;
    }


    /**
     * Search for a DN.
     *
     * @param string|array $base
     * The base, or bases, which to search from.
     * @param string|array $attribute
     * The attribute name(s) searched for.
     * @param string $value
     * The attribute value searched for.
     * @param bool $allowZeroHits
     * Determines if the method will throw an exception if no hits are found.
     * Defaults to FALSE.
     * @param string|null $searchFilter
     * Additional searchFilter to be added to the (attribute=value) filter
     * @param string $scope
     * The scope of the search
     * @return string|null
     * The DN of the matching element, if found. If no element was found and
     * $allowZeroHits is set to FALSE, an exception will be thrown; otherwise
     * NULL will be returned.
     * @throws Error\AuthSource if:
     * - LDAP search encounter some problems when searching cataloge
     * - Not able to connect to LDAP server
     * @throws Error\UserNotFound if:
     * - $allowZeroHits is FALSE and no result is found
     *
     */
    public function searchfordn(
        $base,
        $attribute,
        $value,
        $allowZeroHits = false,
        $searchFilter = null,
        $scope = 'subtree'
    ) {
        // Traverse all search bases, returning DN if found
        $bases = \SimpleSAML\Utils\Arrays::arrayize($base);
        foreach ($bases as $current) {
            try {
                // Single base search
                $result = $this->search($current, $attribute, $value, $searchFilter, $scope);

                // We don't hawe to look any futher if user is found
                if (!empty($result)) {
                    return $result;
                }
                // If search failed, attempt the other base DNs
            } catch (Error\UserNotFound $e) {
                // Just continue searching
            }
        }
        // Decide what to do for zero entries
        Logger::debug('Library - LDAP searchfordn(): No entries found');
        if ($allowZeroHits) {
            // Zero hits allowed
            return null;
        } else {
            // Zero hits not allowed
            throw $this->makeException('Library - LDAP searchfordn(): LDAP search returned zero entries for'.
                ' filter \'('.join(' | ', \SimpleSAML\Utils\Arrays::arrayize($attribute)).' = '.$value.')\' on base(s) \'('.join(' & ', $bases).')\'', 2);
        }
    }


    /**
     * This method was created specifically for the ldap:AttributeAddUsersGroups->searchActiveDirectory()
     * method, but could be used for other LDAP search needs. It will search LDAP and return all the entries.
     *
     * @throws \Exception
     * @param string|array $bases
     * @param string|array $filters Array of 'attribute' => 'values' to be combined into the filter,
     *     or a raw filter string
     * @param string|array $attributes Array of attributes requested from LDAP
     * @param array $binaryAttributes Array of attributes that need to be base64 encoded
     * @param bool $and If multiple filters defined, then either bind them with & or |
     * @param bool $escape Weather to escape the filter values or not
     * @param string $scope The scope of the search
     * @return array
     */
    public function searchformultiple(
        $bases,
        $filters,
        $attributes = [],
        $binaryAttributes = [],
        $and = true,
        $escape = true,
        $scope = 'subtree'
    ) {
        // Escape the filter values, if requested
        if ($escape) {
            $filters = $this->escape_filter_value($filters, false);
        }

        // Build search filter
        $filter = '';
        if (is_array($filters)) {
            foreach ($filters as $attribute => $value) {
                $filter .= "($attribute=$value)";
            }
            if (count($filters) > 1) {
                $filter = ($and ? '(&' : '(|').$filter.')';
            }
        } else {
            $filter = $filters;
        }

        // Verify filter was created
        if ($filter == '' || $filter == '(=)') {
            throw $this->makeException('ldap:LdapConnection->search_manual : No search filters defined', ERR_INTERNAL);
        }

        // Verify at least one base was passed
        $bases = (array) $bases;
        if (empty($bases)) {
            throw $this->makeException('ldap:LdapConnection->search_manual : No base DNs were passed', ERR_INTERNAL);
        }

        $attributes = \SimpleSAML\Utils\Arrays::arrayize($attributes);

        // Search each base until result is found
        $result = false;
        foreach ($bases as $base) {
            if ($scope === 'base') {
                $result = @ldap_read($this->ldap, $base, $filter, $attributes, 0, 0, $this->timeout);
            } elseif ($scope === 'onelevel') {
                $result = @ldap_list($this->ldap, $base, $filter, $attributes, 0, 0, $this->timeout);
            } else {
                $result = @ldap_search($this->ldap, $base, $filter, $attributes, 0, 0, $this->timeout);
            }

            if ($result !== false && @ldap_count_entries($this->ldap, $result) > 0) {
                break;
            }
        }

        // Verify that a result was found in one of the bases
        if ($result === false) {
            throw $this->makeException(
                'ldap:LdapConnection->search_manual : Failed to search LDAP using base(s) ['.
                implode('; ', $bases).'] with filter ['.$filter.']. LDAP error ['.
                ldap_error($this->ldap).']'
            );
        } elseif (@ldap_count_entries($this->ldap, $result) < 1) {
            throw $this->makeException(
                'ldap:LdapConnection->search_manual : No entries found in LDAP using base(s) ['.
                implode('; ', $bases).'] with filter ['.$filter.']',
                ERR_NO_USER
            );
        }

        // Get all results
        $results = ldap_get_entries($this->ldap, $result);
        if ($results === false) {
            throw $this->makeException(
                'ldap:LdapConnection->search_manual : Unable to retrieve entries from search results'
            );
        }

        // parse each entry and process its attributes
        for ($i = 0; $i < $results['count']; $i++) {
            $entry = $results[$i];

            // iterate over the attributes of the entry
            for ($j = 0; $j < $entry['count']; $j++) {
                $name = $entry[$j];
                $attribute = $entry[$name];

                // decide whether to base64 encode or not
                for ($k = 0; $k < $attribute['count']; $k++) {
                    // base64 encode binary attributes
                    if (in_array($name, $binaryAttributes, true)) {
                        $results[$i][$name][$k] = base64_encode($attribute[$k]);
                    }
                }
            }
        }

        // Remove the count and return
        unset($results['count']);
        return $results;
    }


    /**
     * Bind to LDAP with a specific DN and password. Simple wrapper around
     * ldap_bind() with some additional logging.
     *
     * @param string $dn
     * The DN used.
     * @param string $password
     * The password used.
     * @param array $sasl_args
     * Array of SASL options for SASL bind
     * @return bool
     * Returns TRUE if successful, FALSE if
     * LDAP_INVALID_CREDENTIALS, LDAP_X_PROXY_AUTHZ_FAILURE,
     * LDAP_INAPPROPRIATE_AUTH, LDAP_INSUFFICIENT_ACCESS
     * @throws Error\Exception on other errors
     */
    public function bind($dn, $password, array $sasl_args = null)
    {
        if ($sasl_args != null) {
            if (!function_exists('ldap_sasl_bind')) {
                $ex_msg = 'Library - missing SASL support';
                throw $this->makeException($ex_msg);
            }

            // SASL Bind, with error handling
            $authz_id = $sasl_args['authz_id'];
            $error = @ldap_sasl_bind(
                $this->ldap,
                $dn,
                $password,
                $sasl_args['mech'],
                $sasl_args['realm'],
                $sasl_args['authc_id'],
                $sasl_args['authz_id'],
                $sasl_args['props']
            );
        } else {
            // Simple Bind, with error handling
            $authz_id = $dn;
            $error = @ldap_bind($this->ldap, $dn, $password);
        }

        if ($error === true) {
            // Good
            $this->authz_id = $authz_id;
            Logger::debug('Library - LDAP bind(): Bind successful with DN \''.$dn.'\'');
            return true;
        }

        /* Handle errors
         * LDAP_INVALID_CREDENTIALS
         * LDAP_INSUFFICIENT_ACCESS */
        switch (ldap_errno($this->ldap)) {
            case 32: // LDAP_NO_SUCH_OBJECT
                // no break
            case 47: // LDAP_X_PROXY_AUTHZ_FAILURE
                // no break
            case 48: // LDAP_INAPPROPRIATE_AUTH
                // no break
            case 49: // LDAP_INVALID_CREDENTIALS
                // no break
            case 50: // LDAP_INSUFFICIENT_ACCESS
                return false;
            default:
                break;
        }

        // Bad
        throw $this->makeException('Library - LDAP bind(): Bind failed with DN \''.$dn.'\'');
    }


    /**
     * Applies an LDAP option to the current connection.
     *
     * @throws Exception
     * @param mixed $option
     * @param mixed $value
     * @return void
     */
    public function setOption($option, $value)
    {
        // Attempt to set the LDAP option
        if (!@ldap_set_option($this->ldap, $option, $value)) {
            throw $this->makeException(
                'ldap:LdapConnection->setOption : Failed to set LDAP option ['.
                $option.'] with the value ['.$value.'] error: '.ldap_error($this->ldap),
                ERR_INTERNAL
            );
        }

        // Log debug message
        Logger::debug(
            'ldap:LdapConnection->setOption : Set the LDAP option ['.
            $option.'] with the value ['.$value.']'
        );
    }


    /**
     * Search a given DN for attributes, and return the resulting associative
     * array.
     *
     * @param string $dn
     * The DN of an element.
     * @param string|array $attributes
     * The names of the attribute(s) to retrieve. Defaults to NULL; that is,
     * all available attributes. Note that this is not very effective.
     * @param array $binaryAttributes
     * The names of the attribute(s) to base64 encode
     * @param int $maxsize
     * The maximum size of any attribute's value(s). If exceeded, the attribute
     * will not be returned.
     * @return array
     * The array of attributes and their values.
     * @see http://no.php.net/manual/en/function.ldap-read.php
     */
    public function getAttributes($dn, $attributes = null, $binaryAttributes = [], $maxsize = null)
    {
        // Preparations, including a pretty debug message...
        $description = 'all attributes';
        if (is_array($attributes)) {
            $description = '\''.join(',', $attributes).'\'';
        } else {
            // Get all attributes...
            // TODO: Verify that this originally was the intended behaviour. Could $attributes be a string?
            $attributes = [];
        }
        Logger::debug('Library - LDAP getAttributes(): Getting '.$description.' from DN \''.$dn.'\'');

        // Attempt to get attributes
        // TODO: Should aliases be dereferenced?
        /** @var array $attributes */
        $result = @ldap_read($this->ldap, $dn, 'objectClass=*', $attributes, 0, 0, $this->timeout);
        if ($result === false) {
            throw $this->makeException('Library - LDAP getAttributes(): Failed to get attributes from DN \''.$dn.'\'');
        }
        $entry = @ldap_first_entry($this->ldap, $result);
        if ($entry === false) {
            throw $this->makeException('Library - LDAP getAttributes(): Could not get first entry from DN \''.$dn.'\'');
        }
        unset($attributes);

        /** @var array|false $attributes */
        $attributes = @ldap_get_attributes($this->ldap, $entry);
        if ($attributes === false) {
            throw $this->makeException(
                'Library - LDAP getAttributes(): Could not get attributes of first entry from DN \''.$dn.'\''
            );
        }

        // Parsing each found attribute into our result set
        $result = []; // Recycling $result... Possibly bad practice.
        for ($i = 0; $i < $attributes['count']; $i++) {
            // Ignore attributes that exceed the maximum allowed size
            $name = $attributes[$i];
            $attribute = $attributes[$name];

            // Deciding whether to base64 encode
            $values = [];
            for ($j = 0; $j < $attribute['count']; $j++) {
                $value = $attribute[$j];

                if (!empty($maxsize) && strlen($value) > $maxsize) {
                    // Ignoring and warning
                    Logger::warning('Library - LDAP getAttributes(): Attribute \''.
                        $name.'\' exceeded maximum allowed size by '.(strlen($value) - $maxsize));
                    continue;
                }

                // Base64 encode binary attributes
                if (in_array($name, $binaryAttributes)) {
                    $values[] = base64_encode($value);
                } else {
                    $values[] = $value;
                }
            }

            // Adding
            $result[$name] = $values;
        }

        // We're done
        Logger::debug('Library - LDAP getAttributes(): Found attributes \'('.join(',', array_keys($result)).')\'');
        return $result;
    }


    /**
     * Enter description here...
     *
     * @param array $config
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function validate($config, $username, $password = null)
    {
        /**
         * Escape any characters with a special meaning in LDAP. The following
         * characters have a special meaning (according to RFC 2253):
         * ',', '+', '"', '\', '<', '>', ';', '*'
         * These characters are escaped by prefixing them with '\'.
         */
        $username = addcslashes($username, ',+"\\<>;*');

        if (isset($config['priv_user_dn'])) {
            $this->bind($config['priv_user_dn'], $config['priv_user_pw']);
        }
        if (isset($config['dnpattern'])) {
            $dn = str_replace('%username%', $username, $config['dnpattern']);
        } else {
            /** @var string $dn */
            $dn = $this->searchfordn($config['searchbase'], $config['searchattributes'], $username, false);
        }

        if ($password !== null) {
            // checking users credentials ... assuming below that she may read her own attributes ...
            // escape characters with a special meaning, also in the password
            $password = addcslashes($password, ',+"\\<>;*');
            if (!$this->bind($dn, $password)) {
                Logger::info(
                    'Library - LDAP validate(): Failed to authenticate \''.$username.'\' using DN \''.$dn.'\''
                );
                return false;
            }
        }

        /**
         * Retrieve attributes from LDAP
         */
        $attributes = $this->getAttributes($dn, $config['attributes'], $config['attributes.binary']);
        return $attributes;
    }


    /**
     * Borrowed function from PEAR:LDAP.
     *
     * Escapes the given VALUES according to RFC 2254 so that they can be safely used in LDAP filters.
     *
     * Any control characters with an ACII code < 32 as well as the characters with special meaning in
     * LDAP filters "*", "(", ")", and "\" (the backslash) are converted into the representation of a
     * backslash followed by two hex digits representing the hexadecimal value of the character.
     *
     * @static
     * @param string|array $values Array of values to escape
     * @param bool $singleValue
     * @return string|array Array $values, but escaped
     */
    public static function escape_filter_value($values = [], $singleValue = true)
    {
        // Parameter validation
        $values = \SimpleSAML\Utils\Arrays::arrayize($values);

        foreach ($values as $key => $val) {
            if ($val === null) {
                $val = '\0'; // apply escaped "null" if string is empty
            } else {
                // Escaping of filter meta characters
                $val = str_replace('\\', '\5c', $val);
                $val = str_replace('*', '\2a', $val);
                $val = str_replace('(', '\28', $val);
                $val = str_replace(')', '\29', $val);

                // ASCII < 32 escaping
                $val = self::asc2hex32($val);
            }

            $values[$key] = $val;
        }
        if ($singleValue) {
            return $values[0];
        }
        return $values;
    }


    /**
     * Borrowed function from PEAR:LDAP.
     *
     * Converts all ASCII chars < 32 to "\HEX"
     *
     * @param string $string String to convert
     *
     * @static
     * @return string
     */
    public static function asc2hex32($string)
    {
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            if (ord($char) < 32) {
                $hex = dechex(ord($char));
                if (strlen($hex) == 1) {
                    $hex = '0'.$hex;
                }
                $string = str_replace($char, '\\'.$hex, $string);
            }
        }
        return $string;
    }

    /**
     * Convert SASL authz_id into a DN
     *
     * @param string $searchBase
     * @param array $searchAttributes
     * @param string $authz_id
     * @return string|null
     */
    private function authzidToDn($searchBase, $searchAttributes, $authz_id)
    {
        if (preg_match("/^dn:/", $authz_id)) {
            return preg_replace("/^dn:/", "", $authz_id);
        }

        if (preg_match("/^u:/", $authz_id)) {
            return $this->searchfordn(
                $searchBase,
                $searchAttributes,
                preg_replace("/^u:/", "", $authz_id)
            );
        }
        return $authz_id;
    }

    /**
     * ldap_exop_whoami accessor, if available. Use requested authz_id
     * otherwise.
     *
     * ldap_exop_whoami() has been provided as a third party patch that
     * waited several years to get its way upstream:
     * http://cvsweb.netbsd.org/bsdweb.cgi/pkgsrc/databases/php-ldap/files
     *
     * When it was integrated into PHP repository, the function prototype
     * was changed, The new prototype was used in third party patch for
     * PHP 7.0 and 7.1, hence the version test below.
     *
     * @param string $searchBase
     * @param array $searchAttributes
     * @throws \Exception
     * @return string
     */
    public function whoami($searchBase, $searchAttributes)
    {
        $authz_id = '';
        if (function_exists('ldap_exop_whoami')) {
            if (version_compare(phpversion(), '7', '<')) {
                /** @psalm-suppress TooManyArguments */
                if (ldap_exop_whoami($this->ldap, $authz_id) === false) {
                    throw $this->makeException('LDAP whoami exop failure');
                }
            } else {
                /** @var string|false $authz_id */
                $authz_id = ldap_exop_whoami($this->ldap);
                if ($authz_id === false) {
                    throw $this->makeException('LDAP whoami exop failure');
                }
            }
        } else {
            $authz_id = $this->authz_id;
        }

        $dn = $this->authzidToDn($searchBase, $searchAttributes, $authz_id);

        if (empty($dn)) {
            throw $this->makeException('Cannot figure userID');
        }

        return $dn;
    }
}

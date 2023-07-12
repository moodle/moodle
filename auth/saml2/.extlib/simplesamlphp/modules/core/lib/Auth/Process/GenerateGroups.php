<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Auth\Process;

use SimpleSAML\Logger;

/**
 * Filter to generate a groups attribute based on many of the attributes of the user.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */
class GenerateGroups extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * The attributes we should generate groups from.
     * @var array
     */
    private $generateGroupsFrom;


    /**
     * Initialize this filter.
     *
     * @param array &$config  Configuration information about this filter.
     * @param mixed $reserved  For future use.
     */
    public function __construct(&$config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert(is_array($config));

        if (count($config) === 0) {
            // Use default groups
            $this->generateGroupsFrom = [
                'eduPersonAffiliation',
                'eduPersonOrgUnitDN',
                'eduPersonEntitlement',
            ];
        } else {
            // Validate configuration
            foreach ($config as $attributeName) {
                if (!is_string($attributeName)) {
                    throw new \Exception('Invalid attribute name for core:GenerateGroups filter: ' .
                        var_export($attributeName, true));
                }
            }
            $this->generateGroupsFrom = $config;
        }
    }


    /**
     * Apply filter to add groups attribute.
     *
     * @param array &$request  The current request
     * @return void
     */
    public function process(&$request)
    {
        assert(is_array($request));
        assert(array_key_exists('Attributes', $request));

        $groups = [];
        $attributes = &$request['Attributes'];

        $realm = self::getRealm($attributes);
        if ($realm !== null) {
            $groups[] = 'realm-' . $realm;
        }

        foreach ($this->generateGroupsFrom as $name) {
            if (!array_key_exists($name, $attributes)) {
                Logger::debug('GenerateGroups - attribute \'' . $name . '\' not found.');
                // Attribute not present
                continue;
            }

            foreach ($attributes[$name] as $value) {
                $value = self::escapeIllegalChars($value);
                $groups[] = $name . '-' . $value;
                if ($realm !== null) {
                    $groups[] = $name . '-' . $realm . '-' . $value;
                }
            }
        }

        if (count($groups) > 0) {
            $attributes['groups'] = $groups;
        }
    }


    /**
     * Determine which realm the user belongs to.
     *
     * This function will attempt to determine the realm a user belongs to based on the
     * eduPersonPrincipalName attribute if it is present. If it isn't, or if it doesn't contain
     * a realm, NULL will be returned.
     *
     * @param array $attributes  The attributes of the user.
     * @return string|null  The realm of the user, or NULL if we are unable to determine the realm.
     */
    private static function getRealm(array $attributes): ?string
    {
        if (!array_key_exists('eduPersonPrincipalName', $attributes)) {
            return null;
        }
        $eppn = $attributes['eduPersonPrincipalName'];

        if (count($eppn) < 1) {
            return null;
        }
        $eppn = $eppn[0];

        $realm = explode('@', $eppn, 2);
        if (count($realm) < 2) {
            return null;
        }
        $realm = $realm[1];

        return self::escapeIllegalChars($realm);
    }


    /**
     * Escape special characters in a string.
     *
     * This function is similar to urlencode, but encodes many more characters.
     * This function takes any characters not in [a-zA-Z0-9_@=.] and encodes them with as
     * %<hex version>. For example, it will encode '+' as '%2b' and '%' as '%25'.
     *
     * @param string $string  The string which should be escaped.
     * @return string  The escaped string.
     */
    private static function escapeIllegalChars(string $string): string
    {
        return preg_replace_callback(
            '/([^a-zA-Z0-9_@=.])/',
            /**
             * @param array $m
             * @return string
             */
            function ($m) {
                return sprintf("%%%02x", ord($m[1]));
            },
            $string
        );
    }
}

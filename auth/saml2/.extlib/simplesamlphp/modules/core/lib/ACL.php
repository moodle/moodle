<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core;

use SimpleSAML\Configuration;
use SimpleSAML\Error;

/**
 * Generic library for access control lists.
 *
 * @package SimpleSAMLphp
 */

class ACL
{
    /**
     * The access control list, as an array.
     *
     * @var array
     */
    private $acl;

    /**
     * Initializer for this access control list.
     *
     * @param array|string $acl  The access control list.
     */
    public function __construct($acl)
    {
        assert(is_string($acl) || is_array($acl));

        if (is_string($acl)) {
            $acl = self::getById($acl);
        }

        foreach ($acl as $rule) {
            if (!is_array($rule)) {
                throw new Error\Exception('Invalid rule in access control list: ' . var_export($rule, true));
            }
            if (count($rule) === 0) {
                throw new Error\Exception('Empty rule in access control list.');
            }

            $action = array_shift($rule);
            if ($action !== 'allow' && $action !== 'deny') {
                throw new Error\Exception(
                    'Invalid action in rule in access control list: ' . var_export($action, true)
                );
            }
        }
        $this->acl = $acl;
    }

    /**
     * Retrieve an access control list with the given id.
     *
     * @param string $id  The id of the access control list.
     * @return array  The access control list array.
     */
    private static function getById(string $id): array
    {
        $config = Configuration::getOptionalConfig('acl.php');
        if (!$config->hasValue($id)) {
            throw new Error\Exception('No ACL with id ' . var_export($id, true) . ' in config/acl.php.');
        }

        return $config->getArray($id);
    }

    /**
     * Match the attributes against the access control list.
     *
     * @param array $attributes  The attributes of an user.
     * @return boolean  TRUE if the user is allowed to access the resource, FALSE if not.
     */
    public function allows(array $attributes)
    {
        foreach ($this->acl as $rule) {
            $action = array_shift($rule);

            if (!self::match($attributes, $rule)) {
                continue;
            }

            if ($action === 'allow') {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Match the attributes against the given rule.
     *
     * @param array $attributes  The attributes of an user.
     * @param array $rule  The rule we should check.
     * @return boolean  TRUE if the rule matches, FALSE if not.
     */
    private static function match(array $attributes, array $rule): bool
    {
        $op = array_shift($rule);
        if ($op === null) {
            // An empty rule always matches
            return true;
        }

        switch ($op) {
            case 'and':
                return self::opAnd($attributes, $rule);
            case 'equals':
                return self::opEquals($attributes, $rule);
            case 'equals-preg':
                return self::opEqualsPreg($attributes, $rule);
            case 'has':
                return self::opHas($attributes, $rule);
            case 'has-preg':
                return self::opHasPreg($attributes, $rule);
            case 'not':
                return !self::match($attributes, $rule);
            case 'or':
                return self::opOr($attributes, $rule);
            default:
                throw new Error\Exception('Invalid ACL operation: ' . var_export($op, true));
        }
    }

    /**
     * 'and' match operator.
     *
     * @param array $attributes  The attributes of an user.
     * @param array $rule  The rule we should check.
     * @return boolean  TRUE if the rule matches, FALSE if not.
     */
    private static function opAnd(array $attributes, array $rule): bool
    {
        foreach ($rule as $subRule) {
            if (!self::match($attributes, $subRule)) {
                return false;
            }
        }

        // All matches
        return true;
    }

    /**
     * 'equals' match operator.
     *
     * @param array $attributes  The attributes of an user.
     * @param array $rule  The rule we should check.
     * @return boolean  TRUE if the rule matches, FALSE if not.
     */
    private static function opEquals(array $attributes, array $rule): bool
    {
        $attributeName = array_shift($rule);

        if (!array_key_exists($attributeName, $attributes)) {
            $attributeValues = [];
        } else {
            $attributeValues = $attributes[$attributeName];
        }

        foreach ($rule as $value) {
            $found = false;
            foreach ($attributeValues as $i => $v) {
                if ($value !== $v) {
                    continue;
                }
                unset($attributeValues[$i]);
                $found = true;
                break;
            }
            if (!$found) {
                return false;
            }
        }
        if (!empty($attributeValues)) {
            // One of the attribute values didn't match
            return false;
        }

        // All the values in the attribute matched one in the rule
        return true;
    }

    /**
     * 'equals-preg' match operator.
     *
     * @param array $attributes  The attributes of an user.
     * @param array $rule  The rule we should check.
     * @return boolean  TRUE if the rule matches, FALSE if not.
     */
    private static function opEqualsPreg(array $attributes, array $rule): bool
    {
        $attributeName = array_shift($rule);

        if (!array_key_exists($attributeName, $attributes)) {
            $attributeValues = [];
        } else {
            $attributeValues = $attributes[$attributeName];
        }

        foreach ($rule as $pattern) {
            $found = false;
            foreach ($attributeValues as $i => $v) {
                if (!preg_match($pattern, $v)) {
                    continue;
                }
                unset($attributeValues[$i]);
                $found = true;
                break;
            }
            if (!$found) {
                return false;
            }
        }

        if (!empty($attributeValues)) {
            // One of the attribute values didn't match
            return false;
        }

        // All the values in the attribute matched one in the rule
        return true;
    }

    /**
     * 'has' match operator.
     *
     * @param array $attributes  The attributes of an user.
     * @param array $rule  The rule we should check.
     * @return boolean  TRUE if the rule matches, FALSE if not.
     */
    private static function opHas(array $attributes, array $rule): bool
    {
        $attributeName = array_shift($rule);

        if (!array_key_exists($attributeName, $attributes)) {
            $attributeValues = [];
        } else {
            $attributeValues = $attributes[$attributeName];
        }

        foreach ($rule as $value) {
            if (!in_array($value, $attributeValues, true)) {
                return false;
            }
        }

        // Found all values in the rule in the attribute
        return true;
    }

    /**
     * 'has-preg' match operator.
     *
     * @param array $attributes  The attributes of an user.
     * @param array $rule  The rule we should check.
     * @return boolean  TRUE if the rule matches, FALSE if not.
     */
    private static function opHasPreg(array $attributes, array $rule): bool
    {
        $attributeName = array_shift($rule);

        if (!array_key_exists($attributeName, $attributes)) {
            $attributeValues = [];
        } else {
            $attributeValues = $attributes[$attributeName];
        }

        foreach ($rule as $pattern) {
            $matches = preg_grep($pattern, $attributeValues);
            if (count($matches) === 0) {
                return false;
            }
        }

        // Found all values in the rule in the attribute
        return true;
    }

    /**
     * 'or' match operator.
     *
     * @param array $attributes  The attributes of an user.
     * @param array $rule  The rule we should check.
     * @return boolean  TRUE if the rule matches, FALSE if not.
     */
    private static function opOr(array $attributes, array $rule): bool
    {
        foreach ($rule as $subRule) {
            if (self::match($attributes, $subRule)) {
                return true;
            }
        }

        // None matches
        return false;
    }
}

<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Ldap_Attribute is a collection of LDAP attribute related functions.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Ldap_Attribute
{
    const PASSWORD_HASH_MD5   = 'md5';
    const PASSWORD_HASH_SMD5  = 'smd5';
    const PASSWORD_HASH_SHA   = 'sha';
    const PASSWORD_HASH_SSHA  = 'ssha';
    const PASSWORD_UNICODEPWD = 'unicodePwd';

    /**
     * Sets a LDAP attribute.
     *
     * @param  array                    $data
     * @param  string                   $attribName
     * @param  scalar|array|Traversable $value
     * @param  boolean                  $append
     * @return void
     */
    public static function setAttribute(array &$data, $attribName, $value, $append = false)
    {
        $attribName = strtolower($attribName);
        $valArray = array();
        if (is_array($value) || ($value instanceof Traversable))
        {
            foreach ($value as $v)
            {
                $v = self::_valueToLdap($v);
                if (!is_null($v)) $valArray[] = $v;
            }
        }
        else if (!is_null($value))
        {
            $value = self::_valueToLdap($value);
            if (!is_null($value)) $valArray[] = $value;
        }

        if ($append === true && isset($data[$attribName]))
        {
            if (is_string($data[$attribName])) $data[$attribName] = array($data[$attribName]);
            $data[$attribName] = array_merge($data[$attribName], $valArray);
        }
        else
        {
            $data[$attribName] = $valArray;
        }
    }

    /**
     * Gets a LDAP attribute.
     *
     * @param  array   $data
     * @param  string  $attribName
     * @param  integer $index
     * @return array|mixed
     */
    public static function getAttribute(array $data, $attribName, $index = null)
    {
        $attribName = strtolower($attribName);
        if (is_null($index)) {
            if (!isset($data[$attribName])) return array();
            $retArray = array();
            foreach ($data[$attribName] as $v)
            {
                $retArray[] = self::_valueFromLdap($v);
            }
            return $retArray;
        } else if (is_int($index)) {
            if (!isset($data[$attribName])) {
                return null;
            } else if ($index >= 0 && $index<count($data[$attribName])) {
                return self::_valueFromLdap($data[$attribName][$index]);
            } else {
                return null;
            }
        }
        return null;
    }

    /**
     * Checks if the given value(s) exist in the attribute
     *
     * @param array       $data
     * @param string      $attribName
     * @param mixed|array $value
     * @return boolean
     */
    public static function attributeHasValue(array &$data, $attribName, $value)
    {
        $attribName = strtolower($attribName);
        if (!isset($data[$attribName])) return false;

        if (is_scalar($value)) {
            $value = array($value);
        }

        foreach ($value as $v) {
            $v = self::_valueToLdap($v);
            if (!in_array($v, $data[$attribName], true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Removes duplicate values from a LDAP attribute
     *
     * @param array  $data
     * @param string $attribName
     * @return void
     */
    public static function removeDuplicatesFromAttribute(array &$data, $attribName)
    {
        $attribName = strtolower($attribName);
        if (!isset($data[$attribName])) return;
        $data[$attribName] = array_values(array_unique($data[$attribName]));
    }

    /**
     * Remove given values from a LDAP attribute
     *
     * @param array       $data
     * @param string      $attribName
     * @param mixed|array $value
     * @return void
     */
    public static function removeFromAttribute(array &$data, $attribName, $value)
    {
        $attribName = strtolower($attribName);
        if (!isset($data[$attribName])) return;

        if (is_scalar($value)) {
            $value = array($value);
        }

        $valArray = array();
        foreach ($value as $v)
        {
            $v = self::_valueToLdap($v);
            if ($v !== null) $valArray[] = $v;
        }

        $resultArray = $data[$attribName];
        foreach ($valArray as $rv) {
            $keys = array_keys($resultArray, $rv);
            foreach ($keys as $k) {
                unset($resultArray[$k]);
            }
        }
        $resultArray = array_values($resultArray);
        $data[$attribName] = $resultArray;
    }

    /**
     * @param  mixed $value
     * @return string|null
     */
    private static function _valueToLdap($value)
    {
        if (is_string($value)) return $value;
        else if (is_int($value) || is_float($value)) return (string)$value;
        else if (is_bool($value)) return ($value === true) ? 'TRUE' : 'FALSE';
        else if (is_object($value) || is_array($value)) return serialize($value);
        else if (is_resource($value) && get_resource_type($value) === 'stream')
            return stream_get_contents($value);
        else return null;
    }

    /**
     * @param  string $value
     * @return string|boolean
     */
    private static function _valueFromLdap($value)
    {
        $value = (string)$value;
        if ($value === 'TRUE') return true;
        else if ($value === 'FALSE') return false;
        else return $value;
    }

    /**
     * Converts a PHP data type into its LDAP representation
     *
     * @param  mixed $value
     * @return string|null - null if the PHP data type cannot be converted.
     */
    public static function convertToLdapValue($value)
    {
        return self::_valueToLdap($value);
    }

    /**
     * Converts an LDAP value into its PHP data type
     *
     * @param  string $value
     * @return mixed
     */
    public static function convertFromLdapValue($value)
    {
        return self::_valueFromLdap($value);
    }

    /**
     * Converts a timestamp into its LDAP date/time representation
     *
     * @param  integer $value
     * @param  boolean $utc
     * @return string|null - null if the value cannot be converted.
     */
    public static function convertToLdapDateTimeValue($value, $utc = false)
    {
        return self::_valueToLdapDateTime($value, $utc);
    }

    /**
     * Converts LDAP date/time representation into a timestamp
     *
     * @param  string $value
     * @return integer|null - null if the value cannot be converted.
     */
    public static function convertFromLdapDateTimeValue($value)
    {
        return self::_valueFromLdapDateTime($value);
    }

    /**
     * Sets a LDAP password.
     *
     * @param  array       $data
     * @param  string      $password
     * @param  string      $hashType
     * @param  string|null $attribName
     * @return null
     */
    public static function setPassword(array &$data, $password, $hashType = self::PASSWORD_HASH_MD5,
        $attribName = null)
    {
        if ($attribName === null) {
            if ($hashType === self::PASSWORD_UNICODEPWD) {
                $attribName = 'unicodePwd';
            } else {
                $attribName = 'userPassword';
            }
        }

        $hash = self::createPassword($password, $hashType);
        self::setAttribute($data, $attribName, $hash, false);
    }

    /**
     * Creates a LDAP password.
     *
     * @param  string $password
     * @param  string $hashType
     * @return string
     */
    public static function createPassword($password, $hashType = self::PASSWORD_HASH_MD5)
    {
        switch ($hashType) {
            case self::PASSWORD_UNICODEPWD:
                /* see:
                 * http://msdn.microsoft.com/en-us/library/cc223248(PROT.10).aspx
                 */
                $password = '"' . $password . '"';
                if (function_exists('mb_convert_encoding')) {
                    $password = mb_convert_encoding($password, 'UTF-16LE', 'UTF-8');
                } else if (function_exists('iconv')) {
                    $password = iconv('UTF-8', 'UTF-16LE', $password);
                } else {
                    $len = strlen($password);
                    $new = '';
                    for($i=0; $i < $len; $i++) {
                        $new .= $password[$i] . "\x00";
                    }
                    $password = $new;
                }
                return $password;
            case self::PASSWORD_HASH_SSHA:
                $salt    = substr(sha1(uniqid(mt_rand(), true), true), 0, 4);
                $rawHash = sha1($password . $salt, true) . $salt;
                $method  = '{SSHA}';
                break;
            case self::PASSWORD_HASH_SHA:
                $rawHash = sha1($password, true);
                $method  = '{SHA}';
                break;
            case self::PASSWORD_HASH_SMD5:
                $salt    = substr(sha1(uniqid(mt_rand(), true), true), 0, 4);
                $rawHash = md5($password . $salt, true) . $salt;
                $method  = '{SMD5}';
                break;
            case self::PASSWORD_HASH_MD5:
            default:
                $rawHash = md5($password, true);
                $method  = '{MD5}';
                break;
        }
        return $method . base64_encode($rawHash);
    }

    /**
     * Sets a LDAP date/time attribute.
     *
     * @param  array                     $data
     * @param  string                    $attribName
     * @param  integer|array|Traversable $value
     * @param  boolean                   $utc
     * @param  boolean                   $append
     * @return null
     */
    public static function setDateTimeAttribute(array &$data, $attribName, $value, $utc = false,
        $append = false)
    {
        $convertedValues = array();
        if (is_array($value) || ($value instanceof Traversable))
        {
            foreach ($value as $v) {
                $v = self::_valueToLdapDateTime($v, $utc);
                if (!is_null($v)) $convertedValues[] = $v;
            }
        }
        else if (!is_null($value)) {
            $value = self::_valueToLdapDateTime($value, $utc);
            if (!is_null($value)) $convertedValues[] = $value;
        }
        self::setAttribute($data, $attribName, $convertedValues, $append);
    }

    /**
     * @param  integer $value
     * @param  boolean $utc
     * @return string|null
     */
    private static function _valueToLdapDateTime($value, $utc)
    {
        if (is_int($value)) {
            if ($utc === true) return gmdate('YmdHis', $value) . 'Z';
            else return date('YmdHisO', $value);
        }
        else return null;
    }

    /**
     * Gets a LDAP date/time attribute.
     *
     * @param  array   $data
     * @param  string  $attribName
     * @param  integer $index
     * @return array|integer
     */
    public static function getDateTimeAttribute(array $data, $attribName, $index = null)
    {
        $values = self::getAttribute($data, $attribName, $index);
        if (is_array($values)) {
            for ($i = 0; $i<count($values); $i++) {
                $newVal = self::_valueFromLdapDateTime($values[$i]);
                if (!is_null($newVal)) $values[$i] = $newVal;
            }
        }
        else $values = self::_valueFromLdapDateTime($values);
        return $values;
    }

    /**
     * @param  string $value
     * @return integer|null
     */
    private static function _valueFromLdapDateTime($value)
    {
        $matches = array();
        if (preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})([+-]\d{4}|Z)$/', $value, $matches)) {
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];
            $hour = $matches[4];
            $minute = $matches[5];
            $second = $matches[6];
            $timezone = $matches[7];
            $date = gmmktime($hour, $minute, $second, $month, $day, $year);
            if ($timezone !== 'Z') {
                $tzDirection = substr($timezone, 0, 1);
                $tzOffsetHour = substr($timezone, 1, 2);
                $tzOffsetMinute = substr($timezone, 3, 2);
                $tzOffset = ($tzOffsetHour*60*60) + ($tzOffsetMinute*60);
                if ($tzDirection == '+') $date -= $tzOffset;
                else if ($tzDirection == '-') $date += $tzOffset;
            }
            return $date;
        }
        else return null;
    }
}
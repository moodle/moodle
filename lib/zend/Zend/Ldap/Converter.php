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
 * Zend_Ldap_Converter is a collection of useful LDAP related conversion functions.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Ldap_Converter
{
    /**
     * Converts all ASCII chars < 32 to "\HEX"
     *
     * @see Net_LDAP2_Util::asc2hex32() from Benedikt Hallinger <beni@php.net>
     * @link http://pear.php.net/package/Net_LDAP2
     * @author Benedikt Hallinger <beni@php.net>
     *
     * @param  string $string String to convert
     * @return string
     */
    public static function ascToHex32($string)
    {
        for ($i = 0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            if (ord($char)<32) {
                $hex = dechex(ord($char));
                if (strlen($hex) == 1) $hex = '0' . $hex;
                $string = str_replace($char, '\\' . $hex, $string);
            }
        }
        return $string;
    }

    /**
     * Converts all Hex expressions ("\HEX") to their original ASCII characters
     *
     * @see Net_LDAP2_Util::hex2asc() from Benedikt Hallinger <beni@php.net>,
     * heavily based on work from DavidSmith@byu.net
     * @link http://pear.php.net/package/Net_LDAP2
     * @author Benedikt Hallinger <beni@php.net>, heavily based on work from DavidSmith@byu.net
     *
     * @param  string $string String to convert
     * @return string
     */
    public static function hex32ToAsc($string)
    {
        $string = preg_replace("/\\\([0-9A-Fa-f]{2})/e", "''.chr(hexdec('\\1')).''", $string);
        return $string;
    }
}
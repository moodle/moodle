<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * utility functions
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    HTML
 * @package     HTML_QuickForm
 * @author      Chuck Burgess <ashnazg@php.net>
 * @copyright   2001-2018 The PHP Group
 * @license     http://www.php.net/license/3_01.txt PHP License 3.01
 * @version     CVS: $Id$
 * @link        http://pear.php.net/package/HTML_QuickForm
 */

/**
 * Provides a collection of static methods for array manipulation.
 *
 * (courtesy of CiviCRM project (https://civicrm.org/)
 *
 * @category    HTML
 * @package     HTML_QuickForm
 * @author      Chuck Burgess <ashnazg@php.net>
 * @version     Release: @package_version@
 * @since       3.2
 */
class HTML_QuickForm_utils
{
    /**
     * Get a single value from an array-tree.
     *
     * @param   array     $values   Ex: ['foo' => ['bar' => 123]].
     * @param   array     $path     Ex: ['foo', 'bar'].
     * @param   mixed     $default
     * @return  mixed               Ex 123.
     *
     * @access  public
     * @static
     */
    static function pathGet($values, $path, $default = NULL) {
        foreach ($path as $key) {
            if (!is_array($values) || !isset($values[$key])) {
                return $default;
            }
            $values = $values[$key];
        }
        return $values;
    }

    /**
     * Check if a key isset which may be several layers deep.
     *
     * This is a helper for when the calling function does not know how many layers deep
     * the path array is so cannot easily check.
     *
     * @param   array $values
     * @param   array $path
     * @return  bool
     *
     * @access  public
     * @static
     */
    static function pathIsset($values, $path) {
        foreach ($path as $key) {
            if (!is_array($values) || !isset($values[$key])) {
                return FALSE;
            }
            $values = $values[$key];
        }
        return TRUE;
    }

    /**
     * Set a single value in an array tree.
     *
     * @param   array   $values     Ex: ['foo' => ['bar' => 123]].
     * @param   array   $pathParts  Ex: ['foo', 'bar'].
     * @param   mixed   $value      Ex: 456.
     * @return  void
     *
     * @access  public
     * @static
     */
    static function pathSet(&$values, $pathParts, $value) {
        $r = &$values;
        $last = array_pop($pathParts);
        foreach ($pathParts as $part) {
            if (!isset($r[$part])) {
                $r[$part] = array();
            }
            $r = &$r[$part];
        }
        $r[$last] = $value;
    }

    /**
     * Check if a key isset which may be several layers deep.
     *
     * This is a helper for when the calling function does not know how many layers deep the
     * path array is so cannot easily check.
     *
     * @param   array $array
     * @param   array $path
     * @return  bool
     *
     * @access  public
     * @static
     */
    static function recursiveIsset($array, $path) {
        return self::pathIsset($array, $path);
    }

    /**
     * Check if a key isset which may be several layers deep.
     *
     * This is a helper for when the calling function does not know how many layers deep the
     * path array is so cannot easily check.
     *
     * @param   array   $array
     * @param   array   $path       An array of keys,
     *                              e.g [0, 'bob', 8] where we want to check if $array[0]['bob'][8]
     * @param   mixed   $default    Value to return if not found.
     * @return  bool
     *
     * @access  public
     * @static
     */
    static function recursiveValue($array, $path, $default = NULL) {
        return self::pathGet($array, $path, $default);
    }

    /**
     * Append the value to the array using the key provided.
     *
     * e.g if value is 'llama' & path is [0, 'email', 'location'] result will be
     * [0 => ['email' => ['location' => 'llama']]
     *
     * @param           $path
     * @param           $value
     * @param   array   $source
     * @return  array
     *
     * @access  public
     * @static
     */
    static function recursiveBuild($path, $value, $source = array()) {
        self::pathSet($source, $path, $value);
        return $source;
    }
}
?>

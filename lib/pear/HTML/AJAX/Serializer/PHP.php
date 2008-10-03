<?php
// $Id$
/**
 * PHP Serializer
 *
 * @category   HTML
 * @package    AJAX
 * @author     Arpad Ray <arpad@php.net>
 * @copyright  2005 Arpad Ray
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: 0.5.6
 * @link       http://pear.php.net/package/HTML_AJAX
 */
class HTML_AJAX_Serializer_PHP 
{    
    function serialize($input) 
    {
        return serialize($input);
    }

    /**
     * Unserializes the given string
     *
     * Triggers an error if a class is found which is not
     * in the provided array of allowed class names.
     *
     * @param   string  $input
     *  the serialized string to process
     * @param   array   $allowedClasses
     *  an array of class names to check objects against
     *  before instantion
     * @return  mixed
     *  the unserialized variable on success, or false on
     *  failure. If this method fails it will also trigger
     *  a warning.
     */
    function unserialize($input, $allowedClasses) 
    {
        if (version_compare(PHP_VERSION, '4.3.10', '<')
             || (substr(PHP_VERSION, 0, 1) == '5' && version_compare(PHP_VERSION, '5.0.3', '<'))) {
            trigger_error('Unsafe version of PHP for native unserialization');
            return false;
        }
        $classes = $this->_getSerializedClassNames($input);
        if ($classes === false) {
            trigger_error('Invalidly serialized string');
            return false;
        }
        $diff = array_diff($classes, $allowedClasses);
        if (!empty($diff)) {
            trigger_error('Class(es) not allowed to be serialized');
            return false;
        }
        return unserialize($input);
    }
    
    /**
     * Extract class names from serialized string
     *
     * Adapted from code by Harry Fuecks
     *
     * @param   string  $string
     *  the serialized string to process
     * @return  mixed
     *  an array of class names found, or false if the input
     *  is invalidly formed
     */
    function _getSerializedClassNames($string) {
        // Strip any string representations (which might contain object syntax)
        while (($pos = strpos($string, 's:')) !== false) {
            $pos2 = strpos($string, ':', $pos + 2);
            if ($pos2 === false) {
                // invalidly serialized string
                return false;    
            }
            $end = $pos + 2 + substr($string, $pos + 2, $pos2) + 1;
            $string = substr($string, 0, $pos) . substr($string, $end);
        }
        
        // Pull out the class names
        preg_match_all('/O:[0-9]+:"(.*)"/U', $string, $matches);
        
        // Make sure names are unique (same object serialized twice)
        return array_unique($matches[1]);
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>

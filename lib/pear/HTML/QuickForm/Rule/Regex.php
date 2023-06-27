<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Bertrand Mansion <bmansion@mamasam.com>                     |
// +----------------------------------------------------------------------+
//
// $Id$

require_once('HTML/QuickForm/Rule.php');

/**
* Validates values using regular expressions
* @version     1.0
*/
class HTML_QuickForm_Rule_Regex extends HTML_QuickForm_Rule
{
    /**
     * Array of regular expressions
     *
     * Array is in the format:
     * $_data['rulename'] = 'pattern';
     *
     * @var     array
     * @access  private
     */
    var $_data = array(
                    'lettersonly'   => '/^[a-zA-Z]+$/',
                    'alphanumeric'  => '/^[a-zA-Z0-9]+$/',
                    'numeric'       => '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/',
                    'nopunctuation' => '/^[^().\/\*\^\?#!@$%+=,\"\'><~\[\]{}]+$/',
                    'nonzero'       => '/^-?[1-9][0-9]*/',
                    'positiveint'   => '/^[1-9]\d*$/'
                    );

    /**
     * Validates a value using a regular expression
     *
     * @param     string    $value      Value to be checked
     * @param     string    $regex      Regular expression
     * @access    public
     * @return    boolean   true if value is valid
     */
    function validate($value, $regex = null)
    {
        if (isset($this->_data[$this->name])) {
            if (!preg_match($this->_data[$this->name], $value)) {
                return false;
            }
        } else {
            if (!preg_match($regex, $value)) {
                return false;
            }
        }
        return true;
    } // end func validate

    /**
     * Adds new regular expressions to the list
     *
     * @param     string    $name       Name of rule
     * @param     string    $pattern    Regular expression pattern
     * @access    public
     */
    function addData($name, $pattern)
    {
        $this->_data[$name] = $pattern;
    } // end func addData


    function getValidationScript($options = null)
    {
        $regex = isset($this->_data[$this->name]) ? $this->_data[$this->name] : $options;

        return array("  var regex = " . $regex . ";\n", "{jsVar} != '' && !regex.test({jsVar})");
    } // end func getValidationScript

} // end class HTML_QuickForm_Rule_Regex
?>
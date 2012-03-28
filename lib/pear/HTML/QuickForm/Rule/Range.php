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
* Validates values using range comparison
* @version     1.0
*/
class HTML_QuickForm_Rule_Range extends HTML_QuickForm_Rule
{
    /**
     * Validates a value using a range comparison
     *
     * @param     string    $value      Value to be checked
     * @param     mixed     $options    Int for length, array for range
     * @access    public
     * @return    boolean   true if value is valid
     */
    function validate($value, $options = null)
    {
        $length = strlen($value);
        switch ($this->name) {
            case 'minlength': return ($length >= $options);
            case 'maxlength': return ($length <= $options);
            default:          return ($length >= $options[0] && $length <= $options[1]);
        }
    } // end func validate


    function getValidationScript($options = null)
    {
        switch ($this->name) {
            case 'minlength':
                $test = '{jsVar}.length < '.$options;
                break;
            case 'maxlength':
                $test = '{jsVar}.length > '.$options;
                break;
            default:
                $test = '({jsVar}.length < '.$options[0].' || {jsVar}.length > '.$options[1].')';
        }
        return array('', "{jsVar} != '' && {$test}");
    } // end func getValidationScript

} // end class HTML_QuickForm_Rule_Range
?>
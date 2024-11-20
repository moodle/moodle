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

class HTML_QuickForm_Rule
{
   /**
    * Name of the rule to use in validate method
    *
    * This property is used in more global rules like Callback and Regex
    * to determine which callback and which regex is to be used for validation
    *
    * @var  string
    * @access   public
    */
    var $name;

   /**
    * Validates a value
    *
    * @access public
    * @abstract
    */
    function validate($value, $options = null)
    {
        return true;
    }

   /**
    * Sets the rule name
    *
    * @access public
    */
    function setName($ruleName)
    {
        $this->name = $ruleName;
    }

    /**
     * Returns the javascript test (the test should return true if the value is INVALID)
     *
     * @param     mixed     Options for the rule
     * @access    public
     * @return    array     first element is code to setup validation, second is the check itself
     */
    function getValidationScript($options = null)
    {
        return array('', '');
    }
}
?>
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997, 1998, 1999, 2000, 2001 The PHP Group             |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Adam Daniel <adaniel1@eesus.jnj.com>                        |
// |          Bertrand Mansion <bmansion@mamasam.com>                     |
// +----------------------------------------------------------------------+

require_once("HTML/QuickForm/button.php");

/**
 * HTML class for a button type element
 *
 * @author       Adam Daniel <adaniel1@eesus.jnj.com>
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @version      1.1
 * @since        PHP4.04pl1
 * @access       public
 */
class MoodleQuickForm_button extends HTML_QuickForm_button
{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    function MoodleQuickForm_button($elementName=null, $value=null, $attributes=null) {
        parent::HTML_QuickForm_button($elementName, $value, $attributes);
    }
    /**
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }
    /**
     * get html for help button
     *
     * @access   public
     * @return  string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }
    /**
     * Slightly different container template when frozen.
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'nodisplay';
        } else {
            return 'default';
        }
    }
} //end class MoodleQuickForm_button

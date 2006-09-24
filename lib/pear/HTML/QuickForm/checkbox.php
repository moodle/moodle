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
//
// $Id$

require_once("HTML/QuickForm/input.php");

/**
 * HTML class for a checkbox type field
 * 
 * @author       Adam Daniel <adaniel1@eesus.jnj.com>
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @version      1.1
 * @since        PHP4.04pl1
 * @access       public
 */
class HTML_QuickForm_checkbox extends HTML_QuickForm_input
{
    // {{{ properties

    /**
     * Checkbox display text
     * @var       string
     * @since     1.1
     * @access    private
     */
    var $_text = '';

    // }}}
    // {{{ constructor

    /**
     * Class constructor
     * 
     * @param     string    $elementName    (optional)Input field name attribute
     * @param     string    $elementLabel   (optional)Input field value
     * @param     string    $text           (optional)Checkbox display text
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string 
     *                                      or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    function HTML_QuickForm_checkbox($elementName=null, $elementLabel=null, $text='', $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_text = $text;
        $this->setType('checkbox');
        $this->updateAttributes(array('value'=>1));
        $this->_generateId();
    } //end constructor
    
    // }}}
    // {{{ setChecked()

    /**
     * Sets whether a checkbox is checked
     * 
     * @param     bool    $checked  Whether the field is checked or not
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setChecked($checked)
    {
        if (!$checked) {
            $this->removeAttribute('checked');
        } else {
            $this->updateAttributes(array('checked'=>'checked'));
        }
    } //end func setChecked

    // }}}
    // {{{ getChecked()

    /**
     * Returns whether a checkbox is checked
     * 
     * @since     1.0
     * @access    public
     * @return    bool
     */
    function getChecked()
    {
        return (bool)$this->getAttribute('checked');
    } //end func getChecked
    
    // }}}
    // {{{ toHtml()

    /**
     * Returns the checkbox element in HTML
     * 
     * @since     1.0
     * @access    public
     * @return    string
     */
    function toHtml()
    {
        if (0 == strlen($this->_text)) {
            $label = '';
        } elseif ($this->_flagFrozen) {
            $label = $this->_text;
        } else {
            $label = '<label for="' . $this->getAttribute('id') . '">' . $this->_text . '</label>';
        }
        return HTML_QuickForm_input::toHtml() . $label;
    } //end func toHtml
    
    // }}}
    // {{{ getFrozenHtml()

    /**
     * Returns the value of field without HTML tags
     * 
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getFrozenHtml()
    {
        if ($this->getChecked()) {
            return '<tt>[x]</tt>' .
                   $this->_getPersistantData();
        } else {
            return '<tt>[ ]</tt>';
        }
    } //end func getFrozenHtml

    // }}}
    // {{{ setText()

    /**
     * Sets the checkbox text
     * 
     * @param     string    $text  
     * @since     1.1
     * @access    public
     * @return    void
     */
    function setText($text)
    {
        $this->_text = $text;
    } //end func setText

    // }}}
    // {{{ getText()

    /**
     * Returns the checkbox text 
     * 
     * @since     1.1
     * @access    public
     * @return    string
     */
    function getText()
    {
        return $this->_text;
    } //end func getText

    // }}}
    // {{{ setValue()

    /**
     * Sets the value of the form element
     *
     * @param     string    $value      Default value of the form element
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setValue($value)
    {
        return $this->setChecked($value);
    } // end func setValue

    // }}}
    // {{{ getValue()

    /**
     * Returns the value of the form element
     *
     * @since     1.0
     * @access    public
     * @return    bool
     */
    function getValue()
    {
        return $this->getChecked();
    } // end func getValue

    // }}}
    // {{{ onQuickFormEvent()

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param     string    $event  Name of event
     * @param     mixed     $arg    event arguments
     * @param     object    $caller calling object
     * @since     1.0
     * @access    public
     * @return    void
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'updateValue':
                // constant values override both default and submitted ones
                // default values are overriden by submitted
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    // if no boxes were checked, then there is no value in the array
                    // yet we don't want to display default value in this case
                    if ($caller->isSubmitted()) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                if (null !== $value) {
                    $this->setChecked($value);
                }
                break;
            case 'setGroupValue':
                $this->setChecked($arg);
                break;
            default:
                parent::onQuickFormEvent($event, $arg, $caller);
        }
        return true;
    } // end func onQuickFormEvent

    // }}}
    // {{{ exportValue()

   /**
    * Return true if the checkbox is checked, null if it is not checked (getValue() returns false)
    */
    function exportValue(&$submitValues, $assoc = false)
    {
        $value = $this->_findValue($submitValues);
        if (null === $value) {
            $value = $this->getChecked()? true: null;
        }
        return $this->_prepareValue($value, $assoc);
    }
    
    // }}}
} //end class HTML_QuickForm_checkbox
?>

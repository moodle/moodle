<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
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
// | Authors: Alexey Borzov <avb@php.net>                                 |
// +----------------------------------------------------------------------+
//
// $Id$

require_once 'HTML/QuickForm/element.php';

/**
 * Class for HTML 4.0 <button> element
 * 
 * @author  Alexey Borzov <avb@php.net>
 * @since   3.2.3
 * @access  public
 */
class HTML_QuickForm_xbutton extends HTML_QuickForm_element
{
   /**
    * Contents of the <button> tag
    * @var      string
    * @access   private
    */
    var $_content; 

   /**
    * Class constructor
    * 
    * @param    string  Button name
    * @param    string  Button content (HTML to add between <button></button> tags)
    * @param    mixed   Either a typical HTML attribute string or an associative array
    * @access   public
    */
    function HTML_QuickForm_xbutton($elementName = null, $elementContent = null, $attributes = null)
    {
        $this->HTML_QuickForm_element($elementName, null, $attributes);
        $this->setContent($elementContent);
        $this->setPersistantFreeze(false);
        $this->_type = 'xbutton';
    }


    function toHtml()
    {
        return '<button' . $this->getAttributes(true) . '>' . $this->_content . '</button>';
    }


    function getFrozenHtml()
    {
        return $this->toHtml();
    }


    function freeze()
    {
        return false;
    }


    function setName($name)
    {
        $this->updateAttributes(array(
            'name' => $name 
        ));
    }


    function getName()
    {
        return $this->getAttribute('name');
    }


    function setValue($value)
    {
        $this->updateAttributes(array(
            'value' => $value
        ));
    }


    function getValue()
    {
        return $this->getAttribute('value');
    }


   /**
    * Sets the contents of the button element
    *
    * @param    string  Button content (HTML to add between <button></button> tags)
    */
    function setContent($content)
    {
        $this->_content = $content;
    }


    function onQuickFormEvent($event, $arg, &$caller)
    {
        if ('updateValue' != $event) {
            return parent::onQuickFormEvent($event, $arg, $caller);
        } else {
            $value = $this->_findValue($caller->_constantValues);
            if (null === $value) {
                $value = $this->_findValue($caller->_defaultValues);
            }
            if (null !== $value) {
                $this->setValue($value);
            }
        }
        return true;
    }


   /**
    * Returns a 'safe' element's value
    * 
    * The value is only returned if the button's type is "submit" and if this
    * particlular button was clicked
    */
    function exportValue(&$submitValues, $assoc = false)
    {
        if ('submit' == $this->getAttribute('type')) {
            return $this->_prepareValue($this->_findValue($submitValues), $assoc);
        } else {
            return null;
        }
    }
}
?>

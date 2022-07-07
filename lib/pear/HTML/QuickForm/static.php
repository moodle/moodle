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
// | Authors: Adam Daniel <adaniel1@eesus.jnj.com>                        |
// |          Bertrand Mansion <bmansion@mamasam.com>                     |
// +----------------------------------------------------------------------+
//
// $Id$

require_once("HTML/QuickForm/element.php");

/**
 * HTML class for static data
 * 
 * @author       Wojciech Gdela <eltehaem@poczta.onet.pl>
 * @access       public
 */
class HTML_QuickForm_static extends HTML_QuickForm_element {
    
    // {{{ properties

    /**
     * Display text
     * @var       string
     * @access    private
     */
    var $_text = null;

    // }}}
    // {{{ constructor
    
    /**
     * Class constructor
     * 
     * @param     string    $elementLabel   (optional)Label
     * @param     string    $text           (optional)Display text
     * @access    public
     * @return    void
     */
    public function __construct($elementName=null, $elementLabel=null, $text=null) {
        parent::__construct($elementName, $elementLabel);
        $this->_persistantFreeze = false;
        $this->_type = 'static';
        $this->_text = $text;
    } //end constructor

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function HTML_QuickForm_static($elementName=null, $elementLabel=null, $text=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $text);
    }

    // }}}
    // {{{ setName()

    /**
     * Sets the element name
     * 
     * @param     string    $name   Element name
     * @access    public
     * @return    void
     */
    function setName($name)
    {
        $this->updateAttributes(array('name'=>$name));
    } //end func setName
    
    // }}}
    // {{{ getName()

    /**
     * Returns the element name
     * 
     * @access    public
     * @return    string
     */
    function getName()
    {
        return $this->getAttribute('name');
    } //end func getName

    // }}}
    // {{{ setText()

    /**
     * Sets the text
     *
     * @param     string    $text
     * @access    public
     * @return    void
     */
    function setText($text)
    {
        $this->_text = $text;
    } // end func setText

    // }}}
    // {{{ setValue()

    /**
     * Sets the text (uses the standard setValue call to emulate a form element.
     *
     * @param     string    $text
     * @access    public
     * @return    void
     */
    function setValue($text)
    {
        $this->setText($text);
    } // end func setValue

    // }}}    
    // {{{ toHtml()

    /**
     * Returns the static text element in HTML
     * 
     * @access    public
     * @return    string
     */
    function toHtml()
    {
        return $this->_getTabs() . $this->_text;
    } //end func toHtml
    
    // }}}
    // {{{ getFrozenHtml()

    /**
     * Returns the value of field without HTML tags
     * 
     * @access    public
     * @return    string
     */
    function getFrozenHtml()
    {
        return $this->toHtml();
    } //end func getFrozenHtml

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
     * @throws    
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'updateValue':
                // do NOT use submitted values for static elements
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    $value = $this->_findValue($caller->_defaultValues);
                }
                if (null !== $value) {
                    $this->setValue($value);
                }
                break;
            default:
                parent::onQuickFormEvent($event, $arg, $caller);
        }
        return true;
    } // end func onQuickFormEvent

    // }}}
    // {{{ exportValue()

   /**
    * We override this here because we don't want any values from static elements
    */
    function exportValue(&$submitValues, $assoc = false)
    {
        return null;
    }
    
    // }}}
} //end class HTML_QuickForm_static
?>

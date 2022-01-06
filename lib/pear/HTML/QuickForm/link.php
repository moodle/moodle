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

require_once 'HTML/QuickForm/static.php';

/**
 * HTML class for a link type field
 * 
 * @author       Adam Daniel <adaniel1@eesus.jnj.com>
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */
class HTML_QuickForm_link extends HTML_QuickForm_static
{
    // {{{ properties

    /**
     * Link display text
     * @var       string
     * @since     1.0
     * @access    private
     */
    var $_text = "";

    // }}}
    // {{{ constructor
    
    /**
     * Class constructor
     * 
     * @param     string    $elementLabel   (optional)Link label
     * @param     string    $href           (optional)Link href
     * @param     string    $text           (optional)Link display text
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string 
     *                                      or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     * @throws    
     */
    public function __construct($elementName=null, $elementLabel=null, $href=null, $text=null, $attributes=null) {
        // TODO MDL-52313 Replace with the call to parent::__construct().
        HTML_QuickForm_element::__construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = false;
        $this->_type = 'link';
        $this->setHref($href);
        $this->_text = $text;
    } //end constructor

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function HTML_QuickForm_link($elementName=null, $elementLabel=null, $href=null, $text=null, $attributes=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $href, $text, $attributes);
    }

    // }}}
    // {{{ setName()

    /**
     * Sets the input field name
     * 
     * @param     string    $name   Input field name attribute
     * @since     1.0
     * @access    public
     * @return    void
     * @throws    
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
     * @since     1.0
     * @access    public
     * @return    string
     * @throws    
     */
    function getName()
    {
        return $this->getAttribute('name');
    } //end func getName

    // }}}
    // {{{ setValue()

    /**
     * Sets value for textarea element
     * 
     * @param     string    $value  Value for password element
     * @since     1.0
     * @access    public
     * @return    void
     * @throws    
     */
    function setValue($value)
    {
        return;
    } //end func setValue
    
    // }}}
    // {{{ getValue()

    /**
     * Returns the value of the form element
     *
     * @since     1.0
     * @access    public
     * @return    void
     * @throws    
     */
    function getValue()
    {
        return;
    } // end func getValue

    
    // }}}
    // {{{ setHref()

    /**
     * Sets the links href
     *
     * @param     string    $href
     * @since     1.0
     * @access    public
     * @return    void
     * @throws    
     */
    function setHref($href)
    {
        $this->updateAttributes(array('href'=>$href));
    } // end func setHref

    // }}}
    // {{{ toHtml()

    /**
     * Returns the textarea element in HTML
     * 
     * @since     1.0
     * @access    public
     * @return    string
     * @throws    
     */
    function toHtml()
    {
        $tabs = $this->_getTabs();
        $html = "$tabs<a".$this->_getAttrString($this->_attributes).">";
        $html .= $this->_text;
        $html .= "</a>";
        return $html;
    } //end func toHtml
    
    // }}}
    // {{{ getFrozenHtml()

    /**
     * Returns the value of field without HTML tags (in this case, value is changed to a mask)
     * 
     * @since     1.0
     * @access    public
     * @return    string
     * @throws    
     */
    function getFrozenHtml()
    {
        return;
    } //end func getFrozenHtml

    // }}}

} //end class HTML_QuickForm_textarea
?>

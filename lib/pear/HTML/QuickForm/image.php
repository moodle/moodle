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
 * HTML class for a image type element
 * 
 * @author       Adam Daniel <adaniel1@eesus.jnj.com>
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */
class HTML_QuickForm_image extends HTML_QuickForm_input
{
    // {{{ constructor

    /**
     * Class constructor
     * 
     * @param     string    $elementName    (optional)Element name attribute
     * @param     string    $src            (optional)Image source
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string 
     *                                      or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    public function __construct($elementName=null, $src='', $attributes=null) {
        parent::__construct($elementName, null, $attributes);
        $this->setType('image');
        $this->setSource($src);
    } // end class constructor

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function HTML_QuickForm_image($elementName=null, $src='', $attributes=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $src, $attributes);
    }

    // }}}
    // {{{ setSource()

    /**
     * Sets source for image element
     * 
     * @param     string    $src  source for image element
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setSource($src)
    {
        $this->updateAttributes(array('src' => $src));
    } // end func setSource

    // }}}
    // {{{ setBorder()

    /**
     * Sets border size for image element
     * 
     * @param     string    $border  border for image element
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setBorder($border)
    {
        $this->updateAttributes(array('border' => $border));
    } // end func setBorder

    // }}}
    // {{{ setAlign()

    /**
     * Sets alignment for image element
     * 
     * @param     string    $align  alignment for image element
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setAlign($align)
    {
        $this->updateAttributes(array('align' => $align));
    } // end func setAlign

    // }}}
    // {{{ freeze()

    /**
     * Freeze the element so that only its value is returned
     * 
     * @access    public
     * @return    void
     */
    function freeze()
    {
        return false;
    } //end func freeze

    // }}}

} // end class HTML_QuickForm_image
?>

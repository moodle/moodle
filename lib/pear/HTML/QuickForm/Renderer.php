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
// | Author: Alexey Borzov <borz_off@cs.msu.su>                           |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * An abstract base class for QuickForm renderers
 * 
 * The class implements a Visitor design pattern
 *
 * @abstract
 * @author Alexey Borzov <borz_off@cs.msu.su>
 */
class HTML_QuickForm_Renderer
{
   /**
    * Constructor
    *
    * @access public
    */
    public function __construct() {
    } // end constructor

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function HTML_QuickForm_Renderer() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

   /**
    * Called when visiting a form, before processing any form elements
    *
    * @param    object    An HTML_QuickForm object being visited
    * @access   public
    * @return   void 
    * @abstract
    */
    function startForm(&$form)
    {
        return;
    } // end func startForm

   /**
    * Called when visiting a form, after processing all form elements
    * 
    * @param    object     An HTML_QuickForm object being visited
    * @access   public
    * @return   void 
    * @abstract
    */
    function finishForm(&$form)
    {
        return;
    } // end func finishForm

   /**
    * Called when visiting a header element
    *
    * @param    object     An HTML_QuickForm_header element being visited
    * @access   public
    * @return   void 
    * @abstract
    */
    function renderHeader(&$header)
    {
        return;
    } // end func renderHeader

   /**
    * Called when visiting an element
    *
    * @param    object     An HTML_QuickForm_element object being visited
    * @param    bool       Whether an element is required
    * @param    string     An error message associated with an element
    * @access   public
    * @return   void 
    * @abstract
    */
    function renderElement(&$element, $required, $error)
    {
        return;
    } // end func renderElement

   /**
    * Called when visiting a hidden element
    * 
    * @param    object     An HTML_QuickForm_hidden object being visited
    * @access   public
    * @return   void
    * @abstract 
    */
    function renderHidden(&$element)
    {
        return;
    } // end func renderHidden

   /**
    * Called when visiting a raw HTML/text pseudo-element
    * 
    * Seems that this should not be used when using a template-based renderer
    *
    * @param    object     An HTML_QuickForm_html element being visited
    * @access   public
    * @return   void 
    * @abstract
    */
    function renderHtml(&$data)
    {
        return;
    } // end func renderHtml

   /**
    * Called when visiting a group, before processing any group elements
    *
    * @param    object     An HTML_QuickForm_group object being visited
    * @param    bool       Whether a group is required
    * @param    string     An error message associated with a group
    * @access   public
    * @return   void 
    * @abstract
    */
    function startGroup(&$group, $required, $error)
    {
        return;
    } // end func startGroup

   /**
    * Called when visiting a group, after processing all group elements
    *
    * @param    object     An HTML_QuickForm_group object being visited
    * @access   public
    * @return   void 
    * @abstract
    */
    function finishGroup(&$group)
    {
        return;
    } // end func finishGroup
} // end class HTML_QuickForm_Renderer
?>

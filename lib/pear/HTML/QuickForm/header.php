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

require_once 'HTML/QuickForm/static.php';

/**
 * A pseudo-element used for adding headers to form
 *
 * @author Alexey Borzov <borz_off@cs.msu.su>
 * @access public
 */
class HTML_QuickForm_header extends HTML_QuickForm_static
{
    // {{{ constructor

   /**
    * Class constructor
    *
    * @param string $elementName    Header name
    * @param string $text           Header text
    * @access public
    * @return void
    */
    function HTML_QuickForm_header($elementName = null, $text = null)
    {
        $this->HTML_QuickForm_static($elementName, null, $text);
        $this->_type = 'header';
    }

    // }}}
    // {{{ accept()

   /**
    * Accepts a renderer
    *
    * @param object     An HTML_QuickForm_Renderer object
    * @access public
    * @return void
    */
    function accept(&$renderer, $required=false, $error=null)
    {
        $renderer->renderHeader($this);
    } // end func accept

    // }}}

} //end class HTML_QuickForm_header
?>

<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once("HTML/QuickForm/link.php");

/**
 * HTML class for a multiple checkboxes state controller
 *
 * @author       Nicolas Connault <nicolasconnault@gmail.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */
class MoodleQuickForm_selectallornone extends HTML_QuickForm_link {
    /**
     * The original state of the checkboxes controlled by this element. This determines whether the first click of this element will switch them all to
     * checked or to unchecked. It doesn't change the checked state of the original elements (there could be a mixed of checked/unchecked there), but
     * there has to be a decision as to which action will be taken by clicking "select all/select none" the first time.
     * @var int $originalValue
     */
    var $_originalValue = 0;

    /**
     * Constructor
     * @param string $elementName The name of the group of advcheckboxes this element controls
     * @param string $text The text of the link. Defaults to "select all/none"
     * @param array  $attributes associative array of HTML attributes
     * @param int    $originalValue The original general state of the checkboxes before the user first clicks this element
     */
    function MoodleQuickForm_selectallornone($elementName=null, $text=null, $attributes=null, $originalValue=0) {
        if (is_null($originalValue)) {
            $originalValue = 0;
        }

        global $FULLME;
        $this->_originalValue = $originalValue;

        if (is_null($elementName)) {
            return;
        }
        $elementLabel = '&nbsp;';
        $strselectallornone = get_string('selectallornone', 'form');
        $attributes['onmouseover'] = "window.status='" . $strselectallornone . "';";
        $attributes['onmouseout'] = "window.status='';";
        $attributes['onclick'] = "html_quickform_toggle_checkboxes($elementName); return false;";
        $select_value = optional_param('select'. $elementName, $originalValue, PARAM_INT);

        if ($select_value == 0) {
            $new_select_value = 1;
        } else {
            $new_select_value = 0;
        }

        $old_selectstr = "&select$elementName=$select_value";
        $new_selectstr = "&select$elementName=$new_select_value";
        $new_fullme = str_replace($old_selectstr, '', $FULLME);

        $href = "$new_fullme$new_selectstr";
        
        if (empty($text)) {
            $text = $strselectallornone;
        }
        $this->HTML_QuickForm_link($elementName, $elementLabel, $href, $text, $attributes);
    } 

    function toHtml() {
        if (is_null($this->_originalValue)) {
            return false;
        }

        $group = $this->_attributes['name'];
        if ($this->_flagFrozen) {
            $js = '';
        } else {
            $js = "<script type=\"text/javascript\">\n//<![CDATA[\n";
            if (!defined('HTML_QUICKFORM_SELECTALLORNONE_EXISTS')) {
                $js .= <<<EOS
function html_quickform_toggle_checkboxes(group) {
    var checkboxes = getElementsByClassName(document, 'input', 'checkboxgroup' + group);
    var newvalue = false;
    var global = eval('html_quickform_checkboxgroup' + group + ';');
    if (global == 1) {
        eval('html_quickform_checkboxgroup' + group + ' = 0;'); 
        newvalue = '';
    } else {
        eval('html_quickform_checkboxgroup' + group + ' = 1;'); 
        newvalue = 'checked';
    }

    for (i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = newvalue; 
    }
}
EOS;
                define('HTML_QUICKFORM_SELECTALLORNONE_EXISTS', true);
            }
            $js .= "var html_quickform_checkboxgroup$group=$this->_originalValue;";
            
            $js .= "//]]>\n</script>";
        }
        return $js . parent::toHtml(); 
    }
}
?> 

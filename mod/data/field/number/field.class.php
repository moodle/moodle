<?php // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-onwards Moodle Pty Ltd  http://moodle.com          //
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

class data_field_number extends data_field_base {
    var $type = 'number';

    function data_field_number($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }

    function update_content($recordid, $value, $name='') {
        $content = new object;
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;
        $value = trim($value);
        if (strlen($value) > 0) {
            $content->content = floatval($value);
        } else {
            $content->content = null;
        }
        if ($oldcontent = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
            $content->id = $oldcontent->id;
            return update_record('data_content', $content);
        } else {
            return insert_record('data_content', $content);
        }
    }

    function display_browse_field($recordid, $template) {
        if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)) {
            if (strlen($content->content) < 1) {
                return false;
            }
            $number = $content->content;
            $decimals = trim($this->field->param1);
            // only apply number formatting if param1 contains an integer number >= 0:
            if (preg_match("/^\d+$/", $decimals)) {
                $decimals = $decimals * 1;
                // removes leading zeros (eg. '007' -> '7'; '00' -> '0')
                $str = format_float($number, $decimals, true);
                // For debugging only:
#                $str .= " ($decimals)";
            } else {
                $str = $number;
            }
            return $str;
        }
        return false;
    }

    function display_search_field($value = '') {
        return '<input type="text" size="16" name="f_'.$this->field->id.'" value="'.$value.'" />';
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }
    
    // need to cast?
    function generate_sql($tablealias, $value) {
        return " ({$tablealias}.fieldid = {$this->field->id} AND {$tablealias}.content = '$value') ";
    }

    function get_sort_sql($fieldname) {
        global $CFG;
        switch ($CFG->dbfamily) {
            case 'mysql':
                // string in an arithmetic operation is converted to a floating-point number
                return '('.$fieldname.'+0.0)';
            case 'postgres':
                // cast for PG
                return 'CAST('.$fieldname.' AS REAL)';
            default:
                // the rest, just the field name. TODO: Analyse behaviour under MSSQL and Oracle
                return $fieldname;
        }
    }

}

?>

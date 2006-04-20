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

    function get_sort_sql($fieldname) {
        global $CFG;

        switch ($CFG->dbtype) {
            case 'mysql':      // DECIMAL would be more accurate but only MySQL 5 supports it.
                return 'CAST('.$fieldname.' AS SIGNED)';  

            default:
                return 'CAST('.$fieldname.' AS REAL)';  
        }
    }

}

?>

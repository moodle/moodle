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

class grade_lib_wrapper {
    function get_records_sql($sql, $limitfrom='', $limitnum='') {
        return get_records_sql($sql, $limitfrom, $limitnum);
    }
    
    function get_records_select($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
        return get_records_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
    }

    function get_recordset($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
        return get_recordset($table, $field, $value, $sort, $fields, $limitfrom, $limitnum); 
    }

    function get_recordset_select($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
        return get_recordset_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
    }

    function get_recordset_sql($sql, $limitfrom=null, $limitnum=null) {
        return get_recordset_sql($sql, $limitfrom, $limitnum);
    }
    
    function get_record($table, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields='*') {
        return get_record($table, $field1, $value1, $field2, $value2, $field3, $value3, $fields);
    }

    function get_records($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
        return get_records($table, $field, $value, $sort, $fields, $limitfrom, $limitnum);
    }

    function get_records_list($table, $field='', $values='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
        return get_records_list($table, $field, $values, $sort, $fields, $limitfrom, $limitnum);
    }

    function get_field($table, $return, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {
        return get_field($table, $return, $field1, $value1, $field2, $value2, $field3, $value3);
    }

    function get_field_sql($sql) {
        return get_field_sql($sql);
    }

    function get_field_select($table, $return, $select) {
        return get_field_select($table, $return, $select);
    }

    function set_field($table, $newfield, $newvalue, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {
        return set_field($table, $newfield, $newvalue, $field1, $value1, $field2, $value2, $field3, $value3);
    }

    function set_field_select($table, $newfield, $newvalue, $select, $localcall = false) {
        return set_field_select($table, $newfield, $newvalue, $select, $localcall);
    }

    function rs_fetch_next_record(&$rs) {
        return rs_fetch_next_record($rs);
    }

    function execute_sql($command, $feedback=true) {
        return execute_sql($command, $feedback);
    }
    
    function update_record($table, $dataobject) {
        return update_record($table, $dataobject);
    }
    
    function insert_record($table, $dataobject, $returnid=true, $primarykey='id') {
        return insert_record($table, $dataobject, $returnid, $primarykey);
    }
    
    function delete_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
        return delete_records($table, $field1, $value1, $field2, $value2, $field3, $value3);
    }
    
    function count_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
        return count_records($table, $field1, $value1, $field2, $value2, $field3, $value3);
    }

    function rs_close(&$rs) {
        return;
    }
    
    function get_coursemodule_from_instance($modulename, $instance, $courseid=0) {
        return get_coursemodule_from_instance($modulename, $instance, $courseid);
    }

    function course_scale_used($courseid, $scaleid) {
        return course_scale_used($courseid, $scaleid);
    }
    
    function site_scale_used($scaleid,&$courses) {
        return site_scale_used($scaleid, $courses);
    }
}

?>

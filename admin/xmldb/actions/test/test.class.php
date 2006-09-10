<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
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

/// This class will perform one full test of all the available DDL
/// functions under your DB

class test extends XMLDBAction {

    /** 
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'back' => 'xmldb'
        ));
    }

    /**
     * Invoke method, every class will have its own
     * returns true/false on completion, setting both
     * errormsg and output as necessary
     */
    function invoke() {
        parent::invoke();

        $result = true;

    /// Set own core attributes
        //$this->does_generate = ACTION_NONE;
        $this->does_generate = ACTION_GENERATE_HTML;

    /// These are always here
        global $CFG, $XMLDB, $db;

    /// ADD YOUR CODE HERE
        require_once ($CFG->libdir . '/ddllib.php');

    /// Where all the tests will be stored
        $tests = array();

    /// The back to edit table button
        $b = ' <p align="center" class="buttons">';
        $b .= '<a href="index.php">[' . $this->str['back'] . ']</a>';
        $b .= '</p>';
        $o = $b;

    /// Silenty drop any previous test tables
        $table = new XMLDBTable('testtable');
        $status = drop_table($table, true, false);
        $table = new XMLDBTable ('anothertest');
        $status = drop_table($table, true, false);

    /// 1st test. Complete table creation.
        $table = new XMLDBTable('testtable');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM, array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general');
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, '');
        $table->addFieldInfo('intro', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, '');
        $table->addFieldInfo('logo', XMLDB_TYPE_BINARY, 'big', null, XMLDB_NOTNULL, null, null, null, '');
        $table->addFieldInfo('assessed', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('assesstimestart', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('assesstimefinish', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('scale', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('maxbytes', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('forcesubscribe', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('trackingtype', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '1');
        $table->addFieldInfo('rsstype', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('rssarticles', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('grade', XMLDB_TYPE_NUMBER, '20,0', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('percent', XMLDB_TYPE_NUMBER, '5,2', null, null, null, null, null, null);
        $table->addFieldInfo('warnafter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('blockafter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('blockperiod', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('type-name', XMLDB_KEY_UNIQUE, array('type', 'name'));
        $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->addIndexInfo('rsstype', XMLDB_INDEX_UNIQUE, array('rsstype'));

    /// Get SQL code and execute it
        $test->sql = $table->getCreateTableSQL($CFG->dbtype, $CFG->prefix, false);
        $test->status = create_table($table, false, false);
        if (!$test->status) {
            $test->error = $db->ErrorMsg();
        }
        $tests['create table'] = $test;

    /// 2nd test. drop table
        if ($test->status) {
        /// Get SQL code and execute it
            $test->sql = $table->getDropTableSQL($CFG->dbtype, $CFG->prefix, false);
            $test->status = drop_table($table, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['drop table'] = $test;
        }

    /// 3rd test. creating another, smaller table
        if ($test->status) {
            $table = new XMLDBTable ('anothertest');
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('course', XMLDB_KEY_FOREIGN, array('course'), 'course', array('id'));
        /// Get SQL code and execute it
            $test->sql = $table->getCreateTableSQL($CFG->dbtype, $CFG->prefix, false);
            $test->status = create_table($table, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['create table - 2'] = $test;
        }



    /// Iterate over tests, showing information as needed
        $o .= '<ol>';
        foreach ($tests as $key => $test) {
            $o .= '<li>' . $key . ($test->status ? '<font color="green"> Ok</font>' : ' <font color="red">Error</font>');
            if (!$test->status) {
                $o .= '<pre>' . $test->sql . '</pre>';
            }
            $o .= '</li>';
        }
        $o .= '</ol>';

        $this->output = $o;

    /// Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) { 
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>

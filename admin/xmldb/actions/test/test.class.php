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
        if (table_exists($table)) {
            $status = drop_table($table, true, false);
        }
        $table = new XMLDBTable ('anothertest');
        if (table_exists($table)) {
            $status = drop_table($table, true, false);
        }

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
        $test = new stdClass;
        $test->sql = $table->getCreateTableSQL($CFG->dbtype, $CFG->prefix, true);
        $test->status = create_table($table, false, false);
        if (!$test->status) {
            $test->error = $db->ErrorMsg();
        }
        $tests['create table'] = $test;

    /// 2nd test. drop table
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $test->sql = $table->getDropTableSQL($CFG->dbtype, $CFG->prefix, true);
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
            $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, 'Moodle');
            $table->addFieldInfo('secondname', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('intro', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('grade', XMLDB_TYPE_NUMBER, '20,10', null, null, null, null, null, '');
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        /// Get SQL code and execute it
            $test = new stdClass;
            $test->sql = $table->getCreateTableSQL($CFG->dbtype, $CFG->prefix, true);
            $test->status = create_table($table, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['create table - 2'] = $test;
        }

    /// Insert two records to do the work with real data
        $rec->course = 1;
        $rec->name = 'Martin';
        $rec->secondname = 'Dougiamas';
        $rec->intro = 'The creator of Moodle';
        $rec->grade = 10.0001;
        insert_record('anothertest', $rec);
        $rec->course = 2;
        $rec->name = 'Eloy';
        $rec->secondname = 'Lafuente';
        $rec->intro = 'One poor developer';
        $rec->grade = 9.99;
        insert_record('anothertest', $rec);

    /// 4th test. Adding one complex enum field
        if ($test->status) {
        /// Create a new field with complex specs (enums are good candidates)
            $field = new XMLDBField('type');
            $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM, array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');
        /// Get SQL code and execute it
            $test = new stdClass;
            $test->sql = $table->getAddFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = add_field($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add field'] = $test;
        }

    /// 5th test. Dropping one complex enum field
        if ($test->status) {
        /// Create a new field with complex specs (enums are good candidates)
            $test = new stdClass;
            $test->sql = $table->getDropFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = drop_field($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['drop field'] = $test;
        }

    /// 6th test. Adding one complex enum field
        if ($test->status) {
        /// Create a new field with complex specs (enums are good candidates)
            $field = new XMLDBField('type');
            $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM, array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');
        /// Get SQL code and execute it
            $test = new stdClass;
            $test->sql = $table->getAddFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = add_field($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add field again'] = $test;
        }

    /// 7th test. Dropping one complex enum field
        if ($test->status) {
        /// Create a new field with complex specs (enums are good candidates)
            $test = new stdClass;
            $test->sql = $table->getDropFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = drop_field($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['drop field again'] = $test;
        }

    /// 8th test. Change the type of one column from integer to varchar
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('course');
            $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, '0');

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_type($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field type (int2char)'] = $test;
        }

    /// 9th test. Change the type of one column from varchar to integer
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('course');
            $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_type($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field type (char2int)'] = $test;
        }

    /// 10th test. Change the type of one column from number to varchar
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('grade');
            $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, 'test');

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_type($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field type (number2char)'] = $test;
        }

    /// 11th test. Change the type of one column from varchar to float
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('grade');
            $field->setAttributes(XMLDB_TYPE_FLOAT, '20,10', XMLDB_UNSIGNED, null, null, null, null, null);

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_type($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field type (char2float)'] = $test;
        }

    /// 12th test. Change the type of one column from float to char
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('grade');
            $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, 'test');

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_type($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field type (float2char)'] = $test;
        }

    /// 13th test. Change the type of one column from char to number
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('grade');
            $field->setAttributes(XMLDB_TYPE_NUMBER, '20,10', XMLDB_UNSIGNED, null, null, null, null, null);

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_type($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field type (char2number)'] = $test;
        }


    /// 14th test. Change the precision of one text field
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('intro');
            $field->setAttributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null, null, null);

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_precision($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field precision (text)'] = $test;
        }

    /// 15th test. Change the precision of one char field
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('secondname');
            $field->setAttributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, null);

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_precision($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field precision (char)'] = $test;
        }

    /// 16th test. Change the precision of one numeric field
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('grade');
            $field->setAttributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null, null, null);

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_precision($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field precision (number)'] = $test;
        }

    /// 17th test. Change the sign of one numeric field to unsigned
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('grade');
            $field->setAttributes(XMLDB_TYPE_NUMBER, '10,2', XMLDB_UNSIGNED, null, null, null, null, null);

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_unsigned($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field sign (unsigned)'] = $test;
        }

    /// 18th test. Change the sign of one numeric field to signed
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('grade');
            $field->setAttributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null, null, null);

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_unsigned($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field sign (signed)'] = $test;
        }

    /// 19th test. Change the nullability of one char field to not null
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('name');
            $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, 'Moodle');

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_notnull($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field nullability (not null)'] = $test;
        }

    /// 20th test. Change the nullability of one char field to null
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('name');
            $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, 'Moodle');

            $test->sql = $table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_notnull($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['change field nullability (null)'] = $test;
        }

    /// 21th test. Dropping the default of one field
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('name');
            $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, null);

            $test->sql = $table->getModifyDefaultSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_default($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['drop field default of NULL field'] = $test;
        }

    /// 22th test. Creating the default for one field
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('name');
            $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, 'Moodle');

            $test->sql = $table->getModifyDefaultSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_default($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add field default of NULL field'] = $test;
        }

    /// 23th test. Creating the default for one field
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('secondname');
            $field->setAttributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, 'Moodle2');

            $test->sql = $table->getModifyDefaultSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_default($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add field default of NOT NULL field'] = $test;
        }


    /// 24th test. Dropping the default of one NOT NULL field
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('secondname');
            $field->setAttributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, null);

            $test->sql = $table->getModifyDefaultSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_default($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['drop field default of NOT NULL field'] = $test;
        }

    /// 25th test. Adding one unique index to the table
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $index = new XMLDBIndex('secondname');
            $index->setAttributes(XMLDB_INDEX_UNIQUE, array('name', 'secondname', 'grade'));

            $test->sql = $table->getAddIndexSQL($CFG->dbtype, $CFG->prefix, $index, true);
            $test->status = add_index($table, $index, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add unique index'] = $test;
        }

    /// 26th test. Adding one not unique index to the table
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $index = new XMLDBIndex('secondname');
            $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));

            $test->sql = $table->getAddIndexSQL($CFG->dbtype, $CFG->prefix, $index, true);
            $test->status = add_index($table, $index, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add not unique index'] = $test;
        }

    /// 27th test. Re-add the same index than previous test. Check find_index_name() works.
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $index = new XMLDBIndex('secondname');
            $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('name', 'course'));

            if ($indexfound = find_index_name($table, $index)) {
                $test->status = true;
                $test->sql = array();
            } else {
                $test->status = true;
                $test->error = 'Index not found!';
                $test->sql = array();
            }

            $tests['check find_index_name()'] = $test;
        }

    /// 28th test. Dropping one index from the table
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $index = new XMLDBIndex('name');
            $index->setAttributes(XMLDB_INDEX_UNIQUE, array('name', 'grade', 'secondname'));

            $test->sql = $table->getDropIndexSQL($CFG->dbtype, $CFG->prefix, $index, true);
            $test->status = drop_index($table, $index, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['drop index'] = $test;
        }

    /// 29th test. Adding one unique key to the table
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $key = new XMLDBKey('id-course-grade');
            $key->setAttributes(XMLDB_KEY_UNIQUE, array('id', 'course', 'grade'));

            $test->sql = $table->getAddKeySQL($CFG->dbtype, $CFG->prefix, $key, true);
            $test->status = add_key($table, $key, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add unique key'] = $test;
        }

    /// 30th test. Adding one foreign+unique key to the table
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $key = new XMLDBKey('course');
            $key->setAttributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'anothertest', array('id'));

            $test->sql = $table->getAddKeySQL($CFG->dbtype, $CFG->prefix, $key, true);
            $test->status = add_key($table, $key, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add foreign+unique key'] = $test;
        }

    /// 31th test. Drop one key
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $key = new XMLDBKey('course');
            $key->setAttributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'anothertest', array('id'));

            $test->sql = $table->getDropKeySQL($CFG->dbtype, $CFG->prefix, $key, true);
            $test->status = drop_key($table, $key, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['drop foreign+unique key'] = $test;
        }

    /// 32th test. Adding one foreign key to the table
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $key = new XMLDBKey('course');
            $key->setAttributes(XMLDB_KEY_FOREIGN, array('course'), 'anothertest', array('id'));

            $test->sql = $table->getAddKeySQL($CFG->dbtype, $CFG->prefix, $key, true);
            $test->status = add_key($table, $key, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add foreign key'] = $test;
        }

    /// 33th test. Drop one foreign key
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $key = new XMLDBKey('course');
            $key->setAttributes(XMLDB_KEY_FOREIGN, array('course'), 'anothertest', array('id'));

            $test->sql = $table->getDropKeySQL($CFG->dbtype, $CFG->prefix, $key, true);
            $test->status = drop_key($table, $key, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['drop foreign key'] = $test;
        }

    /// 34th test. Adding one complex enum field
        if ($test->status) {
        /// Create a new field with complex specs (enums are good candidates)
            $field = new XMLDBField('type');
            $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM, array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');
        /// Get SQL code and execute it
            $test = new stdClass;
            $test->sql = $table->getAddFieldSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = add_field($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add field with enum'] = $test;
        }

    /// 35th test. Dropping the enum of one field
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('type');
            $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, 'general', 'course');

            $test->sql = $table->getModifyEnumSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_enum($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['delete enumlist from one field'] = $test;
        }

    /// 36th test. Creating the enum for one field
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('type');
            $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM, array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');

            $test->sql = $table->getModifyEnumSQL($CFG->dbtype, $CFG->prefix, $field, true);
            $test->status = change_field_enum($table, $field, false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['add enumlist to one field'] = $test;
        }

    /// 37th test. Renaming one index
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $index = new XMLDBIndex('newnamefortheindex');
            $index->setAttributes(XMLDB_INDEX_UNIQUE, array('name', 'course'));

            $test->sql = $table->getRenameIndexSQL($CFG->dbtype, $CFG->prefix, $index, true);
            $test->status = rename_index($table, $index, 'newnamefortheindex', false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['rename index (experimental. DO NOT USE IT)'] = $test;
        }

    /// 38th test. Renaming one key
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $key = new XMLDBKey('newnameforthekey');
            $key->setAttributes(XMLDB_KEY_UNIQUE, array('id', 'course', 'grade'));

            $test->sql = $table->getRenameKeySQL($CFG->dbtype, $CFG->prefix, $key, true);
            $test->status = rename_key($table, $key, 'newnameforthekey', false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['rename key (experimental. DO NOT USE IT)'] = $test;
        }

    /// 39th test. Renaming one field
        if ($test->status && 1==2) {
        /// Get SQL code and execute it
            $test = new stdClass;
            $field = new XMLDBField('type');
            $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM, array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');
            
            $test->sql = $table->getRenameFieldSQL($CFG->dbtype, $CFG->prefix, $field, 'newnameforthefield', true);
            $test->status = rename_field($table, $field, 'newnameforthefield', false, false);
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['rename field'] = $test;
        }

    /// 40th test. Renaming one table
        if ($test->status) {
        /// Get SQL code and execute it
            $test = new stdClass;
            
            $test->sql = $table->getRenameTableSQL($CFG->dbtype, $CFG->prefix, 'newnameforthetable', true);
            $db->debug = true;
            $test->status = rename_table($table, 'newnameforthetable', false, false);
            $db->debug = false;
            if (!$test->status) {
                $test->error = $db->ErrorMsg();
            }
            $tests['rename table'] = $test;
        }



    /// TODO: Check here values of the inserted records to see that everything ha the correct value


    /// Iterate over tests, showing information as needed
        $o .= '<ol>';
        foreach ($tests as $key => $test) {
            $o .= '<li>' . $key . ($test->status ? '<font color="green"> Ok</font>' : ' <font color="red">Error</font>');
            if (!$test->status) {
                $o .= '<br/><font color="red">' . $test->error . '</font>';
            }
            $o .= '<pre>' . implode('<br/>', $test->sql) . '</pre>';
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

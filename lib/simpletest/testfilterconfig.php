<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
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

/**
 * Tests for the parts of ../filterlib.php that involve loading the configuration
 * from, and saving the configuration to, the database.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/filterlib.php');

/**
 * Test functions that use just the filter_active table.
 */
class filter_active_test extends UnitTestCaseUsingDatabase {
    private $syscontextid;

    public function setUp() {
        // Make sure accesslib has cached a sensible system context object
        // before we switch to the test DB.
        $this->syscontextid = get_context_instance(CONTEXT_SYSTEM)->id;

        // Create the table we need and switch to test DB.
        $this->create_test_table('filter_active', 'lib');
        $this->switch_to_test_db();
    }

    private function assert_only_one_filter_globally($filter, $state) {
        $recs = $this->testdb->get_records('filter_active');
        $this->assertEqual(1, count($recs), 'More than one record returned %s.');
        $rec = reset($recs);
        $expectedrec = new stdClass;
        $expectedrec->filter = $filter;
        $expectedrec->contextid = $this->syscontextid;
        $expectedrec->active = $state;
        $expectedrec->sortorder = 1;
        $this->assert(new CheckSpecifiedFieldsExpectation($expectedrec), $rec);
    }

    private function assert_global_sort_order($filters) {
        $sortedfilters = $this->testdb->get_records_menu('filter_active',
                array('contextid' => $this->syscontextid), 'sortorder', 'sortorder,filter');
        $testarray = array();
        $index = 1;
        foreach($filters as $filter) {
            $testarray[$index++] = $filter;
        }
        $this->assertEqual($testarray, $sortedfilters);
    }

    public function test_set_filter_globally_on() {
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('filter/name', TEXTFILTER_ON, 1);
        // Validate.
        $this->assert_only_one_filter_globally('filter/name', TEXTFILTER_ON);
    }

    public function test_set_filter_globally_off() {
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('filter/name', TEXTFILTER_OFF, 1);
        // Validate.
        $this->assert_only_one_filter_globally('filter/name', TEXTFILTER_OFF);
    }

    public function test_set_filter_globally_disabled() {
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('filter/name', TEXTFILTER_DISABLED, 1);
        // Validate.
        $this->assert_only_one_filter_globally('filter/name', TEXTFILTER_DISABLED);
    }

    public function test_global_config_exception_on_invalid_state() {
        // Setup fixture.
        // Set expectation.
        $this->expectException();
        // Exercise SUT.
        filter_set_global_state('filter/name', 0, 1);
    }

    public function test_set_no_sortorder_clash() {
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('filter/one', TEXTFILTER_DISABLED, 1);
        filter_set_global_state('filter/two', TEXTFILTER_DISABLED, 1);
        // Validate - should have pushed other filters down.
        $this->assert_global_sort_order(array('filter/two', 'filter/one'));
    }

    public function test_auto_sort_order_disabled() {
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('filter/one', TEXTFILTER_DISABLED);
        filter_set_global_state('filter/two', TEXTFILTER_DISABLED);
        // Validate.
        $this->assert_global_sort_order(array('filter/one', 'filter/two'));
    }

    public function test_auto_sort_order_enabled() {
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('filter/one', TEXTFILTER_ON);
        filter_set_global_state('filter/two', TEXTFILTER_OFF);
        // Validate.
        $this->assert_global_sort_order(array('filter/one', 'filter/two'));
    }

    public function test_auto_sort_order_mixed() {
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('filter/0', TEXTFILTER_DISABLED);
        filter_set_global_state('filter/1', TEXTFILTER_ON);
        filter_set_global_state('filter/2', TEXTFILTER_DISABLED);
        filter_set_global_state('filter/3', TEXTFILTER_OFF);
        // Validate.
        $this->assert_global_sort_order(array('filter/1', 'filter/3', 'filter/0', 'filter/2'));
    }

    public function test_update_existing_dont_duplicate() {
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_global_state('filter/name', TEXTFILTER_OFF);
        // Validate.
        $this->assert_only_one_filter_globally('filter/name', TEXTFILTER_OFF);
    }

    public function test_sort_order_not_too_low() {
        // Setup fixture.
        filter_set_global_state('filter/1', TEXTFILTER_ON);
        // Set expectation.
        $this->expectException();
        // Exercise SUT.
        filter_set_global_state('filter/2', TEXTFILTER_ON, 0);
    }

    public function test_sort_order_not_too_high() {
        // Setup fixture.
        filter_set_global_state('filter/1', TEXTFILTER_ON);
        // Set expectation.
        $this->expectException();
        // Exercise SUT.
        filter_set_global_state('filter/2', TEXTFILTER_ON, 3);
    }

    public function test_update_reorder_down() {
        // Setup fixture.
        filter_set_global_state('filter/1', TEXTFILTER_ON);
        filter_set_global_state('filter/2', TEXTFILTER_ON);
        filter_set_global_state('filter/3', TEXTFILTER_ON);
        // Exercise SUT.
        filter_set_global_state('filter/2', TEXTFILTER_ON, 1);
        // Validate.
        $this->assert_global_sort_order(array('filter/2', 'filter/1', 'filter/3'));
    }

    public function test_update_reorder_up() {
        // Setup fixture.
        filter_set_global_state('filter/1', TEXTFILTER_ON);
        filter_set_global_state('filter/2', TEXTFILTER_ON);
        filter_set_global_state('filter/3', TEXTFILTER_ON);
        filter_set_global_state('filter/4', TEXTFILTER_ON);
        // Exercise SUT.
        filter_set_global_state('filter/2', TEXTFILTER_ON, 3);
        // Validate.
        $this->assert_global_sort_order(array('filter/1', 'filter/3', 'filter/2', 'filter/4'));
    }

    public function test_auto_sort_order_change_to_enabled() {
        // Setup fixture.
        filter_set_global_state('filter/1', TEXTFILTER_ON);
        filter_set_global_state('filter/2', TEXTFILTER_DISABLED);
        filter_set_global_state('filter/3', TEXTFILTER_DISABLED);
        // Exercise SUT.
        filter_set_global_state('filter/3', TEXTFILTER_ON);
        // Validate.
        $this->assert_global_sort_order(array('filter/1', 'filter/3', 'filter/2'));
    }

    public function test_auto_sort_order_change_to_disabled() {
        // Setup fixture.
        filter_set_global_state('filter/1', TEXTFILTER_ON);
        filter_set_global_state('filter/2', TEXTFILTER_ON);
        filter_set_global_state('filter/3', TEXTFILTER_DISABLED);
        // Exercise SUT.
        filter_set_global_state('filter/1', TEXTFILTER_DISABLED);
        // Validate.
        $this->assert_global_sort_order(array('filter/2', 'filter/3', 'filter/1'));
    }
}

/**
 * Test functions that use just the filter_config table.
 */
class filter_config_test extends UnitTestCaseUsingDatabase {
    public function setUp() {
        // Create the table we need and switch to test DB.
        $this->create_test_table('filter_config', 'lib');
        $this->switch_to_test_db();
    }

    private function assert_only_one_config($filter, $context, $name, $value) {
        $recs = $this->testdb->get_records('filter_config');
        $this->assertEqual(1, count($recs), 'More than one record returned %s.');
        $rec = reset($recs);
        $expectedrec = new stdClass;
        $expectedrec->filter = $filter;
        $expectedrec->contextid = $context;
        $expectedrec->name = $name;
        $expectedrec->value = $value;
        $this->assert(new CheckSpecifiedFieldsExpectation($expectedrec), $rec);
    }

    public function test_set_new_config() {
        // Exercise SUT.
        filter_set_local_config('filter/name', 123, 'settingname', 'An arbitrary value');
        // Validate.
        $this->assert_only_one_config('filter/name', 123, 'settingname', 'An arbitrary value');
    }

    public function test_update_existing_config() {
        // Setup fixture.
        filter_set_local_config('filter/name', 123, 'settingname', 'An arbitrary value');
        // Exercise SUT.
        filter_set_local_config('filter/name', 123, 'settingname', 'A changed value');
        // Validate.
        $this->assert_only_one_config('filter/name', 123, 'settingname', 'A changed value');
    }

    public function test_filter_get_local_config() {
        // Setup fixture.
        filter_set_local_config('filter/name', 123, 'setting1', 'An arbitrary value');
        filter_set_local_config('filter/name', 123, 'setting2', 'Another arbitrary value');
        filter_set_local_config('filter/name', 122, 'settingname', 'Value from another context');
        filter_set_local_config('filter/other', 123, 'settingname', 'Someone else\'s value');
        // Exercise SUT.
        $config = filter_get_local_config('filter/name', 123);
        // Validate.
        $this->assertEqual(array('setting1' => 'An arbitrary value', 'setting2' => 'Another arbitrary value'), $config);
    }
}
?>

<?php

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
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class filter_active_global_test extends UnitTestCaseUsingDatabase {
    private $syscontextid;
    public static $includecoverage = array('lib/filterlib.php');

    public function setUp() {
        parent::setUp();

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
        $this->assert_global_sort_order(array('filter/2', 'filter/1', 'filter/3'));
    }

    public function test_filter_get_global_states() {
        // Setup fixture.
        filter_set_global_state('filter/1', TEXTFILTER_ON);
        filter_set_global_state('filter/2', TEXTFILTER_OFF);
        filter_set_global_state('filter/3', TEXTFILTER_DISABLED);
        // Exercise SUT.
        $filters = filter_get_global_states();
        // Validate.
        $this->assertEqual(array(
            'filter/1' => (object) array('filter' => 'filter/1', 'active' => TEXTFILTER_ON, 'sortorder' => 1),
            'filter/2' => (object) array('filter' => 'filter/2', 'active' => TEXTFILTER_OFF, 'sortorder' => 2),
            'filter/3' => (object) array('filter' => 'filter/3', 'active' => TEXTFILTER_DISABLED, 'sortorder' => 3)
        ), $filters);
    }
}

/**
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class filter_active_local_test extends UnitTestCaseUsingDatabase {
    public function setUp() {
        parent::setUp();

        // Create the table we need and switch to test DB.
        $this->create_test_table('filter_active', 'lib');
        $this->switch_to_test_db();
    }

    private function assert_only_one_local_setting($filter, $contextid, $state) {
        $recs = $this->testdb->get_records('filter_active');
        $this->assertEqual(1, count($recs), 'More than one record returned %s.');
        $rec = reset($recs);
        $expectedrec = new stdClass;
        $expectedrec->filter = $filter;
        $expectedrec->contextid = $contextid;
        $expectedrec->active = $state;
        $this->assert(new CheckSpecifiedFieldsExpectation($expectedrec), $rec);
    }

    private function assert_no_local_setting() {
        $this->assertEqual(0, $this->testdb->count_records('filter_active'));
    }

    public function test_local_on() {
        // Exercise SUT.
        filter_set_local_state('filter/name', 123, TEXTFILTER_ON);
        // Validate.
        $this->assert_only_one_local_setting('filter/name', 123, TEXTFILTER_ON);
    }

    public function test_local_off() {
        // Exercise SUT.
        filter_set_local_state('filter/name', 123, TEXTFILTER_OFF);
        // Validate.
        $this->assert_only_one_local_setting('filter/name', 123, TEXTFILTER_OFF);
    }

    public function test_local_inherit() {
        global $DB;
        // Exercise SUT.
        filter_set_local_state('filter/name', 123, TEXTFILTER_INHERIT);
        // Validate.
        $this->assert_no_local_setting();
    }

    public function test_local_invalid_state_throws_exception() {
        // Set expectation.
        $this->expectException();
        // Exercise SUT.
        filter_set_local_state('filter/name', 123, -9999);
    }

    public function test_throws_exception_when_setting_global() {
        // Set expectation.
        $this->expectException();
        // Exercise SUT.
        filter_set_local_state('filter/name', get_context_instance(CONTEXT_SYSTEM)->id, TEXTFILTER_INHERIT);
    }

    public function test_local_inherit_deletes_existing() {
        global $DB;
        // Setup fixture.
        filter_set_local_state('filter/name', 123, TEXTFILTER_INHERIT);
        // Exercise SUT.
        filter_set_local_state('filter/name', 123, TEXTFILTER_INHERIT);
        // Validate.
        $this->assert_no_local_setting();
    }
}

/**
 * Test functions that use just the filter_config table.
 */
class filter_config_test extends UnitTestCaseUsingDatabase {
    public function setUp() {
        parent::setUp();

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

class filter_get_active_available_in_context_test extends UnitTestCaseUsingDatabase {
    private $syscontext;
    private $childcontext;
    private $childcontext2;

    public function setUp() {
        parent::setUp();

        // Make sure accesslib has cached a sensible system context object
        // before we switch to the test DB.
        $this->syscontext = get_context_instance(CONTEXT_SYSTEM);

        // Create the table we need and switch to test DB.
        $this->create_test_tables(array('filter_active', 'filter_config', 'context'), 'lib');
        $this->switch_to_test_db();

        // Set up systcontext in the test database.
        $this->testdb->insert_record('context', $this->syscontext);
        $this->syscontext->id = 1;

        // Set up a child context.
        $this->childcontext = new stdClass;
        $this->childcontext->contextlevel = CONTEXT_COURSECAT;
        $this->childcontext->instanceid = 1;
        $this->childcontext->depth = 2;
        $this->childcontext->path = '/1/2';
        $this->testdb->insert_record('context', $this->childcontext);
        $this->childcontext->id = 2;

        // Set up a grandchild context.
        $this->childcontext2 = new stdClass;
        $this->childcontext2->contextlevel = CONTEXT_COURSE;
        $this->childcontext2->instanceid = 2;
        $this->childcontext2->depth = 3;
        $this->childcontext2->path = '/1/2/3';
        $this->testdb->insert_record('context', $this->childcontext2);
        $this->childcontext2->id = 3;
    }

    private function assert_filter_list($expectedfilters, $filters) {
        $this->assert(new ArraysHaveSameValuesExpectation($expectedfilters), array_keys($filters));
    }

    public function test_globally_on_is_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_active_in_context($this->syscontext);
        // Validate.
        $this->assert_filter_list(array('filter/name'), $filters);
        // Check no config returned correctly.
        $this->assertEqual(array(), $filters['filter/name']);
    }

    public function test_globally_off_not_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_active_in_context($this->childcontext2);
        // Validate.
        $this->assert_filter_list(array(), $filters);
    }

    public function test_globally_off_overridden() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_OFF);
        filter_set_local_state('filter/name', $this->childcontext->id, TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_active_in_context($this->childcontext2);
        // Validate.
        $this->assert_filter_list(array('filter/name'), $filters);
    }

    public function test_globally_on_overridden() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_state('filter/name', $this->childcontext->id, TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_active_in_context($this->childcontext2);
        // Validate.
        $this->assert_filter_list(array(), $filters);
    }

    public function test_globally_disabled_not_overridden() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_DISABLED);
        filter_set_local_state('filter/name', $this->childcontext->id, TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_active_in_context($this->syscontext);
        // Validate.
        $this->assert_filter_list(array(), $filters);
    }

    public function test_single_config_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_config('filter/name', $this->childcontext->id, 'settingname', 'A value');
        // Exercise SUT.
        $filters = filter_get_active_in_context($this->childcontext);
        // Validate.
        $this->assertEqual(array('settingname' => 'A value'), $filters['filter/name']);
    }

    public function test_multi_config_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_config('filter/name', $this->childcontext->id, 'settingname', 'A value');
        filter_set_local_config('filter/name', $this->childcontext->id, 'anothersettingname', 'Another value');
        // Exercise SUT.
        $filters = filter_get_active_in_context($this->childcontext);
        // Validate.
        $this->assertEqual(array('settingname' => 'A value', 'anothersettingname' => 'Another value'), $filters['filter/name']);
    }

    public function test_config_from_other_context_not_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_config('filter/name', $this->childcontext->id, 'settingname', 'A value');
        filter_set_local_config('filter/name', $this->childcontext2->id, 'anothersettingname', 'Another value');
        // Exercise SUT.
        $filters = filter_get_active_in_context($this->childcontext2);
        // Validate.
        $this->assertEqual(array('anothersettingname' => 'Another value'), $filters['filter/name']);
    }

    public function test_config_from_other_filter_not_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_config('filter/name', $this->childcontext->id, 'settingname', 'A value');
        filter_set_local_config('filter/other', $this->childcontext->id, 'anothersettingname', 'Another value');
        // Exercise SUT.
        $filters = filter_get_active_in_context($this->childcontext);
        // Validate.
        $this->assertEqual(array('settingname' => 'A value'), $filters['filter/name']);
    }

    protected function assert_one_available_filter($filter, $localstate, $inheritedstate, $filters) {
        $this->assertEqual(1, count($filters), 'More than one record returned %s.');
        $rec = $filters[$filter];
        $expectedrec = new stdClass;
        $expectedrec->filter = $filter;
        $expectedrec->localstate = $localstate;
        $expectedrec->inheritedstate = $inheritedstate;
        $this->assert(new CheckSpecifiedFieldsExpectation($expectedrec), $rec);
    }

    public function test_available_in_context_localoverride() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_state('filter/name', $this->childcontext->id, TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_available_in_context($this->childcontext);
        // Validate.
        $this->assert_one_available_filter('filter/name', TEXTFILTER_OFF, TEXTFILTER_ON, $filters);
    }

    public function test_available_in_context_nolocaloverride() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_state('filter/name', $this->childcontext->id, TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_available_in_context($this->childcontext2);
        // Validate.
        $this->assert_one_available_filter('filter/name', TEXTFILTER_INHERIT, TEXTFILTER_OFF, $filters);
    }

    public function test_available_in_context_disabled_not_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_DISABLED);
        filter_set_local_state('filter/name', $this->childcontext->id, TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_available_in_context($this->childcontext);
        // Validate.
        $this->assertEqual(array(), $filters);
    }

    public function test_available_in_context_exception_with_syscontext() {
        // Set expectation.
        $this->expectException();
        // Exercise SUT.
        filter_get_available_in_context($this->syscontext);
    }
}

class filter_delete_config_test extends UnitTestCaseUsingDatabase {
    protected $syscontext;

    public function setUp() {
        parent::setUp();

        $this->syscontext = get_context_instance(CONTEXT_SYSTEM);

        // Create the table we need and switch to test DB.
        $this->create_test_tables(array('filter_active', 'filter_config', 'config', 'config_plugins'), 'lib');
        $this->switch_to_test_db();
    }

    public function test_filter_delete_all_for_filter() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_global_state('filter/other', TEXTFILTER_ON);
        filter_set_local_config('filter/name', $this->syscontext->id, 'settingname', 'A value');
        filter_set_local_config('filter/other', $this->syscontext->id, 'settingname', 'Other value');
        set_config('configname', 'A config value', 'filter_name');
        set_config('configname', 'Other config value', 'filter_other');
        // Exercise SUT.
        filter_delete_all_for_filter('filter/name');
        // Validate.
        $this->assertEqual(1, $this->testdb->count_records('filter_active'));
        $this->assertTrue($this->testdb->record_exists('filter_active', array('filter' => 'filter/other')));
        $this->assertEqual(1, $this->testdb->count_records('filter_config'));
        $this->assertTrue($this->testdb->record_exists('filter_config', array('filter' => 'filter/other')));
        $expectedconfig = new stdClass;
        $expectedconfig->configname = 'Other config value';
        $this->assertEqual($expectedconfig, get_config('filter_other'));
        $this->assertIdentical(get_config('filter_name'), new stdClass());
    }

    public function test_filter_delete_all_for_context() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_state('filter/name', 123, TEXTFILTER_OFF);
        filter_set_local_config('filter/name', 123, 'settingname', 'A value');
        filter_set_local_config('filter/other', 123, 'settingname', 'Other value');
        filter_set_local_config('filter/other', 122, 'settingname', 'Other value');
        // Exercise SUT.
        filter_delete_all_for_context(123);
        // Validate.
        $this->assertEqual(1, $this->testdb->count_records('filter_active'));
        $this->assertTrue($this->testdb->record_exists('filter_active', array('contextid' => $this->syscontext->id)));
        $this->assertEqual(1, $this->testdb->count_records('filter_config'));
        $this->assertTrue($this->testdb->record_exists('filter_config', array('filter' => 'filter/other')));
    }
}

class filter_filter_set_applies_to_strings extends UnitTestCaseUsingDatabase {
    protected $origcfgstringfilters;
    protected $origcfgfilterall;

    public function setUp() {
        global $CFG;
        parent::setUp();

        // Create the table we need and switch to test DB.
        $this->create_test_table('config', 'lib');
        $this->switch_to_test_db();

        // Store original $CFG;
        $this->origcfgstringfilters = $CFG->stringfilters;
        $this->origcfgfilterall = $CFG->filterall;
    }

    public function tearDown() {
        global $CFG;
        $CFG->stringfilters = $this->origcfgstringfilters;
        $CFG->filterall = $this->origcfgfilterall;

        parent::tearDown();
    }

    public function test_set() {
        global $CFG;
        // Setup fixture.
        $CFG->filterall = 0;
        $CFG->stringfilters = '';
        // Exercise SUT.
        filter_set_applies_to_strings('filter/name', true);
        // Validate.
        $this->assertEqual('filter/name', $CFG->stringfilters);
        $this->assertTrue($CFG->filterall);
    }

    public function test_unset_to_empty() {
        global $CFG;
        // Setup fixture.
        $CFG->filterall = 1;
        $CFG->stringfilters = 'filter/name';
        // Exercise SUT.
        filter_set_applies_to_strings('filter/name', false);
        // Validate.
        $this->assertEqual('', $CFG->stringfilters);
        $this->assertFalse($CFG->filterall);
    }

    public function test_unset_multi() {
        global $CFG;
        // Setup fixture.
        $CFG->filterall = 1;
        $CFG->stringfilters = 'filter/name,filter/other';
        // Exercise SUT.
        filter_set_applies_to_strings('filter/name', false);
        // Validate.
        $this->assertEqual('filter/other', $CFG->stringfilters);
        $this->assertTrue($CFG->filterall);
    }
}


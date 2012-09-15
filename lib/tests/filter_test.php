<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Tests for the parts of ../filterlib.php that involve loading the configuration
 * from, and saving the configuration to, the database.
 *
 * @package   core_filter
 * @category  phpunit
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filterlib.php');

/**
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class filter_active_global_testcase extends advanced_testcase {

    protected function setUp() {
        global $DB;
        parent::setUp();
        $DB->delete_records('filter_active', array());
        $this->resetAfterTest(false);
    }

    private function assert_only_one_filter_globally($filter, $state) {
        global $DB;
        $recs = $DB->get_records('filter_active');
        $this->assertEquals(1, count($recs), 'More than one record returned %s.');
        $rec = reset($recs);
        unset($rec->id);
        $expectedrec = new stdClass();
        $expectedrec->filter = $filter;
        $expectedrec->contextid = context_system::instance()->id;
        $expectedrec->active = $state;
        $expectedrec->sortorder = 1;
        $this->assertEquals($expectedrec, $rec);
    }

    private function assert_global_sort_order($filters) {
        global $DB;

        $sortedfilters = $DB->get_records_menu('filter_active',
            array('contextid' => context_system::instance()->id), 'sortorder', 'sortorder,filter');
        $testarray = array();
        $index = 1;
        foreach($filters as $filter) {
            $testarray[$index++] = $filter;
        }
        $this->assertEquals($testarray, $sortedfilters);
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

    /**
     * @expectedException coding_exception
     * @return void
     */
    public function test_global_config_exception_on_invalid_state() {
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

    /**
     * @expectedException coding_exception
     * @return void
     */
    public function test_sort_order_not_too_low() {
        // Setup fixture.
        filter_set_global_state('filter/1', TEXTFILTER_ON);
        // Exercise SUT.
        filter_set_global_state('filter/2', TEXTFILTER_ON, 0);
    }

    /**
     * @expectedException coding_exception
     * @return void
     */
    public function test_sort_order_not_too_high() {
        // Setup fixture.
        filter_set_global_state('filter/1', TEXTFILTER_ON);
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
        $this->assertEquals(array(
            'filter/1' => (object) array('filter' => 'filter/1', 'active' => TEXTFILTER_ON, 'sortorder' => 1),
            'filter/2' => (object) array('filter' => 'filter/2', 'active' => TEXTFILTER_OFF, 'sortorder' => 2),
            'filter/3' => (object) array('filter' => 'filter/3', 'active' => TEXTFILTER_DISABLED, 'sortorder' => 3)
        ), $filters);
    }
}


/**
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class filter_active_local_testcase extends advanced_testcase {

    protected function setUp() {
        global $DB;
        parent::setUp();
        $DB->delete_records('filter_active', array());
        $this->resetAfterTest(false);
    }

    private function assert_only_one_local_setting($filter, $contextid, $state) {
        global $DB;
        $recs = $DB->get_records('filter_active');
        $this->assertEquals(1, count($recs), 'More than one record returned %s.');
        $rec = reset($recs);
        unset($rec->id);
        unset($rec->sortorder);
        $expectedrec = new stdClass();
        $expectedrec->filter = $filter;
        $expectedrec->contextid = $contextid;
        $expectedrec->active = $state;
        $this->assertEquals($expectedrec, $rec);
    }

    private function assert_no_local_setting() {
        global $DB;
        $this->assertEquals(0, $DB->count_records('filter_active'));
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
        // Exercise SUT.
        filter_set_local_state('filter/name', 123, TEXTFILTER_INHERIT);
        // Validate.
        $this->assert_no_local_setting();
    }

    /**
     * @expectedException coding_exception
     * @return void
     */
    public function test_local_invalid_state_throws_exception() {
        // Exercise SUT.
        filter_set_local_state('filter/name', 123, -9999);
    }

    /**
     * @expectedException coding_exception
     * @return void
     */
    public function test_throws_exception_when_setting_global() {
        // Exercise SUT.
        filter_set_local_state('filter/name', context_system::instance()->id, TEXTFILTER_INHERIT);
    }

    public function test_local_inherit_deletes_existing() {
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
class filter_config_testcase extends advanced_testcase {

    protected function setUp() {
        global $DB;
        parent::setUp();
        $DB->delete_records('filter_config', array());
        $this->resetAfterTest(false);
    }

    private function assert_only_one_config($filter, $context, $name, $value) {
        global $DB;
        $recs = $DB->get_records('filter_config');
        $this->assertEquals(1, count($recs), 'More than one record returned %s.');
        $rec = reset($recs);
        unset($rec->id);
        $expectedrec = new stdClass();
        $expectedrec->filter = $filter;
        $expectedrec->contextid = $context;
        $expectedrec->name = $name;
        $expectedrec->value = $value;
        $this->assertEquals($expectedrec, $rec);
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
        $this->assertEquals(array('setting1' => 'An arbitrary value', 'setting2' => 'Another arbitrary value'), $config);
    }
}


class filter_get_active_available_in_context_testcase extends advanced_testcase {
    private static $syscontext;
    private static $childcontext;
    private static $childcontext2;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        $course = self::getDataGenerator()->create_course(array('category'=>1));

        self::$childcontext = context_coursecat::instance(1);
        self::$childcontext2 = context_course::instance($course->id);
        self::$syscontext = context_system::instance();
    }

    protected function setUp() {
        global $DB;
        parent::setUp();

        $DB->delete_records('filter_active', array());
        $DB->delete_records('filter_config', array());
        $this->resetAfterTest(false);
    }

    private function assert_filter_list($expectedfilters, $filters) {
        $this->assertEquals($expectedfilters, array_keys($filters), '', 0, 10, true);
    }

    public function test_globally_on_is_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_active_in_context(self::$syscontext);
        // Validate.
        $this->assert_filter_list(array('filter/name'), $filters);
        // Check no config returned correctly.
        $this->assertEquals(array(), $filters['filter/name']);
    }

    public function test_globally_off_not_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_active_in_context(self::$childcontext2);
        // Validate.
        $this->assert_filter_list(array(), $filters);
    }

    public function test_globally_off_overridden() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_OFF);
        filter_set_local_state('filter/name', self::$childcontext->id, TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_active_in_context(self::$childcontext2);
        // Validate.
        $this->assert_filter_list(array('filter/name'), $filters);
    }

    public function test_globally_on_overridden() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_state('filter/name', self::$childcontext->id, TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_active_in_context(self::$childcontext2);
        // Validate.
        $this->assert_filter_list(array(), $filters);
    }

    public function test_globally_disabled_not_overridden() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_DISABLED);
        filter_set_local_state('filter/name', self::$childcontext->id, TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_active_in_context(self::$syscontext);
        // Validate.
        $this->assert_filter_list(array(), $filters);
    }

    public function test_single_config_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_config('filter/name', self::$childcontext->id, 'settingname', 'A value');
        // Exercise SUT.
        $filters = filter_get_active_in_context(self::$childcontext);
        // Validate.
        $this->assertEquals(array('settingname' => 'A value'), $filters['filter/name']);
    }

    public function test_multi_config_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_config('filter/name', self::$childcontext->id, 'settingname', 'A value');
        filter_set_local_config('filter/name', self::$childcontext->id, 'anothersettingname', 'Another value');
        // Exercise SUT.
        $filters = filter_get_active_in_context(self::$childcontext);
        // Validate.
        $this->assertEquals(array('settingname' => 'A value', 'anothersettingname' => 'Another value'), $filters['filter/name']);
    }

    public function test_config_from_other_context_not_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_config('filter/name', self::$childcontext->id, 'settingname', 'A value');
        filter_set_local_config('filter/name', self::$childcontext2->id, 'anothersettingname', 'Another value');
        // Exercise SUT.
        $filters = filter_get_active_in_context(self::$childcontext2);
        // Validate.
        $this->assertEquals(array('anothersettingname' => 'Another value'), $filters['filter/name']);
    }

    public function test_config_from_other_filter_not_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_config('filter/name', self::$childcontext->id, 'settingname', 'A value');
        filter_set_local_config('filter/other', self::$childcontext->id, 'anothersettingname', 'Another value');
        // Exercise SUT.
        $filters = filter_get_active_in_context(self::$childcontext);
        // Validate.
        $this->assertEquals(array('settingname' => 'A value'), $filters['filter/name']);
    }

    protected function assert_one_available_filter($filter, $localstate, $inheritedstate, $filters) {
        $this->assertEquals(1, count($filters), 'More than one record returned %s.');
        $rec = $filters[$filter];
        unset($rec->id);
        $expectedrec = new stdClass();
        $expectedrec->filter = $filter;
        $expectedrec->localstate = $localstate;
        $expectedrec->inheritedstate = $inheritedstate;
        $this->assertEquals($expectedrec, $rec);
    }

    public function test_available_in_context_localoverride() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_state('filter/name', self::$childcontext->id, TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_available_in_context(self::$childcontext);
        // Validate.
        $this->assert_one_available_filter('filter/name', TEXTFILTER_OFF, TEXTFILTER_ON, $filters);
    }

    public function test_available_in_context_nolocaloverride() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_state('filter/name', self::$childcontext->id, TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_available_in_context(self::$childcontext2);
        // Validate.
        $this->assert_one_available_filter('filter/name', TEXTFILTER_INHERIT, TEXTFILTER_OFF, $filters);
    }

    public function test_available_in_context_disabled_not_returned() {
        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_DISABLED);
        filter_set_local_state('filter/name', self::$childcontext->id, TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_available_in_context(self::$childcontext);
        // Validate.
        $this->assertEquals(array(), $filters);
    }

    /**
     * @expectedException coding_exception
     * @return void
     */
    public function test_available_in_context_exception_with_syscontext() {
        // Exercise SUT.
        filter_get_available_in_context(self::$syscontext);
    }
}


class filter_preload_activities_testcase extends advanced_testcase {
    private static $syscontext;
    private static $catcontext;
    private static $coursecontext;
    private static $course;
    private static $activity1context;
    private static $activity2context;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::$syscontext = context_system::instance();
        self::$catcontext = context_coursecat::instance(1);
        self::$course = self::getDataGenerator()->create_course(array('category'=>1));
        self::$coursecontext = context_course::instance(self::$course->id);
        $page1 =  self::getDataGenerator()->create_module('page', array('course'=>self::$course->id));
        self::$activity1context = context_module::instance($page1->cmid);
        $page2 =  self::getDataGenerator()->create_module('page', array('course'=>self::$course->id));
        self::$activity2context = context_module::instance($page2->cmid);
    }

    protected function setUp() {
        global $DB;
        parent::setUp();

        $DB->delete_records('filter_active', array());
        $DB->delete_records('filter_config', array());
        $this->resetAfterTest(false);
    }

    private function assert_matches($modinfo) {
        global $FILTERLIB_PRIVATE, $DB;

        // Use preload cache...
        $FILTERLIB_PRIVATE = new stdClass();
        filter_preload_activities($modinfo);

        // Get data and check no queries are made
        $before = $DB->perf_get_reads();
        $plfilters1 = filter_get_active_in_context(self::$activity1context);
        $plfilters2 = filter_get_active_in_context(self::$activity2context);
        $after = $DB->perf_get_reads();
        $this->assertEquals($before, $after);

        // Repeat without cache and check it makes queries now
        $FILTERLIB_PRIVATE = new stdClass;
        $before = $DB->perf_get_reads();
        $filters1 = filter_get_active_in_context(self::$activity1context);
        $filters2 = filter_get_active_in_context(self::$activity2context);
        $after = $DB->perf_get_reads();
        $this->assertTrue($after > $before);

        // Check they match
        $this->assertEquals($plfilters1, $filters1);
        $this->assertEquals($plfilters2, $filters2);
    }

    public function test_preload() {
        // Get course and modinfo
        $modinfo = new course_modinfo(self::$course, 2);

        // Note: All the tests in this function check that the result from the
        // preloaded cache is the same as the result from calling the standard
        // function without preloading.

        // Initially, check with no filters enabled
        $this->assert_matches($modinfo);

        // Enable filter globally, check
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        $this->assert_matches($modinfo);

        // Disable for activity 2
        filter_set_local_state('filter/name', self::$activity2context->id, TEXTFILTER_OFF);
        $this->assert_matches($modinfo);

        // Disable at category
        filter_set_local_state('filter/name', self::$catcontext->id, TEXTFILTER_OFF);
        $this->assert_matches($modinfo);

        // Enable for activity 1
        filter_set_local_state('filter/name', self::$activity1context->id, TEXTFILTER_ON);
        $this->assert_matches($modinfo);

        // Disable globally
        filter_set_global_state('filter/name', TEXTFILTER_DISABLED);
        $this->assert_matches($modinfo);

        // Add another 2 filters
        filter_set_global_state('filter/frog', TEXTFILTER_ON);
        filter_set_global_state('filter/zombie', TEXTFILTER_ON);
        $this->assert_matches($modinfo);

        // Disable random one of these in each context
        filter_set_local_state('filter/zombie', self::$activity1context->id, TEXTFILTER_OFF);
        filter_set_local_state('filter/frog', self::$activity2context->id, TEXTFILTER_OFF);
        $this->assert_matches($modinfo);

        // Now do some filter options
        filter_set_local_config('filter/name', self::$activity1context->id, 'a', 'x');
        filter_set_local_config('filter/zombie', self::$activity1context->id, 'a', 'y');
        filter_set_local_config('filter/frog', self::$activity1context->id, 'a', 'z');
        // These last two don't do anything as they are not at final level but I
        // thought it would be good to have that verified in test
        filter_set_local_config('filter/frog', self::$coursecontext->id, 'q', 'x');
        filter_set_local_config('filter/frog', self::$catcontext->id, 'q', 'z');
        $this->assert_matches($modinfo);
    }
}


class filter_delete_config_testcase extends advanced_testcase {
    protected function setUp() {
        global $DB;
        parent::setUp();

        $DB->delete_records('filter_active', array());
        $DB->delete_records('filter_config', array());
        $this->resetAfterTest(false);
    }

    public function test_filter_delete_all_for_filter() {
        global $DB;

        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_global_state('filter/other', TEXTFILTER_ON);
        filter_set_local_config('filter/name', context_system::instance()->id, 'settingname', 'A value');
        filter_set_local_config('filter/other', context_system::instance()->id, 'settingname', 'Other value');
        set_config('configname', 'A config value', 'filter_name');
        set_config('configname', 'Other config value', 'filter_other');
        // Exercise SUT.
        filter_delete_all_for_filter('filter/name');
        // Validate.
        $this->assertEquals(1, $DB->count_records('filter_active'));
        $this->assertTrue($DB->record_exists('filter_active', array('filter' => 'filter/other')));
        $this->assertEquals(1, $DB->count_records('filter_config'));
        $this->assertTrue($DB->record_exists('filter_config', array('filter' => 'filter/other')));
        $expectedconfig = new stdClass;
        $expectedconfig->configname = 'Other config value';
        $this->assertEquals($expectedconfig, get_config('filter_other'));
        $this->assertEquals(get_config('filter_name'), new stdClass());
    }

    public function test_filter_delete_all_for_context() {
        global $DB;

        // Setup fixture.
        filter_set_global_state('filter/name', TEXTFILTER_ON);
        filter_set_local_state('filter/name', 123, TEXTFILTER_OFF);
        filter_set_local_config('filter/name', 123, 'settingname', 'A value');
        filter_set_local_config('filter/other', 123, 'settingname', 'Other value');
        filter_set_local_config('filter/other', 122, 'settingname', 'Other value');
        // Exercise SUT.
        filter_delete_all_for_context(123);
        // Validate.
        $this->assertEquals(1, $DB->count_records('filter_active'));
        $this->assertTrue($DB->record_exists('filter_active', array('contextid' => context_system::instance()->id)));
        $this->assertEquals(1, $DB->count_records('filter_config'));
        $this->assertTrue($DB->record_exists('filter_config', array('filter' => 'filter/other')));
    }
}

class filter_filter_set_applies_to_strings extends advanced_testcase {
    protected $origcfgstringfilters;
    protected $origcfgfilterall;

    protected function setUp() {
        global $DB, $CFG;
        parent::setUp();

        $DB->delete_records('filter_active', array());
        $DB->delete_records('filter_config', array());
        $this->resetAfterTest(false);

        // Store original $CFG;
        $this->origcfgstringfilters = $CFG->stringfilters;
        $this->origcfgfilterall = $CFG->filterall;
    }

    protected function tearDown() {
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
        $this->assertEquals('filter/name', $CFG->stringfilters);
        $this->assertEquals(1, $CFG->filterall);
    }

    public function test_unset_to_empty() {
        global $CFG;
        // Setup fixture.
        $CFG->filterall = 1;
        $CFG->stringfilters = 'filter/name';
        // Exercise SUT.
        filter_set_applies_to_strings('filter/name', false);
        // Validate.
        $this->assertEquals('', $CFG->stringfilters);
        $this->assertEquals('', $CFG->filterall);
    }

    public function test_unset_multi() {
        global $CFG;
        // Setup fixture.
        $CFG->filterall = 1;
        $CFG->stringfilters = 'filter/name,filter/other';
        // Exercise SUT.
        filter_set_applies_to_strings('filter/name', false);
        // Validate.
        $this->assertEquals('filter/other', $CFG->stringfilters);
        $this->assertEquals(1, $CFG->filterall);
    }
}

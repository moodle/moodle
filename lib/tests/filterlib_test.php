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
 * Test filters.
 */
class core_filterlib_testcase extends advanced_testcase {

    private function assert_only_one_filter_globally($filter, $state) {
        global $DB;
        $recs = $DB->get_records('filter_active');
        $this->assertCount(1, $recs);
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
        foreach ($filters as $filter) {
            $testarray[$index++] = $filter;
        }
        $this->assertEquals($testarray, $sortedfilters);
    }

    public function test_set_filter_globally_on() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('name', TEXTFILTER_ON);
        // Validate.
        $this->assert_only_one_filter_globally('name', TEXTFILTER_ON);
    }

    public function test_set_filter_globally_off() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('name', TEXTFILTER_OFF);
        // Validate.
        $this->assert_only_one_filter_globally('name', TEXTFILTER_OFF);
    }

    public function test_set_filter_globally_disabled() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('name', TEXTFILTER_DISABLED);
        // Validate.
        $this->assert_only_one_filter_globally('name', TEXTFILTER_DISABLED);
    }

    public function test_global_config_exception_on_invalid_state() {
        $this->resetAfterTest();
        $this->expectException(coding_exception::class);
        filter_set_global_state('name', 0);
    }

    public function test_auto_sort_order() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('one', TEXTFILTER_DISABLED);
        filter_set_global_state('two', TEXTFILTER_DISABLED);
        // Validate.
        $this->assert_global_sort_order(array('one', 'two'));
    }

    public function test_auto_sort_order_enabled() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('one', TEXTFILTER_ON);
        filter_set_global_state('two', TEXTFILTER_OFF);
        // Validate.
        $this->assert_global_sort_order(array('one', 'two'));
    }

    public function test_update_existing_dont_duplicate() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        // Exercise SUT.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_global_state('name', TEXTFILTER_OFF);
        // Validate.
        $this->assert_only_one_filter_globally('name', TEXTFILTER_OFF);
    }

    public function test_update_reorder_down() {
        global $DB;

        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        filter_set_global_state('one', TEXTFILTER_ON);
        filter_set_global_state('two', TEXTFILTER_ON);
        filter_set_global_state('three', TEXTFILTER_ON);
        // Exercise SUT.
        filter_set_global_state('two', TEXTFILTER_ON, -1);
        // Validate.
        $this->assert_global_sort_order(array('two', 'one', 'three'));

        // Check this was logged in config log.
        $logs = $DB->get_records('config_log', null, 'id DESC', '*', 0, 1);
        $log = reset($logs);
        $this->assertEquals('core_filter', $log->plugin);
        $this->assertEquals('order', $log->name);
        $this->assertEquals('two, one, three', $log->value);
        $this->assertEquals('one, two, three', $log->oldvalue);
    }

    public function test_update_reorder_up() {
        global $DB;

        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        filter_set_global_state('one', TEXTFILTER_ON);
        filter_set_global_state('two', TEXTFILTER_ON);
        filter_set_global_state('three', TEXTFILTER_ON);
        filter_set_global_state('four', TEXTFILTER_ON);
        // Exercise SUT.
        filter_set_global_state('two', TEXTFILTER_ON, 1);
        // Validate.
        $this->assert_global_sort_order(array('one', 'three', 'two', 'four'));

        // Check this was logged in config log.
        $logs = $DB->get_records('config_log', null, 'id DESC', '*', 0, 1);
        $log = reset($logs);
        $this->assertEquals('core_filter', $log->plugin);
        $this->assertEquals('order', $log->name);
        $this->assertEquals('one, three, two, four', $log->value);
        $this->assertEquals('one, two, three, four', $log->oldvalue);
    }

    public function test_auto_sort_order_change_to_enabled() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        filter_set_global_state('one', TEXTFILTER_ON);
        filter_set_global_state('two', TEXTFILTER_DISABLED);
        filter_set_global_state('three', TEXTFILTER_DISABLED);
        // Exercise SUT.
        filter_set_global_state('three', TEXTFILTER_ON);
        // Validate.
        $this->assert_global_sort_order(array('one', 'three', 'two'));
    }

    public function test_auto_sort_order_change_to_disabled() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        filter_set_global_state('one', TEXTFILTER_ON);
        filter_set_global_state('two', TEXTFILTER_ON);
        filter_set_global_state('three', TEXTFILTER_DISABLED);
        // Exercise SUT.
        filter_set_global_state('one', TEXTFILTER_DISABLED);
        // Validate.
        $this->assert_global_sort_order(array('two', 'one', 'three'));
    }

    public function test_filter_get_global_states() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        filter_set_global_state('one', TEXTFILTER_ON);
        filter_set_global_state('two', TEXTFILTER_OFF);
        filter_set_global_state('three', TEXTFILTER_DISABLED);
        // Exercise SUT.
        $filters = filter_get_global_states();
        // Validate.
        $this->assertEquals(array(
            'one' => (object) array('filter' => 'one', 'active' => TEXTFILTER_ON, 'sortorder' => 1),
            'two' => (object) array('filter' => 'two', 'active' => TEXTFILTER_OFF, 'sortorder' => 2),
            'three' => (object) array('filter' => 'three', 'active' => TEXTFILTER_DISABLED, 'sortorder' => 3)
        ), $filters);
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
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Exercise SUT.
        filter_set_local_state('name', 123, TEXTFILTER_ON);
        // Validate.
        $this->assert_only_one_local_setting('name', 123, TEXTFILTER_ON);
    }

    public function test_local_off() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Exercise SUT.
        filter_set_local_state('name', 123, TEXTFILTER_OFF);
        // Validate.
        $this->assert_only_one_local_setting('name', 123, TEXTFILTER_OFF);
    }

    public function test_local_inherit() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Exercise SUT.
        filter_set_local_state('name', 123, TEXTFILTER_INHERIT);
        // Validate.
        $this->assert_no_local_setting();
    }

    public function test_local_invalid_state_throws_exception() {
        $this->resetAfterTest();
        // Exercise SUT.
        $this->expectException(coding_exception::class);
        filter_set_local_state('name', 123, -9999);
    }

    public function test_throws_exception_when_setting_global() {
        $this->resetAfterTest();
        // Exercise SUT.
        $this->expectException(coding_exception::class);
        filter_set_local_state('name', context_system::instance()->id, TEXTFILTER_INHERIT);
    }

    public function test_local_inherit_deletes_existing() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        filter_set_local_state('name', 123, TEXTFILTER_INHERIT);
        // Exercise SUT.
        filter_set_local_state('name', 123, TEXTFILTER_INHERIT);
        // Validate.
        $this->assert_no_local_setting();
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
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Exercise SUT.
        filter_set_local_config('name', 123, 'settingname', 'An arbitrary value');
        // Validate.
        $this->assert_only_one_config('name', 123, 'settingname', 'An arbitrary value');
    }

    public function test_update_existing_config() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        // Setup fixture.
        filter_set_local_config('name', 123, 'settingname', 'An arbitrary value');
        // Exercise SUT.
        filter_set_local_config('name', 123, 'settingname', 'A changed value');
        // Validate.
        $this->assert_only_one_config('name', 123, 'settingname', 'A changed value');
    }

    public function test_filter_get_local_config() {
        $this->resetAfterTest();
        // Setup fixture.
        filter_set_local_config('name', 123, 'setting1', 'An arbitrary value');
        filter_set_local_config('name', 123, 'setting2', 'Another arbitrary value');
        filter_set_local_config('name', 122, 'settingname', 'Value from another context');
        filter_set_local_config('other', 123, 'settingname', 'Someone else\'s value');
        // Exercise SUT.
        $config = filter_get_local_config('name', 123);
        // Validate.
        $this->assertEquals(array('setting1' => 'An arbitrary value', 'setting2' => 'Another arbitrary value'), $config);
    }

    protected function setup_available_in_context_tests() {
        $course = $this->getDataGenerator()->create_course(array('category' => 1));

        $childcontext = context_coursecat::instance(1);
        $childcontext2 = context_course::instance($course->id);
        $syscontext = context_system::instance();

        return [
            'syscontext' => $syscontext,
            'childcontext' => $childcontext,
            'childcontext2' => $childcontext2
        ];
    }

    protected function remove_all_filters_from_config() {
        global $DB;
        $DB->delete_records('filter_active', array());
        $DB->delete_records('filter_config', array());
    }

    private function assert_filter_list($expectedfilters, $filters) {
        $this->assertEqualsCanonicalizing($expectedfilters, array_keys($filters));
    }

    public function test_globally_on_is_returned() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        [
            'syscontext' => $syscontext
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_active_in_context($syscontext);
        // Validate.
        $this->assert_filter_list(array('name'), $filters);
        // Check no config returned correctly.
        $this->assertEquals(array(), $filters['name']);
    }

    public function test_globally_off_not_returned() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        [
            'childcontext2' => $childcontext2
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_active_in_context($childcontext2);
        // Validate.
        $this->assert_filter_list(array(), $filters);
    }

    public function test_globally_off_overridden() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        [
            'childcontext' => $childcontext,
            'childcontext2' => $childcontext2
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_OFF);
        filter_set_local_state('name', $childcontext->id, TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_active_in_context($childcontext2);
        // Validate.
        $this->assert_filter_list(array('name'), $filters);
    }

    public function test_globally_on_overridden() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        [
            'childcontext' => $childcontext,
            'childcontext2' => $childcontext2
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_local_state('name', $childcontext->id, TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_active_in_context($childcontext2);
        // Validate.
        $this->assert_filter_list(array(), $filters);
    }

    public function test_globally_disabled_not_overridden() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        [
            'syscontext' => $syscontext,
            'childcontext' => $childcontext
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_DISABLED);
        filter_set_local_state('name', $childcontext->id, TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_active_in_context($syscontext);
        // Validate.
        $this->assert_filter_list(array(), $filters);
    }

    public function test_single_config_returned() {
        $this->resetAfterTest();
        [
            'childcontext' => $childcontext
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_local_config('name', $childcontext->id, 'settingname', 'A value');
        // Exercise SUT.
        $filters = filter_get_active_in_context($childcontext);
        // Validate.
        $this->assertEquals(array('settingname' => 'A value'), $filters['name']);
    }

    public function test_multi_config_returned() {
        $this->resetAfterTest();
        [
            'childcontext' => $childcontext
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_local_config('name', $childcontext->id, 'settingname', 'A value');
        filter_set_local_config('name', $childcontext->id, 'anothersettingname', 'Another value');
        // Exercise SUT.
        $filters = filter_get_active_in_context($childcontext);
        // Validate.
        $this->assertEquals(array('settingname' => 'A value', 'anothersettingname' => 'Another value'), $filters['name']);
    }

    public function test_config_from_other_context_not_returned() {
        $this->resetAfterTest();
        [
            'childcontext' => $childcontext,
            'childcontext2' => $childcontext2
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_local_config('name', $childcontext->id, 'settingname', 'A value');
        filter_set_local_config('name', $childcontext2->id, 'anothersettingname', 'Another value');
        // Exercise SUT.
        $filters = filter_get_active_in_context($childcontext2);
        // Validate.
        $this->assertEquals(array('anothersettingname' => 'Another value'), $filters['name']);
    }

    public function test_config_from_other_filter_not_returned() {
        $this->resetAfterTest();
        [
            'childcontext' => $childcontext
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_local_config('name', $childcontext->id, 'settingname', 'A value');
        filter_set_local_config('other', $childcontext->id, 'anothersettingname', 'Another value');
        // Exercise SUT.
        $filters = filter_get_active_in_context($childcontext);
        // Validate.
        $this->assertEquals(array('settingname' => 'A value'), $filters['name']);
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
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        [
            'childcontext' => $childcontext
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_local_state('name', $childcontext->id, TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_available_in_context($childcontext);
        // Validate.
        $this->assert_one_available_filter('name', TEXTFILTER_OFF, TEXTFILTER_ON, $filters);
    }

    public function test_available_in_context_nolocaloverride() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        [
            'childcontext' => $childcontext,
            'childcontext2' => $childcontext2
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_local_state('name', $childcontext->id, TEXTFILTER_OFF);
        // Exercise SUT.
        $filters = filter_get_available_in_context($childcontext2);
        // Validate.
        $this->assert_one_available_filter('name', TEXTFILTER_INHERIT, TEXTFILTER_OFF, $filters);
    }

    public function test_available_in_context_disabled_not_returned() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        [
            'childcontext' => $childcontext
        ] = $this->setup_available_in_context_tests();
        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_DISABLED);
        filter_set_local_state('name', $childcontext->id, TEXTFILTER_ON);
        // Exercise SUT.
        $filters = filter_get_available_in_context($childcontext);
        // Validate.
        $this->assertEquals(array(), $filters);
    }

    public function test_available_in_context_exception_with_syscontext() {
        $this->resetAfterTest();
        [
            'syscontext' => $syscontext
        ] = $this->setup_available_in_context_tests();
        // Exercise SUT.
        $this->expectException(coding_exception::class);
        filter_get_available_in_context($syscontext);
    }

    protected function setup_preload_activities_test() {
        $syscontext = context_system::instance();
        $catcontext = context_coursecat::instance(1);
        $course = $this->getDataGenerator()->create_course(array('category' => 1));
        $coursecontext = context_course::instance($course->id);
        $page1 = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $activity1context = context_module::instance($page1->cmid);
        $page2 = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $activity2context = context_module::instance($page2->cmid);
        return [
            'syscontext' => $syscontext,
            'catcontext' => $catcontext,
            'course' => $course,
            'coursecontext' => $coursecontext,
            'activity1context' => $activity1context,
            'activity2context' => $activity2context
         ];
    }

    private function assert_matches($modinfo, $activity1context, $activity2context) {
        global $FILTERLIB_PRIVATE, $DB;

        // Use preload cache...
        $FILTERLIB_PRIVATE = new stdClass();
        filter_preload_activities($modinfo);

        // Get data and check no queries are made.
        $before = $DB->perf_get_reads();
        $plfilters1 = filter_get_active_in_context($activity1context);
        $plfilters2 = filter_get_active_in_context($activity2context);
        $after = $DB->perf_get_reads();
        $this->assertEquals($before, $after);

        // Repeat without cache and check it makes queries now.
        $FILTERLIB_PRIVATE = new stdClass;
        $before = $DB->perf_get_reads();
        $filters1 = filter_get_active_in_context($activity1context);
        $filters2 = filter_get_active_in_context($activity2context);
        $after = $DB->perf_get_reads();
        $this->assertTrue($after > $before);

        // Check they match.
        $this->assertEquals($plfilters1, $filters1);
        $this->assertEquals($plfilters2, $filters2);
    }

    public function test_preload() {
        $this->resetAfterTest();
        [
            'catcontext' => $catcontext,
            'course' => $course,
            'coursecontext' => $coursecontext,
            'activity1context' => $activity1context,
            'activity2context' => $activity2context
         ] = $this->setup_preload_activities_test();
        // Get course and modinfo.
        $modinfo = new course_modinfo($course, 2);

        // Note: All the tests in this function check that the result from the
        // preloaded cache is the same as the result from calling the standard
        // function without preloading.

        // Initially, check with no filters enabled.
        $this->assert_matches($modinfo, $activity1context, $activity2context);

        // Enable filter globally, check.
        filter_set_global_state('name', TEXTFILTER_ON);
        $this->assert_matches($modinfo, $activity1context, $activity2context);

        // Disable for activity 2.
        filter_set_local_state('name', $activity2context->id, TEXTFILTER_OFF);
        $this->assert_matches($modinfo, $activity1context, $activity2context);

        // Disable at category.
        filter_set_local_state('name', $catcontext->id, TEXTFILTER_OFF);
        $this->assert_matches($modinfo, $activity1context, $activity2context);

        // Enable for activity 1.
        filter_set_local_state('name', $activity1context->id, TEXTFILTER_ON);
        $this->assert_matches($modinfo, $activity1context, $activity2context);

        // Disable globally.
        filter_set_global_state('name', TEXTFILTER_DISABLED);
        $this->assert_matches($modinfo, $activity1context, $activity2context);

        // Add another 2 filters.
        filter_set_global_state('frog', TEXTFILTER_ON);
        filter_set_global_state('zombie', TEXTFILTER_ON);
        $this->assert_matches($modinfo, $activity1context, $activity2context);

        // Disable random one of these in each context.
        filter_set_local_state('zombie', $activity1context->id, TEXTFILTER_OFF);
        filter_set_local_state('frog', $activity2context->id, TEXTFILTER_OFF);
        $this->assert_matches($modinfo, $activity1context, $activity2context);

        // Now do some filter options.
        filter_set_local_config('name', $activity1context->id, 'a', 'x');
        filter_set_local_config('zombie', $activity1context->id, 'a', 'y');
        filter_set_local_config('frog', $activity1context->id, 'a', 'z');
        // These last two don't do anything as they are not at final level but I
        // thought it would be good to have that verified in test.
        filter_set_local_config('frog', $coursecontext->id, 'q', 'x');
        filter_set_local_config('frog', $catcontext->id, 'q', 'z');
        $this->assert_matches($modinfo, $activity1context, $activity2context);
    }

    public function test_filter_delete_all_for_filter() {
        global $DB;
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.

        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_global_state('other', TEXTFILTER_ON);
        filter_set_local_config('name', context_system::instance()->id, 'settingname', 'A value');
        filter_set_local_config('other', context_system::instance()->id, 'settingname', 'Other value');
        set_config('configname', 'A config value', 'filter_name');
        set_config('configname', 'Other config value', 'filter_other');
        // Exercise SUT.
        filter_delete_all_for_filter('name');
        // Validate.
        $this->assertEquals(1, $DB->count_records('filter_active'));
        $this->assertTrue($DB->record_exists('filter_active', array('filter' => 'other')));
        $this->assertEquals(1, $DB->count_records('filter_config'));
        $this->assertTrue($DB->record_exists('filter_config', array('filter' => 'other')));
        $expectedconfig = new stdClass;
        $expectedconfig->configname = 'Other config value';
        $this->assertEquals($expectedconfig, get_config('filter_other'));
        $this->assertEquals(get_config('filter_name'), new stdClass());
    }

    public function test_filter_delete_all_for_context() {
        global $DB;
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.

        // Setup fixture.
        filter_set_global_state('name', TEXTFILTER_ON);
        filter_set_local_state('name', 123, TEXTFILTER_OFF);
        filter_set_local_config('name', 123, 'settingname', 'A value');
        filter_set_local_config('other', 123, 'settingname', 'Other value');
        filter_set_local_config('other', 122, 'settingname', 'Other value');
        // Exercise SUT.
        filter_delete_all_for_context(123);
        // Validate.
        $this->assertEquals(1, $DB->count_records('filter_active'));
        $this->assertTrue($DB->record_exists('filter_active', array('contextid' => context_system::instance()->id)));
        $this->assertEquals(1, $DB->count_records('filter_config'));
        $this->assertTrue($DB->record_exists('filter_config', array('filter' => 'other')));
    }

    public function test_set() {
        global $CFG;
        $this->resetAfterTest();

        $this->assertFileExists("$CFG->dirroot/filter/emailprotect"); // Any standard filter.
        $this->assertFileExists("$CFG->dirroot/filter/tidy");         // Any standard filter.
        $this->assertFileDoesNotExist("$CFG->dirroot/filter/grgrggr");   // Any non-existent filter.

        // Setup fixture.
        set_config('filterall', 0);
        set_config('stringfilters', '');
        // Exercise SUT.
        filter_set_applies_to_strings('tidy', true);
        // Validate.
        $this->assertEquals('tidy', $CFG->stringfilters);
        $this->assertEquals(1, $CFG->filterall);

        filter_set_applies_to_strings('grgrggr', true);
        $this->assertEquals('tidy', $CFG->stringfilters);
        $this->assertEquals(1, $CFG->filterall);

        filter_set_applies_to_strings('emailprotect', true);
        $this->assertEquals('tidy,emailprotect', $CFG->stringfilters);
        $this->assertEquals(1, $CFG->filterall);
    }

    public function test_unset_to_empty() {
        global $CFG;
        $this->resetAfterTest();

        $this->assertFileExists("$CFG->dirroot/filter/tidy"); // Any standard filter.

        // Setup fixture.
        set_config('filterall', 1);
        set_config('stringfilters', 'tidy');
        // Exercise SUT.
        filter_set_applies_to_strings('tidy', false);
        // Validate.
        $this->assertEquals('', $CFG->stringfilters);
        $this->assertEquals('', $CFG->filterall);
    }

    public function test_unset_multi() {
        global $CFG;
        $this->resetAfterTest();

        $this->assertFileExists("$CFG->dirroot/filter/emailprotect"); // Any standard filter.
        $this->assertFileExists("$CFG->dirroot/filter/tidy");         // Any standard filter.
        $this->assertFileExists("$CFG->dirroot/filter/multilang");    // Any standard filter.

        // Setup fixture.
        set_config('filterall', 1);
        set_config('stringfilters', 'emailprotect,tidy,multilang');
        // Exercise SUT.
        filter_set_applies_to_strings('tidy', false);
        // Validate.
        $this->assertEquals('emailprotect,multilang', $CFG->stringfilters);
        $this->assertEquals(1, $CFG->filterall);
    }

    public function test_filter_manager_instance() {
        $this->resetAfterTest();

        set_config('perfdebug', 7);
        filter_manager::reset_caches();
        $filterman = filter_manager::instance();
        $this->assertInstanceOf('filter_manager', $filterman);
        $this->assertNotInstanceOf('performance_measuring_filter_manager', $filterman);

        set_config('perfdebug', 15);
        filter_manager::reset_caches();
        $filterman = filter_manager::instance();
        $this->assertInstanceOf('filter_manager', $filterman);
        $this->assertInstanceOf('performance_measuring_filter_manager', $filterman);
    }

    public function test_filter_get_active_state_contextid_parameter() {
        $this->resetAfterTest();

        filter_set_global_state('glossary', TEXTFILTER_ON);
        // Using system context by default.
        $active = filter_get_active_state('glossary');
        $this->assertEquals($active, TEXTFILTER_ON);

        $systemcontext = context_system::instance();
        // Passing $systemcontext object.
        $active = filter_get_active_state('glossary', $systemcontext);
        $this->assertEquals($active, TEXTFILTER_ON);

        // Passing $systemcontext id.
        $active = filter_get_active_state('glossary', $systemcontext->id);
        $this->assertEquals($active, TEXTFILTER_ON);

        // Not system context.
        filter_set_local_state('glossary', '123', TEXTFILTER_ON);
        $active = filter_get_active_state('glossary', '123');
        $this->assertEquals($active, TEXTFILTER_ON);
    }

    public function test_filter_get_active_state_filtername_parameter() {
        $this->resetAfterTest();

        filter_set_global_state('glossary', TEXTFILTER_ON);
        // Using full filtername.
        $active = filter_get_active_state('filter/glossary');
        $this->assertEquals($active, TEXTFILTER_ON);

        // Wrong filtername.
        $this->expectException('coding_exception');
        $active = filter_get_active_state('mod/glossary');
    }

    public function test_filter_get_active_state_after_change() {
        $this->resetAfterTest();

        filter_set_global_state('glossary', TEXTFILTER_ON);
        $systemcontextid = context_system::instance()->id;
        $active = filter_get_active_state('glossary', $systemcontextid);
        $this->assertEquals($active, TEXTFILTER_ON);

        filter_set_global_state('glossary', TEXTFILTER_OFF);
        $systemcontextid = context_system::instance()->id;
        $active = filter_get_active_state('glossary', $systemcontextid);
        $this->assertEquals($active, TEXTFILTER_OFF);

        filter_set_global_state('glossary', TEXTFILTER_DISABLED);
        $systemcontextid = context_system::instance()->id;
        $active = filter_get_active_state('glossary', $systemcontextid);
        $this->assertEquals($active, TEXTFILTER_DISABLED);
    }

    public function test_filter_get_globally_enabled_default() {
        $this->resetAfterTest();
        $enabledfilters = filter_get_globally_enabled();
        $this->assertArrayNotHasKey('glossary', $enabledfilters);
    }

    public function test_filter_get_globally_enabled_after_change() {
        $this->resetAfterTest();
        filter_set_global_state('glossary', TEXTFILTER_ON);
        $enabledfilters = filter_get_globally_enabled();
        $this->assertArrayHasKey('glossary', $enabledfilters);
    }

    public function test_filter_get_globally_enabled_filters_with_config() {
        $this->resetAfterTest();
        $this->remove_all_filters_from_config(); // Remove all filters.
        [
            'syscontext' => $syscontext,
            'childcontext' => $childcontext
        ] = $this->setup_available_in_context_tests();
        $this->remove_all_filters_from_config(); // Remove all filters.

        // Set few filters.
        filter_set_global_state('one', TEXTFILTER_ON);
        filter_set_global_state('three', TEXTFILTER_OFF, -1);
        filter_set_global_state('two', TEXTFILTER_DISABLED);

        // Set global config.
        filter_set_local_config('one', $syscontext->id, 'test1a', 'In root');
        filter_set_local_config('one', $syscontext->id, 'test1b', 'In root');
        filter_set_local_config('two', $syscontext->id, 'test2a', 'In root');
        filter_set_local_config('two', $syscontext->id, 'test2b', 'In root');

        // Set child config.
        filter_set_local_config('one', $childcontext->id, 'test1a', 'In child');
        filter_set_local_config('one', $childcontext->id, 'test1b', 'In child');
        filter_set_local_config('two', $childcontext->id, 'test2a', 'In child');
        filter_set_local_config('two', $childcontext->id, 'test2b', 'In child');
        filter_set_local_config('three', $childcontext->id, 'test3a', 'In child');
        filter_set_local_config('three', $childcontext->id, 'test3b', 'In child');

        // Check.
        $actual = filter_get_globally_enabled_filters_with_config();
        $this->assertCount(2, $actual);
        $this->assertEquals(['three', 'one'], array_keys($actual));     // Checks sortorder.
        $this->assertArrayHasKey('one', $actual);
        $this->assertArrayNotHasKey('two', $actual);
        $this->assertArrayHasKey('three', $actual);
        $this->assertEquals(['test1a' => 'In root', 'test1b' => 'In root'], $actual['one']);
        $this->assertEquals([], $actual['three']);
    }
}

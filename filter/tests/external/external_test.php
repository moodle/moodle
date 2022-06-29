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
 * External filter functions unit tests.
 *
 * @package    core_filters
 * @category   external
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.4
 */

namespace core_filters\external;

use externallib_advanced_testcase;
use core_filters\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External filter functions unit tests.
 *
 * @package    core_filters
 * @category   external
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.4
 */
class external_test extends externallib_advanced_testcase {

    /**
     * Test get_available_in_context_system
     */
    public function test_get_available_in_context_system() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('moodle_exception');
        external::get_available_in_context(array(array('contextlevel' => 'system', 'instanceid' => 0)));
    }

    /**
     * Test get_available_in_context_category
     */
    public function test_get_available_in_context_category() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $category = self::getDataGenerator()->create_category();

        // Get all filters and disable them all globally.
        $allfilters = filter_get_all_installed();
        foreach ($allfilters as $filter => $filtername) {
            filter_set_global_state($filter, TEXTFILTER_DISABLED);
        }

        $result = external::get_available_in_context(array(array('contextlevel' => 'coursecat', 'instanceid' => $category->id)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertEmpty($result['filters']); // No filters, all disabled.
        $this->assertEmpty($result['warnings']);

        // Enable one filter at global level.
        reset($allfilters);
        $firstfilter = key($allfilters);
        filter_set_global_state($firstfilter, TEXTFILTER_ON);

        $result = external::get_available_in_context(array(array('contextlevel' => 'coursecat', 'instanceid' => $category->id)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertEquals($firstfilter, $result['filters'][0]['filter']); // OK, the filter is enabled.
        $this->assertEquals(TEXTFILTER_INHERIT, $result['filters'][0]['localstate']); // Inherits the parent context status.
        $this->assertEquals(TEXTFILTER_ON, $result['filters'][0]['inheritedstate']); // In the parent context is available.

        // Set off the same filter at local context level.
        filter_set_local_state($firstfilter, \context_coursecat::instance($category->id)->id, TEXTFILTER_OFF);
        $result = external::get_available_in_context(array(array('contextlevel' => 'coursecat', 'instanceid' => $category->id)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertEquals($firstfilter, $result['filters'][0]['filter']); // OK, the filter is enabled globally.
        $this->assertEquals(TEXTFILTER_OFF, $result['filters'][0]['localstate']); // It is not available in this context.
        $this->assertEquals(TEXTFILTER_ON, $result['filters'][0]['inheritedstate']); // In the parent context is available.
    }

    /**
     * Test get_available_in_context_course
     */
    public function test_get_available_in_context_course() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = self::getDataGenerator()->create_course();

        // Get all filters and disable them all globally.
        $allfilters = filter_get_all_installed();
        foreach ($allfilters as $filter => $filtername) {
            filter_set_global_state($filter, TEXTFILTER_DISABLED);
        }

        $result = external::get_available_in_context(array(array('contextlevel' => 'course', 'instanceid' => $course->id)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertEmpty($result['filters']); // No filters, all disabled at global level.
        $this->assertEmpty($result['warnings']);

        // Enable one filter at global level.
        reset($allfilters);
        $firstfilter = key($allfilters);
        filter_set_global_state($firstfilter, TEXTFILTER_ON);

        $result = external::get_available_in_context(array(array('contextlevel' => 'course', 'instanceid' => $course->id)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertEquals($firstfilter, $result['filters'][0]['filter']); // OK, the filter is enabled.
        $this->assertEquals(TEXTFILTER_INHERIT, $result['filters'][0]['localstate']); // Inherits the parent context status.
        $this->assertEquals(TEXTFILTER_ON, $result['filters'][0]['inheritedstate']); // In the parent context is available.

        // Set off the same filter at local context level.
        filter_set_local_state($firstfilter, \context_course::instance($course->id)->id, TEXTFILTER_OFF);
        $result = external::get_available_in_context(array(array('contextlevel' => 'course', 'instanceid' => $course->id)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertEquals($firstfilter, $result['filters'][0]['filter']); // OK, the filter is enabled globally.
        $this->assertEquals(TEXTFILTER_OFF, $result['filters'][0]['localstate']); // It is not available in this context.
        $this->assertEquals(TEXTFILTER_ON, $result['filters'][0]['inheritedstate']); // In the parent context is available.
    }

    /**
     * Test get_available_in_context_module
     */
    public function test_get_available_in_context_module() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create one activity.
        $course = self::getDataGenerator()->create_course();
        $forum = self::getDataGenerator()->create_module('forum', (object) array('course' => $course->id));

        // Get all filters and disable them all globally.
        $allfilters = filter_get_all_installed();
        foreach ($allfilters as $filter => $filtername) {
            filter_set_global_state($filter, TEXTFILTER_DISABLED);
        }

        $result = external::get_available_in_context(array(array('contextlevel' => 'module', 'instanceid' => $forum->cmid)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertEmpty($result['filters']); // No filters, all disabled at global level.
        $this->assertEmpty($result['warnings']);

        // Enable one filter at global level.
        reset($allfilters);
        $firstfilter = key($allfilters);
        filter_set_global_state($firstfilter, TEXTFILTER_ON);

        $result = external::get_available_in_context(array(array('contextlevel' => 'module', 'instanceid' => $forum->cmid)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertEquals($firstfilter, $result['filters'][0]['filter']); // OK, the filter is enabled.
        $this->assertEquals(TEXTFILTER_INHERIT, $result['filters'][0]['localstate']); // Inherits the parent context status.
        $this->assertEquals(TEXTFILTER_ON, $result['filters'][0]['inheritedstate']); // In the parent context is available.

        // Set off the same filter at local context level.
        filter_set_local_state($firstfilter, \context_module::instance($forum->cmid)->id, TEXTFILTER_OFF);
        $result = external::get_available_in_context(array(array('contextlevel' => 'module', 'instanceid' => $forum->cmid)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertEquals($firstfilter, $result['filters'][0]['filter']); // OK, the filter is enabled globally.
        $this->assertEquals(TEXTFILTER_OFF, $result['filters'][0]['localstate']); // It is not available in this context.
        $this->assertEquals(TEXTFILTER_ON, $result['filters'][0]['inheritedstate']); // In the parent context is available.

        // Try user without permission, warning expected.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $result = external::get_available_in_context(array(array('contextlevel' => 'module', 'instanceid' => $forum->cmid)));
        $result = \external_api::clean_returnvalue(external::get_available_in_context_returns(), $result);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('context', $result['warnings'][0]['item']);
        $this->assertEquals($forum->cmid, $result['warnings'][0]['itemid']);
    }
}

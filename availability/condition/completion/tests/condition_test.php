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
 * Unit tests for the completion condition.
 *
 * @package availability_completion
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use availability_completion\condition;

global $CFG;
require_once($CFG->libdir . '/completionlib.php');

/**
 * Unit tests for the completion condition.
 *
 * @package availability_completion
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability_completion_condition_testcase extends advanced_testcase {
    /**
     * Load required classes.
     */
    public function setUp() {
        // Load the mock info class so that it can be used.
        global $CFG;
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info.php');
    }

    /**
     * Tests constructing and using condition as part of tree.
     */
    public function test_in_tree() {
        global $USER, $CFG;
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create course with completion turned on and a Page.
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(array('enablecompletion' => 1));
        $page = $generator->get_plugin_generator('mod_page')->create_instance(
                array('course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL));

        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($page->cmid);
        $info = new \core_availability\mock_info($course, $USER->id);

        $structure = (object)array('op' => '|', 'show' => true, 'c' => array(
                (object)array('type' => 'completion', 'cm' => (int)$cm->id,
                'e' => COMPLETION_COMPLETE)));
        $tree = new \core_availability\tree($structure);

        // Initial check (user has not completed activity).
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertFalse($result->is_available());

        // Mark activity complete.
        $completion = new completion_info($course);
        $completion->update_state($cm, COMPLETION_COMPLETE);

        // Now it's true!
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertTrue($result->is_available());
    }

    /**
     * Tests the constructor including error conditions. Also tests the
     * string conversion feature (intended for debugging only).
     */
    public function test_constructor() {
        // No parameters.
        $structure = new stdClass();
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Missing or invalid ->cm', $e->getMessage());
        }

        // Invalid $cm.
        $structure->cm = 'hello';
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Missing or invalid ->cm', $e->getMessage());
        }

        // Missing $e.
        $structure->cm = 42;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Missing or invalid ->e', $e->getMessage());
        }

        // Invalid $e.
        $structure->e = 99;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Missing or invalid ->e', $e->getMessage());
        }

        // Successful construct & display with all different expected values.
        $structure->e = COMPLETION_COMPLETE;
        $cond = new condition($structure);
        $this->assertEquals('{completion:cm42 COMPLETE}', (string)$cond);

        $structure->e = COMPLETION_COMPLETE_PASS;
        $cond = new condition($structure);
        $this->assertEquals('{completion:cm42 COMPLETE_PASS}', (string)$cond);

        $structure->e = COMPLETION_COMPLETE_FAIL;
        $cond = new condition($structure);
        $this->assertEquals('{completion:cm42 COMPLETE_FAIL}', (string)$cond);

        $structure->e = COMPLETION_INCOMPLETE;
        $cond = new condition($structure);
        $this->assertEquals('{completion:cm42 INCOMPLETE}', (string)$cond);
    }

    /**
     * Tests the save() function.
     */
    public function test_save() {
        $structure = (object)array('cm' => 42, 'e' => COMPLETION_COMPLETE);
        $cond = new condition($structure);
        $structure->type = 'completion';
        $this->assertEquals($structure, $cond->save());
    }

    /**
     * Tests the is_available and get_description functions.
     */
    public function test_usage() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $this->resetAfterTest();

        // Create course with completion turned on.
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(array('enablecompletion' => 1));
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $this->setUser($user);

        // Create a Page with manual completion for basic checks.
        $page = $generator->get_plugin_generator('mod_page')->create_instance(
                array('course' => $course->id, 'name' => 'Page!',
                'completion' => COMPLETION_TRACKING_MANUAL));

        // Create an assignment - we need to have something that can be graded
        // so as to test the PASS/FAIL states. Set it up to be completed based
        // on its grade item.
        $assignrow = $this->getDataGenerator()->create_module('assign', array(
                'course' => $course->id, 'name' => 'Assign!',
                'completion' => COMPLETION_TRACKING_AUTOMATIC));
        $DB->set_field('course_modules', 'completiongradeitemnumber', 0,
                array('id' => $assignrow->cmid));
        $assign = new assign(context_module::instance($assignrow->cmid), false, false);

        // Get basic details.
        $modinfo = get_fast_modinfo($course);
        $pagecm = $modinfo->get_cm($page->cmid);
        $assigncm = $assign->get_course_module();
        $info = new \core_availability\mock_info($course, $user->id);

        // COMPLETE state (false), positive and NOT.
        $cond = new condition((object)array(
                'cm' => (int)$pagecm->id, 'e' => COMPLETION_COMPLETE));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Page!.*is marked complete~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // INCOMPLETE state (true).
        $cond = new condition((object)array(
                'cm' => (int)$pagecm->id, 'e' => COMPLETION_INCOMPLETE));
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Page!.*is marked complete~', $information);

        // Mark page complete.
        $completion = new completion_info($course);
        $completion->update_state($pagecm, COMPLETION_COMPLETE);

        // COMPLETE state (true).
        $cond = new condition((object)array(
                'cm' => (int)$pagecm->id, 'e' => COMPLETION_COMPLETE));
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Page!.*is incomplete~', $information);

        // INCOMPLETE state (false).
        $cond = new condition((object)array(
                'cm' => (int)$pagecm->id, 'e' => COMPLETION_INCOMPLETE));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Page!.*is incomplete~', $information);
        $this->assertTrue($cond->is_available(true, $info,
                true, $user->id));

        // We are going to need the grade item so that we can get pass/fails.
        $gradeitem = $assign->get_grade_item();
        grade_object::set_properties($gradeitem, array('gradepass' => 50.0));
        $gradeitem->update();

        // With no grade, it should return true for INCOMPLETE and false for
        // the other three.
        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_INCOMPLETE));
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Check $information for COMPLETE_PASS and _FAIL as we haven't yet.
        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_PASS));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Assign!.*is complete and passed~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_FAIL));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Assign!.*is complete and failed~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Change the grade to be complete and failed.
        self::set_grade($assignrow, $user->id, 40);

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_INCOMPLETE));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE));
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_PASS));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Assign!.*is complete and passed~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_FAIL));
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Assign!.*is not complete and failed~', $information);

        // Now change it to pass.
        self::set_grade($assignrow, $user->id, 60);

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_INCOMPLETE));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE));
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_PASS));
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Assign!.*is not complete and passed~', $information);

        $cond = new condition((object)array(
                'cm' => (int)$assigncm->id, 'e' => COMPLETION_COMPLETE_FAIL));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~Assign!.*is complete and failed~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Simulate deletion of an activity by using an invalid cmid. These
        // conditions always fail, regardless of NOT flag or INCOMPLETE.
        $cond = new condition((object)array(
                'cm' => ($assigncm->id + 100), 'e' => COMPLETION_COMPLETE));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $course);
        $this->assertRegExp('~(Missing activity).*is marked complete~', $information);
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $cond = new condition((object)array(
                'cm' => ($assigncm->id + 100), 'e' => COMPLETION_INCOMPLETE));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
    }

    /**
     * Tests completion_value_used static function.
     */
    public function test_completion_value_used() {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Create course with completion turned on and some sections.
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('numsections' => 1, 'enablecompletion' => 1),
                array('createsections' => true));
        availability_completion\condition::wipe_static_cache();

        // Create three pages with manual completion.
        $page1 = $generator->get_plugin_generator('mod_page')->create_instance(
                array('course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL));
        $page2 = $generator->get_plugin_generator('mod_page')->create_instance(
                array('course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL));
        $page3 = $generator->get_plugin_generator('mod_page')->create_instance(
                array('course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL));

        // Set up page3 to depend on page1, and section1 to depend on page2.
        $DB->set_field('course_modules', 'availability',
                '{"op":"|","show":true,"c":[' .
                '{"type":"completion","e":1,"cm":' . $page1->cmid . '}]}',
                array('id' => $page3->cmid));
        $DB->set_field('course_sections', 'availability',
                '{"op":"|","show":true,"c":[' .
                '{"type":"completion","e":1,"cm":' . $page2->cmid . '}]}',
                array('course' => $course->id, 'section' => 1));

        // Now check: nothing depends on page3 but something does on the others.
        $this->assertTrue(availability_completion\condition::completion_value_used(
                $course, $page1->cmid));
        $this->assertTrue(availability_completion\condition::completion_value_used(
                $course, $page2->cmid));
        $this->assertFalse(availability_completion\condition::completion_value_used(
                $course, $page3->cmid));
    }

    /**
     * Updates the grade of a user in the given assign module instance.
     *
     * @param stdClass $assignrow Assignment row from database
     * @param int $userid User id
     * @param float $grade Grade
     */
    protected static function set_grade($assignrow, $userid, $grade) {
        $grades = array();
        $grades[$userid] = (object)array(
                'rawgrade' => $grade, 'userid' => $userid);
        $assignrow->cmidnumber = null;
        assign_grade_item_update($assignrow, $grades);
    }

    /**
     * Tests the update_dependency_id() function.
     */
    public function test_update_dependency_id() {
        $cond = new condition((object)array(
                'cm' => 123, 'e' => COMPLETION_COMPLETE));
        $this->assertFalse($cond->update_dependency_id('frogs', 123, 456));
        $this->assertFalse($cond->update_dependency_id('course_modules', 12, 34));
        $this->assertTrue($cond->update_dependency_id('course_modules', 123, 456));
        $after = $cond->save();
        $this->assertEquals(456, $after->cm);
    }
}

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
 * Cohort enrolment sync functional test.
 *
 * @package    enrol_cohort
 * @category   test
 * @copyright  2015 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->dirroot.'/group/lib.php');

/**
 * Contains tests for the cohort library.
 *
 * @package   enrol_cohort
 * @copyright 2015 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_cohort_lib_testcase extends advanced_testcase {

    /**
     * Test that a new group with the name of the cohort is created.
     */
    public function test_enrol_cohort_create_new_group() {
        global $DB;
        $this->resetAfterTest();
        // Create a category.
        $category = $this->getDataGenerator()->create_category();
        // Create two courses.
        $course = $this->getDataGenerator()->create_course(array('category' => $category->id));
        $course2 = $this->getDataGenerator()->create_course(array('category' => $category->id));
        // Create a cohort.
        $cohort = $this->getDataGenerator()->create_cohort(array('context' => context_coursecat::instance($category->id)->id));
        // Run the function.
        $groupid = enrol_cohort_create_new_group($course->id, $cohort->id);
        // Check the results.
        $group = $DB->get_record('groups', array('id' => $groupid));
        // The group name should match the cohort name.
        $this->assertEquals($cohort->name . ' cohort', $group->name);
        // Group course id should match the course id.
        $this->assertEquals($course->id, $group->courseid);

        // Create a group that will have the same name as the cohort.
        $groupdata = new stdClass();
        $groupdata->courseid = $course2->id;
        $groupdata->name = $cohort->name . ' cohort';
        groups_create_group($groupdata);
        // Create a group for the cohort in course 2.
        $groupid = enrol_cohort_create_new_group($course2->id, $cohort->id);
        $groupinfo = $DB->get_record('groups', array('id' => $groupid));
        // Check that the group name has been changed.
        $this->assertEquals($cohort->name . ' cohort (2)', $groupinfo->name);

        // Create another group that will have the same name as a generated cohort.
        $groupdata = new stdClass();
        $groupdata->courseid = $course2->id;
        $groupdata->name = $cohort->name . ' cohort (2)';
        groups_create_group($groupdata);
        // Create a group for the cohort in course 2.
        $groupid = enrol_cohort_create_new_group($course2->id, $cohort->id);
        $groupinfo = $DB->get_record('groups', array('id' => $groupid));
        // Check that the group name has been changed.
        $this->assertEquals($cohort->name . ' cohort (3)', $groupinfo->name);

    }

    /**
     * Test for getting user enrolment actions.
     */
    public function test_get_user_enrolment_actions() {
        global $CFG, $PAGE;
        $this->resetAfterTest();

        // Set page URL to prevent debugging messages.
        $PAGE->set_url('/enrol/editinstance.php');

        $pluginname = 'cohort';

        // Only enable the cohort enrol plugin.
        $CFG->enrol_plugins_enabled = $pluginname;

        $generator = $this->getDataGenerator();

        // Get the enrol plugin.
        $plugin = enrol_get_plugin($pluginname);

        // Create a course.
        $course = $generator->create_course();
        // Enable this enrol plugin for the course.
        $plugin->add_instance($course);

        // Create a student.
        $student = $generator->create_user();
        // Enrol the student to the course.
        $generator->enrol_user($student->id, $course->id, 'student', $pluginname);

        // Teachers don't have enrol/cohort:unenrol capability by default. Login as admin for simplicity.
        $this->setAdminUser();
        require_once($CFG->dirroot . '/enrol/locallib.php');
        $manager = new course_enrolment_manager($PAGE, $course);

        $userenrolments = $manager->get_user_enrolments($student->id);
        $this->assertCount(1, $userenrolments);

        $ue = reset($userenrolments);
        $actions = $plugin->get_user_enrolment_actions($manager, $ue);
        // Cohort-sync has no enrol actions for active students.
        $this->assertCount(0, $actions);

        // Enrol actions for a suspended student.
        // Suspend the student.
        $ue->status = ENROL_USER_SUSPENDED;

        $actions = $plugin->get_user_enrolment_actions($manager, $ue);
        // Cohort-sync has enrol actions for suspended students -- unenrol.
        $this->assertCount(1, $actions);
    }
}

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

namespace enrol_cohort;

use core\plugininfo\enrol;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->dirroot.'/group/lib.php');

/**
 * Contains tests for the cohort library.
 *
 * @package   enrol_cohort
 * @category  test
 * @copyright 2015 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends \advanced_testcase {

    /**
     * Test that a new group with the name of the cohort is created.
     */
    public function test_enrol_cohort_create_new_group(): void {
        global $DB;
        $this->resetAfterTest();
        // Create a category.
        $category = $this->getDataGenerator()->create_category();
        // Create two courses.
        $course = $this->getDataGenerator()->create_course(array('category' => $category->id));
        $course2 = $this->getDataGenerator()->create_course(array('category' => $category->id));
        // Create a cohort.
        $cohort = $this->getDataGenerator()->create_cohort(array('context' => \context_coursecat::instance($category->id)->id));
        // Run the function.
        $groupid = enrol_cohort_create_new_group($course->id, $cohort->id);
        // Check the results.
        $group = $DB->get_record('groups', array('id' => $groupid));
        // The group name should match the cohort name.
        $this->assertEquals($cohort->name . ' cohort', $group->name);
        // Group course id should match the course id.
        $this->assertEquals($course->id, $group->courseid);

        // Create a group that will have the same name as the cohort.
        $groupdata = new \stdClass();
        $groupdata->courseid = $course2->id;
        $groupdata->name = $cohort->name . ' cohort';
        groups_create_group($groupdata);
        // Create a group for the cohort in course 2.
        $groupid = enrol_cohort_create_new_group($course2->id, $cohort->id);
        $groupinfo = $DB->get_record('groups', array('id' => $groupid));
        // Check that the group name has been changed.
        $this->assertEquals($cohort->name . ' cohort (2)', $groupinfo->name);

        // Create another group that will have the same name as a generated cohort.
        $groupdata = new \stdClass();
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
    public function test_get_user_enrolment_actions(): void {
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
        $manager = new \course_enrolment_manager($PAGE, $course);

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

    public function test_enrol_cohort_unenrolaction_suspend_only(): void {
        global $CFG, $DB, $PAGE;
        $this->resetAfterTest();

        $trace = new \null_progress_trace();

        $cohortplugin = enrol_get_plugin('cohort');
        $cohortplugin->set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPEND);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->assertNotEmpty($studentrole);

        // Setup a test course.
        $course = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $cohort = $this->getDataGenerator()->create_cohort();

        $cohortplugin->add_instance($course, ['customint1' => $cohort->id,
            'roleid' => $studentrole->id]
        );

        cohort_add_member($cohort->id, $user1->id);
        cohort_add_member($cohort->id, $user2->id);
        cohort_add_member($cohort->id, $user3->id);
        cohort_add_member($cohort->id, $user4->id);

        // Test sync.
        enrol_cohort_sync($trace, $course->id);

        // All users should be enrolled.
        $this->assertTrue(is_enrolled(\context_course::instance($course->id), $user1));
        $this->assertTrue(is_enrolled(\context_course::instance($course->id), $user2));
        $this->assertTrue(is_enrolled(\context_course::instance($course->id), $user3));
        $this->assertTrue(is_enrolled(\context_course::instance($course->id), $user4));

        // Remove cohort member.
        cohort_remove_member($cohort->id, $user1->id);
        $this->assertTrue(is_enrolled(\context_course::instance($course->id), $user1));

        // Run the sync again.
        enrol_cohort_sync($trace, $course->id);

        $enrolid = $DB->get_field('enrol', 'id', ['enrol' => 'cohort', 'customint1' => $cohort->id]);
        $ue = $DB->get_record('user_enrolments', ['enrolid' => $enrolid, 'userid' => $user1->id]);

        // Check user is suspended.
        $this->assertEquals($ue->status, ENROL_USER_SUSPENDED);
        // Check that user4 still have student role.
        $userrole = $DB->get_record('role_assignments', ['userid' => $user1->id]);
        $this->assertNotEmpty($userrole);
        $this->assertEquals($studentrole->id, $userrole->roleid);

        // Delete the cohort.
        cohort_delete_cohort($cohort);

        // Run the sync again.
        enrol_cohort_sync($trace, $course->id);

        $ue = $DB->get_records('user_enrolments', ['enrolid' => $enrolid], '', 'userid, status, enrolid');

        // Check users are suspended.
        $this->assertEquals($ue[$user2->id]->status, ENROL_USER_SUSPENDED);
        $this->assertEquals($ue[$user3->id]->status, ENROL_USER_SUSPENDED);
        $this->assertEquals($ue[$user4->id]->status, ENROL_USER_SUSPENDED);

        // Check that users still have student role.
        $usersrole = $DB->get_records('role_assignments', ['itemid' => $enrolid], '', 'userid, roleid');
        $this->assertNotEmpty($usersrole);
        $this->assertEquals($studentrole->id, $usersrole[$user2->id]->roleid);
        $this->assertEquals($studentrole->id, $usersrole[$user3->id]->roleid);
        $this->assertEquals($studentrole->id, $usersrole[$user4->id]->roleid);
    }

    /**
     * Test the behaviour of validate_plugin_data_context().
     *
     * @covers ::validate_plugin_data_context
     */
    public function test_validate_plugin_data_context(): void {
        $this->resetAfterTest();

        $cohortplugin = enrol_get_plugin('cohort');

        $cat = $this->getDataGenerator()->create_category();
        $cat1 = $this->getDataGenerator()->create_category(['parent' => $cat->id]);
        $cat2 = $this->getDataGenerator()->create_category(['parent' => $cat->id]);

        $course = $this->getDataGenerator()->create_course(['category' => $cat1->id, 'shortname' => 'ANON']);

        $cohort1 = $this->getDataGenerator()->create_cohort([
            'contextid' => \context_coursecat::instance($cat1->id)->id,
            'idnumber' => 'one',
        ]);
        $cohort2 = $this->getDataGenerator()->create_cohort([
            'contextid' => \context_coursecat::instance($cat2->id)->id,
            'idnumber' => 'two',
        ]);

        $enrolmentdata = [
            'customint1' => $cohort2->id,
            'cohortidnumber' => $cohort2->idnumber,
        ];
        $error = $cohortplugin->validate_plugin_data_context($enrolmentdata, $course->id);
        $this->assertInstanceOf('lang_string', $error);
        $this->assertEquals('contextcohortnotallowed', $error->get_identifier());

        $enrolmentdata = [
            'customint1' => $cohort1->id,
            'cohortidnumber' => $cohort1->idnumber,
            'courseid' => $course->id,
            'id' => null,
            'status' => ENROL_INSTANCE_ENABLED,
        ];
        $enrolmentdata = $cohortplugin->fill_enrol_custom_fields($enrolmentdata, $course->id);
        $error = $cohortplugin->validate_plugin_data_context($enrolmentdata, $course->id);
        $this->assertNull($error);
    }

    /**
     * Test the behaviour of fill_enrol_custom_fields().
     *
     * @covers ::fill_enrol_custom_fields
     */
    public function test_fill_enrol_custom_fields(): void {
        $this->resetAfterTest();

        $cohortplugin = enrol_get_plugin('cohort');

        $cat = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $cat->id, 'shortname' => 'ANON']);
        $cohort = $this->getDataGenerator()->create_cohort([
            'contextid' => \context_coursecat::instance($cat->id)->id,
            'idnumber' => 'one',
        ]);
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $enrolmentdata['cohortidnumber'] = $cohort->idnumber;
        $enrolmentdata = $cohortplugin->fill_enrol_custom_fields($enrolmentdata, $course->id);
        $this->assertArrayHasKey('customint1', $enrolmentdata);
        $this->assertEquals($cohort->id, $enrolmentdata['customint1']);
        $this->assertArrayNotHasKey('customint2', $enrolmentdata);

        $enrolmentdata['cohortidnumber'] = 'notexist';
        $enrolmentdata = $cohortplugin->fill_enrol_custom_fields($enrolmentdata, $course->id);
        $this->assertArrayHasKey('customint1', $enrolmentdata);
        $this->assertFalse($enrolmentdata['customint1']);
        $this->assertArrayNotHasKey('customint2', $enrolmentdata);

        $enrolmentdata['cohortidnumber'] = $cohort->idnumber;

        $enrolmentdata['addtogroup'] = COHORT_NOGROUP;
        $enrolmentdata = $cohortplugin->fill_enrol_custom_fields($enrolmentdata, $course->id);
        $this->assertArrayHasKey('customint1', $enrolmentdata);
        $this->assertEquals($cohort->id, $enrolmentdata['customint1']);
        $this->assertArrayHasKey('customint2', $enrolmentdata);
        $this->assertEquals(COHORT_NOGROUP, $enrolmentdata['customint2']);

        unset($enrolmentdata['addtogroup']);
        $enrolmentdata['groupname'] = $group->name;
        $enrolmentdata = $cohortplugin->fill_enrol_custom_fields($enrolmentdata, $course->id);
        $this->assertArrayHasKey('customint1', $enrolmentdata);
        $this->assertEquals($cohort->id, $enrolmentdata['customint1']);
        $this->assertArrayHasKey('customint2', $enrolmentdata);
        $this->assertEquals($group->id, $enrolmentdata['customint2']);

        $enrolmentdata['groupname'] = 'notexist';
        $enrolmentdata = $cohortplugin->fill_enrol_custom_fields($enrolmentdata, $course->id);
        $this->assertArrayHasKey('customint1', $enrolmentdata);
        $this->assertEquals($cohort->id, $enrolmentdata['customint1']);
        $this->assertArrayHasKey('customint2', $enrolmentdata);
        $this->assertFalse($enrolmentdata['customint2']);
    }

    /**
     * Test the behaviour of validate_enrol_plugin_data().
     *
     * @covers ::validate_enrol_plugin_data
     */
    public function test_validate_enrol_plugin_data(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $cat = $this->getDataGenerator()->create_category();
        $cat1 = $this->getDataGenerator()->create_category(['parent' => $cat->id]);
        $cat2 = $this->getDataGenerator()->create_category(['parent' => $cat->id]);

        $course = $this->getDataGenerator()->create_course(['category' => $cat1->id, 'shortname' => 'ANON']);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Group 1']);

        $cohort1 = $this->getDataGenerator()->create_cohort([
            'contextid' => \context_coursecat::instance($cat1->id)->id,
            'idnumber' => 'one',
        ]);
        $cohort2 = $this->getDataGenerator()->create_cohort([
            'contextid' => \context_coursecat::instance($cat2->id)->id,
            'idnumber' => 'two',
        ]);

        enrol::enable_plugin('cohort', false);

        $cohortplugin = enrol_get_plugin('cohort');

        // Plugin is disabled in system and cohort name is missing in csv.
        $enrolmentdata = [];
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata);
        $this->assertArrayHasKey('plugindisabled', $errors);
        $this->assertArrayHasKey('missingmandatoryfields', $errors);

        enrol::enable_plugin('cohort', true);

        // Unknown cohort idnumber and missing role.
        $enrolmentdata['cohortidnumber'] = 'test';
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata);
        $this->assertArrayHasKey('missingmandatoryfields', $errors);
        $this->assertArrayHasKey('unknowncohort', $errors);

        // Non-valid 'addtogroup' option.
        $enrolmentdata['cohortidnumber'] = $cohort1->idnumber;
        $enrolmentdata['addtogroup'] = 2;
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata, $course->id);
        $this->assertArrayHasKey('erroraddtogroup', $errors);

        // Options 'addtogroup' and 'groupname' are not allowed together.
        $enrolmentdata['addtogroup'] = 0;
        $enrolmentdata['groupname'] = 'test';
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata, $course->id);
        $this->assertArrayHasKey('erroraddtogroupgroupname', $errors);

        // Cohort is not allowed on a given category context.
        $enrolmentdata['cohortidnumber'] = $cohort2->idnumber;
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata, $course->id);
        $this->assertArrayHasKey('contextnotallowed', $errors);

        // Group does not exist.
        unset($enrolmentdata['addtogroup']);
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata, $course->id);
        $this->assertArrayHasKey('errorinvalidgroup', $errors);

        // Unknown role.
        $enrolmentdata['role'] = 'test';
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata, $course->id);
        $this->assertArrayHasKey('unknownrole', $errors);

        // Valid data when trying to create a group.
        $enrolmentdata['cohortidnumber'] = $cohort1->idnumber;
        $enrolmentdata['role'] = 'student';
        $enrolmentdata['addtogroup'] = 1;
        unset($enrolmentdata['groupname']);
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata, $course->id);
        $this->assertEmpty($errors);

        // Valid data when trying to add to existing group.
        $enrolmentdata['groupname'] = $group1->name;
        unset($enrolmentdata['addtogroup']);
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata, $course->id);
        $this->assertEmpty($errors);

        // Valid data when trying without group mode.
        $enrolmentdata['addtogroup'] = 0;
        unset($enrolmentdata['groupname']);
        $errors = $cohortplugin->validate_enrol_plugin_data($enrolmentdata, $course->id);
        $this->assertEmpty($errors);
    }

    /**
     * Test the behaviour of find_instance().
     *
     * @covers ::find_instance
     */
    public function test_find_instance(): void {
        global $DB;
        $this->resetAfterTest();

        $cat = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $cat->id, 'shortname' => 'ANON']);

        $cohort1 = $this->getDataGenerator()->create_cohort([
            'contextid' => \context_coursecat::instance($cat->id)->id,
            'idnumber' => 'one',
        ]);
        $cohort2 = $this->getDataGenerator()->create_cohort([
            'contextid' => \context_coursecat::instance($cat->id)->id,
            'idnumber' => 'two',
        ]);

        $cohort3 = $this->getDataGenerator()->create_cohort([
            'contextid' => \context_coursecat::instance($cat->id)->id,
            'idnumber' => 'three',
        ]);

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $managerrole = $DB->get_record('role', ['shortname' => 'manager']);
        $cohortplugin = enrol_get_plugin('cohort');

        // Add three cohort enrol instances.
        $instanceid1 = $cohortplugin->add_instance($course, ['customint1' => $cohort1->id, 'roleid' => $teacherrole->id]);
        $instanceid2 = $cohortplugin->add_instance($course, ['customint1' => $cohort2->id, 'roleid' => $managerrole->id]);
        $instanceid3 = $cohortplugin->add_instance($course, ['customint1' => $cohort2->id, 'roleid' => $studentrole->id]);

        $instance1 = $DB->get_record('enrol', ['id' => $instanceid1]);
        $instance2 = $DB->get_record('enrol', ['id' => $instanceid2]);

        $enrolmentdata = [];
        $instance = $cohortplugin->find_instance($enrolmentdata, $course->id);
        $this->assertNull($instance);

        // Unknown idnumber.
        $enrolmentdata['cohortidnumber'] = 'test';
        $instance = $cohortplugin->find_instance($enrolmentdata, $course->id);
        $this->assertNull($instance);

        // Unknown role.
        $enrolmentdata['role'] = 'test';
        $enrolmentdata['cohortidnumber'] = $cohort1->idnumber;
        $instance = $cohortplugin->find_instance($enrolmentdata, $course->id);
        $this->assertNull($instance);

        // Cohort3 instance has not matching role and cohort.
        $enrolmentdata['role'] = $teacherrole->shortname;
        $enrolmentdata['cohortidnumber'] = $cohort3->idnumber;
        $instance = $cohortplugin->find_instance($enrolmentdata, $course->id);
        $this->assertNull($instance);

        // Cohort2 instance has matching cohort, but not matching role.
        $enrolmentdata['role'] = $teacherrole->shortname;
        $enrolmentdata['cohortidnumber'] = $cohort2->idnumber;
        $instance = $cohortplugin->find_instance($enrolmentdata, $course->id);
        $this->assertNull($instance);

        $enrolmentdata['role'] = $teacherrole->shortname;
        $enrolmentdata['cohortidnumber'] = $cohort1->idnumber;
        $instance = $cohortplugin->find_instance($enrolmentdata, $course->id);
        $this->assertEquals($instance1->id, $instance->id);

        $enrolmentdata['role'] = $managerrole->shortname;
        $enrolmentdata['cohortidnumber'] = $cohort2->idnumber;
        $instance = $cohortplugin->find_instance($enrolmentdata, $course->id);
        $this->assertEquals($instance2->id, $instance->id);
    }
}

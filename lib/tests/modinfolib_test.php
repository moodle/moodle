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
 * Unit tests for lib/modinfolib.php.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Andrew Davis
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/modinfolib.php');
require_once($CFG->libdir . '/conditionlib.php');

/**
 * Unit tests for modinfolib.php
 *
 * @copyright 2012 Andrew Davis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_modinfolib_testcase extends advanced_testcase {
    public function test_section_info_properties() {
        global $DB, $CFG;

        $this->resetAfterTest();
        $oldcfgenableavailability = $CFG->enableavailability;
        $oldcfgenablecompletion = $CFG->enablecompletion;
        set_config('enableavailability', 1);
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Generate the course and pre-requisite module.
        $course = $this->getDataGenerator()->create_course(
                array('format' => 'topics',
                    'numsections' => 3,
                    'enablecompletion' => 1,
                    'groupmode' => SEPARATEGROUPS,
                    'forcegroupmode' => 0),
                array('createsections' => true));
        $coursecontext = context_course::instance($course->id);
        $prereqforum = $this->getDataGenerator()->create_module('forum',
                array('course' => $course->id),
                array('completion' => 1));

        // Generate the module and add availability conditions.
        $conditionscompletion = array($prereqforum->cmid => COMPLETION_COMPLETE);
        $conditionsgrade = array(666 => (object)array('min' => 0.4, 'max' => null, 'name' => '!missing'));
        $conditionsfield = array('email' => (object)array(
            'fieldname' => 'email',
            'operator' => 'contains',
            'value' => 'test'
        ));
        $sectiondb = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 2));
        $ci = new condition_info_section((object)array('id' => $sectiondb->id), CONDITION_MISSING_EVERYTHING);
        foreach ($conditionscompletion as $cmid => $requiredcompletion) {
            $ci->add_completion_condition($cmid, $requiredcompletion);
        }
        foreach ($conditionsgrade as $gradeid => $conditiongrade) {
            $ci->add_grade_condition($gradeid, $conditiongrade->min, $conditiongrade->max, true);
        }
        foreach ($conditionsfield as $conditionfield) {
            $ci->add_user_field_condition($conditionfield->fieldname, $conditionfield->operator, $conditionfield->value);
        }

        // Create and enrol a student.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);
        $enrolplugin = enrol_get_plugin('manual');
        $enrolinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
        $enrolplugin->enrol_user($enrolinstance, $student->id);
        $this->setUser($student);

        // Get modinfo.
        $modinfo = get_fast_modinfo($course->id);
        $si = $modinfo->get_section_info(2);

        $this->assertEquals($sectiondb->id, $si->id);
        $this->assertEquals($sectiondb->course, $si->course);
        $this->assertEquals($sectiondb->section, $si->section);
        $this->assertEquals($sectiondb->name, $si->name);
        $this->assertEquals($sectiondb->visible, $si->visible);
        $this->assertEquals($sectiondb->summary, $si->summary);
        $this->assertEquals($sectiondb->summaryformat, $si->summaryformat);
        $this->assertEquals($sectiondb->showavailability, $si->showavailability);
        $this->assertEquals($sectiondb->availablefrom, $si->availablefrom);
        $this->assertEquals($sectiondb->availableuntil, $si->availableuntil);
        $this->assertEquals($sectiondb->groupingid, $si->groupingid);
        $this->assertEquals($sectiondb->sequence, $si->sequence); // Since this section does not contain invalid modules.
        $this->assertEquals($conditionscompletion, $si->conditionscompletion);
        $this->assertEquals($conditionsgrade, $si->conditionsgrade);
        $this->assertEquals($conditionsfield, $si->conditionsfield);

        // Dynamic fields, just test that they can be retrieved (must be carefully tested in each activity type).
        $this->assertEquals(0, $si->available);
        $this->assertNotEmpty($si->availableinfo); // Lists all unmet availability conditions.
        $this->assertEquals(0, $si->uservisible);

        // Restore settings.
        set_config('enableavailability', $oldcfgenableavailability);
        set_config('enablecompletion', $oldcfgenablecompletion);
    }


    /**
     * Test is_user_access_restricted_by_group()
     *
     * The underlying groups system is more thoroughly tested in lib/tests/grouplib_test.php
     */
    public function test_is_user_access_restricted_by_group() {
        global $DB, $CFG, $USER;

        $this->resetAfterTest();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // Create a mod_assign instance.
        $assign = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id));
        $cm_info = get_fast_modinfo($course)->instances['assign'][$assign->id];

        // Create and enrol a student.
        // Enrolment is necessary for groups to work.
        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);
        $enrolplugin = enrol_get_plugin('manual');
        $enrolplugin->add_instance($course);
        $enrolinstances = enrol_get_instances($course->id, false);
        foreach ($enrolinstances as $enrolinstance) {
            if ($enrolinstance->enrol === 'manual') {
                break;
            }
        }
        $enrolplugin->enrol_user($enrolinstance, $student->id);

        // Switch to a student and reload the context info.
        $this->setUser($student);
        $cm_info = $this->refresh_cm_info($course, $assign);

        // Create up a teacher.
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);

        // Create 2 groupings.
        $grouping1 = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id, 'name' => 'grouping1'));
        $grouping2 = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id, 'name' => 'grouping2'));

        // Create 2 groups and put them in the groupings.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id, 'idnumber' => 'group1'));
        groups_assign_grouping($grouping1->id, $group1->id);
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id, 'idnumber' => 'group2'));
        groups_assign_grouping($grouping2->id, $group2->id);

        // If groups are disabled, the activity isn't restricted.
        $CFG->enablegroupmembersonly = false;
        $this->assertFalse($cm_info->is_user_access_restricted_by_group());

        // If groups are on but "group members only" is off, the activity isn't restricted.
        $CFG->enablegroupmembersonly = true;
        $cm_info->groupmembersonly = NOGROUPS;
        $this->assertFalse($cm_info->is_user_access_restricted_by_group());

        // If "group members only" is on but user is in the wrong group, the activity is restricted.
        $cm_info->groupmembersonly = SEPARATEGROUPS;
        $cm_info->groupingid = $grouping1->id;
        $this->assertTrue(groups_add_member($group2, $USER));
        $this->assertTrue($cm_info->is_user_access_restricted_by_group());

        // If the user is in the required group, the activity isn't restricted.
        groups_remove_member($group2, $USER);
        $this->assertTrue(groups_add_member($group1, $USER));
        $cm_info = $this->refresh_cm_info($course, $assign);
        $this->assertFalse($cm_info->is_user_access_restricted_by_group());

        // Switch to a teacher and reload the context info.
        $this->setUser($teacher);
        $cm_info = $this->refresh_cm_info($course, $assign);

        // If the user isn't in the required group but has 'moodle/site:accessallgroups', the activity isn't restricted.
        $this->assertTrue(has_capability('moodle/site:accessallgroups', $coursecontext));
        $this->assertFalse($cm_info->is_user_access_restricted_by_group());
    }

    /**
     * Test is_user_access_restricted_by_conditional_access()
     *
     * The underlying conditional access system is more thoroughly tested in lib/tests/conditionlib_test.php
     */
    public function test_is_user_access_restricted_by_conditional_access() {
        global $DB, $CFG;

        $this->resetAfterTest();

        // Create a course and a mod_assign instance.
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id));
        $cm_info = get_fast_modinfo($course)->instances['assign'][$assign->id];

        // Set up a teacher.
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);

        // Mark the activity as unavailable (due to unmet conditions).
        // Testing of the code that normally turns this flag on and off is done in conditionlib_test.php.
        $cm_info->available = false;
        // Set the activity to be hidden entirely if it is unavailable to the user.
        $cm_info->showavailability = CONDITION_STUDENTVIEW_HIDE;

        // If conditional availability is disabled the activity will always be unrestricted.
        $CFG->enableavailability = false;
        $this->assertFalse($cm_info->is_user_access_restricted_by_conditional_access());

        // Turn on conditional availability.
        $CFG->enableavailability = true;

        // The unavailable, hidden entirely activity should now be restricted.
        $this->assertTrue($cm_info->is_user_access_restricted_by_conditional_access());

        // If the activity is available it should not be restricted.
        $cm_info->available = true;
        $this->assertFalse($cm_info->is_user_access_restricted_by_conditional_access());

        // If the activity is unavailable and set to be greyed out it should not be restricted.
        $cm_info->available = false;
        $cm_info->showavailability = CONDITION_STUDENTVIEW_SHOW;
        $this->assertFalse($cm_info->is_user_access_restricted_by_conditional_access());

        // If the activity is unavailable and set to be hidden entirely its restricted unless user has 'moodle/course:viewhiddenactivities'.
        $cm_info->available = false;
        $cm_info->showavailability = CONDITION_STUDENTVIEW_HIDE;

        // Switch to a teacher and reload the context info.
        $this->setUser($teacher);
        $cm_info = $this->refresh_cm_info($course, $assign);

        $this->assertTrue(has_capability('moodle/course:viewhiddenactivities', $coursecontext));
        $this->assertFalse($cm_info->is_user_access_restricted_by_conditional_access());
    }

    public function test_is_user_access_restricted_by_capability() {
        global $DB;

        $this->resetAfterTest();

        // Create a course and a mod_assign instance.
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id));

        // Create and enrol a student.
        $coursecontext = context_course::instance($course->id);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);
        $enrolplugin = enrol_get_plugin('manual');
        $enrolinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
        $enrolplugin->enrol_user($enrolinstance, $student->id);
        $this->setUser($student);

        // Make sure student can see the module.
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertTrue($cm->uservisible);
        $this->assertFalse($cm->is_user_access_restricted_by_capability());

        // Prohibit student to view mod_assign for the course.
        role_change_permission($studentrole->id, $coursecontext, 'mod/assign:view', CAP_PROHIBIT);
        get_fast_modinfo($course->id, 0, true);
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertFalse($cm->uservisible);
        $this->assertTrue($cm->is_user_access_restricted_by_capability());

        // Restore permission to student to view mod_assign for the course.
        role_change_permission($studentrole->id, $coursecontext, 'mod/assign:view', CAP_INHERIT);
        get_fast_modinfo($course->id, 0, true);
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertTrue($cm->uservisible);
        $this->assertFalse($cm->is_user_access_restricted_by_capability());

        // Prohibit student to view mod_assign for the particular module.
        role_change_permission($studentrole->id, context_module::instance($cm->id), 'mod/assign:view', CAP_PROHIBIT);
        get_fast_modinfo($course->id, 0, true);
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertFalse($cm->uservisible);
        $this->assertTrue($cm->is_user_access_restricted_by_capability());

        // Check calling get_fast_modinfo() for different user:
        $this->setAdminUser();
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertTrue($cm->uservisible);
        $this->assertFalse($cm->is_user_access_restricted_by_capability());
        $cm = get_fast_modinfo($course->id, $student->id)->instances['assign'][$assign->id];
        $this->assertFalse($cm->uservisible);
        $this->assertTrue($cm->is_user_access_restricted_by_capability());
    }

    private function refresh_cm_info($course, $assign) {
        get_fast_modinfo(0, 0, true);
        return get_fast_modinfo($course)->instances['assign'][$assign->id];
    }
}

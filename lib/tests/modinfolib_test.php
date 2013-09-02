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

    public function test_cm_info_properties() {
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
        $assign = $this->getDataGenerator()->create_module('assign',
                array('course' => $course->id),
                array('idnumber' => 123,
                    'groupmode' => VISIBLEGROUPS,
                    'availablefrom' => time() + 3600,
                    'availableuntil' => time() + 5*3600));
        $ci = new condition_info((object)array('id' => $assign->cmid), CONDITION_MISSING_EVERYTHING);
        foreach ($conditionscompletion as $cmid => $requiredcompletion) {
            $ci->add_completion_condition($cmid, $requiredcompletion);
        }
        foreach ($conditionsgrade as $gradeid => $conditiongrade) {
            $ci->add_grade_condition($gradeid, $conditiongrade->min, $conditiongrade->max, true);
        }
        foreach ($conditionsfield as $conditionfield) {
            $ci->add_user_field_condition($conditionfield->fieldname, $conditionfield->operator, $conditionfield->value);
        }

        // Retrieve all related records from DB.
        $assigndb = $DB->get_record('assign', array('id' => $assign->id));
        $moduletypedb = $DB->get_record('modules', array('name' => 'assign'));
        $moduledb = $DB->get_record('course_modules', array('module' => $moduletypedb->id, 'instance' => $assign->id));
        $sectiondb = $DB->get_record('course_sections', array('id' => $moduledb->section));
        $modnamessingular = get_module_types_names(false);
        $modnamesplural = get_module_types_names(true);

        // Create and enrol a student.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);
        $enrolplugin = enrol_get_plugin('manual');
        $enrolinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
        $enrolplugin->enrol_user($enrolinstance, $student->id);
        $this->setUser($student);

        // Emulate data used in building course cache to receive the same instance of cached_cm_info as was used in building modinfo.
        $rawmods = get_course_mods($course->id);
        $cachedcminfo = assign_get_coursemodule_info($rawmods[$moduledb->id]);

        // Get modinfo.
        $modinfo = get_fast_modinfo($course->id);
        $cm = $modinfo->instances['assign'][$assign->id];

        $this->assertEquals($moduledb->id, $cm->id);
        $this->assertEquals($assigndb->id, $cm->instance);
        $this->assertEquals($moduledb->course, $cm->course);
        $this->assertEquals($moduledb->idnumber, $cm->idnumber);
        $this->assertEquals($moduledb->added, $cm->added);
        $this->assertEquals($moduledb->visible, $cm->visible);
        $this->assertEquals($moduledb->visibleold, $cm->visibleold);
        $this->assertEquals($moduledb->groupmode, $cm->groupmode);
        $this->assertEquals(VISIBLEGROUPS, $cm->groupmode);
        $this->assertEquals($moduledb->groupingid, $cm->groupingid);
        $this->assertEquals($moduledb->groupmembersonly, $cm->groupmembersonly);
        $this->assertEquals($course->groupmodeforce, $cm->coursegroupmodeforce);
        $this->assertEquals($course->groupmode, $cm->coursegroupmode);
        $this->assertEquals(SEPARATEGROUPS, $cm->coursegroupmode);
        $this->assertEquals($course->groupmodeforce ? $course->groupmode : $moduledb->groupmode,
                $cm->effectivegroupmode); // (since mod_assign supports groups).
        $this->assertEquals(VISIBLEGROUPS, $cm->effectivegroupmode);
        $this->assertEquals($moduledb->indent, $cm->indent);
        $this->assertEquals($moduledb->completion, $cm->completion);
        $this->assertEquals($moduledb->completiongradeitemnumber, $cm->completiongradeitemnumber);
        $this->assertEquals($moduledb->completionview, $cm->completionview);
        $this->assertEquals($moduledb->completionexpected, $cm->completionexpected);
        $this->assertEquals($moduledb->availablefrom, $cm->availablefrom);
        $this->assertEquals($moduledb->availableuntil, $cm->availableuntil);
        $this->assertEquals($moduledb->showavailability, $cm->showavailability);
        $this->assertEquals($moduledb->showdescription, $cm->showdescription);
        $this->assertEquals(null, $cm->extra); // Deprecated field. Used in module types that don't return cached_cm_info.
        $this->assertEquals($cachedcminfo->icon, $cm->icon);
        $this->assertEquals($cachedcminfo->iconcomponent, $cm->iconcomponent);
        $this->assertEquals('assign', $cm->modname);
        $this->assertEquals($moduledb->module, $cm->module);
        $this->assertEquals($cachedcminfo->name, $cm->name);
        $this->assertEquals($sectiondb->section, $cm->sectionnum);
        $this->assertEquals($moduledb->section, $cm->section);
        $this->assertEquals($conditionscompletion, $cm->conditionscompletion);
        $this->assertEquals($conditionsgrade, $cm->conditionsgrade);
        $this->assertEquals($conditionsfield, $cm->conditionsfield);
        $this->assertEquals(context_module::instance($moduledb->id), $cm->context);
        $this->assertEquals($modnamessingular['assign'], $cm->modfullname);
        $this->assertEquals($modnamesplural['assign'], $cm->modplural);
        $this->assertEquals(new moodle_url('/mod/assign/view.php', array('id' => $moduledb->id)), $cm->url);
        $this->assertEquals($cachedcminfo->customdata, $cm->customdata);

        // Dynamic fields, just test that they can be retrieved (must be carefully tested in each activity type).
        $this->assertNotEmpty($cm->availableinfo); // Lists all unmet availability conditions.
        $this->assertEquals(0, $cm->uservisible);
        $this->assertEquals('', $cm->extraclasses);
        $this->assertEquals('', $cm->onclick);
        $this->assertEquals(null, $cm->afterlink);
        $this->assertEquals(null, $cm->afterediticons);
        $this->assertEquals('', $cm->content);

        // Attempt to access and set non-existing field.
        $this->assertTrue(empty($modinfo->somefield));
        $this->assertFalse(isset($modinfo->somefield));
        $cm->somefield;
        $this->assertDebuggingCalled();
        $cm->somefield = 'Some value';
        $this->assertDebuggingCalled();
        $this->assertEmpty($cm->somefield);
        $this->assertDebuggingCalled();

        // Attempt to overwrite an existing field.
        $prevvalue = $cm->name;
        $this->assertNotEmpty($cm->name);
        $this->assertFalse(empty($cm->name));
        $this->assertTrue(isset($cm->name));
        $cm->name = 'Illegal overwriting';
        $this->assertDebuggingCalled();
        $this->assertEquals($prevvalue, $cm->name);
        $this->assertDebuggingNotCalled();

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
        $cm_info = get_fast_modinfo($course)->instances['assign'][$assign->id];

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

        // Turn groups setting on.
        $CFG->enablegroupmembersonly = true;
        // Create a mod_assign instance with "group members only", the activity should not be restricted.
        $assignnogroups = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id),
            array('groupmembersonly' => NOGROUPS));
        $cm_info = get_fast_modinfo($course->id)->instances['assign'][$assignnogroups->id];
        $this->assertFalse($cm_info->is_user_access_restricted_by_group());

        // If "group members only" is on but user is in the wrong group, the activity is restricted.
        $assignsepgroups = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id),
            array('groupmembersonly' => SEPARATEGROUPS, 'groupingid' => $grouping1->id));
        $this->assertTrue(groups_add_member($group2, $USER));
        get_fast_modinfo($course->id, 0, true);
        $cm_info = get_fast_modinfo($course->id)->instances['assign'][$assignsepgroups->id];
        $this->assertEquals($grouping1->id, $cm_info->groupingid);
        $this->assertTrue($cm_info->is_user_access_restricted_by_group());

        // If the user is in the required group, the activity isn't restricted.
        groups_remove_member($group2, $USER);
        $this->assertTrue(groups_add_member($group1, $USER));
        get_fast_modinfo($course->id, 0, true);
        $cm_info = get_fast_modinfo($course->id)->instances['assign'][$assignsepgroups->id];
        $this->assertFalse($cm_info->is_user_access_restricted_by_group());

        // Switch to a teacher and reload the context info.
        $this->setUser($teacher);
        $cm_info = get_fast_modinfo($course->id)->instances['assign'][$assignsepgroups->id];

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

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        // 1. Create an activity that is currently unavailable and hidden entirely (for students).
        $assign1 = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id),
                array('availablefrom' => time() + 10000, 'showavailability' => CONDITION_STUDENTVIEW_HIDE));
        // 2. Create an activity that is currently available.
        $assign2 = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id));
        // 3. Create an activity that is currently unavailable and set to be greyed out.
        $assign3 = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id),
                array('availablefrom' => time() + 10000, 'showavailability' => CONDITION_STUDENTVIEW_SHOW));

        // Set up a teacher.
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);

        // If conditional availability is disabled the activity will always be unrestricted.
        $CFG->enableavailability = false;
        $cm_info = get_fast_modinfo($course)->instances['assign'][$assign1->id];
        $this->assertFalse($cm_info->is_user_access_restricted_by_conditional_access());

        // Turn on conditional availability and reset the get_fast_modinfo cache.
        $CFG->enableavailability = true;
        get_fast_modinfo($course, 0, true);

        // The unavailable, hidden entirely activity should now be restricted.
        $cm_info = get_fast_modinfo($course)->instances['assign'][$assign1->id];
        $this->assertFalse($cm_info->available);
        $this->assertEquals(CONDITION_STUDENTVIEW_HIDE, $cm_info->showavailability);
        $this->assertTrue($cm_info->is_user_access_restricted_by_conditional_access());

        // If the activity is available it should not be restricted.
        $cm_info = get_fast_modinfo($course)->instances['assign'][$assign2->id];
        $this->assertTrue($cm_info->available);
        $this->assertFalse($cm_info->is_user_access_restricted_by_conditional_access());

        // If the activity is unavailable and set to be greyed out it should not be restricted.
        $cm_info = get_fast_modinfo($course)->instances['assign'][$assign3->id];
        $this->assertFalse($cm_info->available);
        $this->assertEquals(CONDITION_STUDENTVIEW_SHOW, $cm_info->showavailability);
        $this->assertFalse($cm_info->is_user_access_restricted_by_conditional_access());

        // If the activity is unavailable and set to be hidden entirely its restricted unless user has 'moodle/course:viewhiddenactivities'.
        // Switch to a teacher and reload the context info.
        $this->setUser($teacher);
        $cm_info = get_fast_modinfo($course)->instances['assign'][$assign1->id];
        $this->assertFalse($cm_info->available);
        $this->assertEquals(CONDITION_STUDENTVIEW_HIDE, $cm_info->showavailability);

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
}

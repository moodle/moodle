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

        // Add availability conditions.
        $availability = '{"op":"&","showc":[true,true,true],"c":[' .
                '{"type":"completion","cm":' . $prereqforum->cmid . ',"e":"' .
                    COMPLETION_COMPLETE . '"},' .
                '{"type":"grade","id":666,"min":0.4},' .
                '{"type":"profile","op":"contains","sf":"email","v":"test"}' .
                ']}';
        $DB->set_field('course_sections', 'availability', $availability,
                array('course' => $course->id, 'section' => 2));
        rebuild_course_cache($course->id, true);
        $sectiondb = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 2));

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
        $this->assertEquals($sectiondb->sequence, $si->sequence); // Since this section does not contain invalid modules.
        $this->assertEquals($availability, $si->availability);

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

        // Generate module and add availability conditions.
        $availability = '{"op":"&","showc":[true,true,true],"c":[' .
                '{"type":"completion","cm":' . $prereqforum->cmid . ',"e":"' .
                    COMPLETION_COMPLETE . '"},' .
                '{"type":"grade","id":666,"min":0.4},' .
                '{"type":"profile","op":"contains","sf":"email","v":"test"}' .
                ']}';
        $assign = $this->getDataGenerator()->create_module('assign',
                array('course' => $course->id),
                array('idnumber' => 123,
                    'groupmode' => VISIBLEGROUPS,
                    'availability' => $availability));
        rebuild_course_cache($course->id, true);

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
        $this->assertEquals($moduledb->showdescription, $cm->showdescription);
        $this->assertEquals(null, $cm->extra); // Deprecated field. Used in module types that don't return cached_cm_info.
        $this->assertEquals($cachedcminfo->icon, $cm->icon);
        $this->assertEquals($cachedcminfo->iconcomponent, $cm->iconcomponent);
        $this->assertEquals('assign', $cm->modname);
        $this->assertEquals($moduledb->module, $cm->module);
        $this->assertEquals($cachedcminfo->name, $cm->name);
        $this->assertEquals($sectiondb->section, $cm->sectionnum);
        $this->assertEquals($moduledb->section, $cm->section);
        $this->assertEquals($availability, $cm->availability);
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

    public function test_matching_cacherev() {
        global $DB, $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate the course and pre-requisite module.
        $course = $this->getDataGenerator()->create_course(
                array('format' => 'topics',
                    'numsections' => 3),
                array('createsections' => true));

        // Make sure the cacherev is set.
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertGreaterThan(0, $cacherev);
        $prevcacherev = $cacherev;

        // Reset course cache and make sure cacherev is bumped up but cache is empty.
        rebuild_course_cache($course->id, true);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $this->assertEmpty($cache->get($course->id));
        $prevcacherev = $cacherev;

        // Build course cache. Cacherev should not change but cache is now not empty. Make sure cacherev is the same everywhere.
        $modinfo = get_fast_modinfo($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertEquals($prevcacherev, $cacherev);
        $cachedvalue = $cache->get($course->id);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Little trick to check that cache is not rebuilt druing the next step - substitute the value in MUC and later check that it is still there.
        $cache->set($course->id, (object)array_merge((array)$cachedvalue, array('secretfield' => 1)));

        // Clear static cache and call get_fast_modinfo() again (pretend we are in another request). Cache should not be rebuilt.
        course_modinfo::clear_instance_cache();
        $modinfo = get_fast_modinfo($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertEquals($prevcacherev, $cacherev);
        $cachedvalue = $cache->get($course->id);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertNotEmpty($cachedvalue->secretfield);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Rebuild course cache. Cacherev must be incremented everywhere.
        rebuild_course_cache($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $cachedvalue = $cache->get($course->id);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $modinfo = get_fast_modinfo($course->id);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Update cacherev in DB and make sure the cache will be rebuilt on the next call to get_fast_modinfo().
        increment_revision_number('course', 'cacherev', 'id = ?', array($course->id));
        // We need to clear static cache for course_modinfo instances too.
        course_modinfo::clear_instance_cache();
        $modinfo = get_fast_modinfo($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $cachedvalue = $cache->get($course->id);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Reset cache for all courses and make sure this course cache is reset.
        rebuild_course_cache(0, true);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $this->assertEmpty($cache->get($course->id));
        // Rebuild again.
        $modinfo = get_fast_modinfo($course->id);
        $cachedvalue = $cache->get($course->id);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Purge all caches and make sure cacherev is increased and data from MUC erased.
        purge_all_caches();
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $this->assertEmpty($cache->get($course->id));
    }

    public function test_course_modinfo_properties() {
        global $USER, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Generate the course and some modules. Make one section hidden.
        $course = $this->getDataGenerator()->create_course(
                array('format' => 'topics',
                    'numsections' => 3),
                array('createsections' => true));
        $DB->execute('UPDATE {course_sections} SET visible = 0 WHERE course = ? and section = ?',
                array($course->id, 3));
        $coursecontext = context_course::instance($course->id);
        $forum0 = $this->getDataGenerator()->create_module('forum',
                array('course' => $course->id), array('section' => 0));
        $assign0 = $this->getDataGenerator()->create_module('assign',
                array('course' => $course->id), array('section' => 0, 'visible' => 0));
        $forum1 = $this->getDataGenerator()->create_module('forum',
                array('course' => $course->id), array('section' => 1));
        $assign1 = $this->getDataGenerator()->create_module('assign',
                array('course' => $course->id), array('section' => 1));
        $page1 = $this->getDataGenerator()->create_module('page',
                array('course' => $course->id), array('section' => 1));
        $page3 = $this->getDataGenerator()->create_module('page',
                array('course' => $course->id), array('section' => 3));

        $modinfo = get_fast_modinfo($course->id);

        $this->assertEquals(array($forum0->cmid, $assign0->cmid, $forum1->cmid, $assign1->cmid, $page1->cmid, $page3->cmid),
                array_keys($modinfo->cms));
        $this->assertEquals($course->id, $modinfo->courseid);
        $this->assertEquals($USER->id, $modinfo->userid);
        $this->assertEquals(array(0 => array($forum0->cmid, $assign0->cmid),
            1 => array($forum1->cmid, $assign1->cmid, $page1->cmid), 3 => array($page3->cmid)), $modinfo->sections);
        $this->assertEquals(array('forum', 'assign', 'page'), array_keys($modinfo->instances));
        $this->assertEquals(array($assign0->id, $assign1->id), array_keys($modinfo->instances['assign']));
        $this->assertEquals(array($forum0->id, $forum1->id), array_keys($modinfo->instances['forum']));
        $this->assertEquals(array($page1->id, $page3->id), array_keys($modinfo->instances['page']));
        $this->assertEquals(groups_get_user_groups($course->id), $modinfo->groups);
        $this->assertEquals(array(0 => array($forum0->cmid, $assign0->cmid),
            1 => array($forum1->cmid, $assign1->cmid, $page1->cmid),
            3 => array($page3->cmid)), $modinfo->get_sections());
        $this->assertEquals(array(0, 1, 2, 3), array_keys($modinfo->get_section_info_all()));
        $this->assertEquals($forum0->cmid . ',' . $assign0->cmid, $modinfo->get_section_info(0)->sequence);
        $this->assertEquals($forum1->cmid . ',' . $assign1->cmid . ',' . $page1->cmid, $modinfo->get_section_info(1)->sequence);
        $this->assertEquals('', $modinfo->get_section_info(2)->sequence);
        $this->assertEquals($page3->cmid, $modinfo->get_section_info(3)->sequence);
        $this->assertEquals($course->id, $modinfo->get_course()->id);
        $this->assertEquals(array('assign', 'forum', 'page'),
                array_keys($modinfo->get_used_module_names()));
        $this->assertEquals(array('assign', 'forum', 'page'),
                array_keys($modinfo->get_used_module_names(true)));
        // Admin can see hidden modules/sections.
        $this->assertTrue($modinfo->cms[$assign0->cmid]->uservisible);
        $this->assertTrue($modinfo->get_section_info(3)->uservisible);

        // Get modinfo for non-current user (without capability to view hidden activities/sections).
        $user = $this->getDataGenerator()->create_user();
        $modinfo = get_fast_modinfo($course->id, $user->id);
        $this->assertEquals($user->id, $modinfo->userid);
        $this->assertFalse($modinfo->cms[$assign0->cmid]->uservisible);
        $this->assertFalse($modinfo->get_section_info(3)->uservisible);

        // Attempt to access and set non-existing field.
        $this->assertTrue(empty($modinfo->somefield));
        $this->assertFalse(isset($modinfo->somefield));
        $modinfo->somefield;
        $this->assertDebuggingCalled();
        $modinfo->somefield = 'Some value';
        $this->assertDebuggingCalled();
        $this->assertEmpty($modinfo->somefield);
        $this->assertDebuggingCalled();

        // Attempt to overwrite existing field.
        $this->assertFalse(empty($modinfo->cms));
        $this->assertTrue(isset($modinfo->cms));
        $modinfo->cms = 'Illegal overwriting';
        $this->assertDebuggingCalled();
        $this->assertNotEquals('Illegal overwriting', $modinfo->cms);
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

        // Enable conditional availability before creating modules, otherwise the condition data is not written in DB.
        $CFG->enableavailability = true;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        // 1. Create an activity that is currently unavailable and hidden entirely (for students).
        $assign1 = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id),
                array('availability' => '{"op":"|","show":false,"c":[' .
                '{"type":"date","d":">=","t":' . (time() + 10000) . '}]}'));
        // 2. Create an activity that is currently available.
        $assign2 = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id));
        // 3. Create an activity that is currently unavailable and set to be greyed out.
        $assign3 = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id),
                array('availability' => '{"op":"|","show":true,"c":[' .
                '{"type":"date","d":">=","t":' . (time() + 10000) . '}]}'));

        // Set up a teacher.
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);

        // If conditional availability is disabled the activity will always be unrestricted.
        $CFG->enableavailability = false;
        $cm = get_fast_modinfo($course)->instances['assign'][$assign1->id];
        $this->assertTrue($cm->uservisible);

        // Test deprecated function.
        $this->assertFalse($cm->is_user_access_restricted_by_conditional_access());
        $this->assertEquals(1, count(phpunit_util::get_debugging_messages()));
        phpunit_util::reset_debugging();

        // Turn on conditional availability and reset the get_fast_modinfo cache.
        $CFG->enableavailability = true;
        get_fast_modinfo($course, 0, true);

        // The unavailable, hidden entirely activity should now be restricted.
        $cm = get_fast_modinfo($course)->instances['assign'][$assign1->id];
        $this->assertFalse($cm->uservisible);
        $this->assertFalse($cm->available);
        $this->assertEquals('', $cm->availableinfo);

        // Test deprecated function.
        $this->assertTrue($cm->is_user_access_restricted_by_conditional_access());
        $this->assertEquals(1, count(phpunit_util::get_debugging_messages()));
        phpunit_util::reset_debugging();

        // If the activity is available it should not be restricted.
        $cm = get_fast_modinfo($course)->instances['assign'][$assign2->id];
        $this->assertTrue($cm->uservisible);
        $this->assertTrue($cm->available);

        // If the activity is unavailable and set to be greyed out it should not be restricted.
        $cm = get_fast_modinfo($course)->instances['assign'][$assign3->id];
        $this->assertFalse($cm->uservisible);
        $this->assertFalse($cm->available);
        $this->assertNotEquals('', (string)$cm->availableinfo);

        // Test deprecated function (weird case, it actually checks visibility).
        $this->assertFalse($cm->is_user_access_restricted_by_conditional_access());
        $this->assertEquals(1, count(phpunit_util::get_debugging_messages()));
        phpunit_util::reset_debugging();

        // If the activity is unavailable and set to be hidden entirely its restricted unless user has 'moodle/course:viewhiddenactivities'.
        // Switch to a teacher and reload the context info.
        $this->setUser($teacher);
        $this->assertTrue(has_capability('moodle/course:viewhiddenactivities', $coursecontext));
        $cm = get_fast_modinfo($course)->instances['assign'][$assign1->id];
        $this->assertTrue($cm->uservisible);
        $this->assertFalse($cm->available);
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

    /**
     * Tests that various deprecated cm_info methods are throwing debuggign messages
     */
    public function test_cm_info_property_deprecations() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course( array('format' => 'topics', 'numsections' => 3),
                array('createsections' => true));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $cm = get_fast_modinfo($course->id)->instances['forum'][$forum->id];

        $cm->get_url();
        $this->assertDebuggingCalled('cm_info::get_url() is deprecated, please use the property cm_info->url instead.');

        $cm->get_content();
        $this->assertDebuggingCalled('cm_info::get_content() is deprecated, please use the property cm_info->content instead.');

        $cm->get_extra_classes();
        $this->assertDebuggingCalled('cm_info::get_extra_classes() is deprecated, please use the property cm_info->extraclasses instead.');

        $cm->get_on_click();
        $this->assertDebuggingCalled('cm_info::get_on_click() is deprecated, please use the property cm_info->onclick instead.');

        $cm->get_custom_data();
        $this->assertDebuggingCalled('cm_info::get_custom_data() is deprecated, please use the property cm_info->customdata instead.');

        $cm->get_after_link();
        $this->assertDebuggingCalled('cm_info::get_after_link() is deprecated, please use the property cm_info->afterlink instead.');

        $cm->get_after_edit_icons();
        $this->assertDebuggingCalled('cm_info::get_after_edit_icons() is deprecated, please use the property cm_info->afterediticons instead.');

        $cm->obtain_dynamic_data();
        $this->assertDebuggingCalled('cm_info::obtain_dynamic_data() is deprecated and should not be used.');
    }

    /**
     * Tests for function cm_info::get_course_module_record()
     */
    public function test_cm_info_get_course_module_record() {
        global $DB, $CFG;

        $this->resetAfterTest();

        set_config('enableavailability', 1);
        set_config('enablecompletion', 1);

        $course = $this->getDataGenerator()->create_course(
                array('format' => 'topics', 'numsections' => 3, 'enablecompletion' => 1),
                array('createsections' => true));
        $mods = array();
        $mods[0] = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $mods[1] = $this->getDataGenerator()->create_module('assign',
                array('course' => $course->id,
                    'section' => 3,
                    'idnumber' => '12345',
                    'showdescription' => true
                    ));
        // Pick a small valid availability value to use.
        $availabilityvalue = '{"op":"|","show":true,"c":[{"type":"date","d":">=","t":4}]}';
        $mods[2] = $this->getDataGenerator()->create_module('book',
                array('course' => $course->id,
                    'indent' => 5,
                    'availability' => $availabilityvalue,
                    'showdescription' => false,
                    'completion' => true,
                    'completionview' => true,
                    'completionexpected' => time() + 5000,
                    ));
        $mods[3] = $this->getDataGenerator()->create_module('forum',
                array('course' => $course->id,
                    'visible' => 0,
                    'groupmode' => 1,
                    'availability' => null));
        $mods[4] = $this->getDataGenerator()->create_module('forum',
                array('course' => $course->id,
                    'groupmembersonly' => true,
                    'grouping' => 12));

        $modinfo = get_fast_modinfo($course->id);

        // Make sure that object returned by get_course_module_record(false) has exactly the same fields as DB table 'course_modules'.
        $dbfields = array_keys($DB->get_columns('course_modules'));
        sort($dbfields);
        $cmrecord = $modinfo->get_cm($mods[0]->cmid)->get_course_module_record();
        $cmrecordfields = array_keys((array)$cmrecord);
        sort($cmrecordfields);
        $this->assertEquals($dbfields, $cmrecordfields);

        // Make sure that object returned by get_course_module_record(true) has exactly the same fields
        // as object returned by get_coursemodule_from_id(,,,true,);
        $cmrecordfull = $modinfo->get_cm($mods[0]->cmid)->get_course_module_record(true);
        $cmrecordfullfields = array_keys((array)$cmrecordfull);
        $cm = get_coursemodule_from_id(null, $mods[0]->cmid, 0, true, MUST_EXIST);
        $cmfields = array_keys((array)$cm);
        $this->assertEquals($cmfields, $cmrecordfullfields);

        // Make sure that object returned by get_course_module_record(true) has exactly the same fields
        // as object returned by get_coursemodule_from_instance(,,,true,);
        $cm = get_coursemodule_from_instance('forum', $mods[0]->id, null, true, MUST_EXIST);
        $cmfields = array_keys((array)$cm);
        $this->assertEquals($cmfields, $cmrecordfullfields);

        // Make sure the objects have the same properties.
        $cm1 = get_coursemodule_from_id(null, $mods[0]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('forum', $mods[0]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[0]->cmid);
        $record = $DB->get_record('course_modules', array('id' => $mods[0]->cmid));
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));

        $cm1 = get_coursemodule_from_id(null, $mods[1]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('assign', $mods[1]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[1]->cmid);
        $record = $DB->get_record('course_modules', array('id' => $mods[1]->cmid));
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));

        $cm1 = get_coursemodule_from_id(null, $mods[2]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('book', $mods[2]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[2]->cmid);
        $record = $DB->get_record('course_modules', array('id' => $mods[2]->cmid));
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));

        $cm1 = get_coursemodule_from_id(null, $mods[3]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('forum', $mods[3]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[3]->cmid);
        $record = $DB->get_record('course_modules', array('id' => $mods[3]->cmid));
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));

        $cm1 = get_coursemodule_from_id(null, $mods[4]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('forum', $mods[4]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[4]->cmid);
        $record = $DB->get_record('course_modules', array('id' => $mods[4]->cmid));
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));

    }

    /**
     * Tests the availability property that has been added to course modules
     * and sections (just to see that it is correctly saved and accessed).
     */
    public function test_availability_property() {
        global $DB, $CFG;

        $this->resetAfterTest();

        // Create a course with two modules and three sections.
        $course = $this->getDataGenerator()->create_course(
                array('format' => 'topics', 'numsections' => 3),
                array('createsections' => true));
        $forum = $this->getDataGenerator()->create_module('forum',
                array('course' => $course->id));
        $forum2 = $this->getDataGenerator()->create_module('forum',
                array('course' => $course->id));

        // Get modinfo. Check that availability is null for both cm and sections.
        $modinfo = get_fast_modinfo($course->id);
        $cm = $modinfo->get_cm($forum->cmid);
        $this->assertNull($cm->availability);
        $section = $modinfo->get_section_info(1, MUST_EXIST);
        $this->assertNull($section->availability);

        // Update availability for cm and section in database.
        $DB->set_field('course_modules', 'availability', '{}', array('id' => $cm->id));
        $DB->set_field('course_sections', 'availability', '{}', array('id' => $section->id));

        // Clear cache and get modinfo again.
        rebuild_course_cache($course->id, true);
        get_fast_modinfo(0, 0, true);
        $modinfo = get_fast_modinfo($course->id);

        // Check values that were changed.
        $cm = $modinfo->get_cm($forum->cmid);
        $this->assertEquals('{}', $cm->availability);
        $section = $modinfo->get_section_info(1, MUST_EXIST);
        $this->assertEquals('{}', $section->availability);

        // Check other values are still null.
        $cm = $modinfo->get_cm($forum2->cmid);
        $this->assertNull($cm->availability);
        $section = $modinfo->get_section_info(2, MUST_EXIST);
        $this->assertNull($section->availability);
    }

    /**
     * Some properties have been deprecated from both the section and module
     * classes. This checks they still work (and show warnings).
     */
    public function test_availability_deprecations() {
        global $CFG, $DB;
        $this->resetAfterTest();
        $CFG->enableavailability = true;

        // Create a course with two modules. The modules are not available to
        // users. One of them is set to show this information, the other is not.
        // Same setup for sections.
        $generator = $this->getDataGenerator();
        $course = $this->getDataGenerator()->create_course(
                array('format' => 'topics', 'numsections' => 2),
                array('createsections' => true));
        $show = '{"op":"|","show":true,"c":[{"type":"date","d":"<","t":1395857332}]}';
        $noshow = '{"op":"|","show":false,"c":[{"type":"date","d":"<","t":1395857332}]}';
        $forum1 = $generator->create_module('forum',
                array('course' => $course->id, 'availability' => $show));
        $forum2 = $generator->create_module('forum',
                array('course' => $course->id, 'availability' => $noshow));
        $DB->set_field('course_sections', 'availability',
                $show, array('course' => $course->id, 'section' => 1));
        $DB->set_field('course_sections', 'availability',
                $noshow, array('course' => $course->id, 'section' => 2));

        // Create a user without special permissions.
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        // Get modinfo and cm objects.
        $modinfo = get_fast_modinfo($course, $user->id);
        $cm1 = $modinfo->get_cm($forum1->cmid);
        $cm2 = $modinfo->get_cm($forum2->cmid);

        // Check the showavailability property.
        $this->assertEquals(1, $cm1->showavailability);
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);
        $this->assertEquals(0, $cm2->showavailability);
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);

        // Check the dates (these always return 0 now).
        $this->assertEquals(0, $cm1->availablefrom);
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);
        $this->assertEquals(0, $cm1->availableuntil);
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);

        // Get section objects.
        $section1 = $modinfo->get_section_info(1);
        $section2 = $modinfo->get_section_info(2);

        // Check showavailability.
        $this->assertEquals(1, $section1->showavailability);
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);
        $this->assertEquals(0, $section2->showavailability);
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);

        // Check dates (zero).
        $this->assertEquals(0, $section1->availablefrom);
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);
                $this->assertEquals(0, $section1->availableuntil);
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);

        // Check groupingid (zero).
        $this->assertEquals(0, $section1->groupingid);
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);
    }
}

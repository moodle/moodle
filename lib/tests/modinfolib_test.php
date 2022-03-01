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

namespace core;

use advanced_testcase;
use cache;
use cm_info;
use coding_exception;
use context_course;
use context_module;
use course_modinfo;
use moodle_exception;
use moodle_url;
use Exception;

/**
 * Unit tests for lib/modinfolib.php.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Andrew Davis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modinfolib_test extends advanced_testcase {
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
        $this->assertEquals($course->groupmodeforce, $cm->coursegroupmodeforce);
        $this->assertEquals($course->groupmode, $cm->coursegroupmode);
        $this->assertEquals(SEPARATEGROUPS, $cm->coursegroupmode);
        $this->assertEquals($course->groupmodeforce ? $course->groupmode : $moduledb->groupmode,
                $cm->effectivegroupmode); // (since mod_assign supports groups).
        $this->assertEquals(VISIBLEGROUPS, $cm->effectivegroupmode);
        $this->assertEquals($moduledb->indent, $cm->indent);
        $this->assertEquals($moduledb->completion, $cm->completion);
        $this->assertEquals($moduledb->completiongradeitemnumber, $cm->completiongradeitemnumber);
        $this->assertEquals($moduledb->completionpassgrade, $cm->completionpassgrade);
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
        $this->assertEmpty($cache->get_versioned($course->id, $prevcacherev));
        $prevcacherev = $cacherev;

        // Build course cache. Cacherev should not change but cache is now not empty. Make sure cacherev is the same everywhere.
        $modinfo = get_fast_modinfo($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertEquals($prevcacherev, $cacherev);
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Little trick to check that cache is not rebuilt druing the next step - substitute the value in MUC and later check that it is still there.
        $cache->set_versioned($course->id, $cacherev, (object)array_merge((array)$cachedvalue, array('secretfield' => 1)));

        // Clear static cache and call get_fast_modinfo() again (pretend we are in another request). Cache should not be rebuilt.
        course_modinfo::clear_instance_cache();
        $modinfo = get_fast_modinfo($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertEquals($prevcacherev, $cacherev);
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertNotEmpty($cachedvalue->secretfield);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Rebuild course cache. Cacherev must be incremented everywhere.
        rebuild_course_cache($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
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
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Reset cache for all courses and make sure this course cache is reset.
        rebuild_course_cache(0, true);
        $cacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $this->assertEmpty($cache->get_versioned($course->id, $cacherev));
        // Rebuild again.
        $modinfo = get_fast_modinfo($course->id);
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
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
        $names = array_keys($modinfo->get_used_module_names());
        sort($names);
        $this->assertEquals(array('assign', 'forum', 'page'), $names);
        $names = array_keys($modinfo->get_used_module_names(true));
        sort($names);
        $this->assertEquals(array('assign', 'forum', 'page'), $names);
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
     * Tests for function cm_info::get_course_module_record()
     */
    public function test_cm_info_get_course_module_record() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

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
     * Tests for get_groups() method.
     */
    public function test_get_groups() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Create courses.
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $course3 = $generator->create_course();

        // Create users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();

        // Enrol users on courses.
        $generator->enrol_user($user1->id, $course1->id);
        $generator->enrol_user($user2->id, $course2->id);
        $generator->enrol_user($user3->id, $course2->id);
        $generator->enrol_user($user3->id, $course3->id);

        // Create groups.
        $group1 = $generator->create_group(array('courseid' => $course1->id));
        $group2 = $generator->create_group(array('courseid' => $course2->id));
        $group3 = $generator->create_group(array('courseid' => $course2->id));

        // Assign users to groups and assert the result.
        $this->assertTrue($generator->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($generator->create_group_member(array('groupid' => $group2->id, 'userid' => $user2->id)));
        $this->assertTrue($generator->create_group_member(array('groupid' => $group3->id, 'userid' => $user2->id)));
        $this->assertTrue($generator->create_group_member(array('groupid' => $group2->id, 'userid' => $user3->id)));

        // Create groupings.
        $grouping1 = $generator->create_grouping(array('courseid' => $course1->id));
        $grouping2 = $generator->create_grouping(array('courseid' => $course2->id));

        // Assign and assert group to groupings.
        groups_assign_grouping($grouping1->id, $group1->id);
        groups_assign_grouping($grouping2->id, $group2->id);
        groups_assign_grouping($grouping2->id, $group3->id);

        // Test with one single group.
        $modinfo = get_fast_modinfo($course1, $user1->id);
        $groups = $modinfo->get_groups($grouping1->id);
        $this->assertCount(1, $groups);
        $this->assertArrayHasKey($group1->id, $groups);

        // Test with two groups.
        $modinfo = get_fast_modinfo($course2, $user2->id);
        $groups = $modinfo->get_groups();
        $this->assertCount(2, $groups);
        $this->assertTrue(in_array($group2->id, $groups));
        $this->assertTrue(in_array($group3->id, $groups));

        // Test with no groups.
        $modinfo = get_fast_modinfo($course3, $user3->id);
        $groups = $modinfo->get_groups();
        $this->assertCount(0, $groups);
        $this->assertArrayNotHasKey($group1->id, $groups);
    }

    /**
     * Tests the function for constructing a cm_info from mixed data.
     */
    public function test_create() {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Create a course and an activity.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $page = $generator->create_module('page', array('course' => $course->id,
                'name' => 'Annie'));

        // Null is passed through.
        $this->assertNull(cm_info::create(null));

        // Stdclass object turns into cm_info.
        $cm = cm_info::create(
                (object)array('id' => $page->cmid, 'course' => $course->id));
        $this->assertInstanceOf('cm_info', $cm);
        $this->assertEquals('Annie', $cm->name);

        // A cm_info object stays as cm_info.
        $this->assertSame($cm, cm_info::create($cm));

        // Invalid object (missing fields) causes error.
        try {
            cm_info::create((object)array('id' => $page->cmid));
            $this->fail();
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        // Create a second hidden activity.
        $hiddenpage = $generator->create_module('page', array('course' => $course->id,
                'name' => 'Annie', 'visible' => 0));

        // Create 2 user accounts, one is a manager who can see everything.
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $manager = $generator->create_user();
        $generator->enrol_user($manager->id, $course->id,
                $DB->get_field('role', 'id', array('shortname' => 'manager'), MUST_EXIST));

        // User can see the normal page but not the hidden one.
        $cm = cm_info::create((object)array('id' => $page->cmid, 'course' => $course->id),
                $user->id);
        $this->assertTrue($cm->uservisible);
        $cm = cm_info::create((object)array('id' => $hiddenpage->cmid, 'course' => $course->id),
                $user->id);
        $this->assertFalse($cm->uservisible);

        // Manager can see the hidden one too.
        $cm = cm_info::create((object)array('id' => $hiddenpage->cmid, 'course' => $course->id),
                $manager->id);
        $this->assertTrue($cm->uservisible);
    }

    /**
     * Tests function for getting $course and $cm at once quickly from modinfo
     * based on cmid or cm record.
     */
    public function test_get_course_and_cm_from_cmid() {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Create a course and an activity.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(array('shortname' => 'Halls'));
        $page = $generator->create_module('page', array('course' => $course->id,
                'name' => 'Annie'));

        // Successful usage.
        list($course, $cm) = get_course_and_cm_from_cmid($page->cmid);
        $this->assertEquals('Halls', $course->shortname);
        $this->assertInstanceOf('cm_info', $cm);
        $this->assertEquals('Annie', $cm->name);

        // Specified module type.
        list($course, $cm) = get_course_and_cm_from_cmid($page->cmid, 'page');
        $this->assertEquals('Annie', $cm->name);

        // With id in object.
        $fakecm = (object)array('id' => $page->cmid);
        list($course, $cm) = get_course_and_cm_from_cmid($fakecm);
        $this->assertEquals('Halls', $course->shortname);
        $this->assertEquals('Annie', $cm->name);

        // With both id and course in object.
        $fakecm->course = $course->id;
        list($course, $cm) = get_course_and_cm_from_cmid($fakecm);
        $this->assertEquals('Halls', $course->shortname);
        $this->assertEquals('Annie', $cm->name);

        // With supplied course id.
        list($course, $cm) = get_course_and_cm_from_cmid($page->cmid, 'page', $course->id);
        $this->assertEquals('Annie', $cm->name);

        // With supplied course object (modified just so we can check it is
        // indeed reusing the supplied object).
        $course->silly = true;
        list($course, $cm) = get_course_and_cm_from_cmid($page->cmid, 'page', $course);
        $this->assertEquals('Annie', $cm->name);
        $this->assertTrue($course->silly);

        // Incorrect module type.
        try {
            get_course_and_cm_from_cmid($page->cmid, 'forum');
            $this->fail();
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidcoursemodule', $e->errorcode);
        }

        // Invalid module name.
        try {
            get_course_and_cm_from_cmid($page->cmid, 'pigs can fly');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertStringContainsString('Invalid modulename parameter', $e->getMessage());
        }

        // Doesn't exist.
        try {
            get_course_and_cm_from_cmid($page->cmid + 1);
            $this->fail();
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Create a second hidden activity.
        $hiddenpage = $generator->create_module('page', array('course' => $course->id,
                'name' => 'Annie', 'visible' => 0));

        // Create 2 user accounts, one is a manager who can see everything.
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $manager = $generator->create_user();
        $generator->enrol_user($manager->id, $course->id,
                $DB->get_field('role', 'id', array('shortname' => 'manager'), MUST_EXIST));

        // User can see the normal page but not the hidden one.
        list($course, $cm) = get_course_and_cm_from_cmid($page->cmid, 'page', 0, $user->id);
        $this->assertTrue($cm->uservisible);
        list($course, $cm) = get_course_and_cm_from_cmid($hiddenpage->cmid, 'page', 0, $user->id);
        $this->assertFalse($cm->uservisible);

        // Manager can see the hidden one too.
        list($course, $cm) = get_course_and_cm_from_cmid($hiddenpage->cmid, 'page', 0, $manager->id);
        $this->assertTrue($cm->uservisible);
    }

    /**
     * Tests function for getting $course and $cm at once quickly from modinfo
     * based on instance id or record.
     */
    public function test_get_course_and_cm_from_instance() {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Create a course and an activity.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(array('shortname' => 'Halls'));
        $page = $generator->create_module('page', array('course' => $course->id,
                'name' => 'Annie'));

        // Successful usage.
        list($course, $cm) = get_course_and_cm_from_instance($page->id, 'page');
        $this->assertEquals('Halls', $course->shortname);
        $this->assertInstanceOf('cm_info', $cm);
        $this->assertEquals('Annie', $cm->name);

        // With id in object.
        $fakeinstance = (object)array('id' => $page->id);
        list($course, $cm) = get_course_and_cm_from_instance($fakeinstance, 'page');
        $this->assertEquals('Halls', $course->shortname);
        $this->assertEquals('Annie', $cm->name);

        // With both id and course in object.
        $fakeinstance->course = $course->id;
        list($course, $cm) = get_course_and_cm_from_instance($fakeinstance, 'page');
        $this->assertEquals('Halls', $course->shortname);
        $this->assertEquals('Annie', $cm->name);

        // With supplied course id.
        list($course, $cm) = get_course_and_cm_from_instance($page->id, 'page', $course->id);
        $this->assertEquals('Annie', $cm->name);

        // With supplied course object (modified just so we can check it is
        // indeed reusing the supplied object).
        $course->silly = true;
        list($course, $cm) = get_course_and_cm_from_instance($page->id, 'page', $course);
        $this->assertEquals('Annie', $cm->name);
        $this->assertTrue($course->silly);

        // Doesn't exist (or is wrong type).
        try {
            get_course_and_cm_from_instance($page->id, 'forum');
            $this->fail();
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Invalid module name.
        try {
            get_course_and_cm_from_cmid($page->cmid, '1337 h4x0ring');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertStringContainsString('Invalid modulename parameter', $e->getMessage());
        }

        // Create a second hidden activity.
        $hiddenpage = $generator->create_module('page', array('course' => $course->id,
                'name' => 'Annie', 'visible' => 0));

        // Create 2 user accounts, one is a manager who can see everything.
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $manager = $generator->create_user();
        $generator->enrol_user($manager->id, $course->id,
                $DB->get_field('role', 'id', array('shortname' => 'manager'), MUST_EXIST));

        // User can see the normal page but not the hidden one.
        list($course, $cm) = get_course_and_cm_from_cmid($page->cmid, 'page', 0, $user->id);
        $this->assertTrue($cm->uservisible);
        list($course, $cm) = get_course_and_cm_from_cmid($hiddenpage->cmid, 'page', 0, $user->id);
        $this->assertFalse($cm->uservisible);

        // Manager can see the hidden one too.
        list($course, $cm) = get_course_and_cm_from_cmid($hiddenpage->cmid, 'page', 0, $manager->id);
        $this->assertTrue($cm->uservisible);
    }

    /**
     * Test test_get_section_info_by_id method
     *
     * @dataProvider get_section_info_by_id_provider
     * @covers \course_modinfo::get_section_info_by_id
     *
     * @param int $sectionnum the section number
     * @param int $strictness the search strict mode
     * @param bool $expectnull if the function will return a null
     * @param bool $expectexception if the function will throw an exception
     */
    public function test_get_section_info_by_id(
        int $sectionnum,
        int $strictness = IGNORE_MISSING,
        bool $expectnull = false,
        bool $expectexception = false
    ) {
        global $DB;

        $this->resetAfterTest();

        // Create a course with 4 sections.
        $course = $this->getDataGenerator()->create_course(['numsections' => 4]);

        // Index sections.
        $sectionindex = [];
        $modinfo = get_fast_modinfo($course);
        $allsections = $modinfo->get_section_info_all();
        foreach ($allsections as $section) {
            $sectionindex[$section->section] = $section->id;
        }

        if ($expectexception) {
            $this->expectException(moodle_exception::class);
        }

        $sectionid = $sectionindex[$sectionnum] ?? -1;

        $section = $modinfo->get_section_info_by_id($sectionid, $strictness);

        if ($expectnull) {
            $this->assertNull($section);
        } else {
            $this->assertEquals($sectionid, $section->id);
            $this->assertEquals($sectionnum, $section->section);
        }
    }

    /**
     * Data provider for test_get_section_info_by_id().
     *
     * @return array
     */
    public function get_section_info_by_id_provider() {
        return [
            'Valid section id' => [
                'sectionnum' => 1,
                'strictness' => IGNORE_MISSING,
                'expectnull' => false,
                'expectexception' => false,
            ],
            'Section zero' => [
                'sectionnum' => 0,
                'strictness' => IGNORE_MISSING,
                'expectnull' => false,
                'expectexception' => false,
            ],
            'invalid section ignore missing' => [
                'sectionnum' => -1,
                'strictness' => IGNORE_MISSING,
                'expectnull' => true,
                'expectexception' => false,
            ],
            'invalid section must exists' => [
                'sectionnum' => -1,
                'strictness' => MUST_EXIST,
                'expectnull' => false,
                'expectexception' => true,
            ],
        ];
    }

    /**
     * Test purge_section_cache_by_id method
     *
     * @covers \course_modinfo::purge_course_section_cache_by_id
     * @return void
     */
    public function test_purge_section_cache_by_id(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate the course and pre-requisite section.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 3], ['createsections' => true]);
        // Reset course cache.
        rebuild_course_cache($course->id, true);
        // Build course cache.
        get_fast_modinfo($course->id);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        // Make sure that we will have 4 section caches here.
        $this->assertCount(4, $sectioncaches);
        $this->assertArrayHasKey(0, $sectioncaches);
        $this->assertArrayHasKey(1, $sectioncaches);
        $this->assertArrayHasKey(2, $sectioncaches);
        $this->assertArrayHasKey(3, $sectioncaches);

        // Purge cache for the section by id.
        course_modinfo::purge_course_section_cache_by_id($course->id, $sectioncaches[1]->id);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        // Make sure that we will have 3 section caches left.
        $this->assertCount(3, $sectioncaches);
        $this->assertArrayNotHasKey(1, $sectioncaches);
        $this->assertArrayHasKey(0, $sectioncaches);
        $this->assertArrayHasKey(2, $sectioncaches);
        $this->assertArrayHasKey(3, $sectioncaches);
        // Make sure that the cacherev will be reset.
        $this->assertEquals(-1, $coursemodinfo->cacherev);
    }

    /**
     * Test purge_section_cache_by_number method
     *
     * @covers \course_modinfo::purge_course_section_cache_by_number
     * @return void
     */
    public function test_section_cache_by_number(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate the course and pre-requisite section.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 3], ['createsections' => true]);
        // Reset course cache.
        rebuild_course_cache($course->id, true);
        // Build course cache.
        get_fast_modinfo($course->id);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        // Make sure that we will have 4 section caches here.
        $this->assertCount(4, $sectioncaches);
        $this->assertArrayHasKey(0, $sectioncaches);
        $this->assertArrayHasKey(1, $sectioncaches);
        $this->assertArrayHasKey(2, $sectioncaches);
        $this->assertArrayHasKey(3, $sectioncaches);

        // Purge cache for the section with section number is 1.
        course_modinfo::purge_course_section_cache_by_number($course->id, 1);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        // Make sure that we will have 3 section caches left.
        $this->assertCount(3, $sectioncaches);
        $this->assertArrayNotHasKey(1, $sectioncaches);
        $this->assertArrayHasKey(0, $sectioncaches);
        $this->assertArrayHasKey(2, $sectioncaches);
        $this->assertArrayHasKey(3, $sectioncaches);
        // Make sure that the cacherev will be reset.
        $this->assertEquals(-1, $coursemodinfo->cacherev);
    }
}

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

namespace core_course;

use core\context\course as context_course;
use core\context\module as context_module;
use core\url;

/**
 * Tests for
 *
 * @package    core
 * @category   test
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(cm_info::class)]
final class cm_info_test extends \advanced_testcase {
    public function test_cm_info_properties(): void {
        global $DB;

        $this->resetAfterTest();
        set_config('enableavailability', 1);
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Generate the course and pre-requisite module.
        $course = $this->getDataGenerator()->create_course(
            [
                'format' => 'topics',
                'numsections' => 3,
                'enablecompletion' => 1,
                'groupmode' => SEPARATEGROUPS,
                'forcegroupmode' => 0,
            ],
            ['createsections' => true],
        );
        $coursecontext = context_course::instance($course->id);
        $prereqforum = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $course->id],
            ['completion' => 1],
        );

        // Generate module and add availability conditions.
        $availability = '{"op":"&","showc":[true,true,true],"c":[' .
                '{"type":"completion","cm":' . $prereqforum->cmid . ',"e":"' .
                    COMPLETION_COMPLETE . '"},' .
                '{"type":"grade","id":666,"min":0.4},' .
                '{"type":"profile","op":"contains","sf":"email","v":"test"}' .
                ']}';
        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id],
            [
                'idnumber' => 123,
                'groupmode' => VISIBLEGROUPS,
                'availability' => $availability,
            ],
        );
        rebuild_course_cache($course->id, true);

        // Retrieve all related records from DB.
        $assigndb = $DB->get_record('assign', ['id' => $assign->id]);
        $moduletypedb = $DB->get_record('modules', ['name' => 'assign']);
        $moduledb = $DB->get_record('course_modules', ['module' => $moduletypedb->id, 'instance' => $assign->id]);
        $sectiondb = $DB->get_record('course_sections', ['id' => $moduledb->section]);
        $modnamessingular = get_module_types_names(false);
        $modnamesplural = get_module_types_names(true);

        // Create and enrol a student.
        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);
        $enrolplugin = enrol_get_plugin('manual');
        $enrolinstance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
        $enrolplugin->enrol_user($enrolinstance, $student->id);
        $this->setUser($student);

        // Emulate data used in building course cache to receive the same instance of
        // cached_cm_info as was used in building modinfo.
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

        // Since mod_assign supports groups.
        $this->assertEquals(
            $course->groupmodeforce ? $course->groupmode : $moduledb->groupmode,
            $cm->effectivegroupmode,
        );
        $this->assertEquals(VISIBLEGROUPS, $cm->effectivegroupmode);
        $this->assertEquals($moduledb->indent, $cm->indent);
        $this->assertEquals($moduledb->completion, $cm->completion);
        $this->assertEquals($moduledb->completiongradeitemnumber, $cm->completiongradeitemnumber);
        $this->assertEquals($moduledb->completionpassgrade, $cm->completionpassgrade);
        $this->assertEquals($moduledb->completionview, $cm->completionview);
        $this->assertEquals($moduledb->completionexpected, $cm->completionexpected);
        $this->assertEquals($moduledb->showdescription, $cm->showdescription);
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
        $this->assertEquals(new url('/mod/assign/view.php', ['id' => $moduledb->id]), $cm->url);
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

        // Deprecated fields.
        $this->assertEquals(null, $cm->extra); // Deprecated field. Used in module types that don't return cached_cm_info.
        $this->assertDebuggingCalled();
    }

    /**
     * Tests for function cm_info::get_course_module_record()
     */
    public function test_cm_info_get_course_module_record(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('enableavailability', 1);
        set_config('enablecompletion', 1);

        $course = $this->getDataGenerator()->create_course(
            ['format' => 'topics', 'numsections' => 3, 'enablecompletion' => 1],
            ['createsections' => true],
        );
        $mods = [];
        $mods[0] = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $mods[1] = $this->getDataGenerator()->create_module(
            'assign',
            [
                'course' => $course->id,
                'section' => 3,
                'idnumber' => '12345',
                'showdescription' => true,
            ],
        );
        // Pick a small valid availability value to use.
        $availabilityvalue = '{"op":"|","show":true,"c":[{"type":"date","d":">=","t":4}]}';
        $mods[2] = $this->getDataGenerator()->create_module(
            'book',
            [
                'course' => $course->id,
                'indent' => 5,
                'availability' => $availabilityvalue,
                'showdescription' => false,
                'completion' => true,
                'completionview' => true,
                'completionexpected' => time() + 5000,
            ],
        );
        $mods[3] = $this->getDataGenerator()->create_module(
            'forum',
            [
                'course' => $course->id,
                'visible' => 0,
                'groupmode' => 1,
            'availability' => null,
            ],
        );
        $mods[4] = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $course->id, 'grouping' => 12],
        );

        $modinfo = get_fast_modinfo($course->id);

        // Make sure that object returned by get_course_module_record(false) has exactly
        // the same fields as DB table 'course_modules'.
        $dbfields = array_keys($DB->get_columns('course_modules'));
        sort($dbfields);
        $cmrecord = $modinfo->get_cm($mods[0]->cmid)->get_course_module_record();
        $cmrecordfields = array_keys((array)$cmrecord);
        sort($cmrecordfields);
        $this->assertEquals($dbfields, $cmrecordfields);

        // Make sure that object returned by get_course_module_record(true) has exactly the same fields
        // as object returned by get_coursemodule_from_id(,,,true,).
        $cmrecordfull = $modinfo->get_cm($mods[0]->cmid)->get_course_module_record(true);
        $cmrecordfullfields = array_keys((array)$cmrecordfull);
        $cm = get_coursemodule_from_id(null, $mods[0]->cmid, 0, true, MUST_EXIST);
        $cmfields = array_keys((array)$cm);
        $this->assertEquals($cmfields, $cmrecordfullfields);

        // Make sure that object returned by get_course_module_record(true) has exactly the same fields
        // as object returned by get_coursemodule_from_instance(,,,true,).
        $cm = get_coursemodule_from_instance('forum', $mods[0]->id, null, true, MUST_EXIST);
        $cmfields = array_keys((array)$cm);
        $this->assertEquals($cmfields, $cmrecordfullfields);

        // Make sure the objects have the same properties.
        $cm1 = get_coursemodule_from_id(null, $mods[0]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('forum', $mods[0]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[0]->cmid);
        $record = $DB->get_record('course_modules', ['id' => $mods[0]->cmid]);
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));

        $cm1 = get_coursemodule_from_id(null, $mods[1]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('assign', $mods[1]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[1]->cmid);
        $record = $DB->get_record('course_modules', ['id' => $mods[1]->cmid]);
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));

        $cm1 = get_coursemodule_from_id(null, $mods[2]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('book', $mods[2]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[2]->cmid);
        $record = $DB->get_record('course_modules', ['id' => $mods[2]->cmid]);
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));

        $cm1 = get_coursemodule_from_id(null, $mods[3]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('forum', $mods[3]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[3]->cmid);
        $record = $DB->get_record('course_modules', ['id' => $mods[3]->cmid]);
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));

        $cm1 = get_coursemodule_from_id(null, $mods[4]->cmid, 0, true, MUST_EXIST);
        $cm2 = get_coursemodule_from_instance('forum', $mods[4]->id, 0, true, MUST_EXIST);
        $cminfo = $modinfo->get_cm($mods[4]->cmid);
        $record = $DB->get_record('course_modules', ['id' => $mods[4]->cmid]);
        $this->assertEquals($record, $cminfo->get_course_module_record());
        $this->assertEquals($cm1, $cminfo->get_course_module_record(true));
        $this->assertEquals($cm2, $cminfo->get_course_module_record(true));
    }

    /**
     * Tests for function cm_info::get_activitybadge().
     */
    public function test_cm_info_get_activitybadge(): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $resource = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $label = $this->getDataGenerator()->create_module('label', ['course' => $course->id]);

        $renderer = $PAGE->get_renderer('core');
        $modinfo = get_fast_modinfo($course->id);

        // Forum and resource implements the activitybadge feature.
        $cminfo = $modinfo->get_cm($forum->cmid);
        $this->assertNotNull($cminfo->get_activitybadge($renderer));
        $cminfo = $modinfo->get_cm($resource->cmid);
        $this->assertNotNull($cminfo->get_activitybadge($renderer));

        // Assign and label don't implement the activitybadge feature (at least for now).
        $cminfo = $modinfo->get_cm($assign->cmid);
        $this->assertNull($cminfo->get_activitybadge($renderer));
        $cminfo = $modinfo->get_cm($label->cmid);
        $this->assertNull($cminfo->get_activitybadge($renderer));
    }

    /**
     * Test get_sections_delegated_by_cm method.
     */
    public function test_get_delegated_section_info(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);

        // Add a section delegated by a course module.
        $subsection = $this->getDataGenerator()->create_module('subsection', ['course' => $course]);
        $otheractivity = $this->getDataGenerator()->create_module('page', ['course' => $course]);

        $modinfo = get_fast_modinfo($course);
        $delegatedsections = $modinfo->get_sections_delegated_by_cm();

        $delegated = $modinfo->get_cm($subsection->cmid)->get_delegated_section_info();
        $this->assertNotNull($delegated);
        $this->assertEquals($delegated, $delegatedsections[$subsection->cmid]);

        $delegated = $modinfo->get_cm($otheractivity->cmid)->get_delegated_section_info();
        $this->assertNull($delegated);
    }

    /**
     * Test for cm_info::get_instance_record.
     */
    public function test_section_get_instance_record(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 2]);
        $activity = $this->getDataGenerator()->create_module('page', ['course' => $course], ['section' => 0]);

        $modinfo = get_fast_modinfo($course->id);
        $cminfo = $modinfo->get_cm($activity->cmid);

        $instancerecord = $DB->get_record('page', ['id' => $activity->id]);

        $instance = $cminfo->get_instance_record();
        $this->assertEquals($instancerecord, $instance);

        // The instance record should be cached.
        $DB->delete_records('page', ['id' => $activity->id]);

        $instance2 = $cminfo->get_instance_record();
        $this->assertEquals($instancerecord, $instance);
        $this->assertEquals($instance, $instance2);
    }
}

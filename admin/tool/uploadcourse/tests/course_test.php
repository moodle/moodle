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
 * File containing tests for the course class.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Course test case.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class tool_uploadcourse_course_testcase extends advanced_testcase {

    public function test_proceed_without_prepare() {
        $this->resetAfterTest(true);
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array();
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->expectException(coding_exception::class);
        $co->proceed();
    }

    public function test_proceed_when_prepare_failed() {
        $this->resetAfterTest(true);
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array();
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->expectException(moodle_exception::class);
        $co->proceed();
    }

    public function test_proceed_when_already_started() {
        $this->resetAfterTest(true);
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('shortname' => 'test', 'fullname' => 'New course', 'summary' => 'New', 'category' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->expectException('coding_exception');
        $co->proceed();
    }

    public function test_invalid_shortname() {
        $this->resetAfterTest(true);
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('shortname' => '<invalid>', 'fullname' => 'New course', 'summary' => 'New', 'category' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('invalidshortname', $co->get_errors());
    }

    public function test_invalid_shortname_too_long() {
        $this->resetAfterTest();

        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;

        $upload = new tool_uploadcourse_course($mode, $updatemode, [
            'category' => 1,
            'fullname' => 'New course',
            'shortname' => str_repeat('X', 2000),
        ]);

        $this->assertFalse($upload->prepare());
        $this->assertArrayHasKey('invalidshortnametoolong', $upload->get_errors());
    }

    public function test_invalid_fullname_too_long() {
        $this->resetAfterTest();

        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;

        $upload = new tool_uploadcourse_course($mode, $updatemode, [
            'category' => 1,
            'fullname' => str_repeat('X', 2000),
        ]);

        $this->assertFalse($upload->prepare());
        $this->assertArrayHasKey('invalidfullnametoolong', $upload->get_errors());
    }

    public function test_invalid_visibility() {
        $this->resetAfterTest(true);
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('shortname' => 'test', 'fullname' => 'New course', 'summary' => 'New', 'category' => 1, 'visible' => 2);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('invalidvisibilitymode', $co->get_errors());
    }

    public function test_create() {
        global $DB;
        $this->resetAfterTest(true);

        // Existing course.
        $c1 = $this->getDataGenerator()->create_course(array('shortname' => 'c1', 'summary' => 'Yay!'));
        $this->assertTrue($DB->record_exists('course', array('shortname' => 'c1')));

        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;

        // Try to add a new course.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $data = array('shortname' => 'newcourse', 'fullname' => 'New course', 'summary' => 'New', 'category' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $this->assertFalse($DB->record_exists('course', array('shortname' => 'newcourse')));
        $co->proceed();
        $course = $DB->get_record('course', array('shortname' => 'newcourse'), '*', MUST_EXIST);
        $this->assertEquals(0, course_get_format($course)->get_course()->coursedisplay);

        // Try to add a new course, that already exists.
        $coursecount = $DB->count_records('course', array());
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $data = array('shortname' => 'c1', 'fullname' => 'C1FN', 'summary' => 'C1', 'category' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('courseexistsanduploadnotallowed', $co->get_errors());
        $this->assertEquals($coursecount, $DB->count_records('course', array()));
        $this->assertNotEquals('C1', $DB->get_field_select('course', 'summary', 'shortname = :s', array('s' => 'c1')));

        // Try to add new with shortname incrementation.
        $mode = tool_uploadcourse_processor::MODE_CREATE_ALL;
        $data = array('shortname' => 'c1', 'fullname' => 'C1FN', 'summary' => 'C1', 'category' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertTrue($DB->record_exists('course', array('shortname' => 'c2')));

        // Add a new course with non-default course format option.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $data = array('shortname' => 'c3', 'fullname' => 'C3', 'summary' => 'New c3', 'category' => 1,
            'format' => 'weeks', 'coursedisplay' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $course = $DB->get_record('course', array('shortname' => 'c3'), '*', MUST_EXIST);
        $this->assertEquals(1, course_get_format($course)->get_course()->coursedisplay);
    }

    public function test_create_with_sections() {
        global $DB;
        $this->resetAfterTest(true);
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $defaultnumsections = get_config('moodlecourse', 'numsections');

        // Add new course, make sure default number of sections is created.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $data = array('shortname' => 'newcourse1', 'fullname' => 'New course1', 'format' => 'topics', 'category' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $courseid = $DB->get_field('course', 'id', array('shortname' => 'newcourse1'));
        $this->assertNotEmpty($courseid);
        $this->assertEquals($defaultnumsections + 1,
            $DB->count_records('course_sections', ['course' => $courseid]));

        // Add new course specifying number of sections.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $data = array('shortname' => 'newcourse2', 'fullname' => 'New course2', 'format' => 'topics', 'category' => 1,
            'numsections' => 15);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $courseid = $DB->get_field('course', 'id', array('shortname' => 'newcourse2'));
        $this->assertNotEmpty($courseid);
        $this->assertEquals(15 + 1,
            $DB->count_records('course_sections', ['course' => $courseid]));
    }

    public function test_delete() {
        global $DB;
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();

        $this->assertTrue($DB->record_exists('course', array('shortname' => $c1->shortname)));
        $this->assertFalse($DB->record_exists('course', array('shortname' => 'DoesNotExist')));

        $mode = tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;

        // Try delete when option not available.
        $importoptions = array('candelete' => false);
        $data = array('shortname' => $c1->shortname, 'delete' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('coursedeletionnotallowed', $co->get_errors());
        $this->assertTrue($DB->record_exists('course', array('shortname' => $c1->shortname)));

        // Try delete when not requested.
        $importoptions = array('candelete' => true);
        $data = array('shortname' => $c1->shortname, 'delete' => 0);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertTrue($DB->record_exists('course', array('shortname' => $c1->shortname)));

        // Try delete when requested.
        $importoptions = array('candelete' => true);
        $data = array('shortname' => $c1->shortname, 'delete' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertFalse($DB->record_exists('course', array('shortname' => $c1->shortname)));
        $this->assertTrue($DB->record_exists('course', array('shortname' => $c2->shortname)));

        // Try deleting non-existing record, this should not fail.
        $data = array('shortname' => 'DoesNotExist', 'delete' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('cannotdeletecoursenotexist', $co->get_errors());
    }

    public function test_update() {
        global $DB;
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course(array('shortname' => 'c1'));

        // Try to update with existing shortnames, not allowing creation, and updating nothing.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('shortname' => 'c1', 'fullname' => 'New fullname');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('updatemodedoessettonothing', $co->get_errors());

        // Try to update with non-existing shortnames.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'DoesNotExist', 'fullname' => 'New fullname');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('coursedoesnotexistandcreatenotallowed', $co->get_errors());

        // Try a proper update.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'c1', 'fullname' => 'New fullname');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertEquals('New fullname', $DB->get_field_select('course', 'fullname', 'shortname = :s', array('s' => 'c1')));

        // Try a proper update with defaults.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_OR_DEFAUTLS;
        $data = array('shortname' => 'c1', 'fullname' => 'Another fullname');
        $defaults = array('fullname' => 'Not this one', 'summary' => 'Awesome summary');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, $defaults);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertEquals('Another fullname', $DB->get_field_select('course', 'fullname', 'shortname = :s', array('s' => 'c1')));
        $this->assertEquals('Awesome summary', $DB->get_field_select('course', 'summary', 'shortname = :s', array('s' => 'c1')));

        // Try a proper update missing only.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_MISSING_WITH_DATA_OR_DEFAUTLS;
        $DB->set_field('course', 'summary', '', array('shortname' => 'c1'));
        $this->assertEquals('', $DB->get_field_select('course', 'summary', 'shortname = :s', array('s' => 'c1')));
        $data = array('shortname' => 'c1', 'summary' => 'Fill in summary');
        $defaults = array('summary' => 'Do not use this summary');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, $defaults);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertEquals('Fill in summary', $DB->get_field_select('course', 'summary', 'shortname = :s', array('s' => 'c1')));

        // Try a proper update missing only using defaults.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_MISSING_WITH_DATA_OR_DEFAUTLS;
        $DB->set_field('course', 'summary', '', array('shortname' => 'c1'));
        $this->assertEquals('', $DB->get_field_select('course', 'summary', 'shortname = :s', array('s' => 'c1')));
        $data = array('shortname' => 'c1');
        $defaults = array('summary' => 'Use this summary');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, $defaults);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertEquals('Use this summary', $DB->get_field_select('course', 'summary', 'shortname = :s', array('s' => 'c1')));

        // Update course format option.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'c1', 'coursedisplay' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $course = $DB->get_record('course', array('shortname' => 'c1'), '*', MUST_EXIST);
        $this->assertEquals(1, course_get_format($course)->get_course()->coursedisplay);
    }

    public function test_data_saved() {
        global $DB;
        $this->resetAfterTest(true);

        $this->setAdminUser(); // To avoid warnings related to 'moodle/course:setforcedlanguage' capability check.

        // Create.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array(
            'shortname' => 'c1',
            'fullname' => 'Fullname',
            'category' => '1',
            'visible' => '0',
            'idnumber' => '123abc',
            'summary' => 'Summary',
            'format' => 'topics',
            'theme' => 'afterburner',
            'lang' => 'en',
            'newsitems' => '7',
            'showgrades' => '0',
            'showreports' => '1',
            'legacyfiles' => '1',
            'maxbytes' => '1234',
            'groupmode' => '2',
            'groupmodeforce' => '1',
            'enablecompletion' => '1',
            'tags' => 'Cat, Dog',

            'role_teacher' => 'Knight',
            'role_manager' => 'Jedi',

            'enrolment_1' => 'guest',
            'enrolment_2' => 'self',
            'enrolment_2_roleid' => '1',
            'enrolment_3' => 'manual',
            'enrolment_3_disable' => '1',
        );

        // There should be a start date if there is a end date.
        $data['enddate'] = '7 June 1990';
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('nostartdatenoenddate', $co->get_errors());

        $data['startdate'] = '8 June 1990';
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('enddatebeforestartdate', $co->get_errors());

        // They are correct now.
        $data['enddate'] = '18 June 1990';

        $this->assertFalse($DB->record_exists('course', array('shortname' => 'c1')));
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertTrue($DB->record_exists('course', array('shortname' => 'c1')));
        $course = $DB->get_record('course', array('shortname' => 'c1'));
        $ctx = context_course::instance($course->id);

        $this->assertEquals($data['fullname'], $course->fullname);
        $this->assertEquals($data['category'], $course->category);
        $this->assertEquals($data['visible'], $course->visible);
        $this->assertEquals(mktime(0, 0, 0, 6, 8, 1990), $course->startdate);
        $this->assertEquals(mktime(0, 0, 0, 6, 18, 1990), $course->enddate);
        $this->assertEquals($data['idnumber'], $course->idnumber);
        $this->assertEquals($data['summary'], $course->summary);
        $this->assertEquals($data['format'], $course->format);
        $this->assertEquals($data['theme'], $course->theme);
        $this->assertEquals($data['lang'], $course->lang);
        $this->assertEquals($data['newsitems'], $course->newsitems);
        $this->assertEquals($data['showgrades'], $course->showgrades);
        $this->assertEquals($data['showreports'], $course->showreports);
        $this->assertEquals($data['legacyfiles'], $course->legacyfiles);
        $this->assertEquals($data['maxbytes'], $course->maxbytes);
        $this->assertEquals($data['groupmode'], $course->groupmode);
        $this->assertEquals($data['groupmodeforce'], $course->groupmodeforce);
        $this->assertEquals($data['enablecompletion'], $course->enablecompletion);
        $this->assertEquals($data['tags'], join(', ', core_tag_tag::get_item_tags_array('core', 'course', $course->id)));

        // Roles.
        $roleids = array();
        $roles = get_all_roles();
        foreach ($roles as $role) {
            $roleids[$role->shortname] = $role->id;
        }
        $this->assertEquals('Knight', $DB->get_field_select('role_names', 'name',
            'roleid = :roleid AND contextid = :ctxid', array('ctxid' => $ctx->id, 'roleid' => $roleids['teacher'])));
        $this->assertEquals('Jedi', $DB->get_field_select('role_names', 'name',
            'roleid = :roleid AND contextid = :ctxid', array('ctxid' => $ctx->id, 'roleid' => $roleids['manager'])));

        // Enrolment methods.
        $enroldata = array();
        $instances = enrol_get_instances($course->id, false);
        $this->assertCount(3, $instances);
        foreach ($instances as $instance) {
            $enroldata[$instance->enrol] = $instance;
        }

        $this->assertNotEmpty($enroldata['guest']);
        $this->assertEquals(ENROL_INSTANCE_ENABLED, $enroldata['guest']->status);
        $this->assertNotEmpty($enroldata['self']);
        $this->assertEquals($data['enrolment_2_roleid'], $enroldata['self']->roleid);
        $this->assertEquals(ENROL_INSTANCE_ENABLED, $enroldata['self']->status);
        $this->assertNotEmpty($enroldata['manual']);
        $this->assertEquals(ENROL_INSTANCE_DISABLED, $enroldata['manual']->status);

        // Update existing course.
        $cat = $this->getDataGenerator()->create_category();
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array(
            'shortname' => 'c1',
            'fullname' => 'Fullname 2',
            'category' => $cat->id,
            'visible' => '1',
            'idnumber' => 'changeidn',
            'summary' => 'Summary 2',
            'format' => 'topics',
            'theme' => 'classic',
            'lang' => '',
            'newsitems' => '2',
            'showgrades' => '1',
            'showreports' => '0',
            'legacyfiles' => '0',
            'maxbytes' => '4321',
            'groupmode' => '1',
            'groupmodeforce' => '0',
            'enablecompletion' => '0',

            'role_teacher' => 'Teacher',
            'role_manager' => 'Manager',

            'enrolment_1' => 'guest',
            'enrolment_1_disable' => '1',
            'enrolment_2' => 'self',
            'enrolment_2_roleid' => '2',
            'enrolment_3' => 'manual',
            'enrolment_3_delete' => '1',
        );

        $this->assertTrue($DB->record_exists('course', array('shortname' => 'c1')));

        $data['enddate'] = '31 June 1984';
        // Previous start and end dates are 8 and 18 June 1990.
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('enddatebeforestartdate', $co->get_errors());

        $data['startdate'] = '19 June 1990';
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('enddatebeforestartdate', $co->get_errors());

        // They are correct now.
        $data['startdate'] = '11 June 1984';

        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $course = $DB->get_record('course', array('shortname' => 'c1'));
        $ctx = context_course::instance($course->id);

        $this->assertEquals($data['fullname'], $course->fullname);
        $this->assertEquals($data['category'], $course->category);
        $this->assertEquals($data['visible'], $course->visible);
        $this->assertEquals(mktime(0, 0, 0, 6, 11, 1984), $course->startdate);
        $this->assertEquals(mktime(0, 0, 0, 6, 31, 1984), $course->enddate);
        $this->assertEquals($data['idnumber'], $course->idnumber);
        $this->assertEquals($data['summary'], $course->summary);
        $this->assertEquals($data['format'], $course->format);
        $this->assertEquals($data['theme'], $course->theme);
        $this->assertEquals($data['lang'], $course->lang);
        $this->assertEquals($data['newsitems'], $course->newsitems);
        $this->assertEquals($data['showgrades'], $course->showgrades);
        $this->assertEquals($data['showreports'], $course->showreports);
        $this->assertEquals($data['legacyfiles'], $course->legacyfiles);
        $this->assertEquals($data['maxbytes'], $course->maxbytes);
        $this->assertEquals($data['groupmode'], $course->groupmode);
        $this->assertEquals($data['groupmodeforce'], $course->groupmodeforce);
        $this->assertEquals($data['enablecompletion'], $course->enablecompletion);

        // Roles.
        $roleids = array();
        $roles = get_all_roles();
        foreach ($roles as $role) {
            $roleids[$role->shortname] = $role->id;
        }
        $this->assertEquals('Teacher', $DB->get_field_select('role_names', 'name',
            'roleid = :roleid AND contextid = :ctxid', array('ctxid' => $ctx->id, 'roleid' => $roleids['teacher'])));
        $this->assertEquals('Manager', $DB->get_field_select('role_names', 'name',
            'roleid = :roleid AND contextid = :ctxid', array('ctxid' => $ctx->id, 'roleid' => $roleids['manager'])));

        // Enrolment methods.
        $enroldata = array();
        $instances = enrol_get_instances($course->id, false);
        $this->assertCount(2, $instances);
        foreach ($instances as $instance) {
            $enroldata[$instance->enrol] = $instance;
        }

        $this->assertNotEmpty($enroldata['guest']);
        $this->assertEquals(ENROL_INSTANCE_DISABLED, $enroldata['guest']->status);
        $this->assertNotEmpty($enroldata['self']);
        $this->assertEquals($data['enrolment_2_roleid'], $enroldata['self']->roleid);
        $this->assertEquals(ENROL_INSTANCE_ENABLED, $enroldata['self']->status);
    }

    public function test_default_data_saved() {
        global $DB;
        $this->resetAfterTest(true);

        // Create.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array(
            'shortname' => 'c1',
        );
        $defaultdata = array(
            'fullname' => 'Fullname',
            'category' => '1',
            'visible' => '0',
            'startdate' => 644803200,
            'enddate' => 645667200,
            'idnumber' => '123abc',
            'summary' => 'Summary',
            'format' => 'topics',
            'theme' => 'afterburner',
            'lang' => 'en',
            'newsitems' => '7',
            'showgrades' => '0',
            'showreports' => '1',
            'legacyfiles' => '1',
            'maxbytes' => '1234',
            'groupmode' => '2',
            'groupmodeforce' => '1',
            'enablecompletion' => '1',
        );

        $this->assertFalse($DB->record_exists('course', array('shortname' => 'c1')));
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, $defaultdata);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertTrue($DB->record_exists('course', array('shortname' => 'c1')));
        $course = $DB->get_record('course', array('shortname' => 'c1'));
        $ctx = context_course::instance($course->id);

        $this->assertEquals($defaultdata['fullname'], $course->fullname);
        $this->assertEquals($defaultdata['category'], $course->category);
        $this->assertEquals($defaultdata['visible'], $course->visible);
        $this->assertEquals($defaultdata['startdate'], $course->startdate);
        $this->assertEquals($defaultdata['enddate'], $course->enddate);
        $this->assertEquals($defaultdata['idnumber'], $course->idnumber);
        $this->assertEquals($defaultdata['summary'], $course->summary);
        $this->assertEquals($defaultdata['format'], $course->format);
        $this->assertEquals($defaultdata['theme'], $course->theme);
        $this->assertEquals($defaultdata['lang'], $course->lang);
        $this->assertEquals($defaultdata['newsitems'], $course->newsitems);
        $this->assertEquals($defaultdata['showgrades'], $course->showgrades);
        $this->assertEquals($defaultdata['showreports'], $course->showreports);
        $this->assertEquals($defaultdata['legacyfiles'], $course->legacyfiles);
        $this->assertEquals($defaultdata['maxbytes'], $course->maxbytes);
        $this->assertEquals($defaultdata['groupmode'], $course->groupmode);
        $this->assertEquals($defaultdata['groupmodeforce'], $course->groupmodeforce);
        $this->assertEquals($defaultdata['enablecompletion'], $course->enablecompletion);

        // Update.
        $cat = $this->getDataGenerator()->create_category();
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_OR_DEFAUTLS;
        $data = array(
            'shortname' => 'c1',
        );
        $defaultdata = array(
            'fullname' => 'Fullname 2',
            'category' => $cat->id,
            'visible' => '1',
            'startdate' => 455760000,
            'enddate' => 457488000,
            'idnumber' => 'changedid',
            'summary' => 'Summary 2',
            'format' => 'topics',
            'theme' => 'classic',
            'lang' => '',
            'newsitems' => '2',
            'showgrades' => '1',
            'showreports' => '0',
            'legacyfiles' => '0',
            'maxbytes' => '1111',
            'groupmode' => '1',
            'groupmodeforce' => '0',
            'enablecompletion' => '0',
        );

        $this->assertTrue($DB->record_exists('course', array('shortname' => 'c1')));
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, $defaultdata);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertTrue($DB->record_exists('course', array('shortname' => 'c1')));
        $course = $DB->get_record('course', array('shortname' => 'c1'));
        $ctx = context_course::instance($course->id);

        $this->assertEquals($defaultdata['fullname'], $course->fullname);
        $this->assertEquals($defaultdata['category'], $course->category);
        $this->assertEquals($defaultdata['visible'], $course->visible);
        $this->assertEquals($defaultdata['startdate'], $course->startdate);
        $this->assertEquals($defaultdata['enddate'], $course->enddate);
        $this->assertEquals($defaultdata['idnumber'], $course->idnumber);
        $this->assertEquals($defaultdata['summary'], $course->summary);
        $this->assertEquals($defaultdata['format'], $course->format);
        $this->assertEquals($defaultdata['theme'], $course->theme);
        $this->assertEquals($defaultdata['lang'], $course->lang);
        $this->assertEquals($defaultdata['newsitems'], $course->newsitems);
        $this->assertEquals($defaultdata['showgrades'], $course->showgrades);
        $this->assertEquals($defaultdata['showreports'], $course->showreports);
        $this->assertEquals($defaultdata['legacyfiles'], $course->legacyfiles);
        $this->assertEquals($defaultdata['maxbytes'], $course->maxbytes);
        $this->assertEquals($defaultdata['groupmode'], $course->groupmode);
        $this->assertEquals($defaultdata['groupmodeforce'], $course->groupmodeforce);
        $this->assertEquals($defaultdata['enablecompletion'], $course->enablecompletion);
    }

    public function test_rename() {
        global $DB;
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course(array('shortname' => 'c1'));
        $c2 = $this->getDataGenerator()->create_course(array('shortname' => 'c2'));

        // Cannot rename when creating.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $importoptions = array('canrename' => true);
        $data = array('shortname' => 'c1', 'rename' => 'newshortname');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('courseexistsanduploadnotallowed', $co->get_errors());

        // Cannot rename when creating.
        $mode = tool_uploadcourse_processor::MODE_CREATE_ALL;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $importoptions = array('canrename' => true);
        $data = array('shortname' => 'c1', 'rename' => 'newshortname', 'category' => 1, 'summary' => 'S', 'fullname' => 'F');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('canonlyrenameinupdatemode', $co->get_errors());

        // Error when not allowed to rename the course.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $importoptions = array('canrename' => false);
        $data = array('shortname' => 'c1', 'rename' => 'newshortname');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('courserenamingnotallowed', $co->get_errors());

        // Can rename when updating.
        $mode = tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $importoptions = array('canrename' => true);
        $data = array('shortname' => 'c1', 'rename' => 'newshortname');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertEquals('newshortname', $DB->get_field_select('course', 'shortname', 'id = :id', array('id' => $c1->id)));

        // Can rename when updating.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $importoptions = array('canrename' => true);
        $data = array('shortname' => 'newshortname', 'rename' => 'newshortname2');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertEquals('newshortname2', $DB->get_field_select('course', 'shortname', 'id = :id', array('id' => $c1->id)));

        // Error when course does not exist.
        $mode = tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $importoptions = array('canrename' => true);
        $data = array('shortname' => 'DoesNotExist', 'rename' => 'c1', 'category' => 1, 'summary' => 'S', 'fullname' => 'F');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('cannotrenamecoursenotexist', $co->get_errors());

        // Renaming still updates the other values.
        $mode = tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_OR_DEFAUTLS;
        $importoptions = array('canrename' => true);
        $data = array('shortname' => 'newshortname2', 'rename' => 'c1', 'fullname' => 'Another fullname!');
        $defaultdata = array('summary' => 'New summary!');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, $defaultdata, $importoptions);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertEquals('c1', $DB->get_field_select('course', 'shortname', 'id = :id', array('id' => $c1->id)));
        $this->assertEquals('New summary!', $DB->get_field_select('course', 'summary', 'id = :id', array('id' => $c1->id)));
        $this->assertEquals('Another fullname!', $DB->get_field_select('course', 'fullname', 'id = :id', array('id' => $c1->id)));

        // Renaming with invalid shortname.
        $mode = tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $importoptions = array('canrename' => true);
        $data = array('shortname' => 'c1', 'rename' => '<span>invalid</span>');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('invalidshortname', $co->get_errors());

        // Renaming with invalid shortname.
        $mode = tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $importoptions = array('canrename' => true);
        $data = array('shortname' => 'c1', 'rename' => 'c2');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('cannotrenameshortnamealreadyinuse', $co->get_errors());
    }

    public function test_restore_course() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $c1 = $this->getDataGenerator()->create_course();
        $c1f1 = $this->getDataGenerator()->create_module('forum', array('course' => $c1->id));

        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'A1', 'templatecourse' => $c1->shortname, 'summary' => 'A', 'category' => 1,
            'fullname' => 'A1');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $course = $DB->get_record('course', array('shortname' => 'A1'));
        $modinfo = get_fast_modinfo($course);
        $found = false;
        foreach ($modinfo->get_cms() as $cmid => $cm) {
            if ($cm->modname == 'forum' && $cm->name == $c1f1->name) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        // Restoring twice from the same course should work.
        $data = array('shortname' => 'B1', 'templatecourse' => $c1->shortname, 'summary' => 'B', 'category' => 1,
            'fullname' => 'B1');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $course = $DB->get_record('course', array('shortname' => 'B1'));
        $modinfo = get_fast_modinfo($course);
        $found = false;
        foreach ($modinfo->get_cms() as $cmid => $cm) {
            if ($cm->modname == 'forum' && $cm->name == $c1f1->name) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function test_restore_file() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $c1 = $this->getDataGenerator()->create_course();
        $c1f1 = $this->getDataGenerator()->create_module('forum', array('course' => $c1->id));

        // Restore from a file, checking that the file takes priority over the templatecourse.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'A1', 'backupfile' => __DIR__ . '/fixtures/backup.mbz',
            'summary' => 'A', 'category' => 1, 'fullname' => 'A1', 'templatecourse' => $c1->shortname);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $course = $DB->get_record('course', array('shortname' => 'A1'));
        $modinfo = get_fast_modinfo($course);
        $found = false;
        foreach ($modinfo->get_cms() as $cmid => $cm) {
            if ($cm->modname == 'glossary' && $cm->name == 'Imported Glossary') {
                $found = true;
            } else if ($cm->modname == 'forum' && $cm->name == $c1f1->name) {
                // We should not find this!
                $this->assertTrue(false);
            }
        }
        $this->assertTrue($found);

        // Restoring twice from the same file should work.
        $data = array('shortname' => 'B1', 'backupfile' => __DIR__ . '/fixtures/backup.mbz',
            'summary' => 'B', 'category' => 1, 'fullname' => 'B1');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $course = $DB->get_record('course', array('shortname' => 'B1'));
        $modinfo = get_fast_modinfo($course);
        $found = false;
        foreach ($modinfo->get_cms() as $cmid => $cm) {
            if ($cm->modname == 'glossary' && $cm->name == 'Imported Glossary') {
                $found = true;
            } else if ($cm->modname == 'forum' && $cm->name == $c1f1->name) {
                // We should not find this!
                $this->assertTrue(false);
            }
        }
        $this->assertTrue($found);
    }

    /**
     * Test that specifying course template respects default restore settings
     */
    public function test_restore_file_settings() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Set admin config setting so that activities are not restored by default.
        set_config('restore_general_activities', 0, 'restore');

        $c1 = $this->getDataGenerator()->create_course();

        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'A1', 'backupfile' => __DIR__ . '/fixtures/backup.mbz',
            'summary' => 'A', 'category' => 1, 'fullname' => 'A1', 'templatecourse' => $c1->shortname);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $course = $DB->get_record('course', array('shortname' => 'A1'));

        // Make sure the glossary is not restored.
        $modinfo = get_fast_modinfo($course);
        $this->assertEmpty($modinfo->get_instances_of('glossary'));
    }

    public function test_restore_invalid_file() {
        $this->resetAfterTest();

        // Restore from a non-existing file should not be allowed.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'A1', 'backupfile' => '/lead/no/where',
            'category' => 1, 'fullname' => 'A1');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('cannotreadbackupfile', $co->get_errors());

        // Restore from an invalid file should not be allowed.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'A1', 'backupfile' => __FILE__,
            'category' => 1, 'fullname' => 'A1');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);

        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('invalidbackupfile', $co->get_errors());

        // Zip packer throws a debugging message, this assertion is only here to prevent
        // the message from being displayed.
        $this->assertDebuggingCalled();
    }

    public function test_restore_invalid_course() {
        $this->resetAfterTest();

        // Restore from an invalid file should not be allowed.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'A1', 'templatecourse' => 'iamnotavalidcourse',
            'category' => 1, 'fullname' => 'A1');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('coursetorestorefromdoesnotexist', $co->get_errors());
    }

    /**
     * Testing the reset on groups, group members and enrolments.
     */
    public function test_reset() {
        global $DB;
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c1ctx = context_course::instance($c1->id);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id, $studentrole->id);
        $this->assertCount(1, get_enrolled_users($c1ctx));

        $g1 = $this->getDataGenerator()->create_group(array('courseid' => $c1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $g1->id, 'userid' => $u1->id));
        $this->assertEquals(1, $DB->count_records('groups', array('courseid' => $c1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $g1->id, 'userid' => $u1->id)));

        // Wrong mode.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'DoesNotExist', 'reset' => '1', 'summary' => 'summary', 'fullname' => 'FN', 'category' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('canonlyresetcourseinupdatemode', $co->get_errors());
        $this->assertTrue($DB->record_exists('groups', array('id' => $g1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $g1->id, 'userid' => $u1->id)));
        $this->assertCount(1, get_enrolled_users($c1ctx));

        // Reset not allowed.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $c1->shortname, 'reset' => '1');
        $importoptions = array('canreset' => false);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('courseresetnotallowed', $co->get_errors());
        $this->assertTrue($DB->record_exists('groups', array('id' => $g1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $g1->id, 'userid' => $u1->id)));
        $this->assertCount(1, get_enrolled_users($c1ctx));

        // Reset allowed but not requested.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $c1->shortname, 'reset' => '0');
        $importoptions = array('canreset' => true);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertTrue($DB->record_exists('groups', array('id' => $g1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $g1->id, 'userid' => $u1->id)));
        $this->assertCount(1, get_enrolled_users($c1ctx));

        // Reset passed as a default parameter, should not be taken in account.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $c1->shortname);
        $importoptions = array('canreset' => true);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array('reset' => 1), $importoptions);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertTrue($DB->record_exists('groups', array('id' => $g1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $g1->id, 'userid' => $u1->id)));
        $this->assertCount(1, get_enrolled_users($c1ctx));

        // Reset executed from data.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $c1->shortname, 'reset' => 1);
        $importoptions = array('canreset' => true);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertFalse($DB->record_exists('groups', array('id' => $g1->id)));
        $this->assertFalse($DB->record_exists('groups_members', array('groupid' => $g1->id, 'userid' => $u1->id)));
        $this->assertCount(0, get_enrolled_users($c1ctx));

        // Reset executed from import option.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $c1->shortname, 'reset' => 0);
        $importoptions = array('reset' => 1, 'canreset' => true);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);

        $g1 = $this->getDataGenerator()->create_group(array('courseid' => $c1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $g1->id, 'userid' => $u1->id));
        $this->assertEquals(1, $DB->count_records('groups', array('courseid' => $c1->id)));
        $this->assertTrue($co->prepare());
        $co->proceed();
        $this->assertFalse($DB->record_exists('groups', array('id' => $g1->id)));
    }

    public function test_create_bad_category() {
        global $DB;
        $this->resetAfterTest(true);

        // Ensure fails when category cannot be resolved upon creation.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'c1', 'summary' => 'summary', 'fullname' => 'FN', 'category' => 'Wrong cat');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('couldnotresolvecatgorybyid', $co->get_errors());

        // Ensure fails when category is 0 on create.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'c1', 'summary' => 'summary', 'fullname' => 'FN', 'category' => '0');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('missingmandatoryfields', $co->get_errors());

        // Ensure fails when category cannot be resolved upon update.
        $c1 = $this->getDataGenerator()->create_course();
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $c1->shortname, 'category' => 'Wrong cat');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('couldnotresolvecatgorybyid', $co->get_errors());

        // Ensure does not update the category when it is 0.
        $c1 = $this->getDataGenerator()->create_course();
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $c1->shortname, 'category' => '0');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $this->assertEmpty($co->get_errors());
        $this->assertEmpty($co->get_statuses());
        $co->proceed();
        $this->assertEquals($c1->category, $DB->get_field('course', 'category', array('id' => $c1->id)));

        // Ensure does not update the category when it is set to 0 in the defaults.
        $c1 = $this->getDataGenerator()->create_course();
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_OR_DEFAUTLS;
        $data = array('shortname' => $c1->shortname);
        $defaults = array('category' => '0');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, $defaults);
        $this->assertTrue($co->prepare());
        $this->assertEmpty($co->get_errors());
        $this->assertEmpty($co->get_statuses());
        $co->proceed();
        $this->assertEquals($c1->category, $DB->get_field('course', 'category', array('id' => $c1->id)));
    }

    public function test_enrolment_data() {
        $this->resetAfterTest(true);

        // We need to set the current user as one with the capability to edit manual enrolment instances in the new course.
        $this->setAdminUser();

        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'c1', 'summary' => 'S', 'fullname' => 'FN', 'category' => '1');
        $data['enrolment_1'] = 'manual';
        $data['enrolment_1_role'] = 'teacher';
        $data['enrolment_1_startdate'] = '2nd July 2013';
        $data['enrolment_1_enddate'] = '2nd August 2013';
        $data['enrolment_1_enrolperiod'] = '10 days';
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertTrue($co->prepare());
        $co->proceed();

        // Enrolment methods.
        $enroldata = array();
        $instances = enrol_get_instances($co->get_id(), false);
        foreach ($instances as $instance) {
            $enroldata[$instance->enrol] = $instance;
        }

        $this->assertNotEmpty($enroldata['manual']);
        $this->assertEquals(ENROL_INSTANCE_ENABLED, $enroldata['manual']->status);
        $this->assertEquals(strtotime($data['enrolment_1_startdate']), $enroldata['manual']->enrolstartdate);
        $this->assertEquals(strtotime('1970-01-01 GMT + ' . $data['enrolment_1_enrolperiod']), $enroldata['manual']->enrolperiod);
        $this->assertEquals(strtotime('12th July 2013'), $enroldata['manual']->enrolenddate);
    }

    /**
     * Data provider for testing enrolment errors
     *
     * @return array
     */
    public function enrolment_uploaddata_error_provider(): array {
        return [
            ['errorcannotcreateorupdateenrolment', [
                'shortname' => 'C1',
                'enrolment_1' => 'manual',
            ]],
            ['errorcannotdeleteenrolment', [
                'shortname' => 'C1',
                'enrolment_1' => 'manual',
                'enrolment_1_delete' => '1',
            ]],
            ['errorcannotdisableenrolment', [
                'shortname' => 'C1',
                'enrolment_1' => 'manual',
                'enrolment_1_disable' => '1',
            ]],
        ];
    }

    /**
     * Test that user without permission, cannot modify enrolment instances when creating courses
     *
     * @param string $expectederror
     * @param array $uploaddata
     *
     * @dataProvider enrolment_uploaddata_error_provider
     */
    public function test_enrolment_error_create_course(string $expectederror, array $uploaddata): void {
        global $DB;

        $this->resetAfterTest();

        // Create category in which to create the new course.
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = context_coursecat::instance($category->id);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Assign the user as a manager of the category, disable ability to configure manual enrolment instances.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($roleid, $user->id, $categorycontext);
        role_change_permission($roleid, $categorycontext, 'enrol/manual:config', CAP_PROHIBIT);

        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;

        $upload = new tool_uploadcourse_course($mode, $updatemode, array_merge($uploaddata, [
            'category' => $category->id,
            'fullname' => 'My course',
        ]));

        // Enrolment validation isn't performed during 'prepare' for new courses.
        $this->assertTrue($upload->prepare());
        $upload->proceed();

        $errors = $upload->get_errors();
        $this->assertArrayHasKey($expectederror, $errors);

        $this->assertEquals(get_string($expectederror, 'tool_uploadcourse', 'Manual enrolments'),
            (string) $errors[$expectederror]);
    }

    /**
     * Test that user without permission, cannot modify enrolment instances when updating courses
     *
     * @param string $expectederror
     * @param array $uploaddata
     *
     * @dataProvider enrolment_uploaddata_error_provider
     */
    public function test_enrolment_error_update_course(string $expectederror, array $uploaddata): void {
        global $DB;

        $this->resetAfterTest();

        // Create category in which to create the new course.
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = context_coursecat::instance($category->id);

        $course = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'shortname' => $uploaddata['shortname'],
        ]);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Assign the user as a manager of the category, disable ability to configure manual enrolment instances.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($roleid, $user->id, $categorycontext);
        role_change_permission($roleid, $categorycontext, 'enrol/manual:config', CAP_PROHIBIT);

        // Sanity check.
        $instances = enrol_get_instances($course->id, true);
        $this->assertCount(1, $instances);
        $this->assertEquals('manual', reset($instances)->enrol);

        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;

        $upload = new tool_uploadcourse_course($mode, $updatemode, $uploaddata);

        $this->assertFalse($upload->prepare());

        $errors = $upload->get_errors();
        $this->assertArrayHasKey($expectederror, $errors);

        $this->assertEquals(get_string($expectederror, 'tool_uploadcourse', 'Manual enrolments'),
            (string) $errors[$expectederror]);
    }

    /**
     * Test upload processing of course custom fields
     */
    public function test_custom_fields_data() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['shortname' => 'C1']);

        // Create our custom fields.
        $category = $this->get_customfield_generator()->create_category();
        $this->create_custom_field($category, 'date', 'mydatefield');
        $this->create_custom_field($category, 'text', 'mytextfield');
        $this->create_custom_field($category, 'textarea', 'mytextareafield');

        // Perform upload.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $dataupload = [
            'shortname' => $course->shortname,
            'customfield_mydatefield' => '2020-04-01 16:00',
            'customfield_mytextfield' => 'Hello',
            'customfield_mytextareafield' => 'Is it me you\'re looking for?',
        ];

        $uploader = new tool_uploadcourse_course($mode, $updatemode, $dataupload);
        $this->assertTrue($uploader->prepare());
        $uploader->proceed();

        // Confirm presence of course custom fields.
        $data = \core_course\customfield\course_handler::create()->export_instance_data_object($course->id);
        $this->assertEquals('Wednesday, 1 April 2020, 4:00 PM', $data->mydatefield, '', 0.0, 10, false, true);
        $this->assertEquals($dataupload['customfield_mytextfield'], $data->mytextfield);
        $this->assertStringContainsString($dataupload['customfield_mytextareafield'], $data->mytextareafield);
    }

    /**
     * Test upload processing of course custom field that is required but empty
     */
    public function test_custom_fields_data_required() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['shortname' => 'C1']);

        // Create our custom field.
        $category = $this->get_customfield_generator()->create_category();
        $this->create_custom_field($category, 'select', 'myselect', ['required' => true, 'options' => "Cat\nDog"]);

        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $dataupload = [
            'shortname' => $course->shortname,
            'customfield_myselect' => null,
        ];

        $uploader = new tool_uploadcourse_course($mode, $updatemode, $dataupload);
        $this->assertFalse($uploader->prepare());
        $this->assertArrayHasKey('customfieldinvalid', $uploader->get_errors());

        // Try again with a default value.
        $defaults = [
            'customfield_myselect' => 2, // Our second option: Dog.
        ];

        $uploader = new tool_uploadcourse_course($mode, $updatemode, $dataupload, $defaults);
        $this->assertTrue($uploader->prepare());
        $uploader->proceed();

        // Confirm presence of course custom fields.
        $data = \core_course\customfield\course_handler::create()->export_instance_data_object($course->id);
        $this->assertEquals('Dog', $data->myselect);
    }

    /**
     * Test upload processing of course custom field with an invalid select option
     */
    public function test_custom_fields_data_invalid_select_option() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['shortname' => 'C1']);

        // Create our custom field.
        $category = $this->get_customfield_generator()->create_category();
        $this->create_custom_field($category, 'select', 'myselect',
            ['required' => true, 'options' => "Cat\nDog", 'defaultvalue' => 'Cat']);

        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $dataupload = [
            'shortname' => $course->shortname,
            'customfield_myselect' => 'Fish', // No, invalid.
        ];

        $uploader = new tool_uploadcourse_course($mode, $updatemode, $dataupload);
        $this->assertTrue($uploader->prepare());
        $uploader->proceed();

        // Confirm presence of course custom fields.
        $data = \core_course\customfield\course_handler::create()->export_instance_data_object($course->id);
        $this->assertEquals('Cat', $data->myselect);
    }

    /**
     * Test upload processing of course custom field with an out of range date
     */
    public function test_custom_fields_data_invalid_date() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['shortname' => 'C1']);

        // Create our custom field.
        $category = $this->get_customfield_generator()->create_category();
        $this->create_custom_field($category, 'date', 'mydate',
            ['mindate' => strtotime('2020-04-01'), 'maxdate' => '2020-04-30']);

        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $dataupload = [
            'shortname' => $course->shortname,
            'customfield_mydate' => '2020-05-06', // Out of range.
        ];

        $uploader = new tool_uploadcourse_course($mode, $updatemode, $dataupload);
        $this->assertFalse($uploader->prepare());
        $this->assertArrayHasKey('customfieldinvalid', $uploader->get_errors());
    }

    public function test_idnumber_problems() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course(array('shortname' => 'sntaken', 'idnumber' => 'taken'));
        $c2 = $this->getDataGenerator()->create_course();

        // Create with existing ID number.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => 'c2', 'summary' => 'summary', 'fullname' => 'FN', 'category' => '1',
            'idnumber' => $c1->idnumber);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('idnumberalreadyinuse', $co->get_errors());

        // Rename to existing ID number.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $c2->shortname, 'rename' => 'SN', 'idnumber' => $c1->idnumber);
        $importoptions = array('canrename' => true);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('cannotrenameidnumberconflict', $co->get_errors());

        // Incrementing shortname increments idnumber.
        $mode = tool_uploadcourse_processor::MODE_CREATE_ALL;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('shortname' => $c1->shortname, 'idnumber' => $c1->idnumber, 'summary' => 'S', 'fullname' => 'F',
            'category' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), array());
        $this->assertTrue($co->prepare());
        $this->assertArrayHasKey('courseshortnameincremented', $co->get_statuses());
        $this->assertArrayHasKey('courseidnumberincremented', $co->get_statuses());
        $data = $co->get_data();
        $this->assertEquals('sntaken_2', $data['shortname']);
        $this->assertEquals('taken_2', $data['idnumber']);

        // Incrementing shortname increments idnumber unless available.
        $mode = tool_uploadcourse_processor::MODE_CREATE_ALL;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('shortname' => $c1->shortname, 'idnumber' => 'nottaken', 'summary' => 'S', 'fullname' => 'F',
            'category' => 1);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), array());
        $this->assertTrue($co->prepare());
        $this->assertArrayHasKey('courseshortnameincremented', $co->get_statuses());
        $this->assertArrayNotHasKey('courseidnumberincremented', $co->get_statuses());
        $data = $co->get_data();
        $this->assertEquals('sntaken_2', $data['shortname']);
        $this->assertEquals('nottaken', $data['idnumber']);
    }

    public function test_generate_shortname() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course(array('shortname' => 'taken'));

        // Generate a shortname.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('summary' => 'summary', 'fullname' => 'FN', 'category' => '1', 'idnumber' => 'IDN');
        $importoptions = array('shortnametemplate' => '%i');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertTrue($co->prepare());
        $this->assertArrayHasKey('courseshortnamegenerated', $co->get_statuses());

        // Generate a shortname without a template.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('summary' => 'summary', 'fullname' => 'FN', 'category' => '1');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), array());
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('missingshortnamenotemplate', $co->get_errors());

        // Generate a shortname in update mode.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('summary' => 'summary', 'fullname' => 'FN', 'category' => '1');
        $importoptions = array('shortnametemplate' => '%f');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        // Commented because we never get here as the course without shortname does not exist.
        // $this->assertArrayHasKey('cannotgenerateshortnameupdatemode', $co->get_errors());

        // Generate a shortname to a course that already exists.
        $mode = tool_uploadcourse_processor::MODE_CREATE_NEW;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('summary' => 'summary', 'fullname' => 'taken', 'category' => '1');
        $importoptions = array('shortnametemplate' => '%f');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('generatedshortnamealreadyinuse', $co->get_errors());

        // Generate a shortname to a course that already exists will be incremented.
        $mode = tool_uploadcourse_processor::MODE_CREATE_ALL;
        $updatemode = tool_uploadcourse_processor::UPDATE_NOTHING;
        $data = array('summary' => 'summary', 'fullname' => 'taken', 'category' => '1');
        $importoptions = array('shortnametemplate' => '%f');
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertTrue($co->prepare());
        $this->assertArrayHasKey('courseshortnamegenerated', $co->get_statuses());
        $this->assertArrayHasKey('courseshortnameincremented', $co->get_statuses());
    }

    public function test_mess_with_frontpage() {
        global $SITE;
        $this->resetAfterTest(true);

        // Updating the front page.
        $mode = tool_uploadcourse_processor::MODE_UPDATE_ONLY;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $SITE->shortname, 'idnumber' => 'NewIDN');
        $importoptions = array();
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('cannotupdatefrontpage', $co->get_errors());

        // Updating the front page.
        $mode = tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $SITE->shortname, 'idnumber' => 'NewIDN');
        $importoptions = array();
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('cannotupdatefrontpage', $co->get_errors());

        // Generating a shortname should not be allowed in update mode, and so we cannot update the front page.
        $mode = tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('idnumber' => 'NewIDN', 'fullname' => 'FN', 'category' => 1);
        $importoptions = array('shortnametemplate' => $SITE->shortname);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('cannotgenerateshortnameupdatemode', $co->get_errors());

        // Renaming to the front page should not be allowed.
        $c1 = $this->getDataGenerator()->create_course();
        $mode = tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE;
        $updatemode = tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY;
        $data = array('shortname' => $c1->shortname, 'fullname' => 'FN', 'idnumber' => 'NewIDN', 'rename' => $SITE->shortname);
        $importoptions = array('canrename' => true);
        $co = new tool_uploadcourse_course($mode, $updatemode, $data, array(), $importoptions);
        $this->assertFalse($co->prepare());
        $this->assertArrayHasKey('cannotrenameshortnamealreadyinuse', $co->get_errors());
    }

    /**
     * Get custom field plugin generator
     *
     * @return core_customfield_generator
     */
    protected function get_customfield_generator() : core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    /**
     * Helper method to create custom course field
     *
     * @param \core_customfield\category_controller $category
     * @param string $type
     * @param string $shortname
     * @param array $configdata
     * @return \core_customfield\field_controller
     */
    protected function create_custom_field(\core_customfield\category_controller $category, string $type, string $shortname,
            array $configdata = []) : \core_customfield\field_controller {

        return $this->get_customfield_generator()->create_field([
            'categoryid' => $category->get('id'),
            'type' => $type,
            'shortname' => $shortname,
            'configdata' => $configdata,
        ]);
    }
}

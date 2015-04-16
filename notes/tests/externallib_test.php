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
 * External notes functions unit tests
 *
 * @package    core_notes
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/notes/externallib.php');

class core_notes_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test create_notes
     */
    public function test_create_notes() {

        global $DB, $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();

        // Set the required capabilities by the external function.
        $contextid = context_course::instance($course->id)->id;
        $roleid = $this->assignUserCapability('moodle/notes:manage', $contextid);
        $this->assignUserCapability('moodle/course:view', $contextid, $roleid);

        // Create test note data.
        $note1 = array();
        $note1['userid'] = $USER->id;
        $note1['publishstate'] = 'personal';
        $note1['courseid'] = $course->id;
        $note1['text'] = 'the text';
        $note1['clientnoteid'] = 4;
        $notes = array($note1);

        $creatednotes = core_notes_external::create_notes($notes);
        // We need to execute the return values cleaning process to simulate the web service server.
        $creatednotes = external_api::clean_returnvalue(core_notes_external::create_notes_returns(), $creatednotes);

        $thenote = $DB->get_record('post', array('id' => $creatednotes[0]['noteid']));

        // Confirm that base note data was inserted correctly.
        $this->assertEquals($thenote->userid, $note1['userid']);
        $this->assertEquals($thenote->courseid, $note1['courseid']);
        $this->assertEquals($thenote->publishstate, NOTES_STATE_DRAFT);
        $this->assertEquals($thenote->content, $note1['text']);
        $this->assertEquals($creatednotes[0]['clientnoteid'], $note1['clientnoteid']);

        // Call without required capability.
        $this->unassignUserCapability('moodle/notes:manage', $contextid, $roleid);
        $this->setExpectedException('required_capability_exception');
        $creatednotes = core_notes_external::create_notes($notes);
    }

    public function test_delete_notes() {

        global $DB, $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();

        // Set the required capabilities by the external function.
        $contextid = context_course::instance($course->id)->id;
        $roleid = $this->assignUserCapability('moodle/notes:manage', $contextid);
        $this->assignUserCapability('moodle/course:view', $contextid, $roleid);

        // Create test note data.
        $cnote = array();
        $cnote['userid'] = $USER->id;
        $cnote['publishstate'] = 'personal';
        $cnote['courseid'] = $course->id;
        $cnote['text'] = 'the text';
        $cnote['clientnoteid'] = 4;
        $cnotes = array($cnote);
        $creatednotes = core_notes_external::create_notes($cnotes);
        $creatednotes = external_api::clean_returnvalue(core_notes_external::create_notes_returns(), $creatednotes);

        $dnotes1 = array($creatednotes[0]['noteid']);
        $deletednotes1 = core_notes_external::delete_notes($dnotes1);
        $deletednotes1 = external_api::clean_returnvalue(core_notes_external::delete_notes_returns(), $deletednotes1);

        // Confirm that base note data was deleted correctly.
        $notdeletedcount = $DB->count_records_select('post', 'id = ' . $creatednotes[0]['noteid']);
        $this->assertEquals(0, $notdeletedcount);

        $dnotes2 = array(33); // This note does not exist.
        $deletednotes2 = core_notes_external::delete_notes($dnotes2);
        $deletednotes2 = external_api::clean_returnvalue(core_notes_external::delete_notes_returns(), $deletednotes2);

        $this->assertEquals("note", $deletednotes2[0]["item"]);
        $this->assertEquals(33, $deletednotes2[0]["itemid"]);
        $this->assertEquals("badid", $deletednotes2[0]["warningcode"]);
        $this->assertEquals("Note does not exist", $deletednotes2[0]["message"]);

        // Call without required capability.
        $creatednotes = core_notes_external::create_notes($cnotes);
        $creatednotes = external_api::clean_returnvalue(core_notes_external::create_notes_returns(), $creatednotes);
        $dnotes3 = array($creatednotes[0]['noteid']);

        $this->unassignUserCapability('moodle/notes:manage', $contextid, $roleid);
        $this->setExpectedException('required_capability_exception');
        $deletednotes = core_notes_external::delete_notes($dnotes3);
        $deletednotes = external_api::clean_returnvalue(core_notes_external::delete_notes_returns(), $deletednotes);
    }

    public function test_get_notes() {

        global $DB, $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();

        // Set the required capabilities by the external function.
        $contextid = context_course::instance($course->id)->id;
        $roleid = $this->assignUserCapability('moodle/notes:manage', $contextid);
        $this->assignUserCapability('moodle/notes:view', $contextid, $roleid);
        $this->assignUserCapability('moodle/course:view', $contextid, $roleid);

        // Create test note data.
        $cnote = array();
        $cnote['userid'] = $USER->id;
        $cnote['publishstate'] = 'personal';
        $cnote['courseid'] = $course->id;
        $cnote['text'] = 'the text';
        $cnotes = array($cnote);

        $creatednotes1 = core_notes_external::create_notes($cnotes);
        $creatednotes2 = core_notes_external::create_notes($cnotes);
        $creatednotes3 = core_notes_external::create_notes($cnotes);

        $creatednotes1 = external_api::clean_returnvalue(core_notes_external::create_notes_returns(), $creatednotes1);
        $creatednotes2 = external_api::clean_returnvalue(core_notes_external::create_notes_returns(), $creatednotes2);
        $creatednotes3 = external_api::clean_returnvalue(core_notes_external::create_notes_returns(), $creatednotes3);

        // Note 33 does not exist.
        $gnotes = array($creatednotes1[0]['noteid'], $creatednotes2[0]['noteid'], $creatednotes3[0]['noteid'], 33);
        $getnotes = core_notes_external::get_notes($gnotes);
        $getnotes = external_api::clean_returnvalue(core_notes_external::get_notes_returns(), $getnotes);

        $this->unassignUserCapability('moodle/notes:manage', $contextid, $roleid);
        // Confirm that base note data was retrieved correctly.
        $this->assertEquals($cnote['userid'], $getnotes["notes"][0]["userid"]);
        $this->assertEquals($cnote['text'], $getnotes["notes"][0]["text"]);
        $this->assertEquals($cnote['userid'], $getnotes["notes"][1]["userid"]);
        $this->assertEquals($cnote['text'], $getnotes["notes"][1]["text"]);
        $this->assertEquals($cnote['userid'], $getnotes["notes"][2]["userid"]);
        $this->assertEquals($cnote['text'], $getnotes["notes"][2]["text"]);
        $this->assertEquals("note", $getnotes["warnings"][0]["item"]);
        $this->assertEquals(33, $getnotes["warnings"][0]["itemid"]);
        $this->assertEquals("badid", $getnotes["warnings"][0]["warningcode"]);
        $this->assertEquals("Note does not exist", $getnotes["warnings"][0]["message"]);

        // Call without required capability.
        $this->unassignUserCapability('moodle/notes:view', $contextid, $roleid);
        $this->setExpectedException('required_capability_exception');
        $creatednotes = core_notes_external::get_notes($gnotes);
    }

    public function test_update_notes() {

        global $DB, $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();

        // Set the required capabilities by the external function.
        $contextid = context_course::instance($course->id)->id;
        $roleid = $this->assignUserCapability('moodle/notes:manage', $contextid);
        $this->assignUserCapability('moodle/course:view', $contextid, $roleid);

        // Create test note data.
        $note1 = array();
        $note1['userid'] = $USER->id;
        $note1['publishstate'] = 'personal';
        $note1['courseid'] = $course->id;
        $note1['text'] = 'the text';
        $note2['userid'] = $USER->id;
        $note2['publishstate'] = 'course';
        $note2['courseid'] = $course->id;
        $note2['text'] = 'the text';
        $note3['userid'] = $USER->id;
        $note3['publishstate'] = 'site';
        $note3['courseid'] = $course->id;
        $note3['text'] = 'the text';
        $notes1 = array($note1, $note2, $note3);

        $creatednotes = core_notes_external::create_notes($notes1);
        $creatednotes = external_api::clean_returnvalue(core_notes_external::create_notes_returns(), $creatednotes);

        $note2 = array();
        $note2["id"] = $creatednotes[0]['noteid'];
        $note2['publishstate'] = 'personal';
        $note2['text'] = 'the new text';
        $note2['format'] = FORMAT_HTML;
        $notes2 = array($note2);

        $updatednotes = core_notes_external::update_notes($notes2);

        $updatednotes = external_api::clean_returnvalue(core_notes_external::update_notes_returns(), $updatednotes);
        $thenote = $DB->get_record('post', array('id' => $creatednotes[0]['noteid']));

        // Confirm that base note data was updated correctly.
        $this->assertEquals($thenote->publishstate, NOTES_STATE_DRAFT);
        $this->assertEquals($note2['text'], $thenote->content);

        // Call without required capability.
        $creatednotes = core_notes_external::create_notes($notes1);
        $creatednotes = external_api::clean_returnvalue(core_notes_external::create_notes_returns(), $creatednotes);
        $this->unassignUserCapability('moodle/notes:manage', $contextid, $roleid);
        $this->setExpectedException('required_capability_exception');
        $note2 = array();
        $note2["id"] = $creatednotes[0]['noteid'];
        $note2['publishstate'] = 'personal';
        $note2['text'] = 'the new text';
        $note2['format'] = FORMAT_HTML;
        $notes2 = array($note2);
        $updatednotes = core_notes_external::update_notes($notes2);
        $updatednotes = external_api::clean_returnvalue(core_notes_external::update_notes_returns(), $updatednotes);
    }

    /**
     * Test get_course_notes
     */
    public function test_get_course_notes() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $CFG->enablenotes = true;

        // Take role definitions.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        // Create students and teachers.
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $teacher1 = $this->getDataGenerator()->create_user();
        $teacher2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Enroll students and teachers to COURSE-1.
        $this->getDataGenerator()->enrol_user($student1->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($teacher1->id, $course1->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($teacher2->id, $course1->id, $teacherrole->id);
        // Enroll students and teachers to COURSE-2 (teacher1 is not enrolled in Course 2).
        $this->getDataGenerator()->enrol_user($student1->id, $course2->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course2->id, $studentrole->id);

        $this->getDataGenerator()->enrol_user($teacher2->id, $course2->id, $teacherrole->id);

        // Generate notes.
        $gen = $this->getDataGenerator()->get_plugin_generator('core_notes');

        $this->setUser($teacher1);

        // NoteA1: on student1 (Course1) by Teacher1.
        $params = array('courseid' => $course1->id, 'userid' => $student1->id, 'publishstate' => NOTES_STATE_PUBLIC,
            'usermodified' => $teacher1->id);
        $notea1 = $gen->create_instance($params);
        // NoteA2: on student1 (Course1) by Teacher1.
        $params = array('courseid' => $course1->id, 'userid' => $student1->id, 'publishstate' => NOTES_STATE_PUBLIC,
            'usermodified' => $teacher1->id);
        $notea2 = $gen->create_instance($params);
        // NoteS1: on student1 SITE-LEVEL by teacher1.
        $params = array('courseid' => $course1->id, 'userid' => $student1->id, 'publishstate' => NOTES_STATE_SITE,
            'usermodified' => $teacher1->id);
        $notes1 = $gen->create_instance($params);
        // NoteP1: on student1 PERSONAL by teacher1.
        $params = array('courseid' => $course1->id, 'userid' => $student1->id, 'publishstate' => NOTES_STATE_DRAFT,
            'usermodified' => $teacher1->id);
        $notep1 = $gen->create_instance($params);
        // NoteB1: on student1 (Course2) by teacher1.
        $params = array('courseid' => $course2->id, 'userid' => $student1->id, 'publishstate' => NOTES_STATE_PUBLIC,
            'usermodified' => $teacher1->id);
        $noteb1 = $gen->create_instance($params);

        // Retrieve notes, normal case.
        $result = core_notes_external::get_course_notes($course1->id, $student1->id);
        $result = external_api::clean_returnvalue(core_notes_external::get_course_notes_returns(), $result);
        $this->assertEquals($notes1->id, $result['sitenotes'][0]['id']);
        $this->assertCount(2, $result['coursenotes']);

        foreach ($result['coursenotes'] as $coursenote) {
            if ($coursenote['id'] != $notea1->id and $coursenote['id'] != $notea2->id) {
                $this->fail('the returned notes ids does not match with the created ones');
            }
        }

        $this->assertEquals($notep1->id, $result['personalnotes'][0]['id']);

        // Try to get notes from a course the user is not enrolled.
        try {
            $result = core_notes_external::get_course_notes($course2->id, $student1->id);
            $this->fail('the user is not enrolled in the course');
        } catch (require_login_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        $result = core_notes_external::get_course_notes(0, $student1->id);
        $result = external_api::clean_returnvalue(core_notes_external::get_course_notes_returns(), $result);
        $this->assertEmpty($result['sitenotes']);

        foreach ($result['coursenotes'] as $coursenote) {
            if ($coursenote['id'] != $notea1->id and $coursenote['id'] != $notea2->id) {
                $this->fail('the returned notes ids does not match with the created ones');
            }
        }

        $this->assertCount(2, $result['coursenotes']);

        $this->setAdminUser();
        $result = core_notes_external::get_course_notes(0, $student1->id);
        $result = external_api::clean_returnvalue(core_notes_external::get_course_notes_returns(), $result);
        $this->assertEquals($notes1->id, $result['sitenotes'][0]['id']);
        $this->assertCount(1, $result['sitenotes']);

        $this->setUser($teacher1);
        $result = core_notes_external::get_course_notes(0, 0);
        $result = external_api::clean_returnvalue(core_notes_external::get_course_notes_returns(), $result);
        $this->assertEmpty($result['sitenotes']);
        $this->assertEmpty($result['coursenotes']);
        $this->assertEmpty($result['personalnotes']);

        $this->setUser($teacher2);
        $result = core_notes_external::get_course_notes($course1->id, $student1->id);
        $result = external_api::clean_returnvalue(core_notes_external::get_course_notes_returns(), $result);
        $this->assertEquals($notes1->id, $result['sitenotes'][0]['id']);

        foreach ($result['coursenotes'] as $coursenote) {
            if ($coursenote['id'] != $notea1->id and $coursenote['id'] != $notea2->id) {
                $this->fail('the returned notes ids does not match with the created ones');
            }
        }

        $this->assertCount(1, $result['sitenotes']);
        $this->assertCount(2, $result['coursenotes']);

        $result = core_notes_external::get_course_notes($course1->id, 0);
        $result = external_api::clean_returnvalue(core_notes_external::get_course_notes_returns(), $result);
        $this->assertEquals($notes1->id, $result['sitenotes'][0]['id']);

        foreach ($result['coursenotes'] as $coursenote) {
            if ($coursenote['id'] != $notea1->id and $coursenote['id'] != $notea2->id) {
                $this->fail('the returned notes ids does not match with the created ones');
            }
        }

        $this->assertCount(1, $result['sitenotes']);
        $this->assertCount(2, $result['coursenotes']);

        $this->setUser($teacher1);
        $result = core_notes_external::get_course_notes($course1->id, 0);
        $result = external_api::clean_returnvalue(core_notes_external::get_course_notes_returns(), $result);
        $this->assertEquals($notep1->id, $result['personalnotes'][0]['id']);
        $this->assertCount(1, $result['personalnotes']);

    }

    /**
     * Test view_notes
     */
    public function test_view_notes() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $CFG->enablenotes = true;

        // Take role definitions.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        // Create students and teachers.
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // Enroll students and teachers to course.
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        // Generate notes.
        $gen = $this->getDataGenerator()->get_plugin_generator('core_notes');
        $this->setUser($teacher);

        // NoteA1: on student (Course) by Teacher.
        $params = array('courseid' => $course->id, 'userid' => $student->id, 'publishstate' => NOTES_STATE_PUBLIC,
            'usermodified' => $teacher->id);
        $notea1 = $gen->create_instance($params);

        $sink = $this->redirectEvents();

        $result = core_notes_external::view_notes($course->id, $student->id);
        $result = external_api::clean_returnvalue(core_notes_external::view_notes_returns(), $result);

        $result = core_notes_external::view_notes($course->id);
        $result = external_api::clean_returnvalue(core_notes_external::view_notes_returns(), $result);

        $events = $sink->get_events();

        $this->assertCount(2, $events);

        $this->assertInstanceOf('\core\event\notes_viewed', $events[0]);
        $this->assertEquals($coursecontext, $events[0]->get_context());
        $this->assertEquals($student->id, $events[0]->relateduserid);

        $this->assertInstanceOf('\core\event\notes_viewed', $events[1]);
        $this->assertEquals($coursecontext, $events[1]->get_context());
        $this->assertEquals(0, $events[1]->relateduserid);

        try {
            core_notes_external::view_notes(0);
            $this->fail('Exception expected due to invalid permissions at system level.');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        try {
            core_notes_external::view_notes($course->id, $student->id + 100);
            $this->fail('Exception expected due to invalid user id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invaliduser', $e->errorcode);
        }
    }
}

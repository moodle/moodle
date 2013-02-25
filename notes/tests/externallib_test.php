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

class core_notes_external_testcase extends externallib_advanced_testcase {

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

        $dnotes1 = array("notes"=>array($creatednotes[0]['noteid']));
        $deletednotes1 = core_notes_external::delete_notes($dnotes1);
        $deletednotes1 = external_api::clean_returnvalue(core_notes_external::delete_notes_returns(), $deletednotes1);

        // Confirm that base note data was deleted correctly.
        $notdeletedcount = $DB->count_records_select('post', 'id = ' . $creatednotes[0]['noteid']);
        $this->assertEquals(0, $notdeletedcount);

        $dnotes2 = array("notes"=>array(33)); // This note does not exist.
        $deletednotes2 = core_notes_external::delete_notes($dnotes2);
        $deletednotes2 = external_api::clean_returnvalue(core_notes_external::delete_notes_returns(), $deletednotes2);

        $this->assertEquals("note", $deletednotes2[0]["item"]);
        $this->assertEquals(33, $deletednotes2[0]["itemid"]);
        $this->assertEquals("badid", $deletednotes2[0]["warningcode"]);
        $this->assertEquals("Note does not exist", $deletednotes2[0]["message"]);

        // Call without required capability.
        $creatednotes = core_notes_external::create_notes($cnotes);
        $dnotes3 = array("notes"=>array($creatednotes[0]['noteid']));

        $this->unassignUserCapability('moodle/notes:manage', $contextid, $roleid);
        $this->setExpectedException('required_capability_exception');
        $deletednotes = core_notes_external::delete_notes($dnotes3);
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
        $gnotes = array("notes"=>array($creatednotes1[0]['noteid'], $creatednotes2[0]['noteid'], $creatednotes3[0]['noteid'], 33));
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
        $this->unassignUserCapability('moodle/notes:manage', $contextid, $roleid);
        $this->setExpectedException('required_capability_exception');
        $note2 = array();
        $note2["id"] = $creatednotes[0]['noteid'];
        $note2['publishstate'] = 'personal';
        $note2['text'] = 'the new text';
        $note2['format'] = FORMAT_HTML;
        $notes2 = array($note2);
        $updatednotes = core_notes_external::update_notes($notes2);
    }
}

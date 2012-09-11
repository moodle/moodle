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

        global $DB, $USER, $DB;

        $this->resetAfterTest(true);

        $course  = self::getDataGenerator()->create_course();

        // Set the required capabilities by the external function
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

        $thenote = $DB->get_record('post', array('id' => $creatednotes[0]['noteid']));

        // Confirm that base note data was inserted correctly.
        $this->assertEquals($thenote->userid, $note1['userid']);
        $this->assertEquals($thenote->courseid, $note1['courseid']);
        $this->assertEquals($thenote->publishstate, NOTES_STATE_DRAFT);
        $this->assertEquals($thenote->content, $note1['text']);
        $this->assertEquals($creatednotes[0]['clientnoteid'], $note1['clientnoteid']);

        // Call without required capability
        $this->unassignUserCapability('moodle/notes:manage', $contextid, $roleid);
        $this->setExpectedException('required_capability_exception');
        $creatednotes = core_notes_external::create_notes($notes);

    }
}

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
 * Tests for notes events.
 *
 * @package    core_notes
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace core_notes\event;

/**
 * Class core_notes_events_testcase
 *
 * Class for tests related to notes events.
 *
 * @package    core_notes
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class events_test extends \advanced_testcase {

    /** @var  stdClass A note object. */
    private $eventnote;

    /** @var stdClass A complete record from post table */
    private $noterecord;

    public function setUp(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $gen = $this->getDataGenerator()->get_plugin_generator('core_notes');
        $this->eventnote = $gen->create_instance(array('courseid' => $course->id, 'userid' => $user->id));
        // Get the full record, note_load doesn't return everything.
        $this->noterecord = $DB->get_record('post', array('id' => $this->eventnote->id), '*', MUST_EXIST);

    }

    /**
     * Tests for event note_deleted.
     */
    public function test_note_deleted_event() {
        // Delete a note.
        $sink = $this->redirectEvents();
        note_delete($this->eventnote);
        $events = $sink->get_events();
        $event = array_pop($events); // Delete note event.
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\note_deleted', $event);
        $this->assertEquals($this->eventnote->id, $event->objectid);
        $this->assertEquals($this->eventnote->usermodified, $event->userid);
        $this->assertEquals($this->eventnote->userid, $event->relateduserid);
        $this->assertEquals('post', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($this->noterecord, $event->get_record_snapshot('post', $event->objectid));
        $this->assertEquals(NOTES_STATE_SITE, $event->other['publishstate']);

        // Test legacy data.
        $logurl = new \moodle_url('index.php',
                array('course' => $this->eventnote->courseid, 'user' => $this->eventnote->userid));
        $logurl->set_anchor('note-' . $this->eventnote->id);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event note_created.
     */
    public function test_note_created_event() {

        // Delete a note.
        $sink = $this->redirectEvents();
        $note = clone $this->eventnote;
        unset($note->id);
        note_save($note);
        $events = $sink->get_events();
        $event = array_pop($events); // Delete note event.
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\note_created', $event);
        $this->assertEquals($note->id, $event->objectid);
        $this->assertEquals($note->usermodified, $event->userid);
        $this->assertEquals($note->userid, $event->relateduserid);
        $this->assertEquals('post', $event->objecttable);
        $this->assertEquals(NOTES_STATE_SITE, $event->other['publishstate']);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event note_updated.
     */
    public function test_note_updated_event() {

        // Delete a note.
        $sink = $this->redirectEvents();
        $note = clone $this->eventnote;
        $note->publishstate = NOTES_STATE_DRAFT;
        note_save($note);
        $events = $sink->get_events();
        $event = array_pop($events); // Delete note event.
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\note_updated', $event);
        $this->assertEquals($note->id, $event->objectid);
        $this->assertEquals($note->usermodified, $event->userid);
        $this->assertEquals($note->userid, $event->relateduserid);
        $this->assertEquals('post', $event->objecttable);
        $this->assertEquals(NOTES_STATE_DRAFT, $event->other['publishstate']);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the notes viewed event.
     *
     * It's not possible to use the moodle API to simulate the viewing of notes, so here we
     * simply create the event and trigger it.
     */
    public function test_notes_viewed() {
        $coursecontext = \context_course::instance($this->eventnote->courseid);
        // Trigger event for notes viewed.
        $event = \core\event\notes_viewed::create(array(
            'context' => $coursecontext,
            'relateduserid' => $this->eventnote->userid
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\notes_viewed', $event);
        $this->assertEquals($coursecontext, $event->get_context());
        $this->assertEventContextNotUsed($event);
    }
}

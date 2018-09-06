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
 * Unit tests for (some of) mod/book/lib.php.
 *
 * @package    mod_book
 * @category   phpunit
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/book/lib.php');

/**
 * Unit tests for (some of) mod/book/lib.php.
 *
 * @package    mod_book
 * @category   phpunit
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_book_lib_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    public function test_export_contents() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array('enablecomment' => 1));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // Test book with 3 chapters.
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $cm = get_coursemodule_from_id('book', $book->cmid);

        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $chapter1 = $bookgenerator->create_chapter(array('bookid' => $book->id, "pagenum" => 1));
        $chapter2 = $bookgenerator->create_chapter(array('bookid' => $book->id, "pagenum" => 2));
        $subchapter = $bookgenerator->create_chapter(array('bookid' => $book->id, "pagenum" => 3, "subchapter" => 1));
        $chapter3 = $bookgenerator->create_chapter(array('bookid' => $book->id, "pagenum" => 4, "hidden" => 1));

        $this->setUser($user);

        $contents = book_export_contents($cm, '');
        // The hidden chapter must not be included, and additional page with the structure must be included.
        $this->assertCount(4, $contents);

        $this->assertEquals('structure', $contents[0]['filename']);
        $this->assertEquals('index.html', $contents[1]['filename']);
        $this->assertEquals('Chapter 1', $contents[1]['content']);
        $this->assertEquals('index.html', $contents[2]['filename']);
        $this->assertEquals('Chapter 2', $contents[2]['content']);
        $this->assertEquals('index.html', $contents[3]['filename']);
        $this->assertEquals('Chapter 3', $contents[3]['content']);

        // Test empty book.
        $emptybook = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $cm = get_coursemodule_from_id('book', $emptybook->cmid);
        $contents = book_export_contents($cm, '');

        $this->assertCount(1, $contents);
        $this->assertEquals('structure', $contents[0]['filename']);
        $this->assertEquals(json_encode(array()), $contents[0]['content']);

    }

    /**
     * Test book_view
     * @return void
     */
    public function test_book_view() {
        global $CFG, $DB;

        $CFG->enablecompletion = 1;

        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $chapter = $bookgenerator->create_chapter(array('bookid' => $book->id));

        $context = context_module::instance($book->cmid);
        $cm = get_coursemodule_from_instance('book', $book->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        // Check just opening the book.
        book_view($book, 0, false, $course, $cm, $context);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_book\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/book/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check viewing one book chapter (the only one so it will be the first and last).
        book_view($book, $chapter, true, $course, $cm, $context);

        $events = $sink->get_events();
        // We expect a total of 4 events. One for module viewed, one for chapter viewed and two belonging to completion.
        $this->assertCount(4, $events);

        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);
    }

    public function test_book_core_calendar_provide_event_action() {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $book->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_book_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_book_core_calendar_provide_event_action_in_hidden_section() {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $book->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Set sections 0 as hidden.
        set_section_visible($course->id, 0, 0);

        // Now, log out.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_book_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_book_core_calendar_provide_event_action_for_user() {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $book->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now, log out.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_book_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_book_core_calendar_provide_event_action_as_non_user() {
        global $CFG;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $book->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Log out the user and set force login to true.
        \core\session\manager::init_empty_session();
        $CFG->forcelogin = true;

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_book_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_book_core_calendar_provide_event_action_already_completed() {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('book', $book->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $book->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_book_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_book_core_calendar_provide_event_action_already_completed_for_user() {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('book', $book->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $book->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the student.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_book_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'book';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }

    public function test_mod_book_get_tagged_chapters() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $course3 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1 = $this->getDataGenerator()->create_course();
        $book1 = $this->getDataGenerator()->create_module('book', array('course' => $course1->id));
        $book2 = $this->getDataGenerator()->create_module('book', array('course' => $course2->id));
        $book3 = $this->getDataGenerator()->create_module('book', array('course' => $course3->id));
        $chapter11 = $bookgenerator->create_content($book1, array('tags' => array('Cats', 'Dogs')));
        $chapter12 = $bookgenerator->create_content($book1, array('tags' => array('Cats', 'mice')));
        $chapter13 = $bookgenerator->create_content($book1, array('tags' => array('Cats')));
        $chapter14 = $bookgenerator->create_content($book1);
        $chapter15 = $bookgenerator->create_content($book1, array('tags' => array('Cats')));
        $chapter16 = $bookgenerator->create_content($book1, array('tags' => array('Cats'), 'hidden' => true));
        $chapter21 = $bookgenerator->create_content($book2, array('tags' => array('Cats')));
        $chapter22 = $bookgenerator->create_content($book2, array('tags' => array('Cats', 'Dogs')));
        $chapter23 = $bookgenerator->create_content($book2, array('tags' => array('mice', 'Cats')));
        $chapter31 = $bookgenerator->create_content($book3, array('tags' => array('mice', 'Cats')));

        $tag = core_tag_tag::get_by_name(0, 'Cats');

        // Admin can see everything.
        $res = mod_book_get_tagged_chapters($tag, /*$exclusivemode = */false,
            /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$chapter = */0);
        $this->assertRegExp('/'.$chapter11->title.'</', $res->content);
        $this->assertRegExp('/'.$chapter12->title.'</', $res->content);
        $this->assertRegExp('/'.$chapter13->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter14->title.'</', $res->content);
        $this->assertRegExp('/'.$chapter15->title.'</', $res->content);
        $this->assertRegExp('/'.$chapter16->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter21->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter22->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter23->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter31->title.'</', $res->content);
        $this->assertEmpty($res->prevpageurl);
        $this->assertNotEmpty($res->nextpageurl);
        $res = mod_book_get_tagged_chapters($tag, /*$exclusivemode = */false,
            /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$chapter = */1);
        $this->assertNotRegExp('/'.$chapter11->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter12->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter13->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter14->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter15->title.'</', $res->content);
        $this->assertNotRegExp('/'.$chapter16->title.'</', $res->content);
        $this->assertRegExp('/'.$chapter21->title.'</', $res->content);
        $this->assertRegExp('/'.$chapter22->title.'</', $res->content);
        $this->assertRegExp('/'.$chapter23->title.'</', $res->content);
        $this->assertRegExp('/'.$chapter31->title.'</', $res->content);
        $this->assertNotEmpty($res->prevpageurl);
        $this->assertEmpty($res->nextpageurl);

        // Create and enrol a user.
        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, $studentrole->id, 'manual');
        $this->setUser($student);
        core_tag_index_builder::reset_caches();

        // User can not see chapters in course 3 because he is not enrolled.
        $res = mod_book_get_tagged_chapters($tag, /*$exclusivemode = */false,
            /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$chapter = */1);
        $this->assertRegExp('/'.$chapter22->title.'/', $res->content);
        $this->assertRegExp('/'.$chapter23->title.'/', $res->content);
        $this->assertNotRegExp('/'.$chapter31->title.'/', $res->content);

        // User can search book chapters inside a course.
        $coursecontext = context_course::instance($course1->id);
        $res = mod_book_get_tagged_chapters($tag, /*$exclusivemode = */false,
            /*$fromctx = */0, /*$ctx = */$coursecontext->id, /*$rec = */1, /*$chapter = */0);
        $this->assertRegExp('/'.$chapter11->title.'/', $res->content);
        $this->assertRegExp('/'.$chapter12->title.'/', $res->content);
        $this->assertRegExp('/'.$chapter13->title.'/', $res->content);
        $this->assertNotRegExp('/'.$chapter14->title.'/', $res->content);
        $this->assertRegExp('/'.$chapter15->title.'/', $res->content);
        $this->assertNotRegExp('/'.$chapter21->title.'/', $res->content);
        $this->assertNotRegExp('/'.$chapter22->title.'/', $res->content);
        $this->assertNotRegExp('/'.$chapter23->title.'/', $res->content);
        $this->assertEmpty($res->nextpageurl);

        // User cannot see hidden chapters.
        $this->assertNotRegExp('/'.$chapter16->title.'/', $res->content);
    }
}

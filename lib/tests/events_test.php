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
 * Events tests.
 *
 * @package   core
 * @category  test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/event_fixtures.php');

class core_events_testcase extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test the course category created event.
     */
    public function test_course_category_created() {
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $category = $this->getDataGenerator()->create_category();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_created', $event);
        $this->assertEquals(context_coursecat::instance($category->id), $event->get_context());
        $url = new moodle_url('/course/management.php', array('categoryid' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $expected = array(SITEID, 'category', 'add', 'editcategory.php?id=' . $category->id, $category->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the course category updated event.
     */
    public function test_course_category_updated() {
        // Create a category.
        $category = $this->getDataGenerator()->create_category();

        // Create some data we are going to use to update this category.
        $data = new stdClass();
        $data->name = 'Category name change';

        // Trigger and capture the event for updating a category.
        $sink = $this->redirectEvents();
        $category->update($data);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($category->id), $event->get_context());
        $url = new moodle_url('/course/editcategory.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $expected = array(SITEID, 'category', 'update', 'editcategory.php?id=' . $category->id, $category->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Create another category and a child category.
        $category2 = $this->getDataGenerator()->create_category();
        $childcat = $this->getDataGenerator()->create_category(array('parent' => $category2->id));

        // Trigger and capture the event for changing the parent of a category.
        $sink = $this->redirectEvents();
        $childcat->change_parent($category);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($childcat->id), $event->get_context());
        $expected = array(SITEID, 'category', 'move', 'editcategory.php?id=' . $childcat->id, $childcat->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event for changing the sortorder of a category.
        $sink = $this->redirectEvents();
        $category2->change_sortorder_by_one(true);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($category2->id), $event->get_context());
        $expected = array(SITEID, 'category', 'move', 'management.php?categoryid=' . $category2->id, $category2->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event for deleting a category and moving it's children to another.
        $sink = $this->redirectEvents();
        $category->delete_move($category->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($childcat->id), $event->get_context());
        $expected = array(SITEID, 'category', 'move', 'editcategory.php?id=' . $childcat->id, $childcat->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event for hiding a category.
        $sink = $this->redirectEvents();
        $category2->hide();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($category2->id), $event->get_context());
        $expected = array(SITEID, 'category', 'hide', 'editcategory.php?id=' . $category2->id, $category2->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event for unhiding a category.
        $sink = $this->redirectEvents();
        $category2->show();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($category2->id), $event->get_context());
        $expected = array(SITEID, 'category', 'show', 'editcategory.php?id=' . $category2->id, $category2->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the email failed event.
     *
     * It's not possible to use the moodle API to simulate the failure of sending
     * an email, so here we simply create the event and trigger it.
     */
    public function test_email_failed() {
        // Trigger event for failing to send email.
        $event = \core\event\email_failed::create(array(
            'context' => context_system::instance(),
            'userid' => 1,
            'relateduserid' => 2,
            'other' => array(
                'subject' => 'This is a subject',
                'message' => 'This is a message',
                'errorinfo' => 'The email failed to send!'
            )
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\email_failed', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());
        $expected = array(SITEID, 'library', 'mailer', qualified_me(), 'ERROR: The email failed to send!');
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * There is no api involved so the best we can do is test legacy data by triggering event manually.
     */
    public function test_course_user_report_viewed() {

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $eventparams = array();
        $eventparams['context'] = $context;
        $eventparams['relateduserid'] = $user->id;
        $eventparams['other'] = array();
        $eventparams['other']['mode'] = 'grade';
        $event = \core\event\course_user_report_viewed::create($eventparams);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\course_user_report_viewed', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $expected = array($course->id, 'course', 'user report', 'user.php?id=' . $course->id . '&amp;user='
                . $user->id . '&amp;mode=grade', $user->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * There is no api involved so the best we can do is test legacy data by triggering event manually.
     */
    public function test_course_viewed() {

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        // First try with no optional parameters.
        $eventparams = array();
        $eventparams['context'] = $context;
        $event = \core\event\course_viewed::create($eventparams);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\course_viewed', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $expected = array($course->id, 'course', 'view', 'view.php?id=' . $course->id, $course->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        // Now try with optional parameters.
        $sectionnumber = 7;
        $eventparams = array();
        $eventparams['context'] = $context;
        $eventparams['other'] = array('coursesectionnumber' => $sectionnumber);
        $event = \core\event\course_viewed::create($eventparams);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $loggeddata = $event->get_data();
        $events = $sink->get_events();
        $event = reset($events);


        $this->assertInstanceOf('\core\event\course_viewed', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $expected = array($course->id, 'course', 'view section', 'view.php?id=' . $course->id . '&amp;section='
                . $sectionnumber, $sectionnumber);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        delete_course($course->id, false);
        $restored = \core\event\base::restore($loggeddata, array('origin' => 'web', 'ip' => '127.0.0.1'));
        $this->assertInstanceOf('\core\event\course_viewed', $restored);
        $this->assertNull($restored->get_url());
    }

    public function test_recent_capability_viewed() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $event = \core\event\recent_activity_viewed::create(array('context' => $context));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\recent_activity_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, "course", "recent", "recent.php?id=$course->id", $course->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new moodle_url('/course/recent.php', array('id' => $course->id));
        $this->assertEquals($url, $event->get_url());
        $event->get_name();
    }

    public function test_user_profile_viewed() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // User profile viewed in course context.
        $eventparams = array(
            'objectid' => $user->id,
            'relateduserid' => $user->id,
            'courseid' => $course->id,
            'context' => $coursecontext,
            'other' => array(
                'courseid' => $course->id,
                'courseshortname' => $course->shortname,
                'coursefullname' => $course->fullname
            )
        );
        $event = \core\event\user_profile_viewed::create($eventparams);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\user_profile_viewed', $event);
        $log = array($course->id, 'user', 'view', 'view.php?id=' . $user->id . '&course=' . $course->id, $user->id);
        $this->assertEventLegacyLogData($log, $event);
        $this->assertEventContextNotUsed($event);

        // User profile viewed in user context.
        $usercontext = context_user::instance($user->id);
        $eventparams['context'] = $usercontext;
        unset($eventparams['courseid'], $eventparams['other']);
        $event = \core\event\user_profile_viewed::create($eventparams);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\user_profile_viewed', $event);
        $expected = null;
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * There is no API associated with this event, so we will just test standard features.
     */
    public function test_grade_viewed() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $event = \core_tests\event\grade_report_viewed::create(
            array(
                'context' => $coursecontext,
                'courseid' => $course->id,
                'userid' => $user->id,
            )
        );

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\grade_report_viewed', $event);
        $this->assertEquals($event->courseid, $course->id);
        $this->assertEquals($event->userid, $user->id);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the database text field content replaced event.
     */
    public function test_database_text_field_content_replaced() {
        global $CFG;

        require_once($CFG->dirroot . '/lib/adminlib.php');

        // Trigger and capture the event for finding and replacing strings in the database.
        $sink = $this->redirectEvents();
        ob_start();
        db_replace('searchstring', 'replacestring');
        ob_end_clean();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\database_text_field_content_replaced', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());
        $this->assertEquals('searchstring', $event->other['search']);
        $this->assertEquals('replacestring', $event->other['replace']);
    }
}

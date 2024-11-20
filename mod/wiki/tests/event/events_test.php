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
 * @package    mod_wiki
 * @category   phpunit
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_wiki\event;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot.'/mod/wiki/locallib.php');
/**
 * Events tests class.
 *
 * @package    mod_wiki
 * @category   phpunit
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends \advanced_testcase {
    private $course;
    private $wiki;
    private $wikigenerator;
    private $student;
    private $teacher;

    /**
     * Setup test data.
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();

        $this->resetAfterTest();
        // Create course and wiki.
        $this->course = $this->getDataGenerator()->create_course();
        $this->wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $this->course->id));
        $this->wikigenerator = $this->getDataGenerator()->get_plugin_generator('mod_wiki');

        // Create student and teacher in course.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->student = $this->getDataGenerator()->create_user();
        $this->teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $teacherrole->id);
        $this->setAdminUser();
    }

    /**
     * Test comment_created event.
     */
    public function test_comment_created(): void {
        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        wiki_add_comment($context, $page->id, 'Test comment', $this->wiki->defaultformat);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\comment_created', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->other['itemid']);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test comment_deleted event.
     */
    public function test_comment_deleted(): void {
        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        // Add comment so we can delete it later.
        wiki_add_comment($context, $page->id, 'Test comment', 'html');
        $comment = wiki_get_comments($context->id, $page->id);
        $this->assertCount(1, $comment);
        $comment = array_shift($comment);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        wiki_delete_comment($comment->id, $context, $page->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\comment_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->other['itemid']);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test comment_viewed event.
     */
    public function test_comment_viewed(): void {
        // There is no proper API to call or trigger this event, so simulating event
        // to check if event returns the right information.

        $this->setUp();
        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        $params = array(
                'context' => $context,
                'objectid' => $page->id
                );
        $event = \mod_wiki\event\comments_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\comments_viewed', $event);
        $this->assertEquals($context, $event->get_context());
    }

    /**
     * Test instances_list_viewed event.
     */
    public function test_course_module_instance_list_viewed(): void {
        // There is no proper API to call or trigger this event, so simulating event
        // to check if event returns the right information.

        $this->setUp();
        $context = \context_course::instance($this->course->id);

        $params = array('context' => $context);
        $event = \mod_wiki\event\course_module_instance_list_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\course_module_instance_list_viewed', $event);
        $this->assertEquals($context, $event->get_context());
    }

    /**
     * Test course_module_viewed event.
     */
    public function test_course_module_viewed(): void {
        // There is no proper API to call or trigger this event, so simulating event
        // to check if event returns the right information.

        $this->setUp();
        $context = \context_module::instance($this->wiki->cmid);

        $params = array(
                'context' => $context,
                'objectid' => $this->wiki->id
                );
        $event = \mod_wiki\event\course_module_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($this->wiki->id, $event->objectid);
    }

    /**
     * Test page_viewed event.
     */
    public function test_page_viewed(): void {
        // There is no proper API to call or trigger this event, so simulating event
        // to check if event returns the right information.

        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        $params = array(
                'context' => $context,
                'objectid' => $page->id
                );
        $event = \mod_wiki\event\page_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);
    }

    /**
     * Test page_viewed event for prettypage view.
     */
    public function test_pretty_page_viewed(): void {
        // There is no proper API to call or trigger this event, so simulating event
        // to check if event returns the right information.

        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        $params = array(
                'context' => $context,
                'objectid' => $page->id,
                'other' => array('prettyview' => true)
                );
        $event = \mod_wiki\event\page_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);
    }

    /**
     * Test page_created event.
     */
    public function test_page_created(): void {
        global $USER;

        $this->setUp();

        $context = \context_module::instance($this->wiki->cmid);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $page = $this->wikigenerator->create_first_page($this->wiki);
        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_created', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);
    }

    /**
     * Test page_deleted and page_version_deleted and page_locks_deleted event.
     */
    public function test_page_deleted(): void {
        global $DB;

        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);
        $oldversions = $DB->get_records('wiki_versions', array('pageid' => $page->id));
        $oldversion = array_shift($oldversions);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        wiki_delete_pages($context, array($page->id));
        $events = $sink->get_events();
        $this->assertCount(4, $events);
        $event = array_shift($events);

        // Checking that the event contains the page_version_deleted event.
        $this->assertInstanceOf('\mod_wiki\event\page_version_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->other['pageid']);
        $this->assertEquals($oldversion->id, $event->objectid);

        // Checking that the event contains the page_deleted event.
        $event = array_pop($events);
        $this->assertInstanceOf('\mod_wiki\event\page_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);

        // Checking that the event contains the expected values.
        $event = array_pop($events);
        $this->assertInstanceOf('\mod_wiki\event\page_locks_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);

        // Delete all pages.
        $page1 = $this->wikigenerator->create_first_page($this->wiki);
        $page2 = $this->wikigenerator->create_content($this->wiki);
        $page3 = $this->wikigenerator->create_content($this->wiki, array('title' => 'Custom title'));

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        wiki_delete_pages($context, array($page1->id, $page2->id));
        $events = $sink->get_events();
        $this->assertCount(8, $events);
        $event = array_pop($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page2->id, $event->objectid);
    }

    /**
     * Test page_updated event.
     */
    public function test_page_updated(): void {
        global $USER;

        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        wiki_save_page($page, 'New content', $USER->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_updated', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);
    }

    /**
     * Test page_diff_viewed event.
     */
    public function test_page_diff_viewed(): void {
        // There is no proper API to call or trigger this event, so simulating event
        // to check if event returns the right information.

        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        $params = array(
                'context' => $context,
                'objectid' => $page->id,
                'other' => array(
                    'comparewith' => 1,
                    'compare' => 2
                    )
                );
        $event = \mod_wiki\event\page_diff_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_diff_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);
    }

    /**
     * Test page_history_viewed event.
     */
    public function test_page_history_viewed(): void {
        // There is no proper API to call or trigger this event, so simulating event
        // to check if event returns the right information.

        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        $params = array(
                'context' => $context,
                'objectid' => $page->id
                );
        $event = \mod_wiki\event\page_history_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_history_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);
    }

    /**
     * Test page_map_viewed event.
     */
    public function test_page_map_viewed(): void {
        // There is no proper API to call or trigger this event, so simulating event
        // to check if event returns the right information.

        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        $params = array(
                'context' => $context,
                'objectid' => $page->id,
                'other' => array(
                    'option' => 0
                    )
                );
        $event = \mod_wiki\event\page_map_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_map_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);
        $this->assertEquals(0, $event->other['option']);
    }

    /**
     * Test page_version_viewed event.
     */
    public function test_page_version_viewed(): void {
        // There is no proper API to call or trigger this event, so simulating event
        // to check if event returns the right information.

        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);

        $params = array(
                'context' => $context,
                'objectid' => $page->id,
                'other' => array(
                    'versionid' => 1
                    )
                );
        $event = \mod_wiki\event\page_version_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_version_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($page->id, $event->objectid);
        $this->assertEquals(1, $event->other['versionid']);
    }

    /**
     * Test page_version_restored event.
     */
    public function test_page_version_restored(): void {
        $this->setUp();

        $page = $this->wikigenerator->create_first_page($this->wiki);
        $context = \context_module::instance($this->wiki->cmid);
        $version = wiki_get_current_version($page->id);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        wiki_restore_page($page, $version, $context);
        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $event = array_pop($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_version_restored', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($version->id, $event->objectid);
        $this->assertEquals($page->id, $event->other['pageid']);
    }
}

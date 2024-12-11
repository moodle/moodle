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
 * @package    core_blog
 * @category   test
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_blog\event;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/blog/locallib.php');
require_once($CFG->dirroot . '/blog/lib.php');

/**
 * Unit tests for the blog events.
 *
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class events_test extends \advanced_testcase {

    /** @var $courseid */
    private $courseid;

    /** @var $cmid */
    private $cmid;

    /** @var $groupid */
    private $groupid;

    /** @var $userid */
    private $userid;

    /** @var $tagid */
    private $tagid;

    /** @var $postid */
    private $postid;

    /**
     * Setup the tests.
     */
    protected function setUp(): void {
        global $DB;
        parent::setUp();

        $this->resetAfterTest();

        // Create default course.
        $course = $this->getDataGenerator()->create_course(array('category' => 1, 'shortname' => 'ANON'));
        $this->assertNotEmpty($course);
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $this->assertNotEmpty($page);

        // Create default group.
        $group = new \stdClass();
        $group->courseid = $course->id;
        $group->name = 'ANON';
        $group->id = $DB->insert_record('groups', $group);

        // Create default user.
        $user = $this->getDataGenerator()->create_user(array(
                'username' => 'testuser',
                'firstname' => 'Jimmy',
                'lastname' => 'Kinnon'
        ));

        // Create default tag.
        $tag = $this->getDataGenerator()->create_tag(array('userid' => $user->id,
            'rawname' => 'Testtagname', 'isstandard' => 1));

        // Create default post.
        $post = new \stdClass();
        $post->userid = $user->id;
        $post->groupid = $group->id;
        $post->content = 'test post content text';
        $post->module = 'blog';
        $post->id = $DB->insert_record('post', $post);

        // Grab important ids.
        $this->courseid = $course->id;
        $this->cmid = $page->cmid;
        $this->groupid  = $group->id;
        $this->userid  = $user->id;
        $this->tagid  = $tag->id;
        $this->postid = $post->id;
    }

    /**
     * Test various blog related events.
     */
    public function test_blog_entry_created_event(): void {
        global $USER;

        $this->setAdminUser();
        $this->resetAfterTest();

        // Create a blog entry for another user as Admin.
        $sink = $this->redirectEvents();
        $blog = new \blog_entry();
        $blog->subject = "Subject of blog";
        $blog->userid = $this->userid;
        $states = \blog_entry::get_applicable_publish_states();
        $blog->publishstate = reset($states);
        $blog->add();
        $events = $sink->get_events();
        $sink->close();
        $event = reset($events);
        $sitecontext = \context_system::instance();

        // Validate event data.
        $this->assertInstanceOf('\core\event\blog_entry_created', $event);
        $url = new \moodle_url('/blog/index.php', array('entryid' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($sitecontext->id, $event->contextid);
        $this->assertEquals($blog->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals("post", $event->objecttable);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event blog_entry_updated.
     */
    public function test_blog_entry_updated_event(): void {
        global $USER;

        $this->setAdminUser();
        $this->resetAfterTest();
        $sitecontext = \context_system::instance();

        // Edit a blog entry as Admin.
        $blog = new \blog_entry($this->postid);
        $sink = $this->redirectEvents();
        $blog->summary_editor = array('text' => 'Something', 'format' => FORMAT_MOODLE);
        $blog->edit(array(), null, array(), array());
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\blog_entry_updated', $event);
        $url = new \moodle_url('/blog/index.php', array('entryid' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($sitecontext->id, $event->contextid);
        $this->assertEquals($blog->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals("post", $event->objecttable);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event blog_entry_deleted.
     */
    public function test_blog_entry_deleted_event(): void {
        global $USER, $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        $sitecontext = \context_system::instance();

        // Delete a user blog entry as Admin.
        $blog = new \blog_entry($this->postid);
        $sink = $this->redirectEvents();
        $record = $DB->get_record('post', array('id' => $blog->id));
        $blog->delete();
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\blog_entry_deleted', $event);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($sitecontext->id, $event->contextid);
        $this->assertEquals($blog->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals("post", $event->objecttable);
        $this->assertEquals($record, $event->get_record_snapshot("post", $blog->id));
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event blog_association_deleted.
     */
    public function test_blog_association_deleted_event(): void {
        global $USER;

        $this->setAdminUser();
        $this->resetAfterTest();
        $sitecontext = \context_system::instance();
        $coursecontext = \context_course::instance($this->courseid);
        $contextmodule = \context_module::instance($this->cmid);

        // Add blog associations with a course.
        $blog = new \blog_entry($this->postid);
        $blog->add_association($coursecontext->id);

        $sink = $this->redirectEvents();
        $blog->remove_associations();
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\blog_association_deleted', $event);
        $this->assertEquals($sitecontext->id, $event->contextid);
        $this->assertEquals($blog->id, $event->other['blogid']);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals('blog_association', $event->objecttable);

        // Add blog associations with a module.
        $blog = new \blog_entry($this->postid);
        $blog->add_association($contextmodule->id);
        $sink = $this->redirectEvents();
        $blog->remove_associations();
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Validate event data.
        $this->assertEquals($blog->id, $event->other['blogid']);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event blog_association_created.
     */
    public function test_blog_association_created_event(): void {
        global $USER;

        $this->setAdminUser();
        $this->resetAfterTest();
        $sitecontext = \context_system::instance();
        $coursecontext = \context_course::instance($this->courseid);
        $contextmodule = \context_module::instance($this->cmid);

        // Add blog associations with a course.
        $blog = new \blog_entry($this->postid);
        $sink = $this->redirectEvents();
        $blog->add_association($coursecontext->id);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\blog_association_created', $event);
        $this->assertEquals($sitecontext->id, $event->contextid);
        $url = new \moodle_url('/blog/index.php', array('entryid' => $event->other['blogid']));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($blog->id, $event->other['blogid']);
        $this->assertEquals($this->courseid, $event->other['associateid']);
        $this->assertEquals('course', $event->other['associatetype']);
        $this->assertEquals($blog->subject, $event->other['subject']);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals('blog_association', $event->objecttable);

        // Add blog associations with a module.
        $blog = new \blog_entry($this->postid);
        $sink = $this->redirectEvents();
        $blog->add_association($contextmodule->id);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Validate event data.
        $this->assertEquals($blog->id, $event->other['blogid']);
        $this->assertEquals($this->cmid, $event->other['associateid']);
        $this->assertEquals('coursemodule', $event->other['associatetype']);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event blog_association_created validations.
     */
    public function test_blog_association_created_event_validations(): void {

        $this->resetAfterTest();

         // Make sure associatetype validations work.
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('associateid' => 2 , 'blogid' => 3, 'subject' => 'blog subject')));
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('The \'associatetype\' value must be set in other and be a valid type.', $e->getMessage());
        }
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('associateid' => 2 , 'blogid' => 3, 'associatetype' => 'random', 'subject' => 'blog subject')));
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('The \'associatetype\' value must be set in other and be a valid type.', $e->getMessage());
        }
        // Make sure associateid validations work.
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('blogid' => 3, 'associatetype' => 'course', 'subject' => 'blog subject')));
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('The \'associateid\' value must be set in other.', $e->getMessage());
        }
        // Make sure blogid validations work.
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('associateid' => 3, 'associatetype' => 'course', 'subject' => 'blog subject')));
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('The \'blogid\' value must be set in other.', $e->getMessage());
        }
        // Make sure blogid validations work.
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('blogid' => 3, 'associateid' => 3, 'associatetype' => 'course')));
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('The \'subject\' value must be set in other.', $e->getMessage());
        }
    }

    /**
     * Tests for event blog_entries_viewed.
     */
    public function test_blog_entries_viewed_event(): void {

        $this->setAdminUser();

        $other = array('entryid' => $this->postid, 'tagid' => $this->tagid, 'userid' => $this->userid, 'modid' => $this->cmid,
                       'groupid' => $this->groupid, 'courseid' => $this->courseid, 'search' => 'search', 'fromstart' => 2);

        // Trigger event.
        $sink = $this->redirectEvents();
        $eventparams = array('other' => $other);
        $eventinst = \core\event\blog_entries_viewed::create($eventparams);
        $eventinst->trigger();
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Validate event data.
        $url = new \moodle_url('/blog/index.php', $other);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test comment_created event.
     */
    public function test_blog_comment_created_event(): void {
        global $USER, $CFG;

        $this->setAdminUser();

        require_once($CFG->dirroot . '/comment/lib.php');
        $context = \context_user::instance($USER->id);

        $cmt = new \stdClass();
        $cmt->context = $context;
        $cmt->courseid = $this->courseid;
        $cmt->area = 'format_blog';
        $cmt->itemid = $this->postid;
        $cmt->showcount = 1;
        $cmt->component = 'blog';
        $manager = new \comment($cmt);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $manager->add("New comment");
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\core\event\blog_comment_created', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($this->postid, $event->other['itemid']);
        $url = new \moodle_url('/blog/index.php', array('entryid' => $this->postid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test comment_deleted event.
     */
    public function test_blog_comment_deleted_event(): void {
        global $USER, $CFG;

        $this->setAdminUser();

        require_once($CFG->dirroot . '/comment/lib.php');
        $context = \context_user::instance($USER->id);

        $cmt = new \stdClass();
        $cmt->context = $context;
        $cmt->courseid = $this->courseid;
        $cmt->area = 'format_blog';
        $cmt->itemid = $this->postid;
        $cmt->showcount = 1;
        $cmt->component = 'blog';
        $manager = new \comment($cmt);
        $newcomment = $manager->add("New comment");

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $manager->delete($newcomment->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\core\event\blog_comment_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($this->postid, $event->other['itemid']);
        $url = new \moodle_url('/blog/index.php', array('entryid' => $this->postid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test external blog added event.
     *
     * There is no external API for this, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_external_blog_added_event(): void {

        // Trigger an event: external blog added.
        $eventparams = array(
            'context' => $context = \context_system::instance(),
            'objectid' => 1001,
            'other' => array('url' => 'http://moodle.org')
        );

        $event = \core\event\blog_external_added::create($eventparams);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\blog_external_added', $event);
        $this->assertEquals(1001, $event->objectid);
        $this->assertEquals('http://moodle.org', $event->other['url']);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test external blog updated event.
     *
     * There is no external API for this, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_external_blog_updated_event(): void {

        // Trigger an event: external blog updated.
        $eventparams = array(
            'context' => $context = \context_system::instance(),
            'objectid' => 1001,
            'other' => array('url' => 'http://moodle.org')
        );

        $event = \core\event\blog_external_updated::create($eventparams);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\blog_external_updated', $event);
        $this->assertEquals(1001, $event->objectid);
        $this->assertEquals('http://moodle.org', $event->other['url']);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test external blog removed event.
     *
     * There is no external API for this, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_external_blog_removed_event(): void {

        // Trigger an event: external blog removed.
        $eventparams = array(
            'context' => $context = \context_system::instance(),
            'objectid' => 1001,
        );

        $event = \core\event\blog_external_removed::create($eventparams);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\blog_external_removed', $event);
        $this->assertEquals(1001, $event->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test external blogs viewed event.
     *
     * There is no external API for this, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_external_blogs_viewed_event(): void {

        // Trigger an event: external blogs viewed.
        $eventparams = array(
            'context' => $context = \context_system::instance(),
        );

        $event = \core\event\blog_external_viewed::create($eventparams);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\blog_external_viewed', $event);
        $this->assertDebuggingNotCalled();
    }
}

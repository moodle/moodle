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
 * Unit tests for blog
 *
 * @package    core_blog
 * @category   phpunit
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->dirroot . '/blog/locallib.php');
require_once($CFG->dirroot . '/blog/lib.php');

/**
 * Test functions that rely on the DB tables
 */
class core_bloglib_testcase extends advanced_testcase {

    private $courseid;
    private $cmid;
    private $groupid;
    private $userid;
    private $tagid;
    private $postid;

    protected function setUp() {
        global $DB;
        parent::setUp();

        $this->resetAfterTest();

        // Create default course.
        $course = $this->getDataGenerator()->create_course(array('category'=>1, 'shortname'=>'ANON'));
        $this->assertNotEmpty($course);
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course->id));
        $this->assertNotEmpty($page);

        // Create default group.
        $group = new stdClass();
        $group->courseid = $course->id;
        $group->name = 'ANON';
        $group->id = $DB->insert_record('groups', $group);

        // Create default user.
        $user = $this->getDataGenerator()->create_user(array('username'=>'testuser', 'firstname'=>'Jimmy', 'lastname'=>'Kinnon'));

        // Create default tag.
        $tag = new stdClass();
        $tag->userid = $user->id;
        $tag->name = 'testtagname';
        $tag->rawname = 'Testtagname';
        $tag->tagtype = 'official';
        $tag->id = $DB->insert_record('tag', $tag);

        // Create default post.
        $post = new stdClass();
        $post->userid = $user->id;
        $post->groupid = $group->id;
        $post->content = 'test post content text';
        $post->id = $DB->insert_record('post', $post);

        // Grab important ids.
        $this->courseid = $course->id;
        $this->cmid = $page->cmid;
        $this->groupid  = $group->id;
        $this->userid  = $user->id;
        $this->tagid  = $tag->id;
        $this->postid = $post->id;
    }


    public function test_overrides() {
        global $SITE;

        // Try all the filters at once: Only the entry filter is active.
        $filters = array('site' => $SITE->id, 'course' => $this->courseid, 'module' => $this->cmid,
            'group' => $this->groupid, 'user' => $this->userid, 'tag' => $this->tagid, 'entry' => $this->postid);
        $blog_listing = new blog_listing($filters);
        $this->assertFalse(array_key_exists('site', $blog_listing->filters));
        $this->assertFalse(array_key_exists('course', $blog_listing->filters));
        $this->assertFalse(array_key_exists('module', $blog_listing->filters));
        $this->assertFalse(array_key_exists('group', $blog_listing->filters));
        $this->assertFalse(array_key_exists('user', $blog_listing->filters));
        $this->assertFalse(array_key_exists('tag', $blog_listing->filters));
        $this->assertTrue(array_key_exists('entry', $blog_listing->filters));

        // Again, but without the entry filter: This time, the tag, user and module filters are active.
        $filters = array('site' => $SITE->id, 'course' => $this->courseid, 'module' => $this->cmid,
            'group' => $this->groupid, 'user' => $this->userid, 'tag' => $this->postid);
        $blog_listing = new blog_listing($filters);
        $this->assertFalse(array_key_exists('site', $blog_listing->filters));
        $this->assertFalse(array_key_exists('course', $blog_listing->filters));
        $this->assertFalse(array_key_exists('group', $blog_listing->filters));
        $this->assertTrue(array_key_exists('module', $blog_listing->filters));
        $this->assertTrue(array_key_exists('user', $blog_listing->filters));
        $this->assertTrue(array_key_exists('tag', $blog_listing->filters));

        // We should get the same result by removing the 3 inactive filters: site, course and group.
        $filters = array('module' => $this->cmid, 'user' => $this->userid, 'tag' => $this->tagid);
        $blog_listing = new blog_listing($filters);
        $this->assertFalse(array_key_exists('site', $blog_listing->filters));
        $this->assertFalse(array_key_exists('course', $blog_listing->filters));
        $this->assertFalse(array_key_exists('group', $blog_listing->filters));
        $this->assertTrue(array_key_exists('module', $blog_listing->filters));
        $this->assertTrue(array_key_exists('user', $blog_listing->filters));
        $this->assertTrue(array_key_exists('tag', $blog_listing->filters));

    }

    // The following series of 'test_blog..' functions correspond to the blog_get_headers() function within blog/lib.php.
    // Some cases are omitted due to the optional_param variables used.

    public function test_blog_get_headers_case_1() {
        global $CFG, $PAGE, $OUTPUT;
        $blogheaders = blog_get_headers();
        $this->assertEquals($blogheaders['heading'], get_string('siteblogheading', 'blog'));
    }

    public function test_blog_get_headers_case_6() {
        global $CFG, $PAGE, $OUTPUT;
        $blogheaders = blog_get_headers($this->courseid, null, $this->userid);
        $this->assertNotEquals($blogheaders['heading'], '');
    }

    public function test_blog_get_headers_case_7() {
        global $CFG, $PAGE, $OUTPUT;
        $blogheaders = blog_get_headers(null, $this->groupid);
        $this->assertNotEquals($blogheaders['heading'], '');
    }

    public function test_blog_get_headers_case_10() {
        global $CFG, $PAGE, $OUTPUT;
        $blogheaders = blog_get_headers($this->courseid);
        $this->assertNotEquals($blogheaders['heading'], '');
    }

    /**
     * Test various blog related events.
     */
    public function test_blog_entry_created_event() {
        global $USER;

        $this->setAdminUser();
        $this->resetAfterTest();

        // Create a blog entry for another user as Admin.
        $sink = $this->redirectEvents();
        $blog = new blog_entry();
        $blog->subject = "Subject of blog";
        $blog->userid = $this->userid;
        $states = blog_entry::get_applicable_publish_states();
        $blog->publishstate = reset($states);
        $blog->add();
        $events = $sink->get_events();
        $sink->close();
        $event = reset($events);
        $sitecontext = context_system::instance();

        // Validate event data.
        $this->assertInstanceOf('\core\event\blog_entry_created', $event);
        $url = new moodle_url('/blog/index.php', array('entryid' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($sitecontext->id, $event->contextid);
        $this->assertEquals($blog->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals("post", $event->objecttable);
        $arr = array(SITEID, 'blog', 'add', 'index.php?userid=' . $this->userid . '&entryid=' . $blog->id, $blog->subject);
        $this->assertEventLegacyLogData($arr, $event);
        $this->assertEquals("blog_entry_added", $event->get_legacy_eventname());
        $this->assertEventLegacyData($blog, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event blog_entry_updated.
     */
    public function test_blog_entry_updated_event() {
        global $USER;

        $this->setAdminUser();
        $this->resetAfterTest();
        $sitecontext = context_system::instance();

        // Edit a blog entry as Admin.
        $blog = new blog_entry($this->postid);
        $sink = $this->redirectEvents();
        $blog->summary_editor = array('text' => 'Something', 'format' => FORMAT_MOODLE);
        $blog->edit(array(), null, array(), array());
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\blog_entry_updated', $event);
        $url = new moodle_url('/blog/index.php', array('entryid' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($sitecontext->id, $event->contextid);
        $this->assertEquals($blog->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals("post", $event->objecttable);
        $this->assertEquals("blog_entry_edited", $event->get_legacy_eventname());
        $this->assertEventLegacyData($blog, $event);
        $arr = array (SITEID, 'blog', 'update', 'index.php?userid=' . $this->userid . '&entryid=' . $blog->id, $blog->subject);
        $this->assertEventLegacyLogData($arr, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event blog_entry_deleted.
     */
    public function test_blog_entry_deleted_event() {
        global $USER, $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        $sitecontext = context_system::instance();

        // Delete a user blog entry as Admin.
        $blog = new blog_entry($this->postid);
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
        $this->assertSame('blog_entry_deleted', $event->get_legacy_eventname());
        $arr = array(SITEID, 'blog', 'delete', 'index.php?userid=' . $blog->userid, 'deleted blog entry with entry id# ' .
                $blog->id);
        $this->assertEventLegacyLogData($arr, $event);
        $this->assertEventLegacyData($blog, $event);
        $this->assertEventContextNotUsed($event);
    }


    /**
     * Tests for event blog_association_created.
     */
    public function test_blog_association_created_event() {
        global $USER;

        $this->setAdminUser();
        $this->resetAfterTest();
        $sitecontext = context_system::instance();
        $coursecontext = context_course::instance($this->courseid);
        $contextmodule = context_module::instance($this->cmid);

        // Add blog associations with a course.
        $blog = new blog_entry($this->postid);
        $sink = $this->redirectEvents();
        $blog->add_association($coursecontext->id);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\blog_association_created', $event);
        $this->assertEquals($sitecontext->id, $event->contextid);
        $url = new moodle_url('/blog/index.php', array('entryid' => $event->other['blogid']));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($blog->id, $event->other['blogid']);
        $this->assertEquals($this->courseid, $event->other['associateid']);
        $this->assertEquals('course', $event->other['associatetype']);
        $this->assertEquals($blog->subject, $event->other['subject']);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals('blog_association', $event->objecttable);
        $arr = array(SITEID, 'blog', 'add association', 'index.php?userid=' . $this->userid . '&entryid=' . $blog->id,
                     $blog->subject, 0, $this->userid);
        $this->assertEventLegacyLogData($arr, $event);

        // Add blog associations with a module.
        $blog = new blog_entry($this->postid);
        $sink = $this->redirectEvents();
        $blog->add_association($contextmodule->id);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Validate event data.
        $this->assertEquals($blog->id, $event->other['blogid']);
        $this->assertEquals($this->cmid, $event->other['associateid']);
        $this->assertEquals('coursemodule', $event->other['associatetype']);
        $arr = array(SITEID, 'blog', 'add association', 'index.php?userid=' . $this->userid . '&entryid=' . $blog->id,
                     $blog->subject, $this->cmid, $this->userid);
        $this->assertEventLegacyLogData($arr, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event blog_association_created validations.
     */
    public function test_blog_association_created_event_validations() {

        $this->resetAfterTest();

         // Make sure associatetype validations work.
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('associateid' => 2 , 'blogid' => 3, 'subject' => 'blog subject')));
        } catch (coding_exception $e) {
            $this->assertContains('The \'associatetype\' value must be set in other and be a valid type.', $e->getMessage());
        }
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('associateid' => 2 , 'blogid' => 3, 'associatetype' => 'random', 'subject' => 'blog subject')));
        } catch (coding_exception $e) {
            $this->assertContains('The \'associatetype\' value must be set in other and be a valid type.', $e->getMessage());
        }
        // Make sure associateid validations work.
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('blogid' => 3, 'associatetype' => 'course', 'subject' => 'blog subject')));
        } catch (coding_exception $e) {
            $this->assertContains('The \'associateid\' value must be set in other.', $e->getMessage());
        }
        // Make sure blogid validations work.
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('associateid' => 3, 'associatetype' => 'course', 'subject' => 'blog subject')));
        } catch (coding_exception $e) {
            $this->assertContains('The \'blogid\' value must be set in other.', $e->getMessage());
        }
        // Make sure blogid validations work.
        try {
            \core\event\blog_association_created::create(array(
                'contextid' => 1,
                'objectid' => 3,
                'relateduserid' => 2,
                'other' => array('blogid' => 3, 'associateid' => 3, 'associatetype' => 'course')));
        } catch (coding_exception $e) {
            $this->assertContains('The \'subject\' value must be set in other.', $e->getMessage());
        }
    }

    /**
     * Tests for event blog_entries_viewed.
     */
    public function test_blog_entries_viewed_event() {

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
        $url = new moodle_url('/blog/index.php', $other);
        $url2 = new moodle_url('index.php', $other);
        $this->assertEquals($url, $event->get_url());
        $arr = array(SITEID, 'blog', 'view', $url2->out(), 'view blog entry');
        $this->assertEventLegacyLogData($arr, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test comment_created event.
     */
    public function test_blog_comment_created_event() {
        global $USER, $CFG;

        $this->setAdminUser();

        require_once($CFG->dirroot . '/comment/lib.php');
        $context = context_user::instance($USER->id);

        $cmt = new stdClass();
        $cmt->context = $context;
        $cmt->courseid = $this->courseid;
        $cmt->area = 'format_blog';
        $cmt->itemid = $this->postid;
        $cmt->showcount = 1;
        $cmt->component = 'blog';
        $manager = new comment($cmt);

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
        $url = new moodle_url('/blog/index.php', array('entryid' => $this->postid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test comment_deleted event.
     */
    public function test_blog_comment_deleted_event() {
        global $USER, $CFG;

        $this->setAdminUser();

        require_once($CFG->dirroot . '/comment/lib.php');
        $context = context_user::instance($USER->id);

        $cmt = new stdClass();
        $cmt->context = $context;
        $cmt->courseid = $this->courseid;
        $cmt->area = 'format_blog';
        $cmt->itemid = $this->postid;
        $cmt->showcount = 1;
        $cmt->component = 'blog';
        $manager = new comment($cmt);
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
        $url = new moodle_url('/blog/index.php', array('entryid' => $this->postid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }
}


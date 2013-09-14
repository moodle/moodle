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

    private $courseid; // To store important ids to be used in tests
    private $cmid;
    private $groupid;
    private $userid;
    private $tagid;
    private $postid;

    protected function setUp() {
        global $DB;
        parent::setUp();

        $this->resetAfterTest();

        // Create default course
        $course = $this->getDataGenerator()->create_course(array('category'=>1, 'shortname'=>'ANON'));
        $this->assertNotEmpty($course);
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course->id));
        $this->assertNotEmpty($page);

        // Create default group
        $group = new stdClass();
        $group->courseid = $course->id;
        $group->name = 'ANON';
        $group->id = $DB->insert_record('groups', $group);

        // Create default user
        $user = $this->getDataGenerator()->create_user(array('username'=>'testuser', 'firstname'=>'Jimmy', 'lastname'=>'Kinnon'));

        // Create default tag
        $tag = new stdClass();
        $tag->userid = $user->id;
        $tag->name = 'testtagname';
        $tag->rawname = 'Testtagname';
        $tag->tagtype = 'official';
        $tag->id = $DB->insert_record('tag', $tag);

        // Create default post
        $post = new stdClass();
        $post->userid = $user->id;
        $post->groupid = $group->id;
        $post->content = 'test post content text';
        $post->id = $DB->insert_record('post', $post);

        // Grab important ids
        $this->courseid = $course->id;
        $this->cmid = $page->cmid;
        $this->groupid  = $group->id;
        $this->userid  = $user->id;
        $this->tagid  = $tag->id;
        $this->postid = $post->id;
    }


    public function test_overrides() {
        global $SITE;

        // Try all the filters at once: Only the entry filter is active
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

        // Again, but without the entry filter: This time, the tag, user and module filters are active
        $filters = array('site' => $SITE->id, 'course' => $this->courseid, 'module' => $this->cmid,
            'group' => $this->groupid, 'user' => $this->userid, 'tag' => $this->postid);
        $blog_listing = new blog_listing($filters);
        $this->assertFalse(array_key_exists('site', $blog_listing->filters));
        $this->assertFalse(array_key_exists('course', $blog_listing->filters));
        $this->assertFalse(array_key_exists('group', $blog_listing->filters));
        $this->assertTrue(array_key_exists('module', $blog_listing->filters));
        $this->assertTrue(array_key_exists('user', $blog_listing->filters));
        $this->assertTrue(array_key_exists('tag', $blog_listing->filters));

        // We should get the same result by removing the 3 inactive filters: site, course and group:
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
        $blog_headers = blog_get_headers();
        $this->assertEquals($blog_headers['heading'], get_string('siteblog', 'blog', 'phpunit'));
    }

    public function test_blog_get_headers_case_6() {
        global $CFG, $PAGE, $OUTPUT;
        $blog_headers = blog_get_headers($this->courseid, NULL, $this->userid);
        $this->assertNotEquals($blog_headers['heading'], '');
    }

    public function test_blog_get_headers_case_7() {
        global $CFG, $PAGE, $OUTPUT;
        $blog_headers = blog_get_headers(NULL, $this->groupid);
        $this->assertNotEquals($blog_headers['heading'], '');
    }

    public function test_blog_get_headers_case_10() {
        global $CFG, $PAGE, $OUTPUT;
        $blog_headers = blog_get_headers($this->courseid);
        $this->assertNotEquals($blog_headers['heading'], '');
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
        $this->assertEquals($sitecontext->id, $event->contextid);
        $this->assertEquals($blog->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals("post", $event->objecttable);
        $arr = array(SITEID, 'blog', 'add', 'index.php?userid=' . $this->userid . '&entryid=' . $blog->id, $blog->subject);
        $this->assertEventLegacyLogData($arr, $event);
        $this->assertEquals("blog_entry_added", $event->get_legacy_eventname());
        $this->assertEventLegacyData($blog, $event);
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
        $this->assertEquals($sitecontext->id, $event->contextid);
        $this->assertEquals($blog->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->userid, $event->relateduserid);
        $this->assertEquals("post", $event->objecttable);
        $this->assertEquals("blog_entry_edited", $event->get_legacy_eventname());
        $this->assertEventLegacyData($blog, $event);
        $arr = array (SITEID, 'blog', 'update', 'index.php?userid=' . $this->userid . '&entryid=' . $blog->id, $blog->subject);
        $this->assertEventLegacyLogData($arr, $event);
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
    }
}


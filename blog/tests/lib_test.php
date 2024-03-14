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
namespace core_blog;

use blog_listing;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/blog/locallib.php');
require_once($CFG->dirroot . '/blog/lib.php');

/**
 * Test functions that rely on the DB tables
 */
class lib_test extends \advanced_testcase {

    private $courseid;
    private $cmid;
    private $groupid;
    private $userid;
    private $tagid;
    private $postid;

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


    public function test_overrides() {
        global $SITE;

        // Try all the filters at once: Only the entry filter is active.
        $filters = array('site' => $SITE->id, 'course' => $this->courseid, 'module' => $this->cmid,
            'group' => $this->groupid, 'user' => $this->userid, 'tag' => $this->tagid, 'entry' => $this->postid);
        $bloglisting = new blog_listing($filters);
        $this->assertFalse(array_key_exists('site', $bloglisting->filters));
        $this->assertFalse(array_key_exists('course', $bloglisting->filters));
        $this->assertFalse(array_key_exists('module', $bloglisting->filters));
        $this->assertFalse(array_key_exists('group', $bloglisting->filters));
        $this->assertFalse(array_key_exists('user', $bloglisting->filters));
        $this->assertFalse(array_key_exists('tag', $bloglisting->filters));
        $this->assertTrue(array_key_exists('entry', $bloglisting->filters));

        // Again, but without the entry filter: This time, the tag, user and module filters are active.
        $filters = array('site' => $SITE->id, 'course' => $this->courseid, 'module' => $this->cmid,
            'group' => $this->groupid, 'user' => $this->userid, 'tag' => $this->postid);
        $bloglisting = new blog_listing($filters);
        $this->assertFalse(array_key_exists('site', $bloglisting->filters));
        $this->assertFalse(array_key_exists('course', $bloglisting->filters));
        $this->assertFalse(array_key_exists('group', $bloglisting->filters));
        $this->assertTrue(array_key_exists('module', $bloglisting->filters));
        $this->assertTrue(array_key_exists('user', $bloglisting->filters));
        $this->assertTrue(array_key_exists('tag', $bloglisting->filters));

        // We should get the same result by removing the 3 inactive filters: site, course and group.
        $filters = array('module' => $this->cmid, 'user' => $this->userid, 'tag' => $this->tagid);
        $bloglisting = new blog_listing($filters);
        $this->assertFalse(array_key_exists('site', $bloglisting->filters));
        $this->assertFalse(array_key_exists('course', $bloglisting->filters));
        $this->assertFalse(array_key_exists('group', $bloglisting->filters));
        $this->assertTrue(array_key_exists('module', $bloglisting->filters));
        $this->assertTrue(array_key_exists('user', $bloglisting->filters));
        $this->assertTrue(array_key_exists('tag', $bloglisting->filters));

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
     * Tests the core_blog_myprofile_navigation() function.
     */
    public function test_core_blog_myprofile_navigation() {
        global $USER;

        // Set up the test.
        $tree = new \core_user\output\myprofile\tree();
        $this->setAdminUser();
        $iscurrentuser = true;
        $course = null;

        // Enable blogs.
        set_config('enableblogs', true);

        // Check the node tree is correct.
        core_blog_myprofile_navigation($tree, $USER, $iscurrentuser, $course);
        $reflector = new \ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('blogs', $nodes->getValue($tree));
    }

    /**
     * Tests the core_blog_myprofile_navigation() function as a guest.
     */
    public function test_core_blog_myprofile_navigation_as_guest() {
        global $USER;

        // Set up the test.
        $tree = new \core_user\output\myprofile\tree();
        $iscurrentuser = false;
        $course = null;

        // Set user as guest.
        $this->setGuestUser();

        // Check the node tree is correct.
        core_blog_myprofile_navigation($tree, $USER, $iscurrentuser, $course);
        $reflector = new \ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('blogs', $nodes->getValue($tree));
    }

    /**
     * Tests the core_blog_myprofile_navigation() function when blogs are disabled.
     */
    public function test_core_blog_myprofile_navigation_blogs_disabled() {
        global $USER;

        // Set up the test.
        $tree = new \core_user\output\myprofile\tree();
        $this->setAdminUser();
        $iscurrentuser = false;
        $course = null;

        // Disable blogs.
        set_config('enableblogs', false);

        // Check the node tree is correct.
        core_blog_myprofile_navigation($tree, $USER, $iscurrentuser, $course);
        $reflector = new \ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('blogs', $nodes->getValue($tree));
    }

    public function test_blog_get_listing_course() {
        $this->setAdminUser();
        $coursecontext = \context_course::instance($this->courseid);
        $anothercourse = $this->getDataGenerator()->create_course();

        // Add blog associations with a course.
        $blog = new \blog_entry($this->postid);
        $blog->add_association($coursecontext->id);

        // There is one entry associated with a course.
        $bloglisting = new blog_listing(array('course' => $this->courseid));
        $this->assertCount(1, $bloglisting->get_entries());

        // There is no entry associated with a wrong course.
        $bloglisting = new blog_listing(array('course' => $anothercourse->id));
        $this->assertCount(0, $bloglisting->get_entries());

        // There is no entry associated with a module.
        $bloglisting = new blog_listing(array('module' => $this->cmid));
        $this->assertCount(0, $bloglisting->get_entries());

        // There is one entry associated with a site (id is ignored).
        $bloglisting = new blog_listing(array('site' => 12345));
        $this->assertCount(1, $bloglisting->get_entries());

        // There is one entry associated with course context.
        $bloglisting = new blog_listing(array('context' => $coursecontext->id));
        $this->assertCount(1, $bloglisting->get_entries());
    }

    public function test_blog_get_listing_module() {
        $this->setAdminUser();
        $coursecontext = \context_course::instance($this->courseid);
        $contextmodule = \context_module::instance($this->cmid);
        $anothermodule = $this->getDataGenerator()->create_module('page', array('course' => $this->courseid));

        // Add blog associations with a course.
        $blog = new \blog_entry($this->postid);
        $blog->add_association($contextmodule->id);

        // There is no entry associated with a course.
        $bloglisting = new blog_listing(array('course' => $this->courseid));
        $this->assertCount(0, $bloglisting->get_entries());

        // There is one entry associated with a module.
        $bloglisting = new blog_listing(array('module' => $this->cmid));
        $this->assertCount(1, $bloglisting->get_entries());

        // There is no entry associated with a wrong module.
        $bloglisting = new blog_listing(array('module' => $anothermodule->cmid));
        $this->assertCount(0, $bloglisting->get_entries());

        // There is one entry associated with a site (id is ignored).
        $bloglisting = new blog_listing(array('site' => 12345));
        $this->assertCount(1, $bloglisting->get_entries());

        // There is one entry associated with course context (module is a subcontext of a course).
        $bloglisting = new blog_listing(array('context' => $coursecontext->id));
        $this->assertCount(1, $bloglisting->get_entries());
    }
}


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
final class lib_test extends \advanced_testcase {

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


    public function test_overrides(): void {
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

    public function test_blog_get_headers_case_1(): void {
        global $CFG, $PAGE, $OUTPUT;
        $blogheaders = blog_get_headers();
        $this->assertEquals($blogheaders['heading'], get_string('siteblogheading', 'blog'));
    }

    public function test_blog_get_headers_case_6(): void {
        global $CFG, $PAGE, $OUTPUT;
        $blogheaders = blog_get_headers($this->courseid, null, $this->userid);
        $this->assertNotEquals($blogheaders['heading'], '');
    }

    public function test_blog_get_headers_case_7(): void {
        global $CFG, $PAGE, $OUTPUT;
        $blogheaders = blog_get_headers(null, $this->groupid);
        $this->assertNotEquals($blogheaders['heading'], '');
    }

    public function test_blog_get_headers_case_10(): void {
        global $CFG, $PAGE, $OUTPUT;
        $blogheaders = blog_get_headers($this->courseid);
        $this->assertNotEquals($blogheaders['heading'], '');
    }

    /**
     * Tests the core_blog_myprofile_navigation() function.
     *
     * @covers ::core_blog_myprofile_navigation
     */
    public function test_core_blog_myprofile_navigation(): void {
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
        $nodes = $nodes->getValue($tree);

        $this->assertArrayHasKey('blogs', $nodes);
        $this->assertSame('View my blog entries', $nodes['blogs']->title);
        $this->assertSame(
            (new \moodle_url('/blog/index.php', ['userid' => $USER->id]))->out(false),
            $nodes['blogs']->url->out(false)
        );

        $this->assertArrayHasKey('siteblogs', $nodes);
        $this->assertSame('View site blog entries', $nodes['siteblogs']->title);
        $this->assertSame('blogs', $nodes['siteblogs']->after);
        $this->assertSame((new \moodle_url('/blog/index.php'))->out(false), $nodes['siteblogs']->url->out(false));
    }

    /**
     * Tests the core_blog_myprofile_navigation() function when a course context is provided.
     *
     * @covers ::core_blog_myprofile_navigation
     */
    public function test_core_blog_myprofile_navigation_in_course(): void {
        global $DB, $USER;

        // Set up the test.
        $tree = new \core_user\output\myprofile\tree();
        $this->setAdminUser();
        $iscurrentuser = true;
        $course = $DB->get_record('course', ['id' => $this->courseid], '*', MUST_EXIST);

        // Enable blogs.
        set_config('enableblogs', true);

        // Check the node tree is correct.
        core_blog_myprofile_navigation($tree, $USER, $iscurrentuser, $course);
        $reflector = new \ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes = $nodes->getValue($tree);

        $this->assertSame(
            (new \moodle_url('/blog/index.php', ['userid' => $USER->id, 'courseid' => $course->id]))->out(false),
            $nodes['blogs']->url->out(false)
        );
        $this->assertSame((new \moodle_url('/blog/index.php'))->out(false), $nodes['siteblogs']->url->out(false));
    }

    /**
     * Tests the core_blog_myprofile_navigation() function as a guest.
     *
     * @covers ::core_blog_myprofile_navigation
     */
    public function test_core_blog_myprofile_navigation_as_guest(): void {
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
        $nodes = $nodes->getValue($tree);
        $this->assertArrayNotHasKey('blogs', $nodes);
        $this->assertArrayNotHasKey('siteblogs', $nodes);
    }

    /**
     * Tests the core_blog_myprofile_navigation() function when blogs are disabled.
     *
     * @covers ::core_blog_myprofile_navigation
     */
    public function test_core_blog_myprofile_navigation_blogs_disabled(): void {
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
        $nodes = $nodes->getValue($tree);
        $this->assertArrayNotHasKey('blogs', $nodes);
        $this->assertArrayNotHasKey('siteblogs', $nodes);
    }

    /**
     * Tests the core_blog_myprofile_navigation() function when viewing another user's profile.
     *
     * @covers ::core_blog_myprofile_navigation
     */
    public function test_core_blog_myprofile_navigation_other_user(): void {
        // Set up the test: admin views another user's profile.
        $tree = new \core_user\output\myprofile\tree();
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setAdminUser();
        $iscurrentuser = false;
        $course = null;

        // Enable blogs.
        set_config('enableblogs', true);

        // Check the node tree is correct.
        core_blog_myprofile_navigation($tree, $otheruser, $iscurrentuser, $course);
        $reflector = new \ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes = $nodes->getValue($tree);

        // Personal blog link should use the neutral label (not "my").
        $this->assertArrayHasKey('blogs', $nodes);
        $this->assertSame('View blog entries', $nodes['blogs']->title);

        // URL should reference the profile owner's userid, not the viewer's.
        $this->assertSame(
            (new \moodle_url('/blog/index.php', ['userid' => $otheruser->id]))->out(false),
            $nodes['blogs']->url->out(false)
        );

        $this->assertArrayHasKey('siteblogs', $nodes);
    }

    /**
     * Tests that the site blog link is independent of personal blog visibility, but still
     * requires the site to be configured at (or above) BLOG_SITE_LEVEL.
     *
     * A viewer with moodle/blog:view capability but without moodle/user:readuserblogs on the
     * profile owner's context should still see the site blog link, provided bloglevel allows it.
     *
     * @covers ::core_blog_myprofile_navigation
     */
    public function test_core_blog_myprofile_navigation_site_blog_independent_of_user_visibility(): void {
        // Create a viewer (plain user role) and a profile owner.
        $viewer = $this->getDataGenerator()->create_user();
        $profileowner = $this->getDataGenerator()->create_user();

        // Enable blogs at user level: other users' personal blogs are only visible to those
        // with moodle/user:readuserblogs. Plain users do not have that capability by default,
        // so blog_user_can_view_user_entry() returns false.
        set_config('enableblogs', true);
        set_config('bloglevel', BLOG_USER_LEVEL);

        $this->setUser($viewer);

        $tree = new \core_user\output\myprofile\tree();
        core_blog_myprofile_navigation($tree, $profileowner, false, null);

        $reflector = new \ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes = $nodes->getValue($tree);

        // Personal blog node should be absent (viewer cannot see the owner's personal entries).
        $this->assertArrayNotHasKey('blogs', $nodes);

        // Site blog node should also be absent: /blog/index.php itself requires bloglevel
        // >= BLOG_SITE_LEVEL, so the link must not be offered below that level either.
        $this->assertArrayNotHasKey('siteblogs', $nodes);
    }

    /**
     * Tests that the site blog link is shown once bloglevel allows site-wide access, even when
     * the profile owner's personal entries remain inaccessible to the viewer.
     *
     * @covers ::core_blog_myprofile_navigation
     */
    public function test_core_blog_myprofile_navigation_site_blog_shown_at_site_level(): void {
        // Create a viewer (plain user role) and a profile owner.
        $viewer = $this->getDataGenerator()->create_user();
        $profileowner = $this->getDataGenerator()->create_user();

        // At BLOG_SITE_LEVEL, logged-in viewers have moodle/blog:view by default, matching the
        // access level required by /blog/index.php itself.
        set_config('enableblogs', true);
        set_config('bloglevel', BLOG_SITE_LEVEL);

        $this->setUser($viewer);

        $tree = new \core_user\output\myprofile\tree();
        core_blog_myprofile_navigation($tree, $profileowner, false, null);

        $reflector = new \ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes = $nodes->getValue($tree);

        $this->assertArrayHasKey('siteblogs', $nodes);
        $this->assertSame((new \moodle_url('/blog/index.php'))->out(false), $nodes['siteblogs']->url->out(false));
    }

    public function test_blog_get_listing_course(): void {
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

    public function test_blog_get_listing_module(): void {
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

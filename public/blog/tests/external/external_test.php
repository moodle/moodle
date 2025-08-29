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
 * Unit tests for blog external API.
 *
 * @package    core_blog
 * @copyright  2018 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_blog\external;

use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/blog/locallib.php');
require_once($CFG->dirroot . '/blog/lib.php');


/**
 * Unit tests for blog external API.
 *
 * @package    core_blog
 * @copyright  2018 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class external_test extends \core_external\tests\externallib_testcase {
    private $courseid;
    private $cmid;
    private $userid;
    private $groupid;
    private $tagid;
    private $postid;

    /** @var string publish state. */
    protected $publishstate;

    protected function setUp(): void {
        global $DB, $CFG;
        parent::setUp();

        $this->resetAfterTest();

        // Create default course.
        $course = $this->getDataGenerator()->create_course(array('category' => 1, 'shortname' => 'ANON'));
        $this->assertNotEmpty($course);
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $this->assertNotEmpty($page);

        // Create default user.
        $user = $this->getDataGenerator()->create_user(array(
                'username' => 'testuser',
                'firstname' => 'Jimmy',
                'lastname' => 'Kinnon'
        ));
        // Enrol user.
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $user->id));

        // Create default post.
        $post = new \stdClass();
        $post->userid = $user->id;
        $post->courseid = $course->id;
        $post->groupid = $group->id;
        $post->content = 'test post content text';
        $post->module = 'blog';
        $post->id = $DB->insert_record('post', $post);

        \core_tag_tag::set_item_tags('core', 'post', $post->id, \context_user::instance($user->id), array('tag1'));
        $tagid = $DB->get_field('tag', 'id', array('name' => 'tag1'));

        // Grab important ids.
        $this->courseid = $course->id;
        $this->cmid = $page->cmid;
        $this->userid  = $user->id;
        $this->groupid  = $group->id;
        $this->tagid  = $tagid;
        $this->postid = $post->id;
        $this->publishstate = 'site';   // To be override in tests.

        // Set default blog level.
        $CFG->bloglevel = BLOG_SITE_LEVEL;
    }

    /**
     * Get global public entries even for not authenticated users.
     * We get the entry since is public.
     */
    public function test_get_public_entries_global_level_by_non_logged_users(): void {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;
        $CFG->forcelogin = 0;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'public', array('id' => $this->postid));

        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertCount(1, $result['entries'][0]['tags']);
        $this->assertEquals('tag1', $result['entries'][0]['tags'][0]['rawname']);

        $this->assertEquals($this->postid, $result['entries'][0]['id']);
        $this->assertFalse($result['entries'][0]['canedit']);
    }

    /**
     * Get global public entries even for not authenticated users in closed site.
     */
    public function test_get_public_entries_global_level_by_non_logged_users_closed_site(): void {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;
        $CFG->forcelogin = 1;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'public', array('id' => $this->postid));

        $this->expectException('\moodle_exception');
        \core_blog\external::get_entries();
    }

    /**
     * Get global public entries for guest users.
     * We get the entry since is public.
     */
    public function test_get_public_entries_global_level_by_guest_users(): void {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'public', array('id' => $this->postid));

        $this->setGuestUser();
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertCount(1, $result['entries'][0]['tags']);
        $this->assertEquals('tag1', $result['entries'][0]['tags'][0]['rawname']);

        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Get global not public entries even for not authenticated users withouth being authenticated.
     * We don't get any because they are not public (restricted to site users).
     */
    public function test_get_not_public_entries_global_level_by_non_logged_users(): void {
        global $CFG;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;

        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get global not public entries users being guest.
     * We don't get any because they are not public (restricted to real site users).
     */
    public function test_get_not_public_entries_global_level_by_guest_user(): void {
        global $CFG;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;

        $this->setGuestUser();
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get site not public entries for not authenticated users.
     * We don't get any because they are not public (restricted to real site users).
     */
    public function test_get_not_public_entries_site_level_by_non_logged_users(): void {
        $this->expectException('require_login_exception'); // In this case we get a security exception.
        $result = \core_blog\external::get_entries();
    }

    /**
     * Get site not public entries for guest users.
     * We don't get any because they are not public (restricted to real site users).
     */
    public function test_get_not_public_entries_site_level_by_guest_users(): void {

        $this->setGuestUser();
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get site entries at site level by system users.
     */
    public function test_get_site_entries_site_level_by_normal_users(): void {

        $this->setUser($this->userid);
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Get draft site entries by authors.
     */
    public function test_get_draft_entries_site_level_by_author_users(): void {
        global $DB;

        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));

        $this->setUser($this->userid);
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Get draft site entries by not authors.
     */
    public function test_get_draft_entries_site_level_by_not_author_users(): void {
        global $DB;

        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get draft site entries by admin.
     */
    public function test_get_draft_entries_site_level_by_admin_users(): void {
        global $DB;

        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));
        $user = $this->getDataGenerator()->create_user();

        $this->setAdminUser();
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
        $this->assertTrue($result['entries'][0]['canedit']);
    }

    /**
     * Get draft user entries by authors.
     */
    public function test_get_draft_entries_user_level_by_author_users(): void {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_USER_LEVEL;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));

        $this->setUser($this->userid);
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
        $this->assertTrue($result['entries'][0]['canedit']);
    }

    /**
     * Get draft user entries by not authors.
     */
    public function test_get_draft_entries_user_level_by_not_author_users(): void {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_USER_LEVEL;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get draft user entries by admin.
     */
    public function test_get_draft_entries_user_level_by_admin_users(): void {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_USER_LEVEL;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));
        $user = $this->getDataGenerator()->create_user();

        $this->setAdminUser();
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Test get all entries including testing pagination.
     */
    public function test_get_all_entries_including_pagination(): void {
        global $DB, $USER;

        $DB->set_field('post', 'publishstate', 'site', array('id' => $this->postid));

        // Create another entry.
        $this->setAdminUser();
        $newpost = new \stdClass();
        $newpost->userid = $USER->id;
        $newpost->content = 'test post content text';
        $newpost->module = 'blog';
        $newpost->publishstate = 'site';
        $newpost->created = time() + HOURSECS;
        $newpost->lastmodified = time() + HOURSECS;
        $newpost->id = $DB->insert_record('post', $newpost);

        $this->setUser($this->userid);
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalentries']);
        $this->assertCount(0, $result['entries'][0]['tags']);
        $this->assertCount(1, $result['entries'][1]['tags']);
        $this->assertEquals('tag1', $result['entries'][1]['tags'][0]['rawname']);

        $result = \core_blog\external::get_entries(array(), 0, 1);
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals(2, $result['totalentries']);
        $this->assertEquals($newpost->id, $result['entries'][0]['id']);

        $result = \core_blog\external::get_entries(array(), 1, 1);
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals(2, $result['totalentries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Test get entries filtering by course.
     */
    public function test_get_entries_filtering_by_course(): void {
        global $CFG, $DB;

        $DB->set_field('post', 'publishstate', 'site', array('id' => $this->postid));

        $this->setAdminUser();
        $coursecontext = \context_course::instance($this->courseid);
        $anothercourse = $this->getDataGenerator()->create_course();

        // Add blog associations with a course.
        $blog = new \blog_entry($this->postid);
        $blog->add_association($coursecontext->id);

        // There is one entry associated with a course.
        $result = \core_blog\external::get_entries(array(array('name' => 'courseid', 'value' => $this->courseid)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertCount(1, $result['entries'][0]['tags']);
        $this->assertEquals('tag1', $result['entries'][0]['tags'][0]['rawname']);

        // There is no entry associated with a wrong course.
        $result = \core_blog\external::get_entries(array(array('name' => 'courseid', 'value' => $anothercourse->id)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);

        // There is no entry associated with a module.
        $result = \core_blog\external::get_entries(array(array('name' => 'cmid', 'value' => $this->cmid)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by module.
     */
    public function test_get_entries_filtering_by_module(): void {
        global $CFG, $DB;

        $DB->set_field('post', 'publishstate', 'site', array('id' => $this->postid));

        $this->setAdminUser();
        $coursecontext = \context_course::instance($this->courseid);
        $contextmodule = \context_module::instance($this->cmid);
        $anothermodule = $this->getDataGenerator()->create_module('page', array('course' => $this->courseid));

        // Add blog associations with a module.
        $blog = new \blog_entry($this->postid);
        $blog->add_association($contextmodule->id);

        // There is no entry associated with a course.
        $result = \core_blog\external::get_entries(array(array('name' => 'courseid', 'value' => $this->courseid)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);

        // There is one entry associated with a module.
        $result = \core_blog\external::get_entries(array(array('name' => 'cmid', 'value' => $this->cmid)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);

        // There is no entry associated with a wrong module.
        $result = \core_blog\external::get_entries(array(array('name' => 'cmid', 'value' => $anothermodule->cmid)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by author.
     */
    public function test_get_entries_filtering_by_author(): void {
        $this->setAdminUser();
        // Filter by author.
        $result = \core_blog\external::get_entries(array(array('name' => 'userid', 'value' => $this->userid)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // No author.
        $anotheruser = $this->getDataGenerator()->create_user();
        $result = \core_blog\external::get_entries(array(array('name' => 'userid', 'value' => $anotheruser->id)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by entry.
     */
    public function test_get_entries_filtering_by_entry(): void {
        $this->setAdminUser();
        // Filter by correct entry.
        $result = \core_blog\external::get_entries(array(array('name' => 'entryid', 'value' => $this->postid)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Non-existent entry.
        $this->expectException('\moodle_exception');
        $result = \core_blog\external::get_entries(array(array('name' => 'entryid', 'value' => -1)));
    }

    /**
     * Test get entries filtering by search.
     */
    public function test_get_entries_filtering_by_search(): void {
        $this->setAdminUser();
        // Filter by correct search.
        $result = \core_blog\external::get_entries(array(array('name' => 'search', 'value' => 'test')));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Non-existent search.
        $result = \core_blog\external::get_entries(array(array('name' => 'search', 'value' => 'abc')));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by tag.
     */
    public function test_get_entries_filtering_by_tag(): void {
        $this->setAdminUser();
        // Filter by correct tag.
        $result = \core_blog\external::get_entries(array(array('name' => 'tag', 'value' => 'tag1')));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Create tag.
        $tag = $this->getDataGenerator()->create_tag(array('userid' => $this->userid, 'name' => 'tag2',
            'isstandard' => 1));

        $result = \core_blog\external::get_entries(array(array('name' => 'tag', 'value' => 'tag2')));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by tag id.
     */
    public function test_get_entries_filtering_by_tagid(): void {
        $this->setAdminUser();
        // Filter by correct tag.
        $result = \core_blog\external::get_entries(array(array('name' => 'tagid', 'value' => $this->tagid)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Non-existent tag.

        // Create tag.
        $tag = $this->getDataGenerator()->create_tag(array('userid' => $this->userid, 'name' => 'tag2',
            'isstandard' => 1));

        $result = \core_blog\external::get_entries(array(array('name' => 'tagid', 'value' => $tag->id)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by group.
     */
    public function test_get_entries_filtering_by_group(): void {
        $this->setAdminUser();
        // Add blog associations with a course.
        $coursecontext = \context_course::instance($this->courseid);
        $blog = new \blog_entry($this->postid);
        $blog->add_association($coursecontext->id);

        // Filter by correct group.
        $result = \core_blog\external::get_entries(array(array('name' => 'groupid', 'value' => $this->groupid)));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Non-existent group.
        $anotheruser = $this->getDataGenerator()->create_user();
        $this->expectException('\moodle_exception');
        \core_blog\external::get_entries(array(array('name' => 'groupid', 'value' => -1)));
    }

    /**
     * Test get entries multiple filter.
     */
    public function test_get_entries_multiple_filter(): void {
        $this->setAdminUser();
        // Add blog associations with a course.
        $coursecontext = \context_course::instance($this->courseid);
        $blog = new \blog_entry($this->postid);
        $blog->add_association($coursecontext->id);

        $result = \core_blog\external::get_entries(array(
            array('name' => 'tagid', 'value' => $this->tagid),
            array('name' => 'userid', 'value' => $this->userid),
        ));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);

        // Non-existent multiple filter.
        $result = \core_blog\external::get_entries(array(
            array('name' => 'search', 'value' => 'www'),
            array('name' => 'userid', 'value' => $this->userid),
        ));
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by invalid_filter.
     */
    public function test_get_entries_filtering_by_invalid_filter(): void {
        $this->setAdminUser();
        // Filter by incorrect filter.
        $this->expectException('\moodle_exception');
        $result = \core_blog\external::get_entries(array(array('name' => 'zzZZzz', 'value' => 'wwWWww')));
    }

    /**
     * Test get entries when blog is disabled.
     */
    public function test_get_entries_blog_disabled(): void {
        global $CFG;

        $this->setAdminUser();
        $CFG->enableblogs = 0;
        // Filter by incorrect filter.
        $this->expectException('\moodle_exception');
        $result = \core_blog\external::get_entries(array(array('name' => 'zzZZzz', 'value' => 'wwWWww')));
    }

    /**
     * Test view_blog_entries without filter.
     */
    public function test_view_blog_entries_without_filtering(): void {
        // Test user with full capabilities.
        $this->setUser($this->userid);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = \core_blog\external::view_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::view_entries_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);
        // Checking that the event contains the expected values (empty, no filtering done).
        $this->assertInstanceOf('\core\event\blog_entries_viewed', $event);
        $this->assertEmpty($event->get_data()['relateduserid']);
        $this->assertEmpty($event->get_data()['other']['entryid']);
        $this->assertEmpty($event->get_data()['other']['tagid']);
        $this->assertEmpty($event->get_data()['other']['userid']);
        $this->assertEmpty($event->get_data()['other']['modid']);
        $this->assertEmpty($event->get_data()['other']['groupid']);
        $this->assertEmpty($event->get_data()['other']['search']);
        $this->assertEmpty($event->get_data()['other']['courseid']);
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test view_blog_entries doing filtering.
     */
    public function test_view_blog_entries_with_filtering(): void {
        // Test user with full capabilities.
        $this->setUser($this->userid);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = \core_blog\external::view_entries(array(
            array('name' => 'tagid', 'value' => $this->tagid),
            array('name' => 'userid', 'value' => $this->userid),
        ));
        $result = external_api::clean_returnvalue(\core_blog\external::view_entries_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);
        // Checking that the event contains the expected values (filter by user and tag).
        $this->assertInstanceOf('\core\event\blog_entries_viewed', $event);
        $this->assertEquals($this->userid, $event->get_data()['relateduserid']);
        $this->assertEmpty($event->get_data()['other']['entryid']);
        $this->assertEquals($this->tagid, $event->get_data()['other']['tagid']);
        $this->assertEquals($this->userid, $event->get_data()['other']['userid']);
        $this->assertEmpty($event->get_data()['other']['modid']);
        $this->assertEmpty($event->get_data()['other']['groupid']);
        $this->assertEmpty($event->get_data()['other']['search']);
        $this->assertEmpty($event->get_data()['other']['courseid']);
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test get_access_information
     */
    public function test_get_access_information(): void {
        global $CFG;

        $this->setAdminUser();
        $result = get_access_information::execute();
        $result = external_api::clean_returnvalue(get_access_information::execute_returns(), $result);

        $this->assertTrue($result['canview']);
        $this->assertTrue($result['cansearch']);
        $this->assertTrue($result['canviewdrafts']);
        $this->assertTrue($result['cancreate']);
        $this->assertTrue($result['canmanageentries']);
        $this->assertTrue($result['canmanageexternal']);
        $this->assertEmpty($result['warnings']);

        $this->setUser($this->userid);
        $result = get_access_information::execute();
        $result = external_api::clean_returnvalue(get_access_information::execute_returns(), $result);

        $this->assertTrue($result['canview']);
        $this->assertTrue($result['cansearch']);
        $this->assertFalse($result['canviewdrafts']);
        $this->assertTrue($result['cancreate']);
        $this->assertFalse($result['canmanageentries']);
        $this->assertTrue($result['canmanageexternal']);
        $this->assertEmpty($result['warnings']);
    }

    /**
     * Test add_entry
     */
    public function test_add_entry(): void {
        global $USER;

        $this->resetAfterTest(true);

        // Add post with attachments.
        $this->setAdminUser();

        // Draft files.
        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();
        $usercontext = \context_user::instance($USER->id);
        $inlinefilename = 'inlineimage.png';
        $filerecordinline = [
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftidinlineattach,
            'filepath'  => '/',
            'filename'  => $inlinefilename,
        ];
        $fs = get_file_storage();

        // Create a file in a draft area for regular attachments.
        $filerecordattach = $filerecordinline;
        $attachfilename = 'attachment.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        $options = [
            [
                'name' => 'inlineattachmentsid',
                'value' => $draftidinlineattach,
            ],
            [
                'name' => 'attachmentsid',
                'value' => $draftidattach,
            ],
            [
                'name' => 'tags',
                'value' => 'tag1, tag2',
            ],
            [
                'name' => 'courseassoc',
                'value' => $this->courseid,
            ],
        ];

        $subject = 'First post';
        $summary = 'First post summary';
        $result = add_entry::execute($subject, $summary, FORMAT_HTML, $options);
        $result = external_api::clean_returnvalue(add_entry::execute_returns(), $result);
        $postid = $result['entryid'];

        // Retrieve files via WS.
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);

        foreach ($result['entries'] as $entry) {
            if ($entry['id'] == $postid) {
                $this->assertEquals($subject, $entry['subject']);
                $this->assertEquals($summary, $entry['summary']);
                $this->assertEquals($this->courseid, $entry['courseid']);
                $this->assertCount(1, $entry['attachmentfiles']);
                $this->assertCount(1, $entry['summaryfiles']);
                $this->assertCount(2, $entry['tags']);
                $this->assertEquals($attachfilename, $entry['attachmentfiles'][0]['filename']);
                $this->assertEquals($inlinefilename, $entry['summaryfiles'][0]['filename']);
            }
        }
    }

    /**
     * Test add_entry when blogs not enabled.
     */
    public function test_add_entry_blog_not_enabled(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->enableblogs = 0;
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('blogdisable', 'blog'));
        add_entry::execute('Subject', 'Summary', FORMAT_HTML);
    }

    /**
     * Test add_entry without permissions.
     */
    public function test_add_entry_no_permission(): void {
        global $CFG;

        $this->resetAfterTest(true);

        // Remove capability.
        $sitecontext = \context_system::instance();
        $this->unassignUserCapability('moodle/blog:create', $sitecontext->id, $CFG->defaultuserroleid);
        $user = $this->getDataGenerator()->create_user();
        $this->setuser($user);

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('cannoteditentryorblog', 'blog'));
        add_entry::execute('Subject', 'Summary', FORMAT_HTML);
    }

    /**
     * Test add_entry invalid parameter.
     */
    public function test_add_entry_invalid_parameter(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('errorinvalidparam', 'webservice', 'invalid'));
        $options = [['name' => 'invalid', 'value' => 'invalidvalue']];
        add_entry::execute('Subject', 'Summary', FORMAT_HTML, $options);
    }

    /**
     * Test add_entry diabled associations.
     */
    public function test_add_entry_disabled_assoc(): void {
        global $CFG;
        $CFG->useblogassociations = 0;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('errorinvalidparam', 'webservice', 'modassoc'));
        $options = [['name' => 'modassoc', 'value' => 1]];
        add_entry::execute('Subject', 'Summary', FORMAT_HTML, $options);
    }

    /**
     * Test add_entry invalid publish state.
     */
    public function test_add_entry_invalid_publishstate(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('errorinvalidparam', 'webservice', 'publishstate'));
        $options = [['name' => 'publishstate', 'value' => 'something']];
        add_entry::execute('Subject', 'Summary', FORMAT_HTML, $options);
    }

    /**
     * Test add_entry invalid association.
     */
    public function test_add_entry_invalid_association(): void {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $anothercourse = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);

        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('errorinvalidparam', 'webservice', 'modassoc'));
        $options = [
            ['name' => 'courseassoc', 'value' => $anothercourse->id],
            ['name' => 'modassoc', 'value' => $page->cmid],
        ];
        add_entry::execute('Subject', 'Summary', FORMAT_HTML, $options);
    }

    /**
     * Test delete_entry
     */
    public function test_delete_entry(): void {
        $this->resetAfterTest(true);

        // I can delete my own entry.
        $this->setUser($this->userid);

        $result = delete_entry::execute($this->postid);
        $result = external_api::clean_returnvalue(delete_entry::execute_returns(), $result);
        $this->assertTrue($result['status']);
    }

    /**
     * Test delete_entry from another user (no permissions)
     */
    public function test_delete_entry_no_permissions(): void {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $this->courseid);

        // I can delete my own entry.
        $this->setUser($user);

        $this->expectException('\moodle_exception');
        delete_entry::execute($this->postid);
    }

    /**
     * Test delete_entry when blogs not enabled.
     */
    public function test_delete_entry_blog_not_enabled(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->enableblogs = 0;
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('blogdisable', 'blog'));
        delete_entry::execute($this->postid);
    }

    /**
     * Test delete_entry invalid entry id.
     */
    public function test_delete_entry_invalid_entry(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        delete_entry::execute($this->postid + 1000);
    }

    /**
     * Test prepare_entry_for_edition.
     */
    public function test_prepare_entry_for_edition(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $result = prepare_entry_for_edition::execute($this->postid);
        $result = external_api::clean_returnvalue(prepare_entry_for_edition::execute_returns(), $result);
        $this->assertCount(2, $result['areas']);
        $this->assertIsInt($result['inlineattachmentsid']);
        $this->assertIsInt($result['attachmentsid']);
        foreach ($result['areas'] as $area) {
            if ($area['area'] == 'summary') {
                $this->assertCount(4, $area['options']);
            } else {
                $this->assertEquals('attachment', $area['area']);
                $this->assertCount(3, $area['options']);
            }
        }
    }

    /**
     * Test prepare_entry_for_edition when blogs not enabled.
     */
    public function test_prepare_entry_for_edition_blog_not_enabled(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->enableblogs = 0;
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('blogdisable', 'blog'));
        prepare_entry_for_edition::execute($this->postid);
    }

    /**
     * Test prepare_entry_for_edition invalid entry id.
     */
    public function test_prepare_entry_for_edition_invalid_entry(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        prepare_entry_for_edition::execute($this->postid + 1000);
    }

    /**
     * Test prepare_entry_for_edition without permissions.
     */
    public function test_prepare_entry_for_edition_no_permission(): void {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $this->courseid);
        $this->setuser($user);

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('cannoteditentryorblog', 'blog'));
        prepare_entry_for_edition::execute($this->postid);
    }

    /**
     * Test update_entry
     */
    public function test_update_entry(): void {
        global $USER;

        $this->resetAfterTest(true);

        // Add post with attachments.
        $this->setAdminUser();

        // Draft files.
        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();
        $usercontext = \context_user::instance($USER->id);
        $inlinefilename = 'inlineimage.png';
        $filerecordinline = [
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftidinlineattach,
            'filepath'  => '/',
            'filename'  => $inlinefilename,
        ];
        $fs = get_file_storage();

        // Create a file in a draft area for regular attachments.
        $filerecordattach = $filerecordinline;
        $attachfilename = 'attachment.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        $options = [
            [
                'name' => 'inlineattachmentsid',
                'value' => $draftidinlineattach,
            ],
            [
                'name' => 'attachmentsid',
                'value' => $draftidattach,
            ],
            [
                'name' => 'tags',
                'value' => 'tag1, tag2',
            ],
            [
                'name' => 'courseassoc',
                'value' => $this->courseid,
            ],
        ];

        $subject = 'First post';
        $summary = 'First post summary';
        $result = add_entry::execute($subject, $summary, FORMAT_HTML, $options);
        $result = external_api::clean_returnvalue(add_entry::execute_returns(), $result);
        $entryid = $result['entryid'];

        // Retrieve file areas.
        $result = prepare_entry_for_edition::execute($entryid);
        $result = external_api::clean_returnvalue(prepare_entry_for_edition::execute_returns(), $result);

        // Update files.
        $inlinefilename = 'inlineimage2.png';
        $filerecordinline = [
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $result['inlineattachmentsid'],
            'filepath'  => '/',
            'filename'  => $inlinefilename,
        ];
        $fs = get_file_storage();

        // Create a file in a draft area for regular attachments.
        $filerecordattach = $filerecordinline;
        $newattachfilename = 'attachment2.txt';
        $filerecordattach['filename'] = $newattachfilename;
        $filerecordattach['itemid'] = $result['attachmentsid'];
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        // Remove one previous attachment file.
        $filetoremove = (object) ['filename' => 'attachment.txt', 'filepath' => '/'];
        repository_delete_selected_files($usercontext, 'user', 'draft', $result['attachmentsid'], [$filetoremove]);

        // Update.
        $options = [
            ['name' => 'inlineattachmentsid', 'value' => $result['inlineattachmentsid']],
            ['name' => 'attachmentsid', 'value' => $result['attachmentsid']],
            ['name' => 'tags', 'value' => 'tag3'],
            ['name' => 'courseassoc', 'value' => $this->courseid],
            ['name' => 'modassoc', 'value' => $this->cmid],
        ];

        $subject = 'First post updated';
        $summary = 'First post summary updated';
        $result = update_entry::execute($entryid, $subject, $summary, FORMAT_HTML, $options);
        $result = external_api::clean_returnvalue(update_entry::execute_returns(), $result);

        // Retrieve files via WS.
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);

        foreach ($result['entries'] as $entry) {
            if ($entry['id'] == $entryid) {
                $this->assertEquals($subject, $entry['subject']);
                $this->assertEquals($summary, $entry['summary']);
                $this->assertEquals($this->courseid, $entry['courseid']);
                $this->assertEquals($this->cmid, $entry['coursemoduleid']);
                $this->assertCount(1, $entry['attachmentfiles']);
                $this->assertCount(2, $entry['summaryfiles']);
                $this->assertCount(1, $entry['tags']);
                $this->assertEquals($newattachfilename, $entry['attachmentfiles'][0]['filename']);
            }
        }

        // Update removing associations.
        $options = [
            ['name' => 'courseassoc', 'value' => 0],
            ['name' => 'modassoc', 'value' => 0],
        ];

        $result = update_entry::execute($entryid, $subject, $summary, FORMAT_HTML, $options);
        $result = external_api::clean_returnvalue(update_entry::execute_returns(), $result);

        // Retrieve files via WS.
        $result = \core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(\core_blog\external::get_entries_returns(), $result);

        foreach ($result['entries'] as $entry) {
            if ($entry['id'] == $entryid) {
                $this->assertEmpty($entry['courseid']);
                $this->assertEmpty($entry['coursemoduleid']);
            }
        }
    }

    /**
     * Test update_entry when blogs not enabled.
     */
    public function test_update_entry_blog_not_enabled(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->enableblogs = 0;
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('blogdisable', 'blog'));
        update_entry::execute($this->postid, 'Subject', 'Summary', FORMAT_HTML);
    }

    /**
     * Test update_entry without permissions.
     */
    public function test_update_entry_no_permission(): void {
        global $CFG;

        $this->resetAfterTest(true);

        // Remove capability.
        $sitecontext = \context_system::instance();
        $this->unassignUserCapability('moodle/blog:create', $sitecontext->id, $CFG->defaultuserroleid);
        $user = $this->getDataGenerator()->create_user();
        $this->setuser($user);

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('cannoteditentryorblog', 'blog'));
        update_entry::execute($this->postid, 'Subject', 'Summary', FORMAT_HTML);
    }

    /**
     * Test update_entry invalid parameter.
     */
    public function test_update_entry_invalid_parameter(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('errorinvalidparam', 'webservice', 'invalid'));
        $options = [['name' => 'invalid', 'value' => 'invalidvalue']];
        update_entry::execute($this->postid, 'Subject', 'Summary', FORMAT_HTML, $options);
    }

    /**
     * Test update_entry diabled associations.
     */
    public function test_update_entry_disabled_assoc(): void {
        global $CFG;
        $CFG->useblogassociations = 0;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('errorinvalidparam', 'webservice', 'modassoc'));
        $options = [['name' => 'modassoc', 'value' => 1]];
        update_entry::execute($this->postid, 'Subject', 'Summary', FORMAT_HTML, $options);
    }

    /**
     * Test update_entry invalid publish state.
     */
    public function test_update_entry_invalid_publishstate(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('errorinvalidparam', 'webservice', 'publishstate'));
        $options = [['name' => 'publishstate', 'value' => 'something']];
        update_entry::execute($this->postid, 'Subject', 'Summary', FORMAT_HTML, $options);
    }

    /**
     * Test update_entry invalid association.
     */
    public function test_update_entry_invalid_association(): void {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $anothercourse = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);

        $this->setAdminUser();

        $this->expectException('\moodle_exception');
        $this->expectExceptionMessage(get_string('errorinvalidparam', 'webservice', 'modassoc'));
        $options = [
            ['name' => 'courseassoc', 'value' => $anothercourse->id],
            ['name' => 'modassoc', 'value' => $page->cmid],
        ];
        update_entry::execute($this->postid, 'Subject', 'Summary', FORMAT_HTML, $options);
    }

    /**
     * Test update_entry from another user (no permissions)
     */
    public function test_update_entry_no_permissions(): void {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $this->courseid);

        // I can delete my own entry.
        $this->setUser($user);

        $this->expectException('\moodle_exception');
        update_entry::execute($this->postid, 'Subject', 'Summary', FORMAT_HTML);
    }
}

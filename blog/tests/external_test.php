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
class core_blog_external_testcase extends advanced_testcase {

    private $courseid;
    private $cmid;
    private $userid;
    private $groupid;
    private $tagid;
    private $postid;

    protected function setUp() {
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
        $post = new stdClass();
        $post->userid = $user->id;
        $post->courseid = $course->id;
        $post->groupid = $group->id;
        $post->content = 'test post content text';
        $post->module = 'blog';
        $post->id = $DB->insert_record('post', $post);

        core_tag_tag::set_item_tags('core', 'post', $post->id, context_user::instance($user->id), array('tag1'));
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
    public function test_get_public_entries_global_level_by_non_logged_users() {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;
        $CFG->forcelogin = 0;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'public', array('id' => $this->postid));

        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Get global public entries even for not authenticated users in closed site.
     */
    public function test_get_public_entries_global_level_by_non_logged_users_closed_site() {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;
        $CFG->forcelogin = 1;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'public', array('id' => $this->postid));

        $this->expectException('moodle_exception');
        core_blog\external::get_entries();
    }

    /**
     * Get global public entries for guest users.
     * We get the entry since is public.
     */
    public function test_get_public_entries_global_level_by_guest_users() {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'public', array('id' => $this->postid));

        $this->setGuestUser();
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Get global not public entries even for not authenticated users withouth being authenticated.
     * We don't get any because they are not public (restricted to site users).
     */
    public function test_get_not_public_entries_global_level_by_non_logged_users() {
        global $CFG;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;

        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get global not public entries users being guest.
     * We don't get any because they are not public (restricted to real site users).
     */
    public function test_get_not_public_entries_global_level_by_guest_user() {
        global $CFG;

        $CFG->bloglevel = BLOG_GLOBAL_LEVEL;

        $this->setGuestUser();
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get site not public entries for not authenticated users.
     * We don't get any because they are not public (restricted to real site users).
     */
    public function test_get_not_public_entries_site_level_by_non_logged_users() {
        $this->expectException('require_login_exception'); // In this case we get a security exception.
        $result = core_blog\external::get_entries();
    }

    /**
     * Get site not public entries for guest users.
     * We don't get any because they are not public (restricted to real site users).
     */
    public function test_get_not_public_entries_site_level_by_guest_users() {

        $this->setGuestUser();
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get site entries at site level by system users.
     */
    public function test_get_site_entries_site_level_by_normal_users() {

        $this->setUser($this->userid);
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Get draft site entries by authors.
     */
    public function test_get_draft_entries_site_level_by_author_users() {
        global $DB;

        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));

        $this->setUser($this->userid);
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Get draft site entries by not authors.
     */
    public function test_get_draft_entries_site_level_by_not_author_users() {
        global $DB;

        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get draft site entries by admin.
     */
    public function test_get_draft_entries_site_level_by_admin_users() {
        global $DB;

        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));
        $user = $this->getDataGenerator()->create_user();

        $this->setAdminUser();
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Get draft user entries by authors.
     */
    public function test_get_draft_entries_user_level_by_author_users() {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_USER_LEVEL;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));

        $this->setUser($this->userid);
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Get draft user entries by not authors.
     */
    public function test_get_draft_entries_user_level_by_not_author_users() {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_USER_LEVEL;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Get draft user entries by admin.
     */
    public function test_get_draft_entries_user_level_by_admin_users() {
        global $CFG, $DB;

        $CFG->bloglevel = BLOG_USER_LEVEL;
        // Set current entry global.
        $DB->set_field('post', 'publishstate', 'draft', array('id' => $this->postid));
        $user = $this->getDataGenerator()->create_user();

        $this->setAdminUser();
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Test get all entries including testing pagination.
     */
    public function test_get_all_entries_including_pagination() {
        global $DB, $USER;

        $DB->set_field('post', 'publishstate', 'site', array('id' => $this->postid));

        // Create another entry.
        $this->setAdminUser();
        $newpost = new stdClass();
        $newpost->userid = $USER->id;
        $newpost->content = 'test post content text';
        $newpost->module = 'blog';
        $newpost->publishstate = 'site';
        $newpost->created = time() + HOURSECS;
        $newpost->lastmodified = time() + HOURSECS;
        $newpost->id = $DB->insert_record('post', $newpost);

        $this->setUser($this->userid);
        $result = core_blog\external::get_entries();
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalentries']);

        $result = core_blog\external::get_entries(array(), 0, 1);
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals(2, $result['totalentries']);
        $this->assertEquals($newpost->id, $result['entries'][0]['id']);

        $result = core_blog\external::get_entries(array(), 1, 1);
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals(2, $result['totalentries']);
        $this->assertEquals($this->postid, $result['entries'][0]['id']);
    }

    /**
     * Test get entries filtering by course.
     */
    public function test_get_entries_filtering_by_course() {
        global $CFG, $DB;

        $DB->set_field('post', 'publishstate', 'site', array('id' => $this->postid));

        $this->setAdminUser();
        $coursecontext = context_course::instance($this->courseid);
        $anothercourse = $this->getDataGenerator()->create_course();

        // Add blog associations with a course.
        $blog = new blog_entry($this->postid);
        $blog->add_association($coursecontext->id);

        // There is one entry associated with a course.
        $result = core_blog\external::get_entries(array(array('name' => 'courseid', 'value' => $this->courseid)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);

        // There is no entry associated with a wrong course.
        $result = core_blog\external::get_entries(array(array('name' => 'courseid', 'value' => $anothercourse->id)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);

        // There is no entry associated with a module.
        $result = core_blog\external::get_entries(array(array('name' => 'cmid', 'value' => $this->cmid)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by module.
     */
    public function test_get_entries_filtering_by_module() {
        global $CFG, $DB;

        $DB->set_field('post', 'publishstate', 'site', array('id' => $this->postid));

        $this->setAdminUser();
        $coursecontext = context_course::instance($this->courseid);
        $contextmodule = context_module::instance($this->cmid);
        $anothermodule = $this->getDataGenerator()->create_module('page', array('course' => $this->courseid));

        // Add blog associations with a module.
        $blog = new blog_entry($this->postid);
        $blog->add_association($contextmodule->id);

        // There is no entry associated with a course.
        $result = core_blog\external::get_entries(array(array('name' => 'courseid', 'value' => $this->courseid)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);

        // There is one entry associated with a module.
        $result = core_blog\external::get_entries(array(array('name' => 'cmid', 'value' => $this->cmid)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);

        // There is no entry associated with a wrong module.
        $result = core_blog\external::get_entries(array(array('name' => 'cmid', 'value' => $anothermodule->cmid)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by author.
     */
    public function test_get_entries_filtering_by_author() {
        $this->setAdminUser();
        // Filter by author.
        $result = core_blog\external::get_entries(array(array('name' => 'userid', 'value' => $this->userid)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // No author.
        $anotheruser = $this->getDataGenerator()->create_user();
        $result = core_blog\external::get_entries(array(array('name' => 'userid', 'value' => $anotheruser->id)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by entry.
     */
    public function test_get_entries_filtering_by_entry() {
        $this->setAdminUser();
        // Filter by correct entry.
        $result = core_blog\external::get_entries(array(array('name' => 'entryid', 'value' => $this->postid)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Non-existent entry.
        $this->expectException('moodle_exception');
        $result = core_blog\external::get_entries(array(array('name' => 'entryid', 'value' => -1)));
    }

    /**
     * Test get entries filtering by search.
     */
    public function test_get_entries_filtering_by_search() {
        $this->setAdminUser();
        // Filter by correct search.
        $result = core_blog\external::get_entries(array(array('name' => 'search', 'value' => 'test')));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Non-existent search.
        $result = core_blog\external::get_entries(array(array('name' => 'search', 'value' => 'abc')));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by tag.
     */
    public function test_get_entries_filtering_by_tag() {
        $this->setAdminUser();
        // Filter by correct tag.
        $result = core_blog\external::get_entries(array(array('name' => 'tag', 'value' => 'tag1')));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Create tag.
        $tag = $this->getDataGenerator()->create_tag(array('userid' => $this->userid, 'name' => 'tag2',
            'isstandard' => 1));

        $result = core_blog\external::get_entries(array(array('name' => 'tag', 'value' => 'tag2')));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by tag id.
     */
    public function test_get_entries_filtering_by_tagid() {
        $this->setAdminUser();
        // Filter by correct tag.
        $result = core_blog\external::get_entries(array(array('name' => 'tagid', 'value' => $this->tagid)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Non-existent tag.

        // Create tag.
        $tag = $this->getDataGenerator()->create_tag(array('userid' => $this->userid, 'name' => 'tag2',
            'isstandard' => 1));

        $result = core_blog\external::get_entries(array(array('name' => 'tagid', 'value' => $tag->id)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by group.
     */
    public function test_get_entries_filtering_by_group() {
        $this->setAdminUser();
        // Add blog associations with a course.
        $coursecontext = context_course::instance($this->courseid);
        $blog = new blog_entry($this->postid);
        $blog->add_association($coursecontext->id);

        // Filter by correct group.
        $result = core_blog\external::get_entries(array(array('name' => 'groupid', 'value' => $this->groupid)));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);
        // Non-existent group.
        $anotheruser = $this->getDataGenerator()->create_user();
        $this->expectException('moodle_exception');
        core_blog\external::get_entries(array(array('name' => 'groupid', 'value' => -1)));
    }

    /**
     * Test get entries multiple filter.
     */
    public function test_get_entries_multiple_filter() {
        $this->setAdminUser();
        // Add blog associations with a course.
        $coursecontext = context_course::instance($this->courseid);
        $blog = new blog_entry($this->postid);
        $blog->add_association($coursecontext->id);

        $result = core_blog\external::get_entries(array(
            array('name' => 'tagid', 'value' => $this->tagid),
            array('name' => 'userid', 'value' => $this->userid),
        ));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(1, $result['entries']);

        // Non-existent multiple filter.
        $result = core_blog\external::get_entries(array(
            array('name' => 'search', 'value' => 'www'),
            array('name' => 'userid', 'value' => $this->userid),
        ));
        $result = external_api::clean_returnvalue(core_blog\external::get_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
    }

    /**
     * Test get entries filtering by invalid_filter.
     */
    public function test_get_entries_filtering_by_invalid_filter() {
        $this->setAdminUser();
        // Filter by incorrect filter.
        $this->expectException('moodle_exception');
        $result = core_blog\external::get_entries(array(array('name' => 'zzZZzz', 'value' => 'wwWWww')));
    }

    /**
     * Test get entries when blog is disabled.
     */
    public function test_get_entries_blog_disabled() {
        global $CFG;

        $this->setAdminUser();
        $CFG->enableblogs = 0;
        // Filter by incorrect filter.
        $this->expectException('moodle_exception');
        $result = core_blog\external::get_entries(array(array('name' => 'zzZZzz', 'value' => 'wwWWww')));
    }

    /**
     * Test view_blog_entries without filter.
     */
    public function test_view_blog_entries_without_filtering() {
        // Test user with full capabilities.
        $this->setUser($this->userid);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = core_blog\external::view_entries();
        $result = external_api::clean_returnvalue(core_blog\external::view_entries_returns(), $result);

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
    public function test_view_blog_entries_with_filtering() {
        // Test user with full capabilities.
        $this->setUser($this->userid);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = core_blog\external::view_entries(array(
            array('name' => 'tagid', 'value' => $this->tagid),
            array('name' => 'userid', 'value' => $this->userid),
        ));
        $result = external_api::clean_returnvalue(core_blog\external::view_entries_returns(), $result);

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
}


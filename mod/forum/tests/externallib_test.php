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

namespace mod_forum;

use externallib_advanced_testcase;
use mod_forum_external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * The module forums external functions unit tests
 *
 * @package    mod_forum
 * @category   external
 * @copyright  2012 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class externallib_test extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp(): void {
        global $CFG;

        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();

        require_once($CFG->dirroot . '/mod/forum/externallib.php');
    }

    public function tearDown(): void {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
    }

    /**
     * Get the expected attachment.
     *
     * @param stored_file $file
     * @param array $values
     * @param moodle_url|null $url
     * @return array
     */
    protected function get_expected_attachment(\stored_file $file, array $values  = [], ?\moodle_url $url = null): array {
        if (!$url) {
            $url = \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );
            $url->param('forcedownload', 1);
        }

        return array_merge(
            [
                'contextid' => $file->get_contextid(),
                'component' => $file->get_component(),
                'filearea' => $file->get_filearea(),
                'itemid' => $file->get_itemid(),
                'filepath' => $file->get_filepath(),
                'filename' => $file->get_filename(),
                'isdir' => $file->is_directory(),
                'isimage' => $file->is_valid_image(),
                'timemodified' => $file->get_timemodified(),
                'timecreated' => $file->get_timecreated(),
                'filesize' => $file->get_filesize(),
                'author' => $file->get_author(),
                'license' => $file->get_license(),
                'filenameshort' => $file->get_filename(),
                'filesizeformatted' => display_size((int) $file->get_filesize()),
                'icon' => $file->is_directory() ? file_folder_icon(128) : file_file_icon($file, 128),
                'timecreatedformatted' => userdate($file->get_timecreated()),
                'timemodifiedformatted' => userdate($file->get_timemodified()),
                'url' => $url->out(),
            ], $values);
    }

    /**
     * Test get forums
     */
    public function test_mod_forum_get_forums_by_courses() {
        global $USER, $CFG, $DB;

        $this->resetAfterTest(true);

        // Create a user.
        $user = self::getDataGenerator()->create_user(array('trackforums' => 1));

        // Set to the user.
        self::setUser($user);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        // First forum.
        $record = new \stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_FORCED;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);

        // Second forum.
        $record = new \stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course2->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        $forum2 = self::getDataGenerator()->create_module('forum', $record);
        $forum2->introfiles = [];

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        // Expect one discussion.
        $forum1->numdiscussions = 1;
        $forum1->cancreatediscussions = true;
        $forum1->istracked = true;
        $forum1->unreadpostscount = 0;
        $forum1->introfiles = [];

        $record = new \stdClass();
        $record->course = $course2->id;
        $record->userid = $user->id;
        $record->forum = $forum2->id;
        $discussion2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $discussion3 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        // Expect two discussions.
        $forum2->numdiscussions = 2;
        // Default limited role, no create discussion capability enabled.
        $forum2->cancreatediscussions = false;
        $forum2->istracked = false;

        // Check the forum was correctly created.
        $this->assertEquals(2, $DB->count_records_select('forum', 'id = :forum1 OR id = :forum2',
                array('forum1' => $forum1->id, 'forum2' => $forum2->id)));

        // Enrol the user in two courses.
        // DataGenerator->enrol_user automatically sets a role for the user with the permission mod/form:viewdiscussion.
        $this->getDataGenerator()->enrol_user($user->id, $course1->id, null, 'manual');
        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $user->id);

        // Assign capabilities to view forums for forum 2.
        $cm2 = get_coursemodule_from_id('forum', $forum2->cmid, 0, false, MUST_EXIST);
        $context2 = \context_module::instance($cm2->id);
        $newrole = create_role('Role 2', 'role2', 'Role 2 description');
        $roleid2 = $this->assignUserCapability('mod/forum:viewdiscussion', $context2->id, $newrole);

        // Create what we expect to be returned when querying the two courses.
        unset($forum1->displaywordcount);
        unset($forum2->displaywordcount);

        $expectedforums = array();
        $expectedforums[$forum1->id] = (array) $forum1;
        $expectedforums[$forum2->id] = (array) $forum2;

        // Call the external function passing course ids.
        $forums = mod_forum_external::get_forums_by_courses(array($course1->id, $course2->id));
        $forums = \external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertCount(2, $forums);
        foreach ($forums as $forum) {
            $this->assertEquals($expectedforums[$forum['id']], $forum);
        }

        // Call the external function without passing course id.
        $forums = mod_forum_external::get_forums_by_courses();
        $forums = \external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertCount(2, $forums);
        foreach ($forums as $forum) {
            $this->assertEquals($expectedforums[$forum['id']], $forum);
        }

        // Unenrol user from second course and alter expected forums.
        $enrol->unenrol_user($instance2, $user->id);
        unset($expectedforums[$forum2->id]);

        // Call the external function without passing course id.
        $forums = mod_forum_external::get_forums_by_courses();
        $forums = \external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertCount(1, $forums);
        $this->assertEquals($expectedforums[$forum1->id], $forums[0]);
        $this->assertTrue($forums[0]['cancreatediscussions']);

        // Change the type of the forum, the user shouldn't be able to add discussions.
        $DB->set_field('forum', 'type', 'news', array('id' => $forum1->id));
        $forums = mod_forum_external::get_forums_by_courses();
        $forums = \external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertFalse($forums[0]['cancreatediscussions']);

        // Call for the second course we unenrolled the user from.
        $forums = mod_forum_external::get_forums_by_courses(array($course2->id));
        $forums = \external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertCount(0, $forums);
    }

    /**
     * Test the toggle favourite state
     */
    public function test_mod_forum_toggle_favourite_state() {
        global $USER, $CFG, $DB;

        $this->resetAfterTest(true);

        // Create a user.
        $user = self::getDataGenerator()->create_user(array('trackforums' => 1));

        // Set to the user.
        self::setUser($user);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);

        $record = new \stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);
        $forum1->introfiles = [];

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $response = mod_forum_external::toggle_favourite_state($discussion1->id, 1);
        $response = \external_api::clean_returnvalue(mod_forum_external::toggle_favourite_state_returns(), $response);
        $this->assertTrue($response['userstate']['favourited']);

        $response = mod_forum_external::toggle_favourite_state($discussion1->id, 0);
        $response = \external_api::clean_returnvalue(mod_forum_external::toggle_favourite_state_returns(), $response);
        $this->assertFalse($response['userstate']['favourited']);

        $this->setUser(0);
        try {
            $response = mod_forum_external::toggle_favourite_state($discussion1->id, 0);
        } catch (\moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }
    }

    /**
     * Test the toggle pin state
     */
    public function test_mod_forum_set_pin_state() {
        $this->resetAfterTest(true);

        // Create a user.
        $user = self::getDataGenerator()->create_user(array('trackforums' => 1));

        // Set to the user.
        self::setUser($user);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);

        $record = new \stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);
        $forum1->introfiles = [];

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        try {
            $response = mod_forum_external::set_pin_state($discussion1->id, 1);
        } catch (\Exception $e) {
            $this->assertEquals('cannotpindiscussions', $e->errorcode);
        }

        self::setAdminUser();
        $response = mod_forum_external::set_pin_state($discussion1->id, 1);
        $response = \external_api::clean_returnvalue(mod_forum_external::set_pin_state_returns(), $response);
        $this->assertTrue($response['pinned']);

        $response = mod_forum_external::set_pin_state($discussion1->id, 0);
        $response = \external_api::clean_returnvalue(mod_forum_external::set_pin_state_returns(), $response);
        $this->assertFalse($response['pinned']);
    }

    /**
     * Test get forum posts
     *
     * Tests is similar to the get_forum_discussion_posts only utilizing the new return structure and entities
     */
    public function test_mod_forum_get_discussion_posts() {
        global $CFG;

        $this->resetAfterTest(true);

        // Set the CFG variable to allow track forums.
        $CFG->forum_trackreadposts = true;

        $urlfactory = \mod_forum\local\container::get_url_factory();
        $legacyfactory = \mod_forum\local\container::get_legacy_data_mapper_factory();
        $entityfactory = \mod_forum\local\container::get_entity_factory();

        // Create course to add the module.
        $course1 = self::getDataGenerator()->create_course();

        // Create a user who can track forums.
        $record = new \stdClass();
        $record->trackforums = true;
        $user1 = self::getDataGenerator()->create_user($record);
        // Create a bunch of other users to post.
        $user2 = self::getDataGenerator()->create_user();
        $user2entity = $entityfactory->get_author_from_stdClass($user2);
        $exporteduser2 = [
            'id' => (int) $user2->id,
            'fullname' => fullname($user2),
            'isdeleted' => false,
            'groups' => [],
            'urls' => [
                'profile' => $urlfactory->get_author_profile_url($user2entity, $course1->id)->out(false),
                'profileimage' => $urlfactory->get_author_profile_image_url($user2entity),
            ]
        ];
        $user2->fullname = $exporteduser2['fullname'];

        $user3 = self::getDataGenerator()->create_user(['fullname' => "Mr Pants 1"]);
        $user3entity = $entityfactory->get_author_from_stdClass($user3);
        $exporteduser3 = [
            'id' => (int) $user3->id,
            'fullname' => fullname($user3),
            'groups' => [],
            'isdeleted' => false,
            'urls' => [
                'profile' => $urlfactory->get_author_profile_url($user3entity, $course1->id)->out(false),
                'profileimage' => $urlfactory->get_author_profile_image_url($user3entity),
            ]
        ];
        $user3->fullname = $exporteduser3['fullname'];
        $forumgenerator = self::getDataGenerator()->get_plugin_generator('mod_forum');

        // Set the first created user to the test user.
        self::setUser($user1);

        // Forum with tracking off.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        // Display word count. Otherwise, word and char counts will be set to null by the forum post exporter.
        $record->displaywordcount = true;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);
        $forum1context = \context_module::instance($forum1->cmid);

        // Forum with tracking enabled.
        $record = new \stdClass();
        $record->course = $course1->id;
        $forum2 = self::getDataGenerator()->create_module('forum', $record);
        $forum2cm = get_coursemodule_from_id('forum', $forum2->cmid);
        $forum2context = \context_module::instance($forum2->cmid);

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user1->id;
        $record->forum = $forum1->id;
        $discussion1 = $forumgenerator->create_discussion($record);

        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user2->id;
        $record->forum = $forum1->id;
        $discussion2 = $forumgenerator->create_discussion($record);

        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user2->id;
        $record->forum = $forum2->id;
        $discussion3 = $forumgenerator->create_discussion($record);

        // Add 2 replies to the discussion 1 from different users.
        $record = new \stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user2->id;
        $discussion1reply1 = $forumgenerator->create_post($record);
        $filename = 'shouldbeanimage.jpg';
        // Add a fake inline image to the post.
        $filerecordinline = array(
            'contextid' => $forum1context->id,
            'component' => 'mod_forum',
            'filearea'  => 'post',
            'itemid'    => $discussion1reply1->id,
            'filepath'  => '/',
            'filename'  => $filename,
        );
        $fs = get_file_storage();
        $timepost = time();
        $file = $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        $record->parent = $discussion1reply1->id;
        $record->userid = $user3->id;
        $discussion1reply2 = $forumgenerator->create_post($record);

        // Enrol the user in the  course.
        $enrol = enrol_get_plugin('manual');
        // Following line enrol and assign default role id to the user.
        // So the user automatically gets mod/forum:viewdiscussion on all forums of the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // Delete one user, to test that we still receive posts by this user.
        delete_user($user3);
        $exporteduser3 = [
            'id' => (int) $user3->id,
            'fullname' => get_string('deleteduser', 'mod_forum'),
            'groups' => [],
            'isdeleted' => true,
            'urls' => [
                'profile' => $urlfactory->get_author_profile_url($user3entity, $course1->id)->out(false),
                'profileimage' => $urlfactory->get_author_profile_image_url($user3entity),
            ]
        ];

        // Create what we expect to be returned when querying the discussion.
        $expectedposts = array(
            'posts' => array(),
            'courseid' => $course1->id,
            'forumid' => $forum1->id,
            'ratinginfo' => array(
                'contextid' => $forum1context->id,
                'component' => 'mod_forum',
                'ratingarea' => 'post',
                'canviewall' => null,
                'canviewany' => null,
                'scales' => array(),
                'ratings' => array(),
            ),
            'warnings' => array(),
        );

        // User pictures are initially empty, we should get the links once the external function is called.
        $isolatedurl = $urlfactory->get_discussion_view_url_from_discussion_id($discussion1reply2->discussion);
        $isolatedurl->params(['parent' => $discussion1reply2->id]);
        $message = file_rewrite_pluginfile_urls($discussion1reply2->message, 'pluginfile.php',
            $forum1context->id, 'mod_forum', 'post', $discussion1reply2->id);
        $expectedposts['posts'][] = array(
            'id' => $discussion1reply2->id,
            'discussionid' => $discussion1reply2->discussion,
            'parentid' => $discussion1reply2->parent,
            'hasparent' => true,
            'timecreated' => $discussion1reply2->created,
            'timemodified' => $discussion1reply2->modified,
            'subject' => $discussion1reply2->subject,
            'replysubject' => get_string('re', 'mod_forum') . " {$discussion1reply2->subject}",
            'message' => $message,
            'messageformat' => 1,   // This value is usually changed by external_format_text() function.
            'unread' => null,
            'isdeleted' => false,
            'isprivatereply' => false,
            'haswordcount' => true,
            'wordcount' => count_words($message),
            'charcount' => count_letters($message),
            'author'=> $exporteduser3,
            'attachments' => [],
            'messageinlinefiles' => [],
            'tags' => [],
            'html' => [
                'rating' => null,
                'taglist' => null,
                'authorsubheading' => $forumgenerator->get_author_subheading_html((object)$exporteduser3, $discussion1reply2->created)
            ],
            'capabilities' => [
                'view' => 1,
                'edit' => 0,
                'delete' => 0,
                'split' => 0,
                'reply' => 1,
                'export' => 0,
                'controlreadstatus' => 0,
                'canreplyprivately' => 0,
                'selfenrol' => 0
            ],
            'urls' => [
                'view' => $urlfactory->get_view_post_url_from_post_id($discussion1reply2->discussion, $discussion1reply2->id),
                'viewisolated' => $isolatedurl->out(false),
                'viewparent' => $urlfactory->get_view_post_url_from_post_id($discussion1reply2->discussion, $discussion1reply2->parent),
                'edit' => null,
                'delete' =>null,
                'split' => null,
                'reply' => (new \moodle_url('/mod/forum/post.php#mformforum', [
                    'reply' => $discussion1reply2->id
                ]))->out(false),
                'export' => null,
                'markasread' => null,
                'markasunread' => null,
                'discuss' => $urlfactory->get_discussion_view_url_from_discussion_id($discussion1reply2->discussion),
            ],
        );


        $isolatedurl = $urlfactory->get_discussion_view_url_from_discussion_id($discussion1reply1->discussion);
        $isolatedurl->params(['parent' => $discussion1reply1->id]);
        $message = file_rewrite_pluginfile_urls($discussion1reply1->message, 'pluginfile.php',
            $forum1context->id, 'mod_forum', 'post', $discussion1reply1->id);
        $expectedposts['posts'][] = array(
            'id' => $discussion1reply1->id,
            'discussionid' => $discussion1reply1->discussion,
            'parentid' => $discussion1reply1->parent,
            'hasparent' => true,
            'timecreated' => $discussion1reply1->created,
            'timemodified' => $discussion1reply1->modified,
            'subject' => $discussion1reply1->subject,
            'replysubject' => get_string('re', 'mod_forum') . " {$discussion1reply1->subject}",
            'message' => $message,
            'messageformat' => 1,   // This value is usually changed by external_format_text() function.
            'unread' => null,
            'isdeleted' => false,
            'isprivatereply' => false,
            'haswordcount' => true,
            'wordcount' => count_words($message),
            'charcount' => count_letters($message),
            'author'=> $exporteduser2,
            'attachments' => [],
            'messageinlinefiles' => [
                0 => $this->get_expected_attachment($file)
            ],
            'tags' => [],
            'html' => [
                'rating' => null,
                'taglist' => null,
                'authorsubheading' => $forumgenerator->get_author_subheading_html((object)$exporteduser2, $discussion1reply1->created)
            ],
            'capabilities' => [
                'view' => 1,
                'edit' => 0,
                'delete' => 0,
                'split' => 0,
                'reply' => 1,
                'export' => 0,
                'controlreadstatus' => 0,
                'canreplyprivately' => 0,
                'selfenrol' => 0
            ],
            'urls' => [
                'view' => $urlfactory->get_view_post_url_from_post_id($discussion1reply1->discussion, $discussion1reply1->id),
                'viewisolated' => $isolatedurl->out(false),
                'viewparent' => $urlfactory->get_view_post_url_from_post_id($discussion1reply1->discussion, $discussion1reply1->parent),
                'edit' => null,
                'delete' =>null,
                'split' => null,
                'reply' => (new \moodle_url('/mod/forum/post.php#mformforum', [
                    'reply' => $discussion1reply1->id
                ]))->out(false),
                'export' => null,
                'markasread' => null,
                'markasunread' => null,
                'discuss' => $urlfactory->get_discussion_view_url_from_discussion_id($discussion1reply1->discussion),
            ],
        );

        // Test a discussion with two additional posts (total 3 posts).
        $posts = mod_forum_external::get_discussion_posts($discussion1->id, 'modified', 'DESC', true);
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertEquals(3, count($posts['posts']));

        // Unset the initial discussion post.
        array_pop($posts['posts']);
        $this->assertEquals($expectedposts, $posts);

        // Check we receive the unread count correctly on tracked forum.
        forum_tp_count_forum_unread_posts($forum2cm, $course1, true);    // Reset static cache.
        $result = mod_forum_external::get_forums_by_courses(array($course1->id));
        $result = \external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $result);
        foreach ($result as $f) {
            if ($f['id'] == $forum2->id) {
                $this->assertEquals(1, $f['unreadpostscount']);
            }
        }

        // Test discussion without additional posts. There should be only one post (the one created by the discussion).
        $posts = mod_forum_external::get_discussion_posts($discussion2->id, 'modified', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertEquals(1, count($posts['posts']));

        // Test discussion tracking on not tracked forum.
        $result = mod_forum_external::view_forum_discussion($discussion1->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::view_forum_discussion_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        // Test posts have not been marked as read.
        $posts = mod_forum_external::get_discussion_posts($discussion1->id, 'modified', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        foreach ($posts['posts'] as $post) {
            $this->assertNull($post['unread']);
        }

        // Test discussion tracking on tracked forum.
        $result = mod_forum_external::view_forum_discussion($discussion3->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::view_forum_discussion_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        // Test posts have been marked as read.
        $posts = mod_forum_external::get_discussion_posts($discussion3->id, 'modified', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        foreach ($posts['posts'] as $post) {
            $this->assertFalse($post['unread']);
        }

        // Check we receive 0 unread posts.
        forum_tp_count_forum_unread_posts($forum2cm, $course1, true);    // Reset static cache.
        $result = mod_forum_external::get_forums_by_courses(array($course1->id));
        $result = \external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $result);
        foreach ($result as $f) {
            if ($f['id'] == $forum2->id) {
                $this->assertEquals(0, $f['unreadpostscount']);
            }
        }
    }

    /**
     * Test get forum posts
     */
    public function test_mod_forum_get_discussion_posts_deleted() {
        global $CFG, $PAGE;

        $this->resetAfterTest(true);
        $generator = self::getDataGenerator()->get_plugin_generator('mod_forum');

        // Create a course and enrol some users in it.
        $course1 = self::getDataGenerator()->create_course();

        // Create users.
        $user1 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $user2 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // Set the first created user to the test user.
        self::setUser($user1);

        // Create test data.
        $forum1 = self::getDataGenerator()->create_module('forum', (object) [
            'course' => $course1->id,
        ]);
        $forum1context = \context_module::instance($forum1->cmid);

        // Add discussions to the forum.
        $discussion = $generator->create_discussion((object) [
            'course' => $course1->id,
            'userid' => $user1->id,
            'forum' => $forum1->id,
        ]);

        $discussion2 = $generator->create_discussion((object) [
            'course' => $course1->id,
            'userid' => $user2->id,
            'forum' => $forum1->id,
        ]);

        // Add replies to the discussion.
        $discussionreply1 = $generator->create_post((object) [
            'discussion' => $discussion->id,
            'parent' => $discussion->firstpost,
            'userid' => $user2->id,
        ]);
        $discussionreply2 = $generator->create_post((object) [
            'discussion' => $discussion->id,
            'parent' => $discussionreply1->id,
            'userid' => $user2->id,
            'subject' => '',
            'message' => '',
            'messageformat' => FORMAT_PLAIN,
            'deleted' => 1,
        ]);
        $discussionreply3 = $generator->create_post((object) [
            'discussion' => $discussion->id,
            'parent' => $discussion->firstpost,
            'userid' => $user2->id,
        ]);

        // Test where some posts have been marked as deleted.
        $posts = mod_forum_external::get_discussion_posts($discussion->id, 'modified', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $deletedsubject = get_string('forumsubjectdeleted', 'mod_forum');
        $deletedmessage = get_string('forumbodydeleted', 'mod_forum');

        foreach ($posts['posts'] as $post) {
            if ($post['id'] == $discussionreply2->id) {
                $this->assertTrue($post['isdeleted']);
                $this->assertEquals($deletedsubject, $post['subject']);
                $this->assertEquals($deletedmessage, $post['message']);
            } else {
                $this->assertFalse($post['isdeleted']);
                $this->assertNotEquals($deletedsubject, $post['subject']);
                $this->assertNotEquals($deletedmessage, $post['message']);
            }
        }
    }

    /**
     * Test get forum posts returns inline attachments.
     */
    public function test_mod_forum_get_discussion_posts_inline_attachments() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create a course and enrol some users in it.
        $course = self::getDataGenerator()->create_course();

        // Create users.
        $user = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);


        // Set the first created user to the test user.
        self::setUser($user);

        // Create test data.
        $forum = self::getDataGenerator()->create_module('forum', (object) [
            'course' => $course->id,
        ]);

        // Create a file in a draft area for inline attachments.
        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();
        self::setUser($user);
        $usercontext = \context_user::instance($user->id);
        $filepath = '/';
        $filearea = 'draft';
        $component = 'user';
        $filenameimg = 'fakeimage.png';
        $filerecordinline = [
            'contextid' => $usercontext->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $draftidinlineattach,
            'filepath'  => $filepath,
            'filename'  => $filenameimg,
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        // Create discussion.
        $dummytext = 'Here is an inline image: <img src="' . $CFG->wwwroot .
            "/draftfile.php/{$usercontext->id}/user/draft/{$draftidinlineattach}/{$filenameimg}" .
            '" alt="inlineimage">.';
        $options = [
            [
                'name' => 'inlineattachmentsid',
                'value' => $draftidinlineattach
            ],
            [
                'name' => 'attachmentsid',
                'value' => $draftidattach
            ]
        ];
        $discussion = mod_forum_external::add_discussion($forum->id, 'the inline attachment subject', $dummytext,
            -1, $options);

        $posts = mod_forum_external::get_discussion_posts($discussion['discussionid'], 'modified', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $post = $posts['posts'][0];
        $this->assertCount(0, $post['messageinlinefiles']);
        $this->assertEmpty($post['messageinlinefiles']);

        $posts = mod_forum_external::get_discussion_posts($discussion['discussionid'], 'modified', 'DESC',
            true);
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $post = $posts['posts'][0];
        $this->assertCount(1, $post['messageinlinefiles']);
        $this->assertEquals('fakeimage.png', $post['messageinlinefiles'][0]['filename']);
    }

    /**
     * Test get forum posts (qanda forum)
     */
    public function test_mod_forum_get_discussion_posts_qanda() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $record = new \stdClass();
        $user1 = self::getDataGenerator()->create_user($record);
        $user2 = self::getDataGenerator()->create_user();

        // Set the first created user to the test user.
        self::setUser($user1);

        // Create course to add the module.
        $course1 = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // Forum with tracking off.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->type = 'qanda';
        $forum1 = self::getDataGenerator()->create_module('forum', $record);
        $forum1context = \context_module::instance($forum1->cmid);

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user2->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add 1 reply (not the actual user).
        $record = new \stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user2->id;
        $discussion1reply1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // We still see only the original post.
        $posts = mod_forum_external::get_discussion_posts($discussion1->id, 'modified', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertEquals(1, count($posts['posts']));

        // Add a new reply, the user is going to be able to see only the original post and their new post.
        $record = new \stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user1->id;
        $discussion1reply2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $posts = mod_forum_external::get_discussion_posts($discussion1->id, 'modified', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertEquals(2, count($posts['posts']));

        // Now, we can fake the time of the user post, so he can se the rest of the discussion posts.
        $discussion1reply2->created -= $CFG->maxeditingtime * 2;
        $DB->update_record('forum_posts', $discussion1reply2);

        $posts = mod_forum_external::get_discussion_posts($discussion1->id, 'modified', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertEquals(3, count($posts['posts']));
    }

    /**
     * Test get forum discussions paginated
     */
    public function test_mod_forum_get_forum_discussions_paginated() {
        global $USER, $CFG, $DB, $PAGE;

        $this->resetAfterTest(true);

        // Set the CFG variable to allow track forums.
        $CFG->forum_trackreadposts = true;

        // Create a user who can track forums.
        $record = new \stdClass();
        $record->trackforums = true;
        $user1 = self::getDataGenerator()->create_user($record);
        // Create a bunch of other users to post.
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Set the first created user to the test user.
        self::setUser($user1);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();

        // First forum with tracking off.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user1->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add three replies to the discussion 1 from different users.
        $record = new \stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user2->id;
        $discussion1reply1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $record->parent = $discussion1reply1->id;
        $record->userid = $user3->id;
        $discussion1reply2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $record->userid = $user4->id;
        $discussion1reply3 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // Enrol the user in the first course.
        $enrol = enrol_get_plugin('manual');

        // We don't use the dataGenerator as we need to get the $instance2 to unenrol later.
        $enrolinstances = enrol_get_instances($course1->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance1 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance1, $user1->id);

        // Delete one user.
        delete_user($user4);

        // Assign capabilities to view discussions for forum 1.
        $cm = get_coursemodule_from_id('forum', $forum1->cmid, 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        $newrole = create_role('Role 2', 'role2', 'Role 2 description');
        $this->assignUserCapability('mod/forum:viewdiscussion', $context->id, $newrole);

        // Create what we expect to be returned when querying the forums.

        $post1 = $DB->get_record('forum_posts', array('id' => $discussion1->firstpost), '*', MUST_EXIST);

        // User pictures are initially empty, we should get the links once the external function is called.
        $expecteddiscussions = array(
                'id' => $discussion1->firstpost,
                'name' => $discussion1->name,
                'groupid' => (int) $discussion1->groupid,
                'timemodified' => $discussion1reply3->created,
                'usermodified' => (int) $discussion1reply3->userid,
                'timestart' => (int) $discussion1->timestart,
                'timeend' => (int) $discussion1->timeend,
                'discussion' => $discussion1->id,
                'parent' => 0,
                'userid' => (int) $discussion1->userid,
                'created' => (int) $post1->created,
                'modified' => (int) $post1->modified,
                'mailed' => (int) $post1->mailed,
                'subject' => $post1->subject,
                'message' => $post1->message,
                'messageformat' => (int) $post1->messageformat,
                'messagetrust' => (int) $post1->messagetrust,
                'attachment' => $post1->attachment,
                'totalscore' => (int) $post1->totalscore,
                'mailnow' => (int) $post1->mailnow,
                'userfullname' => fullname($user1),
                'usermodifiedfullname' => fullname($user4),
                'userpictureurl' => '',
                'usermodifiedpictureurl' => '',
                'numreplies' => 3,
                'numunread' => 0,
                'pinned' => (bool) FORUM_DISCUSSION_UNPINNED,
                'locked' => false,
                'canreply' => false,
                'canlock' => false
            );

        // Call the external function passing forum id.
        $discussions = mod_forum_external::get_forum_discussions_paginated($forum1->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);
        $expectedreturn = array(
            'discussions' => array($expecteddiscussions),
            'warnings' => array()
        );

        // Wait the theme to be loaded (the external_api call does that) to generate the user profiles.
        $userpicture = new \user_picture($user1);
        $userpicture->size = 1; // Size f1.
        $expectedreturn['discussions'][0]['userpictureurl'] = $userpicture->get_url($PAGE)->out(false);

        $userpicture = new \user_picture($user4);
        $userpicture->size = 1; // Size f1.
        $expectedreturn['discussions'][0]['usermodifiedpictureurl'] = $userpicture->get_url($PAGE)->out(false);

        $this->assertEquals($expectedreturn, $discussions);

        // Call without required view discussion capability.
        $this->unassignUserCapability('mod/forum:viewdiscussion', $context->id, $newrole);
        try {
            mod_forum_external::get_forum_discussions_paginated($forum1->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('noviewdiscussionspermission', $e->errorcode);
        }

        // Unenrol user from second course.
        $enrol->unenrol_user($instance1, $user1->id);

        // Call for the second course we unenrolled the user from, make sure exception thrown.
        try {
            mod_forum_external::get_forum_discussions_paginated($forum1->id);
            $this->fail('Exception expected due to being unenrolled from the course.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        $this->setAdminUser();
        $discussions = mod_forum_external::get_forum_discussions_paginated($forum1->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);
        $this->assertTrue($discussions['discussions'][0]['canlock']);
    }

    /**
     * Test get forum discussions paginated (qanda forums)
     */
    public function test_mod_forum_get_forum_discussions_paginated_qanda() {

        $this->resetAfterTest(true);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // First forum with tracking off.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->type = 'qanda';
        $forum = self::getDataGenerator()->create_module('forum', $record);

        // Add discussions to the forums.
        $discussionrecord = new \stdClass();
        $discussionrecord->course = $course->id;
        $discussionrecord->userid = $user2->id;
        $discussionrecord->forum = $forum->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($discussionrecord);

        self::setAdminUser();
        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(1, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);

        self::setUser($user1);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(1, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);

    }

    /**
     * Test get forum discussions
     */
    public function test_mod_forum_get_forum_discussions() {
        global $CFG, $DB, $PAGE;

        $this->resetAfterTest(true);

        // Set the CFG variable to allow track forums.
        $CFG->forum_trackreadposts = true;

        // Create a user who can track forums.
        $record = new \stdClass();
        $record->trackforums = true;
        $user1 = self::getDataGenerator()->create_user($record);
        // Create a bunch of other users to post.
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Set the first created user to the test user.
        self::setUser($user1);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();

        // First forum with tracking off.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user1->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add three replies to the discussion 1 from different users.
        $record = new \stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user2->id;
        $discussion1reply1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $record->parent = $discussion1reply1->id;
        $record->userid = $user3->id;
        $discussion1reply2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $record->userid = $user4->id;
        $discussion1reply3 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // Enrol the user in the first course.
        $enrol = enrol_get_plugin('manual');

        // We don't use the dataGenerator as we need to get the $instance2 to unenrol later.
        $enrolinstances = enrol_get_instances($course1->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance1 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance1, $user1->id);

        // Delete one user.
        delete_user($user4);

        // Assign capabilities to view discussions for forum 1.
        $cm = get_coursemodule_from_id('forum', $forum1->cmid, 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        $newrole = create_role('Role 2', 'role2', 'Role 2 description');
        $this->assignUserCapability('mod/forum:viewdiscussion', $context->id, $newrole);

        // Create what we expect to be returned when querying the forums.

        $post1 = $DB->get_record('forum_posts', array('id' => $discussion1->firstpost), '*', MUST_EXIST);

        // User pictures are initially empty, we should get the links once the external function is called.
        $expecteddiscussions = array(
            'id' => $discussion1->firstpost,
            'name' => $discussion1->name,
            'groupid' => (int) $discussion1->groupid,
            'timemodified' => (int) $discussion1reply3->created,
            'usermodified' => (int) $discussion1reply3->userid,
            'timestart' => (int) $discussion1->timestart,
            'timeend' => (int) $discussion1->timeend,
            'discussion' => (int) $discussion1->id,
            'parent' => 0,
            'userid' => (int) $discussion1->userid,
            'created' => (int) $post1->created,
            'modified' => (int) $post1->modified,
            'mailed' => (int) $post1->mailed,
            'subject' => $post1->subject,
            'message' => $post1->message,
            'messageformat' => (int) $post1->messageformat,
            'messagetrust' => (int) $post1->messagetrust,
            'attachment' => $post1->attachment,
            'totalscore' => (int) $post1->totalscore,
            'mailnow' => (int) $post1->mailnow,
            'userfullname' => fullname($user1),
            'usermodifiedfullname' => fullname($user4),
            'userpictureurl' => '',
            'usermodifiedpictureurl' => '',
            'numreplies' => 3,
            'numunread' => 0,
            'pinned' => (bool) FORUM_DISCUSSION_UNPINNED,
            'locked' => false,
            'canreply' => false,
            'canlock' => false,
            'starred' => false,
            'canfavourite' => true
        );

        // Call the external function passing forum id.
        $discussions = mod_forum_external::get_forum_discussions($forum1->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        $expectedreturn = array(
            'discussions' => array($expecteddiscussions),
            'warnings' => array()
        );

        // Wait the theme to be loaded (the external_api call does that) to generate the user profiles.
        $userpicture = new \user_picture($user1);
        $userpicture->size = 2; // Size f2.
        $expectedreturn['discussions'][0]['userpictureurl'] = $userpicture->get_url($PAGE)->out(false);

        $userpicture = new \user_picture($user4);
        $userpicture->size = 2; // Size f2.
        $expectedreturn['discussions'][0]['usermodifiedpictureurl'] = $userpicture->get_url($PAGE)->out(false);

        $this->assertEquals($expectedreturn, $discussions);

        // Test the starring functionality return.
        $t = mod_forum_external::toggle_favourite_state($discussion1->id, 1);
        $expectedreturn['discussions'][0]['starred'] = true;
        $discussions = mod_forum_external::get_forum_discussions($forum1->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        $this->assertEquals($expectedreturn, $discussions);

        // Call without required view discussion capability.
        $this->unassignUserCapability('mod/forum:viewdiscussion', $context->id, $newrole);
        try {
            mod_forum_external::get_forum_discussions($forum1->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('noviewdiscussionspermission', $e->errorcode);
        }

        // Unenrol user from second course.
        $enrol->unenrol_user($instance1, $user1->id);

        // Call for the second course we unenrolled the user from, make sure exception thrown.
        try {
            mod_forum_external::get_forum_discussions($forum1->id);
            $this->fail('Exception expected due to being unenrolled from the course.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        $this->setAdminUser();
        $discussions = mod_forum_external::get_forum_discussions($forum1->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        $this->assertTrue($discussions['discussions'][0]['canlock']);
    }

    /**
     * Test the sorting in get forum discussions
     */
    public function test_mod_forum_get_forum_discussions_sorting() {
        global $CFG, $DB, $PAGE;

        $this->resetAfterTest(true);

        // Set the CFG variable to allow track forums.
        $CFG->forum_trackreadposts = true;

        // Create a user who can track forums.
        $record = new \stdClass();
        $record->trackforums = true;
        $user1 = self::getDataGenerator()->create_user($record);
        // Create a bunch of other users to post.
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Set the first created user to the test user.
        self::setUser($user1);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();

        // Enrol the user in the first course.
        $enrol = enrol_get_plugin('manual');

        // We don't use the dataGenerator as we need to get the $instance2 to unenrol later.
        $enrolinstances = enrol_get_instances($course1->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance1 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance1, $user1->id);

        // First forum with tracking off.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);

        // Assign capabilities to view discussions for forum 1.
        $cm = get_coursemodule_from_id('forum', $forum1->cmid, 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        $newrole = create_role('Role 2', 'role2', 'Role 2 description');
        $this->assignUserCapability('mod/forum:viewdiscussion', $context->id, $newrole);

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user1->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        sleep(1);

        // Add three replies to the discussion 1 from different users.
        $record = new \stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user2->id;
        $discussion1reply1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);
        sleep(1);

        $record->parent = $discussion1reply1->id;
        $record->userid = $user3->id;
        $discussion1reply2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);
        sleep(1);

        $record->userid = $user4->id;
        $discussion1reply3 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);
        sleep(1);

        // Create discussion2.
        $record2 = new \stdClass();
        $record2->course = $course1->id;
        $record2->userid = $user1->id;
        $record2->forum = $forum1->id;
        $discussion2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record2);
        sleep(1);

        // Add one reply to the discussion 2.
        $record2 = new \stdClass();
        $record2->discussion = $discussion2->id;
        $record2->parent = $discussion2->firstpost;
        $record2->userid = $user2->id;
        $discussion2reply1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record2);
        sleep(1);

        // Create discussion 3.
        $record3 = new \stdClass();
        $record3->course = $course1->id;
        $record3->userid = $user1->id;
        $record3->forum = $forum1->id;
        $discussion3 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record3);
        sleep(1);

        // Add two replies to the discussion 3.
        $record3 = new \stdClass();
        $record3->discussion = $discussion3->id;
        $record3->parent = $discussion3->firstpost;
        $record3->userid = $user2->id;
        $discussion3reply1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record3);
        sleep(1);

        $record3->parent = $discussion3reply1->id;
        $record3->userid = $user3->id;
        $discussion3reply2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record3);

        // Call the external function passing forum id.
        $discussions = mod_forum_external::get_forum_discussions($forum1->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        // Discussions should be ordered by last post date in descending order by default.
        $this->assertEquals($discussions['discussions'][0]['discussion'], $discussion3->id);
        $this->assertEquals($discussions['discussions'][1]['discussion'], $discussion2->id);
        $this->assertEquals($discussions['discussions'][2]['discussion'], $discussion1->id);

        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $discussionlistvault = $vaultfactory->get_discussions_in_forum_vault();

        // Call the external function passing forum id and sort order parameter.
        $discussions = mod_forum_external::get_forum_discussions($forum1->id, $discussionlistvault::SORTORDER_LASTPOST_ASC);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        // Discussions should be ordered by last post date in ascending order.
        $this->assertEquals($discussions['discussions'][0]['discussion'], $discussion1->id);
        $this->assertEquals($discussions['discussions'][1]['discussion'], $discussion2->id);
        $this->assertEquals($discussions['discussions'][2]['discussion'], $discussion3->id);

        // Call the external function passing forum id and sort order parameter.
        $discussions = mod_forum_external::get_forum_discussions($forum1->id, $discussionlistvault::SORTORDER_CREATED_DESC);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        // Discussions should be ordered by discussion creation date in descending order.
        $this->assertEquals($discussions['discussions'][0]['discussion'], $discussion3->id);
        $this->assertEquals($discussions['discussions'][1]['discussion'], $discussion2->id);
        $this->assertEquals($discussions['discussions'][2]['discussion'], $discussion1->id);

        // Call the external function passing forum id and sort order parameter.
        $discussions = mod_forum_external::get_forum_discussions($forum1->id, $discussionlistvault::SORTORDER_CREATED_ASC);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        // Discussions should be ordered by discussion creation date in ascending order.
        $this->assertEquals($discussions['discussions'][0]['discussion'], $discussion1->id);
        $this->assertEquals($discussions['discussions'][1]['discussion'], $discussion2->id);
        $this->assertEquals($discussions['discussions'][2]['discussion'], $discussion3->id);

        // Call the external function passing forum id and sort order parameter.
        $discussions = mod_forum_external::get_forum_discussions($forum1->id, $discussionlistvault::SORTORDER_REPLIES_DESC);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        // Discussions should be ordered by the number of replies in descending order.
        $this->assertEquals($discussions['discussions'][0]['discussion'], $discussion1->id);
        $this->assertEquals($discussions['discussions'][1]['discussion'], $discussion3->id);
        $this->assertEquals($discussions['discussions'][2]['discussion'], $discussion2->id);

        // Call the external function passing forum id and sort order parameter.
        $discussions = mod_forum_external::get_forum_discussions($forum1->id, $discussionlistvault::SORTORDER_REPLIES_ASC);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        // Discussions should be ordered by the number of replies in ascending order.
        $this->assertEquals($discussions['discussions'][0]['discussion'], $discussion2->id);
        $this->assertEquals($discussions['discussions'][1]['discussion'], $discussion3->id);
        $this->assertEquals($discussions['discussions'][2]['discussion'], $discussion1->id);

        // Pin discussion2.
        $DB->update_record('forum_discussions',
            (object) array('id' => $discussion2->id, 'pinned' => FORUM_DISCUSSION_PINNED));

        // Call the external function passing forum id.
        $discussions = mod_forum_external::get_forum_discussions($forum1->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        // Discussions should be ordered by last post date in descending order by default.
        // Pinned discussions should be at the top of the list.
        $this->assertEquals($discussions['discussions'][0]['discussion'], $discussion2->id);
        $this->assertEquals($discussions['discussions'][1]['discussion'], $discussion3->id);
        $this->assertEquals($discussions['discussions'][2]['discussion'], $discussion1->id);

        // Call the external function passing forum id and sort order parameter.
        $discussions = mod_forum_external::get_forum_discussions($forum1->id, $discussionlistvault::SORTORDER_LASTPOST_ASC);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);
        // Discussions should be ordered by last post date in ascending order.
        // Pinned discussions should be at the top of the list.
        $this->assertEquals($discussions['discussions'][0]['discussion'], $discussion2->id);
        $this->assertEquals($discussions['discussions'][1]['discussion'], $discussion1->id);
        $this->assertEquals($discussions['discussions'][2]['discussion'], $discussion3->id);
    }

    /**
     * Test add_discussion_post
     */
    public function test_add_discussion_post() {
        global $CFG;

        $this->resetAfterTest(true);

        $user = self::getDataGenerator()->create_user();
        $otheruser = self::getDataGenerator()->create_user();

        self::setAdminUser();

        // Create course to add the module.
        $course = self::getDataGenerator()->create_course(array('groupmode' => VISIBLEGROUPS, 'groupmodeforce' => 0));

        // Forum with tracking off.
        $record = new \stdClass();
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record);
        $cm = get_coursemodule_from_id('forum', $forum->cmid, 0, false, MUST_EXIST);
        $forumcontext = \context_module::instance($forum->cmid);

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Try to post (user not enrolled).
        self::setUser($user);
        try {
            mod_forum_external::add_discussion_post($discussion->firstpost, 'some subject', 'some text here...');
            $this->fail('Exception expected due to being unenrolled from the course.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $this->getDataGenerator()->enrol_user($otheruser->id, $course->id);

        $createdpost = mod_forum_external::add_discussion_post($discussion->firstpost, 'some subject', 'some text here...');
        $createdpost = \external_api::clean_returnvalue(mod_forum_external::add_discussion_post_returns(), $createdpost);

        $posts = mod_forum_external::get_discussion_posts($discussion->id, 'modified', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // We receive the discussion and the post.
        $this->assertEquals(2, count($posts['posts']));

        $tested = false;
        foreach ($posts['posts'] as $thispost) {
            if ($createdpost['postid'] == $thispost['id']) {
                $this->assertEquals('some subject', $thispost['subject']);
                $this->assertEquals('some text here...', $thispost['message']);
                $this->assertEquals(FORMAT_HTML, $thispost['messageformat']); // This is the default if format was not specified.
                $tested = true;
            }
        }
        $this->assertTrue($tested);

        // Let's simulate a call with any other format, it should be stored that way.
        global $DB; // Yes, we are going to use DB facilities too, because cannot rely on other functions for checking
                    // the format. They eat it completely (going back to FORMAT_HTML. So we only can trust DB for further
                    // processing.
        $formats = [FORMAT_PLAIN, FORMAT_MOODLE, FORMAT_MARKDOWN, FORMAT_HTML];
        $options = [];
        foreach ($formats as $format) {
            $createdpost = mod_forum_external::add_discussion_post($discussion->firstpost,
                'with some format', 'some formatted here...', $options, $format);
            $createdpost = \external_api::clean_returnvalue(mod_forum_external::add_discussion_post_returns(), $createdpost);
            $dbformat = $DB->get_field('forum_posts', 'messageformat', ['id' => $createdpost['postid']]);
            $this->assertEquals($format, $dbformat);
        }

        // Now let's try the 'topreferredformat' option. That should end with the content
        // transformed and the format being FORMAT_HTML (when, like in this case,  user preferred
        // format is HTML, inferred from editor in preferences).
        $options = [['name' => 'topreferredformat', 'value' => true]];
        $createdpost = mod_forum_external::add_discussion_post($discussion->firstpost,
            'interesting subject', 'with some https://example.com link', $options, FORMAT_MOODLE);
        $createdpost = \external_api::clean_returnvalue(mod_forum_external::add_discussion_post_returns(), $createdpost);
        $dbpost = $DB->get_record('forum_posts', ['id' => $createdpost['postid']]);
        // Format HTML and content converted, we should get.
        $this->assertEquals(FORMAT_HTML, $dbpost->messageformat);
        $this->assertEquals('<div class="text_to_html">with some https://example.com link</div>', $dbpost->message);

        // Test inline and regular attachment in post
        // Create a file in a draft area for inline attachments.
        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();
        self::setUser($user);
        $usercontext = \context_user::instance($user->id);
        $filepath = '/';
        $filearea = 'draft';
        $component = 'user';
        $filenameimg = 'shouldbeanimage.txt';
        $filerecordinline = array(
            'contextid' => $usercontext->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $draftidinlineattach,
            'filepath'  => $filepath,
            'filename'  => $filenameimg,
        );
        $fs = get_file_storage();

        // Create a file in a draft area for regular attachments.
        $filerecordattach = $filerecordinline;
        $attachfilename = 'attachment.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        $options = array(array('name' => 'inlineattachmentsid', 'value' => $draftidinlineattach),
                         array('name' => 'attachmentsid', 'value' => $draftidattach));
        $dummytext = 'Here is an inline image: <img src="' . $CFG->wwwroot
                     . "/draftfile.php/{$usercontext->id}/user/draft/{$draftidinlineattach}/{$filenameimg}"
                     . '" alt="inlineimage">.';
        $createdpost = mod_forum_external::add_discussion_post($discussion->firstpost, 'new post inline attachment',
                                                               $dummytext, $options);
        $createdpost = \external_api::clean_returnvalue(mod_forum_external::add_discussion_post_returns(), $createdpost);

        $posts = mod_forum_external::get_discussion_posts($discussion->id, 'modified', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // We receive the discussion and the post.
        // Can't guarantee order of posts during tests.
        $postfound = false;
        foreach ($posts['posts'] as $thispost) {
            if ($createdpost['postid'] == $thispost['id']) {
                $this->assertEquals($createdpost['postid'], $thispost['id']);
                $this->assertCount(1, $thispost['attachments']);
                $this->assertEquals('attachment.txt', $thispost['attachments'][0]['filename']);
                $this->assertEquals($thispost['attachments'][0]['filename'], $attachfilename, "There should be 1 attachment");
                $this->assertStringContainsString('pluginfile.php', $thispost['message']);
                $postfound = true;
                break;
            }
        }

        $this->assertTrue($postfound);

        // Check not posting in groups the user is not member of.
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group->id, $otheruser->id);

        $forum = self::getDataGenerator()->create_module('forum', $record, array('groupmode' => SEPARATEGROUPS));
        $record->forum = $forum->id;
        $record->userid = $otheruser->id;
        $record->groupid = $group->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        try {
            mod_forum_external::add_discussion_post($discussion->firstpost, 'some subject', 'some text here...');
            $this->fail('Exception expected due to invalid permissions for posting.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('nopostforum', $e->errorcode);
        }
    }

    /**
     * Test add_discussion_post and auto subscription to a discussion.
     */
    public function test_add_discussion_post_subscribe_discussion() {
        global $USER;

        $this->resetAfterTest(true);

        self::setAdminUser();

        $user = self::getDataGenerator()->create_user();
        $admin = get_admin();
        // Create course to add the module.
        $course = self::getDataGenerator()->create_course(array('groupmode' => VISIBLEGROUPS, 'groupmodeforce' => 0));

        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Forum with tracking off.
        $record = new \stdClass();
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record);
        $cm = get_coursemodule_from_id('forum', $forum->cmid, 0, false, MUST_EXIST);

        // Add discussions to the forums.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $admin->id;
        $record->forum = $forum->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $discussion2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Try to post as user.
        self::setUser($user);
        // Enable auto subscribe discussion.
        $USER->autosubscribe = true;
        // Add a discussion post in a forum discussion where the user is not subscribed (auto-subscribe preference enabled).
        mod_forum_external::add_discussion_post($discussion1->firstpost, 'some subject', 'some text here...');

        $posts = mod_forum_external::get_discussion_posts($discussion1->id, 'modified', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // We receive the discussion and the post.
        $this->assertEquals(2, count($posts['posts']));
        // The user should be subscribed to the discussion after adding a discussion post.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion1->id, $cm));

        // Disable auto subscribe discussion.
        $USER->autosubscribe = false;
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion1->id, $cm));
        // Add a discussion post in a forum discussion where the user is subscribed (auto-subscribe preference disabled).
        mod_forum_external::add_discussion_post($discussion1->firstpost, 'some subject 1', 'some text here 1...');

        $posts = mod_forum_external::get_discussion_posts($discussion1->id, 'modified', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // We receive the discussion and the post.
        $this->assertEquals(3, count($posts['posts']));
        // The user should still be subscribed to the discussion after adding a discussion post.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion1->id, $cm));

        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion2->id, $cm));
        // Add a discussion post in a forum discussion where the user is not subscribed (auto-subscribe preference disabled).
        mod_forum_external::add_discussion_post($discussion2->firstpost, 'some subject 2', 'some text here 2...');

        $posts = mod_forum_external::get_discussion_posts($discussion2->id, 'modified', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // We receive the discussion and the post.
        $this->assertEquals(2, count($posts['posts']));
        // The user should still not be subscribed to the discussion after adding a discussion post.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion2->id, $cm));

        // Passing a value for the discussionsubscribe option parameter.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion2->id, $cm));
        // Add a discussion post in a forum discussion where the user is not subscribed (auto-subscribe preference disabled),
        // and the option parameter 'discussionsubscribe' => true in the webservice.
        $option = array('name' => 'discussionsubscribe', 'value' => true);
        $options[] = $option;
        mod_forum_external::add_discussion_post($discussion2->firstpost, 'some subject 2', 'some text here 2...',
            $options);

        $posts = mod_forum_external::get_discussion_posts($discussion2->id, 'modified', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // We receive the discussion and the post.
        $this->assertEquals(3, count($posts['posts']));
        // The user should now be subscribed to the discussion after adding a discussion post.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion2->id, $cm));
    }

    /*
     * Test add_discussion. A basic test since all the API functions are already covered by unit tests.
     */
    public function test_add_discussion() {
        global $CFG, $USER;
        $this->resetAfterTest(true);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // First forum with tracking off.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->type = 'news';
        $forum = self::getDataGenerator()->create_module('forum', $record);

        self::setUser($user1);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
            $this->fail('Exception expected due to invalid permissions.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        self::setAdminUser();
        $createddiscussion = mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
        $createddiscussion = \external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $createddiscussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(1, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);

        $this->assertEquals($createddiscussion['discussionid'], $discussions['discussions'][0]['discussion']);
        $this->assertEquals(-1, $discussions['discussions'][0]['groupid']);
        $this->assertEquals('the subject', $discussions['discussions'][0]['subject']);
        $this->assertEquals('some text here...', $discussions['discussions'][0]['message']);

        $discussion2pinned = mod_forum_external::add_discussion($forum->id, 'the pinned subject', 'some 2 text here...', -1,
                                                                array('options' => array('name' => 'discussionpinned',
                                                                                         'value' => true)));
        $discussion3 = mod_forum_external::add_discussion($forum->id, 'the non pinnedsubject', 'some 3 text here...');
        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);
        $this->assertCount(3, $discussions['discussions']);
        $this->assertEquals($discussion2pinned['discussionid'], $discussions['discussions'][0]['discussion']);

        // Test inline and regular attachment in new discussion
        // Create a file in a draft area for inline attachments.

        $fs = get_file_storage();

        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();

        $usercontext = \context_user::instance($USER->id);
        $filepath = '/';
        $filearea = 'draft';
        $component = 'user';
        $filenameimg = 'shouldbeanimage.txt';
        $filerecord = array(
            'contextid' => $usercontext->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $draftidinlineattach,
            'filepath'  => $filepath,
            'filename'  => $filenameimg,
        );

        // Create a file in a draft area for regular attachments.
        $filerecordattach = $filerecord;
        $attachfilename = 'attachment.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecord, 'image contents (not really)');
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        $dummytext = 'Here is an inline image: <img src="' . $CFG->wwwroot .
                    "/draftfile.php/{$usercontext->id}/user/draft/{$draftidinlineattach}/{$filenameimg}" .
                    '" alt="inlineimage">.';

        $options = array(array('name' => 'inlineattachmentsid', 'value' => $draftidinlineattach),
                         array('name' => 'attachmentsid', 'value' => $draftidattach));
        $createddiscussion = mod_forum_external::add_discussion($forum->id, 'the inline attachment subject',
                                                                $dummytext, -1, $options);
        $createddiscussion = \external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $createddiscussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(4, $discussions['discussions']);
        $this->assertCount(0, $createddiscussion['warnings']);
        // Can't guarantee order of posts during tests.
        $postfound = false;
        foreach ($discussions['discussions'] as $thisdiscussion) {
            if ($createddiscussion['discussionid'] == $thisdiscussion['discussion']) {
                $this->assertEquals($thisdiscussion['attachment'], 1, "There should be a non-inline attachment");
                $this->assertCount(1, $thisdiscussion['attachments'], "There should be 1 attachment");
                $this->assertEquals($thisdiscussion['attachments'][0]['filename'], $attachfilename, "There should be 1 attachment");
                $this->assertStringNotContainsString('draftfile.php', $thisdiscussion['message']);
                $this->assertStringContainsString('pluginfile.php', $thisdiscussion['message']);
                $postfound = true;
                break;
            }
        }

        $this->assertTrue($postfound);
    }

    /**
     * Test adding discussions in a course with gorups
     */
    public function test_add_discussion_in_course_with_groups() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create course to add the module.
        $course = self::getDataGenerator()->create_course(array('groupmode' => VISIBLEGROUPS, 'groupmodeforce' => 0));
        $user = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Forum forcing separate gropus.
        $record = new \stdClass();
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record, array('groupmode' => SEPARATEGROUPS));

        // Try to post (user not enrolled).
        self::setUser($user);

        // The user is not enroled in any group, try to post in a forum with separate groups.
        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
            $this->fail('Exception expected due to invalid group permissions.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...', 0);
            $this->fail('Exception expected due to invalid group permissions.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        // Create a group.
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        // Try to post in a group the user is not enrolled.
        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...', $group->id);
            $this->fail('Exception expected due to invalid group permissions.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        // Add the user to a group.
        groups_add_member($group->id, $user->id);

        // Try to post in a group the user is not enrolled.
        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...', $group->id + 1);
            $this->fail('Exception expected due to invalid group.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        // Nost add the discussion using a valid group.
        $discussion = mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...', $group->id);
        $discussion = \external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $discussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(1, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);
        $this->assertEquals($discussion['discussionid'], $discussions['discussions'][0]['discussion']);
        $this->assertEquals($group->id, $discussions['discussions'][0]['groupid']);

        // Now add a discussions without indicating a group. The function should guess the correct group.
        $discussion = mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
        $discussion = \external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $discussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(2, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);
        $this->assertEquals($group->id, $discussions['discussions'][0]['groupid']);
        $this->assertEquals($group->id, $discussions['discussions'][1]['groupid']);

        // Enrol the same user in other group.
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group2->id, $user->id);

        // Now add a discussions without indicating a group. The function should guess the correct group (the first one).
        $discussion = mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
        $discussion = \external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $discussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(3, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);
        $this->assertEquals($group->id, $discussions['discussions'][0]['groupid']);
        $this->assertEquals($group->id, $discussions['discussions'][1]['groupid']);
        $this->assertEquals($group->id, $discussions['discussions'][2]['groupid']);

    }

    /*
     * Test set_lock_state.
     */
    public function test_set_lock_state() {
        global $DB;
        $this->resetAfterTest(true);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();
        $user = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // First forum with tracking off.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->type = 'news';
        $forum = self::getDataGenerator()->create_module('forum', $record);

        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // User who is a student.
        self::setUser($user);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id, 'manual');

        // Only a teacher should be able to lock a discussion.
        try {
            $result = mod_forum_external::set_lock_state($forum->id, $discussion->id, 0);
            $this->fail('Exception expected due to missing capability.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('errorcannotlock', $e->errorcode);
        }

        // Set the lock.
        self::setAdminUser();
        $result = mod_forum_external::set_lock_state($forum->id, $discussion->id, 0);
        $result = \external_api::clean_returnvalue(mod_forum_external::set_lock_state_returns(), $result);
        $this->assertTrue($result['locked']);
        $this->assertNotEquals(0, $result['times']['locked']);

        // Unset the lock.
        $result = mod_forum_external::set_lock_state($forum->id, $discussion->id, time());
        $result = \external_api::clean_returnvalue(mod_forum_external::set_lock_state_returns(), $result);
        $this->assertFalse($result['locked']);
        $this->assertEquals('0', $result['times']['locked']);
    }

    /*
     * Test can_add_discussion. A basic test since all the API functions are already covered by unit tests.
     */
    public function test_can_add_discussion() {
        global $DB;
        $this->resetAfterTest(true);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        $user = self::getDataGenerator()->create_user();

        // First forum with tracking off.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->type = 'news';
        $forum = self::getDataGenerator()->create_module('forum', $record);

        // User with no permissions to add in a news forum.
        self::setUser($user);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $result = mod_forum_external::can_add_discussion($forum->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::can_add_discussion_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertFalse($result['canpindiscussions']);
        $this->assertTrue($result['cancreateattachment']);

        // Disable attachments.
        $DB->set_field('forum', 'maxattachments', 0, array('id' => $forum->id));
        $result = mod_forum_external::can_add_discussion($forum->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::can_add_discussion_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertFalse($result['canpindiscussions']);
        $this->assertFalse($result['cancreateattachment']);
        $DB->set_field('forum', 'maxattachments', 1, array('id' => $forum->id));    // Enable attachments again.

        self::setAdminUser();
        $result = mod_forum_external::can_add_discussion($forum->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::can_add_discussion_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertTrue($result['canpindiscussions']);
        $this->assertTrue($result['cancreateattachment']);
    }

    /*
     * A basic test to make sure users cannot post to forum after the cutoff date.
     */
    public function test_can_add_discussion_after_cutoff() {
        $this->resetAfterTest(true);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        $user = self::getDataGenerator()->create_user();

        // Create a forum with cutoff date set to a past date.
        $forum = self::getDataGenerator()->create_module('forum', ['course' => $course->id, 'cutoffdate' => time() - 1]);

        // User with no mod/forum:canoverridecutoff capability.
        self::setUser($user);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $result = mod_forum_external::can_add_discussion($forum->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::can_add_discussion_returns(), $result);
        $this->assertFalse($result['status']);

        self::setAdminUser();
        $result = mod_forum_external::can_add_discussion($forum->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::can_add_discussion_returns(), $result);
        $this->assertTrue($result['status']);
    }

    /**
     * Test get posts discussions including rating information.
     */
    public function test_mod_forum_get_discussion_rating_information() {
        global $DB, $CFG, $PAGE;
        require_once($CFG->dirroot . '/rating/lib.php');
        $PAGE->set_url('/my/index.php');    // Need this because some internal API calls require the $PAGE url to be set.
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Create course to add the module.
        $course = self::getDataGenerator()->create_course();

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Create the forum.
        $record = new \stdClass();
        $record->course = $course->id;
        // Set Aggregate type = Average of ratings.
        $record->assessed = RATING_AGGREGATE_AVERAGE;
        $record->scale = 100;
        $forum = self::getDataGenerator()->create_module('forum', $record);
        $context = \context_module::instance($forum->cmid);

        // Add discussion to the forum.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user1->id;
        $record->forum = $forum->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Retrieve the first post.
        $post = $DB->get_record('forum_posts', array('discussion' => $discussion->id));

        // Rate the discussion as user2.
        $rating1 = new \stdClass();
        $rating1->contextid = $context->id;
        $rating1->component = 'mod_forum';
        $rating1->ratingarea = 'post';
        $rating1->itemid = $post->id;
        $rating1->rating = 50;
        $rating1->scaleid = 100;
        $rating1->userid = $user2->id;
        $rating1->timecreated = time();
        $rating1->timemodified = time();
        $rating1->id = $DB->insert_record('rating', $rating1);

        // Rate the discussion as user3.
        $rating2 = new \stdClass();
        $rating2->contextid = $context->id;
        $rating2->component = 'mod_forum';
        $rating2->ratingarea = 'post';
        $rating2->itemid = $post->id;
        $rating2->rating = 100;
        $rating2->scaleid = 100;
        $rating2->userid = $user3->id;
        $rating2->timecreated = time() + 1;
        $rating2->timemodified = time() + 1;
        $rating2->id = $DB->insert_record('rating', $rating2);

        // Retrieve the rating for the post as student.
        $this->setUser($user1);
        $posts = mod_forum_external::get_discussion_posts($discussion->id, 'id', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertCount(1, $posts['ratinginfo']['ratings']);
        $this->assertTrue($posts['ratinginfo']['ratings'][0]['canviewaggregate']);
        $this->assertFalse($posts['ratinginfo']['canviewall']);
        $this->assertFalse($posts['ratinginfo']['ratings'][0]['canrate']);
        $this->assertEquals(2, $posts['ratinginfo']['ratings'][0]['count']);
        $this->assertEquals(($rating1->rating + $rating2->rating) / 2, $posts['ratinginfo']['ratings'][0]['aggregate']);

        // Retrieve the rating for the post as teacher.
        $this->setUser($teacher);
        $posts = mod_forum_external::get_discussion_posts($discussion->id, 'id', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertCount(1, $posts['ratinginfo']['ratings']);
        $this->assertTrue($posts['ratinginfo']['ratings'][0]['canviewaggregate']);
        $this->assertTrue($posts['ratinginfo']['canviewall']);
        $this->assertTrue($posts['ratinginfo']['ratings'][0]['canrate']);
        $this->assertEquals(2, $posts['ratinginfo']['ratings'][0]['count']);
        $this->assertEquals(($rating1->rating + $rating2->rating) / 2, $posts['ratinginfo']['ratings'][0]['aggregate']);
    }

    /**
     * Test mod_forum_get_forum_access_information.
     */
    public function test_mod_forum_get_forum_access_information() {
        global $DB;

        $this->resetAfterTest(true);

        $student = self::getDataGenerator()->create_user();
        $course = self::getDataGenerator()->create_course();
        // Create the forum.
        $record = new \stdClass();
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');

        self::setUser($student);
        $result = mod_forum_external::get_forum_access_information($forum->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::get_forum_access_information_returns(), $result);

        // Check default values for capabilities.
        $enabledcaps = array('canviewdiscussion', 'canstartdiscussion', 'canreplypost', 'canviewrating', 'cancreateattachment',
            'canexportownpost', 'cancantogglefavourite', 'candeleteownpost', 'canallowforcesubscribe');

        unset($result['warnings']);
        foreach ($result as $capname => $capvalue) {
            if (in_array($capname, $enabledcaps)) {
                $this->assertTrue($capvalue);
            } else {
                $this->assertFalse($capvalue);
            }
        }
        // Now, unassign some capabilities.
        unassign_capability('mod/forum:deleteownpost', $studentrole->id);
        unassign_capability('mod/forum:allowforcesubscribe', $studentrole->id);
        array_pop($enabledcaps);
        array_pop($enabledcaps);
        accesslib_clear_all_caches_for_unit_testing();

        $result = mod_forum_external::get_forum_access_information($forum->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::get_forum_access_information_returns(), $result);
        unset($result['warnings']);
        foreach ($result as $capname => $capvalue) {
            if (in_array($capname, $enabledcaps)) {
                $this->assertTrue($capvalue);
            } else {
                $this->assertFalse($capvalue);
            }
        }
    }

    /**
     * Test add_discussion_post
     */
    public function test_add_discussion_post_private() {
        global $DB;

        $this->resetAfterTest(true);

        self::setAdminUser();

        // Create course to add the module.
        $course = self::getDataGenerator()->create_course();

        // Standard forum.
        $record = new \stdClass();
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record);
        $cm = get_coursemodule_from_id('forum', $forum->cmid, 0, false, MUST_EXIST);
        $forumcontext = \context_module::instance($forum->cmid);
        $generator = self::getDataGenerator()->get_plugin_generator('mod_forum');

        // Create an enrol users.
        $student1 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $student2 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $teacher1 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher1->id, $course->id, 'editingteacher');
        $teacher2 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher2->id, $course->id, 'editingteacher');

        // Add a new discussion to the forum.
        self::setUser($student1);
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $student1->id;
        $record->forum = $forum->id;
        $discussion = $generator->create_discussion($record);

        // Have the teacher reply privately.
        self::setUser($teacher1);
        $post = mod_forum_external::add_discussion_post($discussion->firstpost, 'some subject', 'some text here...', [
                [
                    'name' => 'private',
                    'value' => true,
                ],
            ]);
        $post = \external_api::clean_returnvalue(mod_forum_external::add_discussion_post_returns(), $post);
        $privatereply = $DB->get_record('forum_posts', array('id' => $post['postid']));
        $this->assertEquals($student1->id, $privatereply->privatereplyto);
        // Bump the time of the private reply to ensure order.
        $privatereply->created++;
        $privatereply->modified = $privatereply->created;
        $DB->update_record('forum_posts', $privatereply);

        // The teacher will receive their private reply.
        self::setUser($teacher1);
        $posts = mod_forum_external::get_discussion_posts($discussion->id, 'id', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertEquals(2, count($posts['posts']));
        $this->assertTrue($posts['posts'][0]['isprivatereply']);

        // Another teacher on the course will also receive the private reply.
        self::setUser($teacher2);
        $posts = mod_forum_external::get_discussion_posts($discussion->id, 'id', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertEquals(2, count($posts['posts']));
        $this->assertTrue($posts['posts'][0]['isprivatereply']);

        // The student will receive the private reply.
        self::setUser($student1);
        $posts = mod_forum_external::get_discussion_posts($discussion->id, 'id', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertEquals(2, count($posts['posts']));
        $this->assertTrue($posts['posts'][0]['isprivatereply']);

        // Another student will not receive the private reply.
        self::setUser($student2);
        $posts = mod_forum_external::get_discussion_posts($discussion->id, 'id', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        $this->assertEquals(1, count($posts['posts']));
        $this->assertFalse($posts['posts'][0]['isprivatereply']);

        // A user cannot reply to a private reply.
        self::setUser($teacher2);
        $this->expectException('coding_exception');
        $post = mod_forum_external::add_discussion_post($privatereply->id, 'some subject', 'some text here...', [
                'options' => [
                    'name' => 'private',
                    'value' => false,
                ],
            ]);
    }

    /**
     * Test trusted text enabled.
     */
    public function test_trusted_text_enabled() {
        global $USER, $CFG;

        $this->resetAfterTest(true);
        $CFG->enabletrusttext = 1;

        $dangeroustext = '<button>Untrusted text</button>';
        $cleantext = 'Untrusted text';

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();
        $user1 = self::getDataGenerator()->create_user();

        // First forum with tracking off.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->type = 'qanda';
        $forum = self::getDataGenerator()->create_module('forum', $record);
        $context = \context_module::instance($forum->cmid);

        // Add discussions to the forums.
        $discussionrecord = new \stdClass();
        $discussionrecord->course = $course->id;
        $discussionrecord->userid = $user1->id;
        $discussionrecord->forum = $forum->id;
        $discussionrecord->message = $dangeroustext;
        $discussionrecord->messagetrust  = trusttext_trusted($context);
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($discussionrecord);

        self::setAdminUser();
        $discussionrecord->userid = $USER->id;
        $discussionrecord->messagetrust  = trusttext_trusted($context);
        $discussion2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($discussionrecord);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(2, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);
        // Admin message is fully trusted.
        $this->assertEquals(1, $discussions['discussions'][0]['messagetrust']);
        $this->assertEquals($dangeroustext, $discussions['discussions'][0]['message']);
        // Student message is not trusted.
        $this->assertEquals(0, $discussions['discussions'][1]['messagetrust']);
        $this->assertEquals($cleantext, $discussions['discussions'][1]['message']);

        // Get posts now.
        $posts = mod_forum_external::get_discussion_posts($discussion2->id, 'modified', 'DESC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // Admin message is fully trusted.
        $this->assertEquals($dangeroustext, $posts['posts'][0]['message']);

        $posts = mod_forum_external::get_discussion_posts($discussion1->id, 'modified', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // Student message is not trusted.
        $this->assertEquals($cleantext, $posts['posts'][0]['message']);
    }

    /**
     * Test trusted text disabled.
     */
    public function test_trusted_text_disabled() {
        global $USER, $CFG;

        $this->resetAfterTest(true);
        $CFG->enabletrusttext = 0;

        $dangeroustext = '<button>Untrusted text</button>';
        $cleantext = 'Untrusted text';

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();
        $user1 = self::getDataGenerator()->create_user();

        // First forum with tracking off.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->type = 'qanda';
        $forum = self::getDataGenerator()->create_module('forum', $record);
        $context = \context_module::instance($forum->cmid);

        // Add discussions to the forums.
        $discussionrecord = new \stdClass();
        $discussionrecord->course = $course->id;
        $discussionrecord->userid = $user1->id;
        $discussionrecord->forum = $forum->id;
        $discussionrecord->message = $dangeroustext;
        $discussionrecord->messagetrust = trusttext_trusted($context);
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($discussionrecord);

        self::setAdminUser();
        $discussionrecord->userid = $USER->id;
        $discussionrecord->messagetrust = trusttext_trusted($context);
        $discussion2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($discussionrecord);

        $discussions = mod_forum_external::get_forum_discussions($forum->id);
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_returns(), $discussions);

        $this->assertCount(2, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);
        // Admin message is not trusted because enabletrusttext is disabled.
        $this->assertEquals(0, $discussions['discussions'][0]['messagetrust']);
        $this->assertEquals($cleantext, $discussions['discussions'][0]['message']);
        // Student message is not trusted.
        $this->assertEquals(0, $discussions['discussions'][1]['messagetrust']);
        $this->assertEquals($cleantext, $discussions['discussions'][1]['message']);

        // Get posts now.
        $posts = mod_forum_external::get_discussion_posts($discussion2->id, 'modified', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // Admin message is not trusted because enabletrusttext is disabled.
        $this->assertEquals($cleantext, $posts['posts'][0]['message']);

        $posts = mod_forum_external::get_discussion_posts($discussion1->id, 'modified', 'ASC');
        $posts = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $posts);
        // Student message is not trusted.
        $this->assertEquals($cleantext, $posts['posts'][0]['message']);
    }

    /**
     * Test delete a discussion.
     */
    public function test_delete_post_discussion() {
        global $DB;
        $this->resetAfterTest(true);

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);

        // Add a discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $this->setUser($user);
        $result = mod_forum_external::delete_post($discussion->firstpost);
        $result = \external_api::clean_returnvalue(mod_forum_external::delete_post_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEquals(0, $DB->count_records('forum_posts', array('id' => $discussion->firstpost)));
        $this->assertEquals(0, $DB->count_records('forum_discussions', array('id' => $discussion->id)));
    }

    /**
     * Test delete a post.
     */
    public function test_delete_post_post() {
        global $DB;
        $this->resetAfterTest(true);

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);

        // Add a discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $parentpost = $DB->get_record('forum_posts', array('discussion' => $discussion->id));

        // Add a post.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $record->discussion = $discussion->id;
        $record->parent = $parentpost->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $this->setUser($user);
        $result = mod_forum_external::delete_post($post->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::delete_post_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEquals(1, $DB->count_records('forum_posts', array('discussion' => $discussion->id)));
        $this->assertEquals(1, $DB->count_records('forum_discussions', array('id' => $discussion->id)));
    }

    /**
     * Test delete a different user post.
     */
    public function test_delete_post_other_user_post() {
        global $DB;
        $this->resetAfterTest(true);

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);
        self::getDataGenerator()->enrol_user($otheruser->id, $course->id, $role->id);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $parentpost = $DB->get_record('forum_posts', array('discussion' => $discussion->id));

        // Add a post.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $record->discussion = $discussion->id;
        $record->parent = $parentpost->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $this->setUser($otheruser);
        $this->expectExceptionMessage(get_string('cannotdeletepost', 'forum'));
        mod_forum_external::delete_post($post->id);
    }

    /*
     * Test get forum posts by user id.
     */
    public function test_mod_forum_get_discussion_posts_by_userid() {
        global $DB;
        $this->resetAfterTest(true);

        $urlfactory = \mod_forum\local\container::get_url_factory();
        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $postvault = $vaultfactory->get_post_vault();
        $legacydatamapper = \mod_forum\local\container::get_legacy_data_mapper_factory();
        $legacypostmapper = $legacydatamapper->get_post_data_mapper();

        // Create course to add the module.
        $course1 = self::getDataGenerator()->create_course();

        $user1 = self::getDataGenerator()->create_user();
        $user1entity = $entityfactory->get_author_from_stdClass($user1);
        $exporteduser1 = [
            'id' => (int) $user1->id,
            'fullname' => fullname($user1),
            'groups' => [],
            'urls' => [
                'profile' => $urlfactory->get_author_profile_url($user1entity, $course1->id)->out(false),
                'profileimage' => $urlfactory->get_author_profile_image_url($user1entity),
            ],
            'isdeleted' => false,
        ];
        // Create a bunch of other users to post.
        $user2 = self::getDataGenerator()->create_user();
        $user2entity = $entityfactory->get_author_from_stdClass($user2);
        $exporteduser2 = [
            'id' => (int) $user2->id,
            'fullname' => fullname($user2),
            'groups' => [],
            'urls' => [
                'profile' => $urlfactory->get_author_profile_url($user2entity, $course1->id)->out(false),
                'profileimage' => $urlfactory->get_author_profile_image_url($user2entity),
            ],
            'isdeleted' => false,
        ];
        $user2->fullname = $exporteduser2['fullname'];

        $forumgenerator = self::getDataGenerator()->get_plugin_generator('mod_forum');

        // Set the first created user to the test user.
        self::setUser($user1);

        // Forum with tracking off.
        $record = new \stdClass();
        $record->course = $course1->id;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);
        $forum1context = \context_module::instance($forum1->cmid);

        // Add discussions to the forums.
        $time = time();
        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user1->id;
        $record->forum = $forum1->id;
        $record->timemodified = $time + 100;
        $discussion1 = $forumgenerator->create_discussion($record);
        $discussion1firstpost = $postvault->get_first_post_for_discussion_ids([$discussion1->id]);
        $discussion1firstpost = $discussion1firstpost[$discussion1->firstpost];
        $discussion1firstpostobject = $legacypostmapper->to_legacy_object($discussion1firstpost);

        $record = new \stdClass();
        $record->course = $course1->id;
        $record->userid = $user1->id;
        $record->forum = $forum1->id;
        $record->timemodified = $time + 200;
        $discussion2 = $forumgenerator->create_discussion($record);
        $discussion2firstpost = $postvault->get_first_post_for_discussion_ids([$discussion2->id]);
        $discussion2firstpost = $discussion2firstpost[$discussion2->firstpost];
        $discussion2firstpostobject = $legacypostmapper->to_legacy_object($discussion2firstpost);

        // Add 1 reply to the discussion 1 from a different user.
        $record = new \stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user2->id;
        $discussion1reply1 = $forumgenerator->create_post($record);
        $filename = 'shouldbeanimage.jpg';
        // Add a fake inline image to the post.
        $filerecordinline = array(
                'contextid' => $forum1context->id,
                'component' => 'mod_forum',
                'filearea'  => 'post',
                'itemid'    => $discussion1reply1->id,
                'filepath'  => '/',
                'filename'  => $filename,
        );
        $fs = get_file_storage();
        $file1 = $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        // Add 1 reply to the discussion 2 from a different user.
        $record = new \stdClass();
        $record->discussion = $discussion2->id;
        $record->parent = $discussion2->firstpost;
        $record->userid = $user2->id;
        $discussion2reply1 = $forumgenerator->create_post($record);
        $filename = 'shouldbeanimage.jpg';
        // Add a fake inline image to the post.
        $filerecordinline = array(
                'contextid' => $forum1context->id,
                'component' => 'mod_forum',
                'filearea'  => 'post',
                'itemid'    => $discussion2reply1->id,
                'filepath'  => '/',
                'filename'  => $filename,
        );
        $fs = get_file_storage();
        $file2 = $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        // Following line enrol and assign default role id to the user.
        // So the user automatically gets mod/forum:viewdiscussion on all forums of the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        // Changed display period for the discussions in past.
        $discussion = new \stdClass();
        $discussion->id = $discussion1->id;
        $discussion->timestart = $time - 200;
        $discussion->timeend = $time - 100;
        $DB->update_record('forum_discussions', $discussion);
        $discussion = new \stdClass();
        $discussion->id = $discussion2->id;
        $discussion->timestart = $time - 200;
        $discussion->timeend = $time - 100;
        $DB->update_record('forum_discussions', $discussion);
        // Create what we expect to be returned when querying the discussion.
        $expectedposts = array(
            'discussions' => array(),
            'warnings' => array(),
        );

        $isolatedurluser = $urlfactory->get_discussion_view_url_from_discussion_id($discussion1reply1->discussion);
        $isolatedurluser->params(['parent' => $discussion1reply1->id]);
        $isolatedurlparent = $urlfactory->get_discussion_view_url_from_discussion_id($discussion1firstpostobject->discussion);
        $isolatedurlparent->params(['parent' => $discussion1firstpostobject->id]);

        $expectedposts['discussions'][0] = [
            'name' => $discussion1->name,
            'id' => $discussion1->id,
            'timecreated' => $discussion1firstpost->get_time_created(),
            'authorfullname' => $user1entity->get_full_name(),
            'posts' => [
                'userposts' => [
                    [
                        'id' => $discussion1reply1->id,
                        'discussionid' => $discussion1reply1->discussion,
                        'parentid' => $discussion1reply1->parent,
                        'hasparent' => true,
                        'timecreated' => $discussion1reply1->created,
                        'timemodified' => $discussion1reply1->modified,
                        'subject' => $discussion1reply1->subject,
                        'replysubject' => get_string('re', 'mod_forum') . " {$discussion1reply1->subject}",
                        'message' => file_rewrite_pluginfile_urls($discussion1reply1->message, 'pluginfile.php',
                        $forum1context->id, 'mod_forum', 'post', $discussion1reply1->id),
                        'messageformat' => 1,   // This value is usually changed by external_format_text() function.
                        'unread' => null,
                        'isdeleted' => false,
                        'isprivatereply' => false,
                        'haswordcount' => false,
                        'wordcount' => null,
                        'author' => $exporteduser2,
                        'attachments' => [],
                        'messageinlinefiles' => [],
                        'tags' => [],
                        'html' => [
                            'rating' => null,
                            'taglist' => null,
                            'authorsubheading' => $forumgenerator->get_author_subheading_html(
                                (object)$exporteduser2, $discussion1reply1->created)
                        ],
                        'charcount' => null,
                        'capabilities' => [
                            'view' => true,
                            'edit' => true,
                            'delete' => true,
                            'split' => true,
                            'reply' => true,
                            'export' => false,
                            'controlreadstatus' => false,
                            'canreplyprivately' => true,
                            'selfenrol' => false
                        ],
                        'urls' => [
                            'view' => $urlfactory->get_view_post_url_from_post_id(
                                $discussion1reply1->discussion, $discussion1reply1->id)->out(false),
                            'viewisolated' => $isolatedurluser->out(false),
                            'viewparent' => $urlfactory->get_view_post_url_from_post_id(
                                $discussion1reply1->discussion, $discussion1reply1->parent)->out(false),
                            'edit' => (new \moodle_url('/mod/forum/post.php', [
                                'edit' => $discussion1reply1->id
                            ]))->out(false),
                            'delete' => (new \moodle_url('/mod/forum/post.php', [
                                'delete' => $discussion1reply1->id
                            ]))->out(false),
                            'split' => (new \moodle_url('/mod/forum/post.php', [
                                'prune' => $discussion1reply1->id
                            ]))->out(false),
                            'reply' => (new \moodle_url('/mod/forum/post.php#mformforum', [
                                'reply' => $discussion1reply1->id
                            ]))->out(false),
                            'export' => null,
                            'markasread' => null,
                            'markasunread' => null,
                            'discuss' => $urlfactory->get_discussion_view_url_from_discussion_id(
                                $discussion1reply1->discussion)->out(false),
                        ],
                    ]
                ],
                'parentposts' => [
                    [
                        'id' => $discussion1firstpostobject->id,
                        'discussionid' => $discussion1firstpostobject->discussion,
                        'parentid' => null,
                        'hasparent' => false,
                        'timecreated' => $discussion1firstpostobject->created,
                        'timemodified' => $discussion1firstpostobject->modified,
                        'subject' => $discussion1firstpostobject->subject,
                        'replysubject' => get_string('re', 'mod_forum') . " {$discussion1firstpostobject->subject}",
                        'message' => file_rewrite_pluginfile_urls($discussion1firstpostobject->message, 'pluginfile.php',
                            $forum1context->id, 'mod_forum', 'post', $discussion1firstpostobject->id),
                        'messageformat' => 1,   // This value is usually changed by external_format_text() function.
                        'unread' => null,
                        'isdeleted' => false,
                        'isprivatereply' => false,
                        'haswordcount' => false,
                        'wordcount' => null,
                        'author' => $exporteduser1,
                        'attachments' => [],
                        'messageinlinefiles' => [],
                        'tags' => [],
                        'html' => [
                            'rating' => null,
                            'taglist' => null,
                            'authorsubheading' => $forumgenerator->get_author_subheading_html(
                                (object)$exporteduser1, $discussion1firstpostobject->created)
                        ],
                        'charcount' => null,
                        'capabilities' => [
                            'view' => true,
                            'edit' => true,
                            'delete' => true,
                            'split' => false,
                            'reply' => true,
                            'export' => false,
                            'controlreadstatus' => false,
                            'canreplyprivately' => true,
                            'selfenrol' => false
                        ],
                        'urls' => [
                            'view' => $urlfactory->get_view_post_url_from_post_id(
                                $discussion1firstpostobject->discussion, $discussion1firstpostobject->id)->out(false),
                            'viewisolated' => $isolatedurlparent->out(false),
                            'viewparent' => null,
                            'edit' => (new \moodle_url('/mod/forum/post.php', [
                                'edit' => $discussion1firstpostobject->id
                            ]))->out(false),
                            'delete' => (new \moodle_url('/mod/forum/post.php', [
                                'delete' => $discussion1firstpostobject->id
                            ]))->out(false),
                            'split' => null,
                            'reply' => (new \moodle_url('/mod/forum/post.php#mformforum', [
                                'reply' => $discussion1firstpostobject->id
                            ]))->out(false),
                            'export' => null,
                            'markasread' => null,
                            'markasunread' => null,
                            'discuss' => $urlfactory->get_discussion_view_url_from_discussion_id(
                                $discussion1firstpostobject->discussion)->out(false),
                        ],
                    ]
                ],
            ],
        ];

        $isolatedurluser = $urlfactory->get_discussion_view_url_from_discussion_id($discussion2reply1->discussion);
        $isolatedurluser->params(['parent' => $discussion2reply1->id]);
        $isolatedurlparent = $urlfactory->get_discussion_view_url_from_discussion_id($discussion2firstpostobject->discussion);
        $isolatedurlparent->params(['parent' => $discussion2firstpostobject->id]);

        $expectedposts['discussions'][1] = [
            'name' => $discussion2->name,
            'id' => $discussion2->id,
            'timecreated' => $discussion2firstpost->get_time_created(),
            'authorfullname' => $user1entity->get_full_name(),
            'posts' => [
                'userposts' => [
                    [
                        'id' => $discussion2reply1->id,
                        'discussionid' => $discussion2reply1->discussion,
                        'parentid' => $discussion2reply1->parent,
                        'hasparent' => true,
                        'timecreated' => $discussion2reply1->created,
                        'timemodified' => $discussion2reply1->modified,
                        'subject' => $discussion2reply1->subject,
                        'replysubject' => get_string('re', 'mod_forum') . " {$discussion2reply1->subject}",
                        'message' => file_rewrite_pluginfile_urls($discussion2reply1->message, 'pluginfile.php',
                            $forum1context->id, 'mod_forum', 'post', $discussion2reply1->id),
                        'messageformat' => 1,   // This value is usually changed by external_format_text() function.
                        'unread' => null,
                        'isdeleted' => false,
                        'isprivatereply' => false,
                        'haswordcount' => false,
                        'wordcount' => null,
                        'author' => $exporteduser2,
                        'attachments' => [],
                        'messageinlinefiles' => [],
                        'tags' => [],
                        'html' => [
                            'rating' => null,
                            'taglist' => null,
                            'authorsubheading' => $forumgenerator->get_author_subheading_html(
                                (object)$exporteduser2, $discussion2reply1->created)
                        ],
                        'charcount' => null,
                        'capabilities' => [
                            'view' => true,
                            'edit' => true,
                            'delete' => true,
                            'split' => true,
                            'reply' => true,
                            'export' => false,
                            'controlreadstatus' => false,
                            'canreplyprivately' => true,
                            'selfenrol' => false
                        ],
                        'urls' => [
                            'view' => $urlfactory->get_view_post_url_from_post_id(
                                $discussion2reply1->discussion, $discussion2reply1->id)->out(false),
                            'viewisolated' => $isolatedurluser->out(false),
                            'viewparent' => $urlfactory->get_view_post_url_from_post_id(
                                $discussion2reply1->discussion, $discussion2reply1->parent)->out(false),
                            'edit' => (new \moodle_url('/mod/forum/post.php', [
                                'edit' => $discussion2reply1->id
                            ]))->out(false),
                            'delete' => (new \moodle_url('/mod/forum/post.php', [
                                'delete' => $discussion2reply1->id
                            ]))->out(false),
                            'split' => (new \moodle_url('/mod/forum/post.php', [
                                'prune' => $discussion2reply1->id
                            ]))->out(false),
                            'reply' => (new \moodle_url('/mod/forum/post.php#mformforum', [
                                'reply' => $discussion2reply1->id
                            ]))->out(false),
                            'export' => null,
                            'markasread' => null,
                            'markasunread' => null,
                            'discuss' => $urlfactory->get_discussion_view_url_from_discussion_id(
                                $discussion2reply1->discussion)->out(false),
                        ],
                    ]
                ],
                'parentposts' => [
                    [
                        'id' => $discussion2firstpostobject->id,
                        'discussionid' => $discussion2firstpostobject->discussion,
                        'parentid' => null,
                        'hasparent' => false,
                        'timecreated' => $discussion2firstpostobject->created,
                        'timemodified' => $discussion2firstpostobject->modified,
                        'subject' => $discussion2firstpostobject->subject,
                        'replysubject' => get_string('re', 'mod_forum') . " {$discussion2firstpostobject->subject}",
                        'message' => file_rewrite_pluginfile_urls($discussion2firstpostobject->message, 'pluginfile.php',
                            $forum1context->id, 'mod_forum', 'post', $discussion2firstpostobject->id),
                        'messageformat' => 1,   // This value is usually changed by external_format_text() function.
                        'unread' => null,
                        'isdeleted' => false,
                        'isprivatereply' => false,
                        'haswordcount' => false,
                        'wordcount' => null,
                        'author' => $exporteduser1,
                        'attachments' => [],
                        'messageinlinefiles' => [],
                        'tags' => [],
                        'html' => [
                            'rating' => null,
                            'taglist' => null,
                            'authorsubheading' => $forumgenerator->get_author_subheading_html(
                                (object)$exporteduser1, $discussion2firstpostobject->created)
                        ],
                        'charcount' => null,
                        'capabilities' => [
                            'view' => true,
                            'edit' => true,
                            'delete' => true,
                            'split' => false,
                            'reply' => true,
                            'export' => false,
                            'controlreadstatus' => false,
                            'canreplyprivately' => true,
                            'selfenrol' => false
                        ],
                        'urls' => [
                            'view' => $urlfactory->get_view_post_url_from_post_id(
                                $discussion2firstpostobject->discussion, $discussion2firstpostobject->id)->out(false),
                            'viewisolated' => $isolatedurlparent->out(false),
                            'viewparent' => null,
                            'edit' => (new \moodle_url('/mod/forum/post.php', [
                                'edit' => $discussion2firstpostobject->id
                            ]))->out(false),
                            'delete' => (new \moodle_url('/mod/forum/post.php', [
                                'delete' => $discussion2firstpostobject->id
                            ]))->out(false),
                            'split' => null,
                            'reply' => (new \moodle_url('/mod/forum/post.php#mformforum', [
                                'reply' => $discussion2firstpostobject->id
                            ]))->out(false),
                            'export' => null,
                            'markasread' => null,
                            'markasunread' => null,
                            'discuss' => $urlfactory->get_discussion_view_url_from_discussion_id(
                                $discussion2firstpostobject->discussion)->out(false),

                        ]
                    ],
                ]
            ],
        ];

        // Test discussions with one additional post each (total 2 posts).
        // Also testing that we get the parent posts too.
        $discussions = mod_forum_external::get_discussion_posts_by_userid($user2->id, $forum1->cmid, 'modified', 'DESC');
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_by_userid_returns(), $discussions);

        $this->assertEquals(2, count($discussions['discussions']));

        $this->assertEquals($expectedposts, $discussions);

        // When groupmode is SEPARATEGROUPS, even there is no groupid specified, the post not for the user shouldn't be seen.
        $group1 = self::getDataGenerator()->create_group(['courseid' => $course1->id]);
        $group2 = self::getDataGenerator()->create_group(['courseid' => $course1->id]);
        // Update discussion with group.
        $discussion = new \stdClass();
        $discussion->id = $discussion1->id;
        $discussion->groupid = $group1->id;
        $DB->update_record('forum_discussions', $discussion);
        $discussion = new \stdClass();
        $discussion->id = $discussion2->id;
        $discussion->groupid = $group2->id;
        $DB->update_record('forum_discussions', $discussion);
        $cm = get_coursemodule_from_id('forum', $forum1->cmid);
        $cm->groupmode = SEPARATEGROUPS;
        $DB->update_record('course_modules', $cm);
        $teacher = self::getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'teacher'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($teacher->id, $course1->id, $role->id);
        groups_add_member($group2->id, $teacher->id);
        self::setUser($teacher);
        $discussions = mod_forum_external::get_discussion_posts_by_userid($user2->id, $forum1->cmid, 'modified', 'DESC');
        $discussions = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_by_userid_returns(), $discussions);
        // Discussion is only 1 record (group 2).
        $this->assertEquals(1, count($discussions['discussions']));
        $this->assertEquals($expectedposts['discussions'][1], $discussions['discussions'][0]);
    }

    /**
     * Test get_discussion_post a discussion.
     */
    public function test_get_discussion_post_discussion() {
        global $DB;
        $this->resetAfterTest(true);
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);
        // Add a discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $this->setUser($user);
        $result = mod_forum_external::get_discussion_post($discussion->firstpost);
        $result = \external_api::clean_returnvalue(mod_forum_external::get_discussion_post_returns(), $result);
        $this->assertEquals($discussion->firstpost, $result['post']['id']);
        $this->assertFalse($result['post']['hasparent']);
        $this->assertEquals($discussion->message, $result['post']['message']);
    }

    /**
     * Test get_discussion_post a post.
     */
    public function test_get_discussion_post_post() {
        global $DB;
        $this->resetAfterTest(true);
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);
        // Add a discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $parentpost = $DB->get_record('forum_posts', array('discussion' => $discussion->id));
        // Add a post.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $record->discussion = $discussion->id;
        $record->parent = $parentpost->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);
        $this->setUser($user);
        $result = mod_forum_external::get_discussion_post($post->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::get_discussion_post_returns(), $result);
        $this->assertEquals($post->id, $result['post']['id']);
        $this->assertTrue($result['post']['hasparent']);
        $this->assertEquals($post->message, $result['post']['message']);
    }

    /**
     * Test get_discussion_post a different user post.
     */
    public function test_get_discussion_post_other_user_post() {
        global $DB;
        $this->resetAfterTest(true);
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);
        self::getDataGenerator()->enrol_user($otheruser->id, $course->id, $role->id);
        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $parentpost = $DB->get_record('forum_posts', array('discussion' => $discussion->id));
        // Add a post.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $record->discussion = $discussion->id;
        $record->parent = $parentpost->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);
        // Check other user post.
        $this->setUser($otheruser);
        $result = mod_forum_external::get_discussion_post($post->id);
        $result = \external_api::clean_returnvalue(mod_forum_external::get_discussion_post_returns(), $result);
        $this->assertEquals($post->id, $result['post']['id']);
        $this->assertTrue($result['post']['hasparent']);
        $this->assertEquals($post->message, $result['post']['message']);
    }

    /**
     * Test prepare_draft_area_for_post a different user post.
     */
    public function test_prepare_draft_area_for_post() {
        global $DB;
        $this->resetAfterTest(true);
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);
        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $parentpost = $DB->get_record('forum_posts', array('discussion' => $discussion->id));
        // Add a post.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $record->discussion = $discussion->id;
        $record->parent = $parentpost->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // Add some files only in the attachment area.
        $filename = 'faketxt.txt';
        $filerecordinline = array(
            'contextid' => \context_module::instance($forum->cmid)->id,
            'component' => 'mod_forum',
            'filearea'  => 'attachment',
            'itemid'    => $post->id,
            'filepath'  => '/',
            'filename'  => $filename,
        );
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'fake txt contents 1.');
        $filerecordinline['filename'] = 'otherfaketxt.txt';
        $fs->create_file_from_string($filerecordinline, 'fake txt contents 2.');

        $this->setUser($user);

        // Check attachment area.
        $result = mod_forum_external::prepare_draft_area_for_post($post->id, 'attachment');
        $result = \external_api::clean_returnvalue(mod_forum_external::prepare_draft_area_for_post_returns(), $result);
        $this->assertCount(2, $result['files']);
        $this->assertEquals($filename, $result['files'][0]['filename']);
        $this->assertCount(5, $result['areaoptions']);
        $this->assertEmpty($result['messagetext']);

        // Check again using existing draft item id.
        $result = mod_forum_external::prepare_draft_area_for_post($post->id, 'attachment', $result['draftitemid']);
        $result = \external_api::clean_returnvalue(mod_forum_external::prepare_draft_area_for_post_returns(), $result);
        $this->assertCount(2, $result['files']);

        // Keep only certain files in the area.
        $filestokeep = array(array('filename' => $filename, 'filepath' => '/'));
        $result = mod_forum_external::prepare_draft_area_for_post($post->id, 'attachment', $result['draftitemid'], $filestokeep);
        $result = \external_api::clean_returnvalue(mod_forum_external::prepare_draft_area_for_post_returns(), $result);
        $this->assertCount(1, $result['files']);
        $this->assertEquals($filename, $result['files'][0]['filename']);

        // Check editor (post) area.
        $filerecordinline['filearea'] = 'post';
        $filerecordinline['filename'] = 'fakeimage.png';
        $fs->create_file_from_string($filerecordinline, 'fake image.');
        $result = mod_forum_external::prepare_draft_area_for_post($post->id, 'post');
        $result = \external_api::clean_returnvalue(mod_forum_external::prepare_draft_area_for_post_returns(), $result);
        $this->assertCount(1, $result['files']);
        $this->assertEquals($filerecordinline['filename'], $result['files'][0]['filename']);
        $this->assertCount(5, $result['areaoptions']);
        $this->assertEquals($post->message, $result['messagetext']);
    }

    /**
     * Test update_discussion_post with a discussion.
     */
    public function test_update_discussion_post_discussion() {
        global $DB, $USER;
        $this->resetAfterTest(true);
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $this->setAdminUser();

        // Add a discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $USER->id;
        $record->forum = $forum->id;
        $record->pinned = FORUM_DISCUSSION_UNPINNED;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $subject = 'Hey subject updated';
        $message = 'Hey message updated';
        $messageformat = FORMAT_HTML;
        $options = [
            ['name' => 'pinned', 'value' => true],
        ];

        $result = mod_forum_external::update_discussion_post($discussion->firstpost, $subject, $message, $messageformat,
            $options);
        $result = \external_api::clean_returnvalue(mod_forum_external::update_discussion_post_returns(), $result);
        $this->assertTrue($result['status']);

        // Get the post from WS.
        $result = mod_forum_external::get_discussion_post($discussion->firstpost);
        $result = \external_api::clean_returnvalue(mod_forum_external::get_discussion_post_returns(), $result);
        $this->assertEquals($subject, $result['post']['subject']);
        $this->assertEquals($message, $result['post']['message']);
        $this->assertEquals($messageformat, $result['post']['messageformat']);

        // Get discussion object from DB.
        $discussion = $DB->get_record('forum_discussions', ['id' => $discussion->id]);
        $this->assertEquals($subject, $discussion->name);   // Check discussion subject.
        $this->assertEquals(FORUM_DISCUSSION_PINNED, $discussion->pinned);  // Check discussion pinned.
    }

    /**
     * Test update_discussion_post with a post.
     */
    public function test_update_discussion_post_post() {
        global $DB, $USER;
        $this->resetAfterTest(true);
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid, 0, false, MUST_EXIST);
        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);

        $this->setUser($user);
        // Enable auto subscribe discussion.
        $USER->autosubscribe = true;

        // Add a discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post via WS (so discussion subscription works).
        $result = mod_forum_external::add_discussion_post($discussion->firstpost, 'some subject', 'some text here...');
        $newpost = $result['post'];
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion->id, $cm));

        // Test inline and regular attachment in post
        // Create a file in a draft area for inline attachments.
        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();
        self::setUser($user);
        $usercontext = \context_user::instance($user->id);
        $filepath = '/';
        $filearea = 'draft';
        $component = 'user';
        $filenameimg = 'fakeimage.png';
        $filerecordinline = array(
            'contextid' => $usercontext->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $draftidinlineattach,
            'filepath'  => $filepath,
            'filename'  => $filenameimg,
        );
        $fs = get_file_storage();

        // Create a file in a draft area for regular attachments.
        $filerecordattach = $filerecordinline;
        $attachfilename = 'faketxt.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        // Do not update subject.
        $message = 'Hey message updated';
        $messageformat = FORMAT_HTML;
        $options = [
            ['name' => 'discussionsubscribe', 'value' => false],
            ['name' => 'inlineattachmentsid', 'value' => $draftidinlineattach],
            ['name' => 'attachmentsid', 'value' => $draftidattach],
        ];

        $result = mod_forum_external::update_discussion_post($newpost->id, '', $message, $messageformat, $options);
        $result = \external_api::clean_returnvalue(mod_forum_external::update_discussion_post_returns(), $result);
        $this->assertTrue($result['status']);
        // Check subscription status.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion->id, $cm));

        // Get the post from WS.
        $result = mod_forum_external::get_discussion_posts($discussion->id, 'modified', 'DESC', true);
        $result = \external_api::clean_returnvalue(mod_forum_external::get_discussion_posts_returns(), $result);
        $found = false;
        foreach ($result['posts'] as $post) {
            if ($post['id'] == $newpost->id) {
                $this->assertEquals($newpost->subject, $post['subject']);
                $this->assertEquals($message, $post['message']);
                $this->assertEquals($messageformat, $post['messageformat']);
                $this->assertCount(1, $post['messageinlinefiles']);
                $this->assertEquals('fakeimage.png', $post['messageinlinefiles'][0]['filename']);
                $this->assertCount(1, $post['attachments']);
                $this->assertEquals('faketxt.txt', $post['attachments'][0]['filename']);
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * Test update_discussion_post with other user post (no permissions).
     */
    public function test_update_discussion_post_other_user_post() {
        global $DB, $USER;
        $this->resetAfterTest(true);
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);

        $this->setAdminUser();
        // Add a discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $USER->id;
        $record->forum = $forum->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $USER->id;
        $record->forum = $forum->id;
        $record->discussion = $discussion->id;
        $record->parent = $discussion->firstpost;
        $newpost = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $this->setUser($user);
        $subject = 'Hey subject updated';
        $message = 'Hey message updated';
        $messageformat = FORMAT_HTML;

        $this->expectExceptionMessage(get_string('cannotupdatepost', 'forum'));
        mod_forum_external::update_discussion_post($newpost->id, $subject, $message, $messageformat);
    }

    /**
     * Test that we can update the subject of a post to the string '0'
     */
    public function test_update_discussion_post_set_subject_to_zero(): void {
        global $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion((object) [
            'userid' => $USER->id,
            'course' => $course->id,
            'forum' => $forum->id,
            'name' => 'Test discussion subject',
        ]);

        // Update discussion post subject.
        $result = \external_api::clean_returnvalue(
            mod_forum_external::update_discussion_post_returns(),
            mod_forum_external::update_discussion_post($discussion->firstpost, '0')
        );
        $this->assertTrue($result['status']);

        // Get updated discussion post subject from DB.
        $postsubject = $DB->get_field('forum_posts', 'subject', ['id' => $discussion->firstpost]);
        $this->assertEquals('0', $postsubject);
    }

    /**
     * Test that we can update the message of a post to the string '0'
     */
    public function test_update_discussion_post_set_message_to_zero(): void {
        global $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion((object) [
            'userid' => $USER->id,
            'course' => $course->id,
            'forum' => $forum->id,
            'message' => 'Test discussion message',
            'messageformat' => FORMAT_HTML,
        ]);

        // Update discussion post message.
        $result = \external_api::clean_returnvalue(
            mod_forum_external::update_discussion_post_returns(),
            mod_forum_external::update_discussion_post($discussion->firstpost, '', '0', FORMAT_HTML)
        );
        $this->assertTrue($result['status']);

        // Get updated discussion post subject from DB.
        $postmessage = $DB->get_field('forum_posts', 'message', ['id' => $discussion->firstpost]);
        $this->assertEquals('0', $postmessage);
    }

    /**
     * Test that we can update the message format of a post to {@see FORMAT_MOODLE}
     */
    public function test_update_discussion_post_set_message_format_moodle(): void {
        global $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion((object) [
            'userid' => $USER->id,
            'course' => $course->id,
            'forum' => $forum->id,
            'message' => 'Test discussion message',
            'messageformat' => FORMAT_HTML,
        ]);

        // Update discussion post message & messageformat.
        $result = \external_api::clean_returnvalue(
            mod_forum_external::update_discussion_post_returns(),
            mod_forum_external::update_discussion_post($discussion->firstpost, '', 'Update discussion message', FORMAT_MOODLE)
        );
        $this->assertTrue($result['status']);

        // Get updated discussion post from DB.
        $updatedpost = $DB->get_record('forum_posts', ['id' => $discussion->firstpost], 'message,messageformat');
        $this->assertEquals((object) [
            'message' => 'Update discussion message',
            'messageformat' => FORMAT_MOODLE,
        ], $updatedpost);
    }
}

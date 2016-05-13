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
 * The module forums external functions unit tests
 *
 * @package    mod_forum
 * @category   external
 * @copyright  2012 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

class mod_forum_external_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;

        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();

        require_once($CFG->dirroot . '/mod/forum/externallib.php');
    }

    public function tearDown() {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
    }

    /**
     * Test get forums
     */
    public function test_mod_forum_get_forums_by_courses() {
        global $USER, $CFG, $DB;

        $this->resetAfterTest(true);

        // Create a user.
        $user = self::getDataGenerator()->create_user();

        // Set to the user.
        self::setUser($user);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        // First forum.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course1->id;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);

        // Second forum.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course2->id;
        $forum2 = self::getDataGenerator()->create_module('forum', $record);

        // Add discussions to the forums.
        $record = new stdClass();
        $record->course = $course1->id;
        $record->userid = $user->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        // Expect one discussion.
        $forum1->numdiscussions = 1;
        $forum1->cancreatediscussions = true;

        $record = new stdClass();
        $record->course = $course2->id;
        $record->userid = $user->id;
        $record->forum = $forum2->id;
        $discussion2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $discussion3 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        // Expect two discussions.
        $forum2->numdiscussions = 2;
        // Default limited role, no create discussion capability enabled.
        $forum2->cancreatediscussions = false;

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
        $context2 = context_module::instance($cm2->id);
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
        $forums = external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertCount(2, $forums);
        foreach ($forums as $forum) {
            $this->assertEquals($expectedforums[$forum['id']], $forum);
        }

        // Call the external function without passing course id.
        $forums = mod_forum_external::get_forums_by_courses();
        $forums = external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertCount(2, $forums);
        foreach ($forums as $forum) {
            $this->assertEquals($expectedforums[$forum['id']], $forum);
        }

        // Unenrol user from second course and alter expected forums.
        $enrol->unenrol_user($instance2, $user->id);
        unset($expectedforums[$forum2->id]);

        // Call the external function without passing course id.
        $forums = mod_forum_external::get_forums_by_courses();
        $forums = external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertCount(1, $forums);
        $this->assertEquals($expectedforums[$forum1->id], $forums[0]);
        $this->assertTrue($forums[0]['cancreatediscussions']);

        // Change the type of the forum, the user shouldn't be able to add discussions.
        $DB->set_field('forum', 'type', 'news', array('id' => $forum1->id));
        $forums = mod_forum_external::get_forums_by_courses();
        $forums = external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertFalse($forums[0]['cancreatediscussions']);

        // Call for the second course we unenrolled the user from.
        $forums = mod_forum_external::get_forums_by_courses(array($course2->id));
        $forums = external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);
        $this->assertCount(0, $forums);
    }

    /**
     * Test get forum posts
     */
    public function test_mod_forum_get_forum_discussion_posts() {
        global $CFG, $PAGE;

        $this->resetAfterTest(true);

        // Set the CFG variable to allow track forums.
        $CFG->forum_trackreadposts = true;

        // Create a user who can track forums.
        $record = new stdClass();
        $record->trackforums = true;
        $user1 = self::getDataGenerator()->create_user($record);
        // Create a bunch of other users to post.
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Set the first created user to the test user.
        self::setUser($user1);

        // Create course to add the module.
        $course1 = self::getDataGenerator()->create_course();

        // Forum with tracking off.
        $record = new stdClass();
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);
        $forum1context = context_module::instance($forum1->cmid);

        // Add discussions to the forums.
        $record = new stdClass();
        $record->course = $course1->id;
        $record->userid = $user1->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $record = new stdClass();
        $record->course = $course1->id;
        $record->userid = $user2->id;
        $record->forum = $forum1->id;
        $discussion2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add 2 replies to the discussion 1 from different users.
        $record = new stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user2->id;
        $discussion1reply1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $record->parent = $discussion1reply1->id;
        $record->userid = $user3->id;
        $discussion1reply2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // Enrol the user in the  course.
        $enrol = enrol_get_plugin('manual');
        // Following line enrol and assign default role id to the user.
        // So the user automatically gets mod/forum:viewdiscussion on all forums of the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // Delete one user, to test that we still receive posts by this user.
        delete_user($user3);

        // Create what we expect to be returned when querying the discussion.
        $expectedposts = array(
            'posts' => array(),
            'warnings' => array(),
        );

        // User pictures are initially empty, we should get the links once the external function is called.
        $expectedposts['posts'][] = array(
            'id' => $discussion1reply2->id,
            'discussion' => $discussion1reply2->discussion,
            'parent' => $discussion1reply2->parent,
            'userid' => (int) $discussion1reply2->userid,
            'created' => $discussion1reply2->created,
            'modified' => $discussion1reply2->modified,
            'mailed' => $discussion1reply2->mailed,
            'subject' => $discussion1reply2->subject,
            'message' => file_rewrite_pluginfile_urls($discussion1reply2->message, 'pluginfile.php',
                    $forum1context->id, 'mod_forum', 'post', $discussion1reply2->id),
            'messageformat' => 1,   // This value is usually changed by external_format_text() function.
            'messagetrust' => $discussion1reply2->messagetrust,
            'attachment' => $discussion1reply2->attachment,
            'totalscore' => $discussion1reply2->totalscore,
            'mailnow' => $discussion1reply2->mailnow,
            'children' => array(),
            'canreply' => true,
            'postread' => false,
            'userfullname' => fullname($user3),
            'userpictureurl' => ''
        );

        $expectedposts['posts'][] = array(
            'id' => $discussion1reply1->id,
            'discussion' => $discussion1reply1->discussion,
            'parent' => $discussion1reply1->parent,
            'userid' => (int) $discussion1reply1->userid,
            'created' => $discussion1reply1->created,
            'modified' => $discussion1reply1->modified,
            'mailed' => $discussion1reply1->mailed,
            'subject' => $discussion1reply1->subject,
            'message' => file_rewrite_pluginfile_urls($discussion1reply1->message, 'pluginfile.php',
                    $forum1context->id, 'mod_forum', 'post', $discussion1reply1->id),
            'messageformat' => 1,   // This value is usually changed by external_format_text() function.
            'messagetrust' => $discussion1reply1->messagetrust,
            'attachment' => $discussion1reply1->attachment,
            'totalscore' => $discussion1reply1->totalscore,
            'mailnow' => $discussion1reply1->mailnow,
            'children' => array($discussion1reply2->id),
            'canreply' => true,
            'postread' => false,
            'userfullname' => fullname($user2),
            'userpictureurl' => ''
        );

        // Test a discussion with two additional posts (total 3 posts).
        $posts = mod_forum_external::get_forum_discussion_posts($discussion1->id, 'modified', 'DESC');
        $posts = external_api::clean_returnvalue(mod_forum_external::get_forum_discussion_posts_returns(), $posts);
        $this->assertEquals(3, count($posts['posts']));

        // Generate here the pictures because we need to wait to the external function to init the theme.
        $userpicture = new user_picture($user3);
        $userpicture->size = 1; // Size f1.
        $expectedposts['posts'][0]['userpictureurl'] = $userpicture->get_url($PAGE)->out(false);

        $userpicture = new user_picture($user2);
        $userpicture->size = 1; // Size f1.
        $expectedposts['posts'][1]['userpictureurl'] = $userpicture->get_url($PAGE)->out(false);

        // Unset the initial discussion post.
        array_pop($posts['posts']);
        $this->assertEquals($expectedposts, $posts);

        // Test discussion without additional posts. There should be only one post (the one created by the discussion).
        $posts = mod_forum_external::get_forum_discussion_posts($discussion2->id, 'modified', 'DESC');
        $posts = external_api::clean_returnvalue(mod_forum_external::get_forum_discussion_posts_returns(), $posts);
        $this->assertEquals(1, count($posts['posts']));

    }

    /**
     * Test get forum posts (qanda forum)
     */
    public function test_mod_forum_get_forum_discussion_posts_qanda() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $record = new stdClass();
        $user1 = self::getDataGenerator()->create_user($record);
        $user2 = self::getDataGenerator()->create_user();

        // Set the first created user to the test user.
        self::setUser($user1);

        // Create course to add the module.
        $course1 = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // Forum with tracking off.
        $record = new stdClass();
        $record->course = $course1->id;
        $record->type = 'qanda';
        $forum1 = self::getDataGenerator()->create_module('forum', $record);
        $forum1context = context_module::instance($forum1->cmid);

        // Add discussions to the forums.
        $record = new stdClass();
        $record->course = $course1->id;
        $record->userid = $user2->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add 1 reply (not the actual user).
        $record = new stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user2->id;
        $discussion1reply1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // We still see only the original post.
        $posts = mod_forum_external::get_forum_discussion_posts($discussion1->id, 'modified', 'DESC');
        $posts = external_api::clean_returnvalue(mod_forum_external::get_forum_discussion_posts_returns(), $posts);
        $this->assertEquals(1, count($posts['posts']));

        // Add a new reply, the user is going to be able to see only the original post and their new post.
        $record = new stdClass();
        $record->discussion = $discussion1->id;
        $record->parent = $discussion1->firstpost;
        $record->userid = $user1->id;
        $discussion1reply2 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $posts = mod_forum_external::get_forum_discussion_posts($discussion1->id, 'modified', 'DESC');
        $posts = external_api::clean_returnvalue(mod_forum_external::get_forum_discussion_posts_returns(), $posts);
        $this->assertEquals(2, count($posts['posts']));

        // Now, we can fake the time of the user post, so he can se the rest of the discussion posts.
        $discussion1reply2->created -= $CFG->maxeditingtime * 2;
        $DB->update_record('forum_posts', $discussion1reply2);

        $posts = mod_forum_external::get_forum_discussion_posts($discussion1->id, 'modified', 'DESC');
        $posts = external_api::clean_returnvalue(mod_forum_external::get_forum_discussion_posts_returns(), $posts);
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
        $record = new stdClass();
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
        $record = new stdClass();
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);

        // Add discussions to the forums.
        $record = new stdClass();
        $record->course = $course1->id;
        $record->userid = $user1->id;
        $record->forum = $forum1->id;
        $discussion1 = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add three replies to the discussion 1 from different users.
        $record = new stdClass();
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
        $context = context_module::instance($cm->id);
        $newrole = create_role('Role 2', 'role2', 'Role 2 description');
        $this->assignUserCapability('mod/forum:viewdiscussion', $context->id, $newrole);

        // Create what we expect to be returned when querying the forums.

        $post1 = $DB->get_record('forum_posts', array('id' => $discussion1->firstpost), '*', MUST_EXIST);

        // User pictures are initially empty, we should get the links once the external function is called.
        $expecteddiscussions = array(
                'id' => $discussion1->firstpost,
                'name' => $discussion1->name,
                'groupid' => $discussion1->groupid,
                'timemodified' => $discussion1reply3->created,
                'usermodified' => $discussion1reply3->userid,
                'timestart' => $discussion1->timestart,
                'timeend' => $discussion1->timeend,
                'discussion' => $discussion1->id,
                'parent' => 0,
                'userid' => $discussion1->userid,
                'created' => $post1->created,
                'modified' => $post1->modified,
                'mailed' => $post1->mailed,
                'subject' => $post1->subject,
                'message' => $post1->message,
                'messageformat' => $post1->messageformat,
                'messagetrust' => $post1->messagetrust,
                'attachment' => $post1->attachment,
                'totalscore' => $post1->totalscore,
                'mailnow' => $post1->mailnow,
                'userfullname' => fullname($user1),
                'usermodifiedfullname' => fullname($user4),
                'userpictureurl' => '',
                'usermodifiedpictureurl' => '',
                'numreplies' => 3,
                'numunread' => 0,
                'pinned' => FORUM_DISCUSSION_UNPINNED
            );

        // Call the external function passing forum id.
        $discussions = mod_forum_external::get_forum_discussions_paginated($forum1->id);
        $discussions = external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);
        $expectedreturn = array(
            'discussions' => array($expecteddiscussions),
            'warnings' => array()
        );

        // Wait the theme to be loaded (the external_api call does that) to generate the user profiles.
        $userpicture = new user_picture($user1);
        $userpicture->size = 1; // Size f1.
        $expectedreturn['discussions'][0]['userpictureurl'] = $userpicture->get_url($PAGE)->out(false);

        $userpicture = new user_picture($user4);
        $userpicture->size = 1; // Size f1.
        $expectedreturn['discussions'][0]['usermodifiedpictureurl'] = $userpicture->get_url($PAGE)->out(false);

        $this->assertEquals($expectedreturn, $discussions);

        // Call without required view discussion capability.
        $this->unassignUserCapability('mod/forum:viewdiscussion', $context->id, $newrole);
        try {
            mod_forum_external::get_forum_discussions_paginated($forum1->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('noviewdiscussionspermission', $e->errorcode);
        }

        // Unenrol user from second course.
        $enrol->unenrol_user($instance1, $user1->id);

        // Call for the second course we unenrolled the user from, make sure exception thrown.
        try {
            mod_forum_external::get_forum_discussions_paginated($forum1->id);
            $this->fail('Exception expected due to being unenrolled from the course.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }
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
        $record = new stdClass();
        $record->course = $course->id;
        $record->type = 'qanda';
        $forum = self::getDataGenerator()->create_module('forum', $record);

        // Add discussions to the forums.
        $discussionrecord = new stdClass();
        $discussionrecord->course = $course->id;
        $discussionrecord->userid = $user2->id;
        $discussionrecord->forum = $forum->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($discussionrecord);

        self::setAdminUser();
        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(1, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);

        self::setUser($user1);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(1, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);

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
        $record = new stdClass();
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record);
        $cm = get_coursemodule_from_id('forum', $forum->cmid, 0, false, MUST_EXIST);
        $forumcontext = context_module::instance($forum->cmid);

        // Add discussions to the forums.
        $record = new stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Try to post (user not enrolled).
        self::setUser($user);
        try {
            mod_forum_external::add_discussion_post($discussion->firstpost, 'some subject', 'some text here...');
            $this->fail('Exception expected due to being unenrolled from the course.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $this->getDataGenerator()->enrol_user($otheruser->id, $course->id);

        $createdpost = mod_forum_external::add_discussion_post($discussion->firstpost, 'some subject', 'some text here...');
        $createdpost = external_api::clean_returnvalue(mod_forum_external::add_discussion_post_returns(), $createdpost);

        $posts = mod_forum_external::get_forum_discussion_posts($discussion->id);
        $posts = external_api::clean_returnvalue(mod_forum_external::get_forum_discussion_posts_returns(), $posts);
        // We receive the discussion and the post.
        $this->assertEquals(2, count($posts['posts']));

        $tested = false;
        foreach ($posts['posts'] as $thispost) {
            if ($createdpost['postid'] == $thispost['id']) {
                $this->assertEquals('some subject', $thispost['subject']);
                $this->assertEquals('some text here...', $thispost['message']);
                $tested = true;
            }
        }
        $this->assertTrue($tested);

        // Test inline and regular attachment in post
        // Create a file in a draft area for inline attachments.
        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();
        self::setUser($user);
        $usercontext = context_user::instance($user->id);
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
        $createdpost = external_api::clean_returnvalue(mod_forum_external::add_discussion_post_returns(), $createdpost);

        $posts = mod_forum_external::get_forum_discussion_posts($discussion->id);
        $posts = external_api::clean_returnvalue(mod_forum_external::get_forum_discussion_posts_returns(), $posts);
        // We receive the discussion and the post.
        // Can't guarantee order of posts during tests.
        $postfound = false;
        foreach ($posts['posts'] as $thispost) {
            if ($createdpost['postid'] == $thispost['id']) {
                $this->assertEquals($createdpost['postid'], $thispost['id']);
                $this->assertEquals($thispost['attachment'], 1, "There should be a non-inline attachment");
                $this->assertCount(1, $thispost['attachments'], "There should be 1 attachment");
                $this->assertEquals($thispost['attachments'][0]['filename'], $attachfilename, "There should be 1 attachment");
                $this->assertContains('pluginfile.php', $thispost['message']);
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
        } catch (moodle_exception $e) {
            $this->assertEquals('nopostforum', $e->errorcode);
        }

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
        $record = new stdClass();
        $record->course = $course->id;
        $record->type = 'news';
        $forum = self::getDataGenerator()->create_module('forum', $record);

        self::setUser($user1);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
            $this->fail('Exception expected due to invalid permissions.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        self::setAdminUser();
        $createddiscussion = mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
        $createddiscussion = external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $createddiscussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

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
        $discussions = external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);
        $this->assertCount(3, $discussions['discussions']);
        $this->assertEquals($discussion2pinned['discussionid'], $discussions['discussions'][0]['discussion']);

        // Test inline and regular attachment in new discussion
        // Create a file in a draft area for inline attachments.

        $fs = get_file_storage();

        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();

        $usercontext = context_user::instance($USER->id);
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
        $createddiscussion = external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $createddiscussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(4, $discussions['discussions']);
        $this->assertCount(0, $createddiscussion['warnings']);
        // Can't guarantee order of posts during tests.
        $postfound = false;
        foreach ($discussions['discussions'] as $thisdiscussion) {
            if ($createddiscussion['discussionid'] == $thisdiscussion['discussion']) {
                $this->assertEquals($thisdiscussion['attachment'], 1, "There should be a non-inline attachment");
                $this->assertCount(1, $thisdiscussion['attachments'], "There should be 1 attachment");
                $this->assertEquals($thisdiscussion['attachments'][0]['filename'], $attachfilename, "There should be 1 attachment");
                $this->assertNotContains('draftfile.php', $thisdiscussion['message']);
                $this->assertContains('pluginfile.php', $thisdiscussion['message']);
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
        $record = new stdClass();
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record, array('groupmode' => SEPARATEGROUPS));

        // Try to post (user not enrolled).
        self::setUser($user);

        // The user is not enroled in any group, try to post in a forum with separate groups.
        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
            $this->fail('Exception expected due to invalid group permissions.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...', 0);
            $this->fail('Exception expected due to invalid group permissions.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        // Create a group.
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        // Try to post in a group the user is not enrolled.
        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...', $group->id);
            $this->fail('Exception expected due to invalid group permissions.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        // Add the user to a group.
        groups_add_member($group->id, $user->id);

        // Try to post in a group the user is not enrolled.
        try {
            mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...', $group->id + 1);
            $this->fail('Exception expected due to invalid group.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotcreatediscussion', $e->errorcode);
        }

        // Nost add the discussion using a valid group.
        $discussion = mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...', $group->id);
        $discussion = external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $discussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(1, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);
        $this->assertEquals($discussion['discussionid'], $discussions['discussions'][0]['discussion']);
        $this->assertEquals($group->id, $discussions['discussions'][0]['groupid']);

        // Now add a discussions without indicating a group. The function should guess the correct group.
        $discussion = mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
        $discussion = external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $discussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(2, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);
        $this->assertEquals($group->id, $discussions['discussions'][0]['groupid']);
        $this->assertEquals($group->id, $discussions['discussions'][1]['groupid']);

        // Enrol the same user in other group.
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group2->id, $user->id);

        // Now add a discussions without indicating a group. The function should guess the correct group (the first one).
        $discussion = mod_forum_external::add_discussion($forum->id, 'the subject', 'some text here...');
        $discussion = external_api::clean_returnvalue(mod_forum_external::add_discussion_returns(), $discussion);

        $discussions = mod_forum_external::get_forum_discussions_paginated($forum->id);
        $discussions = external_api::clean_returnvalue(mod_forum_external::get_forum_discussions_paginated_returns(), $discussions);

        $this->assertCount(3, $discussions['discussions']);
        $this->assertCount(0, $discussions['warnings']);
        $this->assertEquals($group->id, $discussions['discussions'][0]['groupid']);
        $this->assertEquals($group->id, $discussions['discussions'][1]['groupid']);
        $this->assertEquals($group->id, $discussions['discussions'][2]['groupid']);

    }

    /*
     * Test can_add_discussion. A basic test since all the API functions are already covered by unit tests.
     */
    public function test_can_add_discussion() {

        $this->resetAfterTest(true);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        $user = self::getDataGenerator()->create_user();

        // First forum with tracking off.
        $record = new stdClass();
        $record->course = $course->id;
        $record->type = 'news';
        $forum = self::getDataGenerator()->create_module('forum', $record);

        // User with no permissions to add in a news forum.
        self::setUser($user);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $result = mod_forum_external::can_add_discussion($forum->id);
        $result = external_api::clean_returnvalue(mod_forum_external::can_add_discussion_returns(), $result);
        $this->assertFalse($result['status']);

        self::setAdminUser();
        $result = mod_forum_external::can_add_discussion($forum->id);
        $result = external_api::clean_returnvalue(mod_forum_external::can_add_discussion_returns(), $result);
        $this->assertTrue($result['status']);

    }

}

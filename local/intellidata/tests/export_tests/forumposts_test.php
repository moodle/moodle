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
 * Forum posts migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\generator;
use local_intellidata\setup_helper;
use local_intellidata\test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');
require_once($CFG->dirroot . '/mod/forum/externallib.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * Forum posts migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class forumposts_test extends custom_db_client_testcase {

    /**
     * Test forum post create.
     *
     * @covers \local_intellidata\entities\forums\forumpost
     * @covers \local_intellidata\entities\forums\postsmigration
     * @covers \local_intellidata\entities\forums\observer::post_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_forumpost_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_forumpost_test(0);
    }

    /**
     * Test forum post update.
     *
     * @covers \local_intellidata\entities\forums\forumpost
     * @covers \local_intellidata\entities\forums\postsmigration
     * @covers \local_intellidata\entities\forums\observer::post_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_forumpost_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_forumpost_test(0);
    }

    /**
     * Test forum post delete.
     *
     * @covers \local_intellidata\entities\forums\forumpost
     * @covers \local_intellidata\entities\forums\postsmigration
     * @covers \local_intellidata\entities\forums\observer::post_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_forumpost_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_forumpost_test(0);
    }

    /**
     * Delete forum post test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function delete_forumpost_test($tracking) {
        global $DB;

        $userdata = [
            'username' => 'ibforumuserforumpost1' . $tracking,
        ];
        $user = $DB->get_record('user', $userdata);

        $coursedata = [
            'fullname' => 'ibcourseforumpost1' . $tracking,
            'idnumber' => '3333333' . $tracking,
        ];
        $course = $DB->get_record('course', $coursedata);

        $forumdata = [
            'course' => $course->id,
        ];
        $forum = $DB->get_record('forum', $forumdata);

        $cm = get_coursemodule_from_instance('forum', $forum->id, $forum->course);

        // Add a discussion.
        $record = [];
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = generator::get_plugin_generator('mod_forum')->create_discussion($record);

        // When creating a discussion we also create a post, so get the post.
        $discussionpost = $DB->get_records('forum_posts');
        // Will only be one here.
        $discussionpost = reset($discussionpost);

        // Add a few posts.
        $record = [];
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $posts = [];
        $posts[$discussionpost->id] = $discussionpost;
        for ($i = 0; $i < 3; $i++) {
            $post = generator::get_plugin_generator('mod_forum')->create_post($record);
            $posts[$post->id] = $post;
        }

        // Delete the last post and capture the event.
        $lastpost = end($posts);
        forum_delete_post($lastpost, true, $course, $cm, $forum);

        $data = [
            'id' => $lastpost->id,
        ];

        $entity = new \local_intellidata\entities\forums\forumpost((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'forumposts']);
        $datarecord = $storage->get_log_entity_data('d', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Update forum post test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_forumpost_test($tracking) {
        global $DB;

        $userdata = [
            'username' => 'ibforumuserforumpost1' . $tracking,
        ];
        $user = $DB->get_record('user', $userdata);

        $coursedata = [
            'fullname' => 'ibcourseforumpost1' . $tracking,
            'idnumber' => '3333333' . $tracking,
        ];
        $course = $DB->get_record('course', $coursedata);

        $forumdata = [
            'course' => $course->id,
        ];
        $forum = $DB->get_record('forum', $forumdata);

        // Add a discussion.
        $record = [];
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = generator::get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = [];
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = generator::get_plugin_generator('mod_forum')->create_post($record);

        $context = forum_get_context($forum->id);

        $params = [
            'context' => $context,
            'objectid' => $post->id,
            'other' => [
                'discussionid' => $discussion->id,
                'forumid' => $forum->id,
                'forumtype' => $forum->type,
            ],
        ];

        $event = \mod_forum\event\post_updated::create($params);
        $event->trigger();

        $data = [
            'id' => $post->id,
            'userid' => $user->id,
            'forum' => $forum->id,
            'discussion' => $discussion->id,
        ];

        $entity = new \local_intellidata\entities\forums\forumpost((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'forumposts']);

        $datarecord = $storage->get_log_entity_data($tracking == 1 ? 'c' : 'u', $data);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create forum post test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_forumpost_test($tracking) {
        $userdata = [
            'firstname' => 'ibforumuserforumpost1',
            'username' => 'ibforumuserforumpost1' . $tracking,
            'password' => 'Ibforumuserforumpost1!',
        ];
        $user = generator::create_user($userdata);

        $coursedata = [
            'fullname' => 'ibcourseforumpost1' . $tracking,
            'idnumber' => '3333333' . $tracking,
        ];
        $course = generator::create_course($coursedata);

        $forumdata = [
            'course' => $course->id,
        ];
        $forum = generator::create_module('forum', $forumdata);

        // Add a discussion.
        $record = [];
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = generator::get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = [];
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = generator::get_plugin_generator('mod_forum')->create_post($record);

        $context = forum_get_context($forum->id);

        $params = [
            'context' => $context,
            'objectid' => $post->id,
            'other' => [
                'discussionid' => $discussion->id,
                'forumid' => $forum->id,
                'forumtype' => $forum->type,
            ],
        ];

        // Create the event.
        $event = \mod_forum\event\post_created::create($params);
        $event->trigger();

        $data = [
            'id' => $post->id,
            'userid' => $user->id,
            'forum' => $forum->id,
            'discussion' => $discussion->id,
        ];

        $entity = new \local_intellidata\entities\forums\forumpost((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'forumposts']);

        $datarecord = $storage->get_log_entity_data('c', $data);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}

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
 * Forum discussion migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

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

/**
 * Forum discussion migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class forumdiscussions_test extends \advanced_testcase {

    public function setUp(): void {
        $this->setAdminUser();

        setup_helper::setup_tests_config();
    }

    /**
     * Test forum discussion create.
     *
     * @covers \local_intellidata\entities\forums\forumdiscussion
     * @covers \local_intellidata\entities\forums\discussionsmigration
     * @covers \local_intellidata\entities\forums\observer::discussion_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        $userdata = [
            'firstname' => 'ibforumuserforumdiscussion1',
            'username' => 'ibforumuserforumdiscussion1',
            'password' => 'Ibforumuserforumdiscussion1!',
        ];
        $user = generator::create_user($userdata);

        $coursedata = [
            'fullname' => 'ibcourseforumdiscussion1',
            'idnumber' => '44444444',
            'shortname' => 'ibcourseforumdiscussion1',
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

        $context = forum_get_context($forum->id);

        $params = [
            'context' => $context,
            'objectid' => $discussion->id,
            'other' => ['forumid' => $forum->id],
        ];

        // Create the event.
        $event = \mod_forum\event\discussion_created::create($params);
        $event->trigger();

        $data = [
            'id' => $discussion->id,
            'forum' => $forum->id,
        ];

        $entity = new \local_intellidata\entities\forums\forumdiscussion((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'forumdiscussions']);
        $datarecord = $storage->get_log_entity_data('c', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Test forum discussion update.
     *
     * @covers \local_intellidata\entities\forums\forumdiscussion
     * @covers \local_intellidata\entities\forums\discussionsmigration
     * @covers \local_intellidata\entities\forums\observer::discussion_updated
     */
    public function test_update() {
        global $DB;

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        $userdata = [
            'username' => 'ibforumuserforumdiscussion1',
        ];
        $user = $DB->get_record('user', $userdata);

        $coursedata = [
            'fullname' => 'ibcourseforumdiscussion1',
            'idnumber' => '44444444',
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

        $context = forum_get_context($forum->id);

        $params = [
            'context' => $context,
            'objectid' => $discussion->id,
            'other' => ['forumid' => $forum->id],
        ];

        // Create the event.
        $event = \mod_forum\event\discussion_updated::create($params);
        $event->trigger();

        $data = [
            'id' => $discussion->id,
            'forum' => $forum->id,
        ];

        $entity = new \local_intellidata\entities\forums\forumdiscussion((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'forumdiscussions']);
        $datarecord = $storage->get_log_entity_data('u', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Test forum discussion move.
     *
     * @covers \local_intellidata\entities\forums\forumdiscussion
     * @covers \local_intellidata\entities\forums\discussionsmigration
     * @covers \local_intellidata\entities\forums\observer::discussion_moved
     */
    public function test_move() {
        global $DB;

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        $userdata = [
            'username' => 'ibforumuserforumdiscussion1',
        ];
        $user = $DB->get_record('user', $userdata);

        $fromcoursedata = [
            'fullname' => 'ibcourseforumdiscussion1',
            'idnumber' => '44444444',
        ];
        $fromcourse = $DB->get_record('course', $fromcoursedata);

        $fromforumdata = [
            'course' => $fromcourse->id,
        ];
        $fromforum = $DB->get_record('forum', $fromforumdata);

        $tocourse = generator::create_course([
            'shortname' => 'ibcourseforumdiscussionm1',
        ]);

        $toforumdata = [
            'course' => $tocourse->id,
        ];
        $toforum = generator::create_module('forum', $toforumdata);

        // Add a discussion.
        $record = [];
        $record['course'] = $fromcourse->id;
        $record['forum'] = $fromforum->id;
        $record['userid'] = $user->id;
        $discussion = generator::get_plugin_generator('mod_forum')->create_discussion($record);

        $context = forum_get_context($toforum->id);

        $params = [
            'context' => $context,
            'objectid' => $discussion->id,
            'other' => ['fromforumid' => $fromforum->id, 'toforumid' => $toforum->id],
        ];

        // Create the event.
        $event = \mod_forum\event\discussion_moved::create($params);
        $event->trigger();

        $data = [
            'id' => $discussion->id,
            'forum' => $toforum->id,
        ];

        $entity = new \local_intellidata\entities\forums\forumdiscussion((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'forumdiscussions']);
        $datarecord = $storage->get_log_entity_data('u', $data);
        $datarecorddata = json_decode($datarecord->data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }

    /**
     * Test forum discussion delete.
     *
     * @covers \local_intellidata\entities\forums\forumdiscussion
     * @covers \local_intellidata\entities\forums\discussionsmigration
     * @covers \local_intellidata\entities\forums\observer::discussion_deleted
     */
    public function test_delete() {
        global $DB;

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        $userdata = [
            'username' => 'ibforumuserforumdiscussion1',
        ];
        $user = $DB->get_record('user', $userdata);

        $coursedata = [
            'fullname' => 'ibcourseforumdiscussion1',
            'idnumber' => '44444444',
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

        $context = forum_get_context($forum->id);

        $params = [
            'context' => $context,
            'objectid' => $discussion->id,
            'other' => ['forumid' => $forum->id],
        ];

        // Create the event.
        $event = \mod_forum\event\discussion_deleted::create($params);
        $event->trigger();

        $data = [
            'id' => $discussion->id,
        ];

        $entity = new \local_intellidata\entities\forums\forumdiscussion((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'forumdiscussions']);
        $datarecord = $storage->get_log_entity_data('d', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}

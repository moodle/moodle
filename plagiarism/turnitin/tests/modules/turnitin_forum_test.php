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
 * Unit tests for (some of) plagiarism/turnitin/classes/modules/turnitin_forum.class.php.
 *
 * @package    plagiarism_turnitin
 * @copyright  2017 Turnitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_turnitin;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/plagiarism/turnitin/lib.php');

use PHPUnit\Framework\Attributes\CoversFunction;

/**
 * Tests for API comms class
 *
 * @package turnitin
 */
#[CoversFunction('\turnitin_forum::set_content')]
final class turnitin_forum_test extends \advanced_testcase {

    /** @var stdClass created in setUp. */
    protected $forum;

    /** @var stdClass created in setUp. */
    protected $discussion;

    /** @var stdClass created in setUp. */
    protected $post;

    /**
     * Create a course and forum module instance
     */
    public function setUp(): void {
        parent::setUp();

        // Create a course, user and a forum.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $record = new \stdClass();
        $record->course = $course->id;
        $this->forum = $this->getDataGenerator()->create_module('forum', $record);

        // Add discussion to course.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $this->forum->id;
        $this->discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add post to discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $this->forum->id;
        $record->discussion = $this->discussion->id;
        $this->post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);
    }

    /**
     * Test to check that content returned by set content is the same as passed in array.
     */
    public function test_to_check_content_in_array_is_returned_by_set_content(): void {

        $this->resetAfterTest(true);

        // Create module object.
        $moduleobject = new \turnitin_forum();

        $params = [
            'content' => $this->post->message,
        ];

        $content = $moduleobject->set_content($params);
        $this->assertEquals($content, $this->post->message);
    }

    /**
     * Test to check that content returned by set content is taken from database
     * if post id is passed in.
     */
    public function test_to_check_content_from_database_is_returned_by_set_content_if_postid_present(): void {

        $this->resetAfterTest(true);

        // Create module object.
        $moduleobject = new \turnitin_forum();

        $params = [
            'content' => 'content should not come back',
            'postid' => $this->post->id,
        ];

        $content = $moduleobject->set_content($params);
        $this->assertEquals($content, $this->post->message);
    }

}

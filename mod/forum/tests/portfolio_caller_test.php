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

/**
 * Class mod_forum_portfolio_caller_testcase
 *
 * Tests behaviour of the forum_portfolio_caller class.
 *
 * @package    mod_forum
 * @copyright  2018 onwards Totara Learning Solutions LTD {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Brendan Cox <brendan.cox@totaralearning.com>
 */
class portfolio_caller_test extends \advanced_testcase {

    /**
     * Ensure that a file will be loaded in an instance of the caller when supplied valid and
     * accessible post and attachment file ids.
     */
    public function test_file_in_user_post_is_loaded() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/locallib.php');
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = \context_module::instance($forum->cmid);

        /* @var mod_forum_generator $forumgenerator */
        $forumgenerator = $this->getDataGenerator()->get_plugin_generator('mod_forum');
        $discussion = $forumgenerator->create_discussion(
            array(
                'course' => $course->id,
                'forum' => $forum->id,
                'userid' => $user->id,
                'attachment' => 1
            )
        );

        $fs = get_file_storage();
        $dummy = (object) array(
            'contextid' => $context->id,
            'component' => 'mod_forum',
            'filearea' => 'attachment',
            'itemid' => $discussion->firstpost,
            'filepath' => '/',
            'filename' => 'myassignmnent.pdf'
        );
        $firstpostfile = $fs->create_file_from_string($dummy, 'Content of ' . $dummy->filename);

        $caller = new \forum_portfolio_caller(array(
            'postid' => $discussion->firstpost,
            'attachment' => $firstpostfile->get_id()
        ));

        $caller->load_data();
        $this->assertEquals($caller->get_sha1_file(), $firstpostfile->get_contenthash());
    }

    /**
     * Ensure that files will not be loaded if the supplied attachment id is for a file that is not attached to
     * the supplied post id.
     */
    public function test_file_not_in_user_post_not_loaded() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/locallib.php');
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = \context_module::instance($forum->cmid);

        /* @var mod_forum_generator $forumgenerator */
        $forumgenerator = $this->getDataGenerator()->get_plugin_generator('mod_forum');
        $discussion = $forumgenerator->create_discussion(
            array(
                'course' => $course->id,
                'forum' => $forum->id,
                'userid' => $user->id,
                'attachment' => 1
            )
        );

        $fs = get_file_storage();
        $dummyone = (object) array(
            'contextid' => $context->id,
            'component' => 'mod_forum',
            'filearea' => 'attachment',
            'itemid' => $discussion->firstpost,
            'filepath' => '/',
            'filename' => 'myassignmnent.pdf'
        );
        $firstpostfile = $fs->create_file_from_string($dummyone, 'Content of ' . $dummyone->filename);

        // Create a second post and add a file there.
        $secondpost = $forumgenerator->create_post(
            array(
                'discussion' => $discussion->id,
                'userid' => $user->id,
                'attachment' => 1
            )
        );
        $dummytwo = (object) array(
            'contextid' => $context->id,
            'component' => 'mod_forum',
            'filearea' => 'attachment',
            'itemid' => $secondpost->id,
            'filepath' => '/',
            'filename' => 'myotherthing.pdf'
        );
        $secondpostfile = $fs->create_file_from_string($dummytwo, 'Content of ' . $dummytwo->filename);

        $caller = new \forum_portfolio_caller(array(
            'postid' => $discussion->firstpost,
            'attachment' => $secondpostfile->get_id()
        ));

        $this->expectExceptionMessage('Sorry, the requested file could not be found');
        $caller->load_data();
    }
}

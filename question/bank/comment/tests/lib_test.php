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

namespace qbank_comment;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/bank/comment/lib.php');


/**
 * Comment lib unit tests.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    /**
     * Test the comment validation callback.
     */
    public function test_qbank_comment_comment_validate() {
        $commentparams = new \stdClass();
        $commentparams->commentarea = 'question';
        $commentparams->component = 'qbank_comment';

        $isvalid = qbank_comment_comment_validate($commentparams);
        $this->assertTrue($isvalid);

        $this->expectException('comment_exception');
        $commentparams->commentarea = 'core_comment';
        $commentparams->component = 'blog_comment';
        qbank_comment_comment_validate($commentparams);

    }

    /**
     * Test the comment display callback.
     */
    public function test_qbank_comment_comment_display() {
        $comment = new \stdClass();
        $comment->text = 'test';
        $comments = [$comment];

        $commentparams = new \stdClass();
        $commentparams->commentarea = 'question';
        $commentparams->component = 'qbank_comment';

        $responses = qbank_comment_comment_display($comments, $commentparams);
        $this->assertEquals($comment->text, $responses[0]->text);

        $this->expectException('comment_exception');
        $commentparams->commentarea = 'core_comment';
        $commentparams->component = 'blog_comment';
        qbank_comment_comment_display($comments, $commentparams);

    }

    /**
     * Test the comment preview callback.
     */
    public function test_qbank_comment_preview_display() {
        $this->resetAfterTest();
        global $PAGE;
        $PAGE->set_url('/');

        // Make a test question.
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $context = \context_coursecat::instance($category->id);
        $qcat = $qgen->create_question_category(['contextid' => $context->id]);
        $question = $qgen->create_question('shortanswer', null, ['category' => $qcat->id, 'idnumber' => 'q1']);

        $result = qbank_comment_preview_display($question, $course->id);

        // User doesn't have perms so expecting no output.
        $this->assertEmpty($result);

        // Expect output.
        $this->setAdminUser();
        $result = qbank_comment_preview_display($question, $course->id);
        $this->assertStringContainsString('comment-action-post', $result);
    }

    /**
     * Test the comment preview callback.
     */
    public function test_qbank_comment_output_fragment_question_comment() {
        $this->resetAfterTest();
        $this->setAdminUser();
        global $PAGE;
        $PAGE->set_url('/');

        // Make a test question.
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $context = \context_coursecat::instance($category->id);
        $qcat = $qgen->create_question_category(['contextid' => $context->id]);
        $question = $qgen->create_question('shortanswer', null, ['category' => $qcat->id, 'idnumber' => 'q1']);
        $args = [
            'questionid' => $question->id,
            'courseid' => $course->id,
            ];

        $result = qbank_comment_output_fragment_question_comment($args);

        // Expect output.
        $this->assertStringContainsString('comment-action-post', $result);
    }
}

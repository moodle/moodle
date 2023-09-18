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

namespace qbank_comment\event;

/**
 * Tests for question_deleted_observer
 *
 * @package   qbank_comment
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qbank_comment\event\question_deleted_observer
 */
class question_deleted_observer_test extends \advanced_testcase {

    /**
     * Deleting a question with comments should also delete the comments
     *
     * @return void
     */
    public function test_delete_question_with_comments(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        [, , , $questions] = $questiongenerator->setup_course_and_questions();
        $question = reset($questions);

        $context = \context_system::instance();
        $commentgenerator = $this->getDataGenerator()->get_plugin_generator('core_comment');
        /** @var \comment $comment */
        $comment = $commentgenerator->create_comment([
            'context' => $context,
            'component' => 'qbank_comment',
            'area' => 'question',
            'itemid' => $question->id,
            'content' => random_string(),
        ]);

        $this->assertEquals(1, $comment->count());

        question_delete_question($question->id);

        $newcomment = new \comment((object)[
            'context' => $context,
            'component' => 'qbank_comment',
            'area' => 'question',
            'itemid' => $question->id,
        ]);

        $this->assertEquals(0, $newcomment->count());
    }
}

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

namespace qbank_tagquestion\event;

/**
 * Tests for question_deleted_observer
 *
 * @package   qbank_tagquestion
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qbank_tagquestion\event\question_deleted_observer
 */
final class question_deleted_observer_test extends \advanced_testcase {

    /**
     * Deleting a question with tags should also delete the tags.
     *
     * @return void
     */
    public function test_delete_question_with_tags(): void {
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        [, , $qcat, $questions] = $questiongenerator->setup_course_and_questions();
        $questioncontext = \context::instance_by_id($qcat->contextid);
        $question = reset($questions);
        $tag = random_string();
        \core_tag_tag::add_item_tag('core_question', 'question', $question->id, $questioncontext, $tag);

        $this->assertCount(1, \core_tag_tag::get_item_tags('core_question', 'question', $question->id));

        question_delete_question($question->id);

        $this->assertEmpty(\core_tag_tag::get_item_tags('core_question', 'question', $question->id));
    }
}

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

namespace core_question;

/**
 * Unit test for the cleanup_questions_without_categories_task class.
 *
 * @package   core_question
 * @copyright 2026 Martin Gauk <martin.gauk@tu-berlin.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\task\cleanup_questions_without_categories_task
 */
final class cleanup_questions_without_categories_task_test extends \advanced_testcase {
    /**
     * A question with no category should be deleted, while other questions remain as-is.
     */
    public function test_cleanup_questions_without_categories(): void {
        global $DB;
        $this->setAdminUser();
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $topcategory = question_get_top_category($context->id, true);
        $defaultcategory = question_get_default_category($context->id);
        $deletedcategory = $questiongenerator->create_question_category(
            ['contextid' => $context->id, 'parent' => $topcategory->id],
        );
        // Create 2 questions. One in the default category, and  in the category being deleted.
        $question = $questiongenerator->create_question('truefalse', overrides: ['category' => $defaultcategory->id]);
        $orphan = $questiongenerator->create_question('truefalse', overrides: ['category' => $deletedcategory->id]);

        $DB->delete_records('question_categories', ['id' => $deletedcategory->id]);

        $task = new \core\task\cleanup_questions_without_categories_task();
        $task->execute();
        $this->expectOutputRegex('/Cleaned up 1 questions left over from restores./');
        $this->resetDebugging();

        // The default category question is unchanged.
        $this->assertTrue(
            $DB->record_exists_sql(
                "SELECT *
                   FROM {question} q
                   JOIN {question_versions} qv on qv.questionid = q.id
                   JOIN {question_bank_entries} qbe ON qv.questionbankentryid = qbe.id
                  WHERE q.id = :questionid AND qbe.questioncategoryid = :categoryid",
                [
                    'questionid' => $question->id,
                    'categoryid' => $defaultcategory->id,
                ],
            ),
        );
        // The orphaned question has been deleted.
        $this->assertFalse($DB->record_exists('question', ['id' => $orphan->id]));
    }
}

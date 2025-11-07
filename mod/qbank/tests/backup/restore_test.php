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

namespace mod_qbank\backup;

use core\context\module;

/**
 * Tests to cover restoring or import a question bank and its questions multiple times to the same course.
 *
 * When we restore/import/duplicate an activity that publishes questions, all of its questions should
 * be restored into the restored activity, even if they already exist in another activity on the same course.
 *
 * @package   mod_qbank
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \restore_dbops::load_questionbanks_to_tempids
 * @covers \restore_questionbanks_parser_processor
 */
final class restore_test extends \advanced_testcase {
    /**
     * Given a context, find the default question category, return the question IDs and records.
     *
     * @param int $contextid
     * @return array[int[], \stdClass[]] An array of the question ID, and an numerically-indexed array of question records.
     */
    protected function get_questions_in_default_category(int $contextid): array {
        global $DB;
        $records = $DB->get_records_sql(
            "
                SELECT q.*
                  FROM {question} q
                  JOIN {question_versions} qv ON q.id = qv.questionid
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE qc.contextid = :contextid
                       AND qc.name != :top
            ",
            [
                'contextid' => $contextid,
                'top' => 'top',
            ],
        );
        return [array_keys($records), array_values($records)];
    }

    /**
     * Create a qbank containing questions and verify the correct records exist.
     *
     * @param int $courseid
     * @return array
     */
    protected function create_qbank_with_questions(int $courseid): array {
        // Create a quiz with questions in the first course.
        $qbank = $this->getDataGenerator()->get_plugin_generator('mod_qbank')->create_instance(['course' => $courseid]);
        $context = \context_module::instance($qbank->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = question_get_default_category($context->id);

        // Create a short answer question.
        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        // Create a multi-answer question with children.
        $maq = $questiongenerator->create_question('multianswer', 'twosubq', ['category' => $cat->id]);

        // Verify that we have 1 qbank, with a default category containing 4 questions (2, plus 2 children).
        $qbanks = get_fast_modinfo($courseid)->get_instances_of('qbank');
        $this->assertCount(1, $qbanks);
        $qbank1 = reset($qbanks);
        $qbankcontext = module::instance($qbank1->id);
        [, $qbankquestions] = $this->get_questions_in_default_category($qbankcontext->id);
        $this->assertCount(4, $qbankquestions);
        return [$qbank, $saq, $maq];
    }

    /**
     * Importing (duplicating) a bank in the same course should give you a second bank with a copy of all the questions.
     */
    public function test_import_qbank_into_same_course(): void {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');

        [$qbank, $originalsaq, $originalmaq] = $this->create_qbank_with_questions($course1->id);

        // Backup qbank.
        $bc = new \backup_controller(
            \backup::TYPE_1ACTIVITY,
            $qbank->cmid,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $teacher->id,
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup into the same course.
        $rc = new \restore_controller(
            $backupid,
            $course1->id,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $teacher->id,
            \backup::TARGET_CURRENT_ADDING,
        );
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Verify that we now have 2 qbanks.
        $qbanks = get_fast_modinfo($course1->id)->get_instances_of('qbank');
        $this->assertCount(2, $qbanks);
        // The first qbank should be the same as before.
        $qbank1 = reset($qbanks);
        $qbank1context = module::instance($qbank1->id);
        [$qbank1questionids, $qbank1questions] = $this->get_questions_in_default_category($qbank1context->id);
        $this->assertCount(4, $qbank1questions);
        $this->assertContains((int) $originalsaq->id, $qbank1questionids);
        $this->assertContains((int) $originalmaq->id, $qbank1questionids);

        // The second qbank should have its own categories and a copy of each question.
        $qbank2 = end($qbanks);
        $qbank2context = module::instance($qbank2->id);
        [$qbank2questionids, $qbank2questions] = $this->get_questions_in_default_category($qbank2context->id);
        $this->assertCount(4, $qbank2questions);
        foreach ($qbank2questions as $key => $qbank2question) {
            $this->assertNotContains((int) $qbank2question->id, $qbank1questionids);
            $this->assertEquals($qbank2question->questiontext, $qbank1questions[$key]->questiontext);
            if ($qbank2question->parent != 0) {
                // Check that child questions are linked to the parent in the same qbank.
                $this->assertNotContains((int) $qbank2question->parent, $qbank1questionids);
                $this->assertContains((int) $qbank2question->parent, $qbank2questionids);
            }
        }
    }

    /**
     * Importing a bank into a different course multiple times should copy all the questions each time.
     */
    public function test_import_qbank_into_different_course_twice(): void {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $generator->enrol_user($teacher->id, $course2->id, 'editingteacher');

        [$qbank, $originalsaq, $originalmaq] = $this->create_qbank_with_questions($course1->id);

        for ($i = 0, $j = 2; $i < $j; $i++) {
            // Backup qbank.
            $bc = new \backup_controller(
                \backup::TYPE_1ACTIVITY,
                $qbank->cmid,
                \backup::FORMAT_MOODLE,
                \backup::INTERACTIVE_NO,
                \backup::MODE_IMPORT,
                $teacher->id,
            );
            $backupid = $bc->get_backupid();
            $bc->execute_plan();
            $bc->destroy();

            // Restore the backup into another course twice.
            $rc = new \restore_controller(
                $backupid,
                $course2->id,
                \backup::INTERACTIVE_NO,
                \backup::MODE_IMPORT,
                $teacher->id,
                \backup::TARGET_CURRENT_ADDING,
            );
            $rc->execute_precheck();
            $rc->execute_plan();
            $rc->destroy();
        }

        $qbankcontext = module::instance($qbank->cmid);
        [$seenquestionids] = $this->get_questions_in_default_category($qbankcontext->id);

        // Verify that we have 2 qbanks on the destination course, each with its own categories and questions.
        $qbanks = get_fast_modinfo($course2->id)->get_instances_of('qbank');
        $this->assertCount(2, $qbanks);
        foreach ($qbanks as $qbank) {
            $qbankcontext = module::instance($qbank->id);
            [$qbankquestionids, $qbankquestions] = $this->get_questions_in_default_category($qbankcontext->id);
            $this->assertCount(4, $qbankquestions);
            // The new question bank doesn't contain the original questions.
            $this->assertNotContains((int) $originalsaq->id, $qbankquestionids);
            $this->assertNotContains((int) $originalmaq->id, $qbankquestionids);
            // The new question bank doesn't contain questions from any other question bank.
            $this->assertEmpty(array_intersect($seenquestionids, $qbankquestionids));
            $seenquestionids = array_merge($seenquestionids, $qbankquestionids);
            // The new question bank does contain its own copy of the questions.
            $this->assertNotEmpty(
                array_filter($qbankquestions, fn($question) => $question->questiontext == $originalsaq->questiontext)
            );
            $qbankmaq = array_filter($qbankquestions, fn($question) => $question->questiontext == $originalmaq->questiontext);
            $qbankmaq = reset($qbankmaq);
            $this->assertNotFalse($qbankmaq);
            // It also contains 2 children of the multianswer question.
            $this->assertCount(2, array_filter($qbankquestions, fn($question) => $question->parent == $qbankmaq->id));
        }
    }
}

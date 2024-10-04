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

namespace qtype_multianswer\task;

/**
 * Unit tests for cleanup_duplicate_subquestions
 *
 * @package   qtype_multianswer
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_multianswer\task\cleanup_duplicate_subquestions
 */
final class cleanup_duplicate_subquestions_test extends \advanced_testcase {

    /**
     * Create a multianswer question and duplicate its subquestions.
     *
     * @return array
     * @throws \dml_exception
     */
    protected function generate_duplicated_subquestions(): array {
        global $DB;
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $question = $generator->create_question('multianswer', 'twosubq', ['category' => $category->id]);

        $subquestions = $DB->get_records('question', ['parent' => $question->id]);
        foreach ($subquestions as $subquestion) {
            $version = $DB->get_record('question_versions', ['questionid' => $subquestion->id]);
            $qbe = $DB->get_record('question_bank_entries', ['id' => $version->questionbankentryid]);
            $duplicate = clone($subquestion);
            unset($duplicate->id);
            $duplicate->id = $DB->insert_record('question', $duplicate);
            $duplicateqbe = clone($qbe);
            unset($duplicateqbe->id);
            $duplicateqbe->id = $DB->insert_record('question_bank_entries', $duplicateqbe);
            $duplicateversion = clone($version);
            unset($duplicateversion->id);
            $duplicateversion->questionid = $duplicate->id;
            $duplicateversion->questionbankentryid = $duplicateqbe->id;
            $duplicateversion->id = $DB->insert_record('question_versions', $duplicateversion);
            $subquestion->duplicate = (object) [
                'question' => $duplicate,
                'version' => $duplicateversion,
                'questionbankentry' => $duplicateqbe,
            ];
        }
        return $subquestions;
    }

    /**
     * We should correctly find subquestions with duplicate records.
     */
    public function test_find_duplicated_subquestions(): void {
        $this->resetAfterTest();
        $task = new cleanup_duplicate_subquestions();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $generator->create_question('multianswer', 'twosubq', ['category' => $category->id]);

        $this->assertEquals(0, count($task->find_duplicated_subquestions()));

        $this->generate_duplicated_subquestions();

        $this->assertEquals(2, count($task->find_duplicated_subquestions()));
    }

    /**
     * Delete duplicate subquestions, but not the originals.
     */
    public function test_execute(): void {
        global $DB;
        $this->resetAfterTest();
        $task = new cleanup_duplicate_subquestions();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $normalquestion = $generator->create_question('multianswer', 'twosubq', ['category' => $category->id]);
        $normalsubquestions = $DB->get_records('question', ['parent' => $normalquestion->id]);
        $duplicatedsubquestions = $this->generate_duplicated_subquestions();

        foreach ($duplicatedsubquestions as $subquestion) {
            $this->expectOutputRegex("~{$subquestion->stamp}~");
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->id]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->parent]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->duplicate->question->id]));
            $this->assertTrue($DB->record_exists('question_versions', ['id' => $subquestion->duplicate->version->id]));
            $this->assertTrue(
                $DB->record_exists('question_bank_entries', ['id' => $subquestion->duplicate->questionbankentry->id]),
            );
        }

        $this->expectOutputRegex('~Found 2 subquestions with duplicates~');
        $task->execute();

        // The non-duplicated questions should not have been touched.
        $this->assertTrue($DB->record_exists('question', ['id' => $normalquestion->id]));
        foreach ($normalsubquestions as $subquestion) {
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->id]));
        }

        // The duplicated questions should have the duplicates deleted, but the originals intact.
        foreach ($duplicatedsubquestions as $subquestion) {
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->id]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->parent]));
            $this->assertFalse($DB->record_exists('question', ['id' => $subquestion->duplicate->question->id]));
            $this->assertFalse($DB->record_exists('question_versions', ['id' => $subquestion->duplicate->version->id]));
            $this->assertFalse(
                $DB->record_exists('question_bank_entries', ['id' => $subquestion->duplicate->questionbankentry->id]),
            );
        }
    }

    /**
     * Don't delete a duplicate subquestion if its somehow being used somewhere.
     *
     * This should never really happen, but just to be on the safe side.
     */
    public function test_execute_with_usage(): void {
        global $DB;
        $this->resetAfterTest();
        $task = new cleanup_duplicate_subquestions();
        $course = $this->getDataGenerator()->create_course();
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id]);

        $duplicatedsubquestions = $this->generate_duplicated_subquestions();
        $firstsubquestion = reset($duplicatedsubquestions);
        $secondsubquestion = next($duplicatedsubquestions);

        quiz_add_quiz_question($firstsubquestion->duplicate->question->id, $quiz);

        foreach ($duplicatedsubquestions as $subquestion) {
            $this->expectOutputRegex("~{$subquestion->stamp}~");
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->id]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->parent]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->duplicate->question->id]));
            $this->assertTrue($DB->record_exists('question_versions', ['id' => $subquestion->duplicate->version->id]));
            $this->assertTrue(
                $DB->record_exists('question_bank_entries', ['id' => $subquestion->duplicate->questionbankentry->id]),
            );
        }

        $this->expectOutputRegex('~Found 2 subquestions with duplicates~');
        $task->execute();

        // The subquestion duplicate which was added to the quiz has not been deleted.
        $this->assertTrue($DB->record_exists('question', ['id' => $firstsubquestion->id]));
        $this->assertTrue($DB->record_exists('question', ['id' => $firstsubquestion->parent]));
        $this->assertTrue($DB->record_exists('question', ['id' => $firstsubquestion->duplicate->question->id]));
        $this->assertTrue($DB->record_exists('question_versions', ['id' => $firstsubquestion->duplicate->version->id]));
        $this->assertTrue(
            $DB->record_exists('question_bank_entries', ['id' => $firstsubquestion->duplicate->questionbankentry->id]),
        );

        // The subquestion duplicate which was not added to the quiz, was deleted.
        $this->assertTrue($DB->record_exists('question', ['id' => $secondsubquestion->id]));
        $this->assertTrue($DB->record_exists('question', ['id' => $secondsubquestion->parent]));
        $this->assertFalse($DB->record_exists('question', ['id' => $secondsubquestion->duplicate->question->id]));
        $this->assertFalse($DB->record_exists('question_versions', ['id' => $secondsubquestion->duplicate->version->id]));
        $this->assertFalse(
            $DB->record_exists('question_bank_entries', ['id' => $secondsubquestion->duplicate->questionbankentry->id]),
        );
    }

    /**
     * Delete a duplicate subquestion even if its parent is being used.
     */
    public function test_execute_with_parent_usage(): void {
        global $DB;
        $this->resetAfterTest();
        $task = new cleanup_duplicate_subquestions();
        $course = $this->getDataGenerator()->create_course();
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id]);

        $duplicatedsubquestions = $this->generate_duplicated_subquestions();

        $firstsubquestion = reset($duplicatedsubquestions);
        quiz_add_quiz_question($firstsubquestion->parent, $quiz);

        foreach ($duplicatedsubquestions as $subquestion) {
            $this->expectOutputRegex("~{$subquestion->stamp}~");
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->id]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->parent]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->duplicate->question->id]));
            $this->assertTrue($DB->record_exists('question_versions', ['id' => $subquestion->duplicate->version->id]));
            $this->assertTrue(
                $DB->record_exists('question_bank_entries', ['id' => $subquestion->duplicate->questionbankentry->id]),
            );
        }

        $this->expectOutputRegex('~Found 2 subquestions with duplicates~');
        $task->execute();

        // The duplicated questions should have the duplicates deleted, but the originals intact.
        foreach ($duplicatedsubquestions as $subquestion) {
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->id]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->parent]));
            $this->assertFalse($DB->record_exists('question', ['id' => $subquestion->duplicate->question->id]));
            $this->assertFalse($DB->record_exists('question_versions', ['id' => $subquestion->duplicate->version->id]));
            $this->assertFalse(
                $DB->record_exists('question_bank_entries', ['id' => $subquestion->duplicate->questionbankentry->id]),
            );
        }
    }

    /**
     * For historical reasons, we might have multiple different questions with the same stamp. Ensure we can handle this.
     */
    public function test_execute_duplicate_stamp(): void {
        global $DB;
        $this->resetAfterTest();
        $task = new cleanup_duplicate_subquestions();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $normalquestion = $generator->create_question('multianswer', 'twosubq', ['category' => $category->id]);
        $normalsubquestions = $DB->get_records('question', ['parent' => $normalquestion->id]);
        $duplicatedsubquestions1 = array_values($this->generate_duplicated_subquestions());
        $duplicatedsubquestions2 = array_values($this->generate_duplicated_subquestions());

        $this->expectOutputRegex("~((?!Did you remember to make the first column something unique).)*$~");

        foreach ($duplicatedsubquestions1 as $key => $subquestion) {
            $this->expectOutputRegex("~{$subquestion->stamp}~");
            $this->expectOutputRegex("~((?!{$duplicatedsubquestions2[$key]->stamp}).)*$~");

            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->id]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->parent]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->duplicate->question->id]));
            $this->assertTrue($DB->record_exists('question_versions', ['id' => $subquestion->duplicate->version->id]));
            $this->assertTrue(
                $DB->record_exists('question_bank_entries', ['id' => $subquestion->duplicate->questionbankentry->id]),
            );
            $this->assertTrue($DB->record_exists('question', ['id' => $duplicatedsubquestions2[$key]->id]));
            $this->assertTrue($DB->record_exists('question', ['id' => $duplicatedsubquestions2[$key]->parent]));
            $this->assertTrue($DB->record_exists('question', ['id' => $duplicatedsubquestions2[$key]->duplicate->question->id]));
            $this->assertTrue(
                $DB->record_exists('question_versions', ['id' => $duplicatedsubquestions2[$key]->duplicate->version->id])
            );
            $this->assertTrue(
                $DB->record_exists(
                    'question_bank_entries',
                    ['id' => $duplicatedsubquestions2[$key]->duplicate->questionbankentry->id],
                ),
            );
            // Set the stamp of the second generated subquestion and its duplicate to match the first.
            $DB->update_record(
                'question',
                (object) [
                    'id' => $duplicatedsubquestions2[$key]->id,
                    'stamp' => $subquestion->stamp,
                ],
            );
            $DB->update_record(
                'question',
                (object) [
                    'id' => $duplicatedsubquestions2[$key]->duplicate->version->questionid,
                    'stamp' => $subquestion->stamp,
                ],
            );
        }

        $this->expectOutputRegex('~Found 4 subquestions with duplicates~');
        $task->execute();

        // The non-duplicated questions should not have been touched.
        $this->assertTrue($DB->record_exists('question', ['id' => $normalquestion->id]));
        foreach ($normalsubquestions as $subquestion) {
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->id]));
        }

        // The duplicated questions should have the duplicates deleted, but the originals intact.
        foreach ($duplicatedsubquestions1 as $key => $subquestion) {
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->id]));
            $this->assertTrue($DB->record_exists('question', ['id' => $subquestion->parent]));
            $this->assertFalse($DB->record_exists('question', ['id' => $subquestion->duplicate->question->id]));
            $this->assertFalse($DB->record_exists('question_versions', ['id' => $subquestion->duplicate->version->id]));
            $this->assertFalse(
                $DB->record_exists('question_bank_entries', ['id' => $subquestion->duplicate->questionbankentry->id]),
            );
            $this->assertTrue($DB->record_exists('question', ['id' => $duplicatedsubquestions2[$key]->id]));
            $this->assertTrue($DB->record_exists('question', ['id' => $duplicatedsubquestions2[$key]->parent]));
            $this->assertFalse($DB->record_exists('question', ['id' => $duplicatedsubquestions2[$key]->duplicate->question->id]));
            $this->assertFalse(
                $DB->record_exists('question_versions', ['id' => $duplicatedsubquestions2[$key]->duplicate->version->id]));
            $this->assertFalse(
                $DB->record_exists(
                    'question_bank_entries',
                    ['id' => $duplicatedsubquestions2[$key]->duplicate->questionbankentry->id],
                ),
            );
        }
    }
}

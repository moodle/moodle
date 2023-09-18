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

namespace mod_quiz;

use core_question\local\bank\question_version_status;
use mod_quiz\external\submit_question_version;
use mod_quiz\question\bank\qbank_helper;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/quiz_question_helper_test_trait.php');

/**
 * Qbank helper test for quiz.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_quiz\question\bank\qbank_helper
 */
class qbank_helper_test extends \advanced_testcase {
    use \quiz_question_helper_test_trait;

    /**
     * @var \stdClass test student user.
     */
    protected $student;

    /**
     * Called before every test.
     */
    public function setUp(): void {
        global $USER;
        parent::setUp();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();
        $this->student = $this->getDataGenerator()->create_user();
        $this->user = $USER;
    }

    /**
     * Test reference records.
     *
     * @covers ::get_version_options
     */
    public function test_reference_records() {
        $this->resetAfterTest();

        $quiz = $this->create_test_quiz($this->course);
        // Test for questions from a different context.
        $context = \context_module::instance($quiz->cmid);

        // Create a couple of questions.
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);
        $numq = $questiongenerator->create_question('essay', null,
            ['category' => $cat->id, 'name' => 'This is the first version']);

        // Create two version.
        $questiongenerator->update_question($numq, null, ['name' => 'This is the second version']);
        $questiongenerator->update_question($numq, null, ['name' => 'This is the third version']);
        quiz_add_quiz_question($numq->id, $quiz);

        // Create the quiz object.
        $quizobj = \mod_quiz\quiz_settings::create($quiz->id);
        $quizobj->preload_questions();
        $quizobj->load_questions();
        $questions = $quizobj->get_questions();
        $question = reset($questions);
        $structure = structure::create_for_quiz($quizobj);
        $slots = $structure->get_slots();
        $slot = reset($slots);
        $this->assertEquals(3, count(qbank_helper::get_version_options($question->id)));
        $this->assertEquals($question->id, qbank_helper::choose_question_for_redo(
                $quiz->id, $context, $slot->id, new \qubaid_list([])));

        // Create another version.
        $questiongenerator->update_question($numq, null, ['name' => 'This is the latest version']);

        // Change to always latest.
        submit_question_version::execute($slot->id, 0);
        $quizobj->preload_questions();
        $quizobj->load_questions();
        $questions = $quizobj->get_questions();
        $question = reset($questions);
        $this->assertEquals($question->id, qbank_helper::choose_question_for_redo(
                $quiz->id, $context, $slot->id, new \qubaid_list([])));
    }

    /**
     * Test question structure data.
     *
     * @covers ::get_question_structure
     * @covers ::get_always_latest_version_question_ids
     */
    public function test_get_question_structure() {
        $this->resetAfterTest();

        // Create a quiz.
        $quiz = $this->create_test_quiz($this->course);
        $quizcontext = \context_module::instance(get_coursemodule_from_instance("quiz", $quiz->id, $this->course->id)->id);

        // Create a question in the quiz question bank.
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category(['contextid' => $quizcontext->id]);
        $q = $questiongenerator->create_question('essay', null,
            ['category' => $cat->id, 'name' => 'This is the first version']);

        // Edit it to create a second and third version.
        $questiongenerator->update_question($q, null, ['name' => 'This is the second version']);
        $finalq = $questiongenerator->update_question($q, null, ['name' => 'This is the third version']);

        // Add the question to the quiz.
        quiz_add_quiz_question($q->id, $quiz);

        // Load the quiz object and check.
        $quizobj = \mod_quiz\quiz_settings::create($quiz->id);
        $quizobj->preload_questions();
        $quizobj->load_questions();
        $questions = $quizobj->get_questions();
        $question = reset($questions);
        $this->assertEquals($finalq->id, $question->id);

        $structure = structure::create_for_quiz($quizobj);
        $slots = $structure->get_slots();
        $slot = reset($slots);
        $this->assertEquals($finalq->id, $slot->questionid);
    }

    /**
     * When a question only has draft versions, we should get those and not a dummy question.
     *
     * @return void
     * @covers ::get_question_structure
     */
    public function test_get_question_structure_with_drafts(): void {
        $this->resetAfterTest();

        // Create a quiz.
        $quiz = $this->create_test_quiz($this->course);
        $quizcontext = \context_module::instance(get_coursemodule_from_instance("quiz", $quiz->id, $this->course->id)->id);

        // Create some questions with drafts in the quiz question bank.
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category(['contextid' => $quizcontext->id]);
        $q1 = $questiongenerator->create_question('essay', null,
                ['category' => $cat->id, 'name' => 'This is q1 the first version']);
        $q2 = $questiongenerator->create_question('essay', null,
                ['category' => $cat->id, 'name' => 'This is q2 the first version',
                        'status' => question_version_status::QUESTION_STATUS_DRAFT]);
        $q3 = $questiongenerator->create_question('essay', null,
                ['category' => $cat->id, 'name' => 'This is q3 the first version',
                        'status' => question_version_status::QUESTION_STATUS_DRAFT]);

        // Create a new draft version of a question.
        $q1final = $questiongenerator->update_question(clone $q1, null,
                ['name' => 'This is q1 the second version', 'status' => question_version_status::QUESTION_STATUS_DRAFT]);
        $q3final = $questiongenerator->update_question(clone $q3, null,
                ['name' => 'This is q3 the second version', 'status' => question_version_status::QUESTION_STATUS_DRAFT]);

        // Add the questions to the quiz.
        quiz_add_quiz_question($q1->id, $quiz);
        quiz_add_quiz_question($q2->id, $quiz);
        quiz_add_quiz_question($q3->id, $quiz);

        // Load the quiz object and check.
        $quizobj = \mod_quiz\quiz_settings::create($quiz->id);
        $quizobj->preload_questions();
        $quizobj->load_questions();
        $questions = $quizobj->get_questions();
        $this->assertCount(3, $questions);
        // When a question has a Ready version, we should get that and not he draft.
        $this->assertTrue(array_key_exists($q1->id, $questions));
        $this->assertFalse(array_key_exists($q1final->id, $questions));
        $this->assertEquals(question_version_status::QUESTION_STATUS_READY, $questions[$q1->id]->status);
        $this->assertEquals('essay', $questions[$q1->id]->qtype);
        // When a question only has a draft, we should get that.
        $this->assertTrue(array_key_exists($q2->id, $questions));
        $this->assertEquals(question_version_status::QUESTION_STATUS_DRAFT, $questions[$q2->id]->status);
        $this->assertEquals('essay', $questions[$q2->id]->qtype);
        // When a question has several versions but all draft, we should get the latest draft.
        $this->assertFalse(array_key_exists($q3->id, $questions));
        $this->assertTrue(array_key_exists($q3final->id, $questions));
        $this->assertEquals(question_version_status::QUESTION_STATUS_DRAFT, $questions[$q3final->id]->status);
        $this->assertEquals('essay', $questions[$q3final->id]->qtype);
    }
}

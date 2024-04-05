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

namespace qtype_ordering;

use question_bank;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/course/externallib.php');

/**
 * Tests for the orderinging question type backup and restore logic.
 *
 * @package   qtype_ordering
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @covers    \backup_qtype_ordering_plugin
 * @covers    \restore_qtype_ordering_plugin
 */
final class backup_test extends \advanced_testcase {
    /**
     * Duplicate quiz with a orderinging question, and check it worked.
     */
    public function test_duplicate_ordering_question(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $coregenerator = $this->getDataGenerator();
        $questiongenerator = $coregenerator->get_plugin_generator('core_question');

        // Create a course with a page that embeds a question.
        $course = $coregenerator->create_course();
        $quiz = $coregenerator->create_module('quiz', ['course' => $course->id]);
        $quizcontext = \context_module::instance($quiz->cmid);

        $cat = $questiongenerator->create_question_category(['contextid' => $quizcontext->id]);
        $question = $questiongenerator->create_question('ordering', 'moodle', ['category' => $cat->id, 'shownumcorrect' => null]);

        // Store some counts.
        $numquizzes = count(get_fast_modinfo($course)->instances['quiz']);
        $numorderingquestions = $DB->count_records('question', ['qtype' => 'ordering']);

        // Duplicate the page.
        duplicate_module($course, get_fast_modinfo($course)->get_cm($quiz->cmid));

        // Verify the copied quiz exists.
        $this->assertCount($numquizzes + 1, get_fast_modinfo($course)->instances['quiz']);

        // Verify the copied question.
        $this->assertEquals($numorderingquestions + 1, $DB->count_records('question', ['qtype' => 'ordering']));
        $neworderingid = $DB->get_field_sql("
                SELECT MAX(id)
                  FROM {question}
                 WHERE qtype = ?
                ", ['ordering']);

        // Declare some parts of the question to be compared.
        $existingorderingdata = question_bank::load_question_data($question->id);
        $orderingdata = question_bank::load_question_data($neworderingid);
        $existinganswers = array_values((array) $existingorderingdata->options->answers);
        $answers = array_values((array) $orderingdata->options->answers);

        // Verify the copied question has the same values without being too verbose.
        foreach ((array) $existingorderingdata as $key => $value) {
            if (in_array($key, ['questiontext', 'generalfeedback', 'partiallycorrectfeedback'])) {
                $this->assertEquals($value, $orderingdata->$key);
            }
        }
        // Verify some parts of the new question we'll know that will be different.
        $this->assertNotEquals($existingorderingdata->id, $orderingdata->id);

        for ($i = 0; $i < count($existinganswers); $i++) {
            $this->assertEquals($existinganswers[$i]->answer, $answers[$i]->answer);
            $this->assertEquals($existinganswers[$i]->fraction, $answers[$i]->fraction);
            $this->assertEquals($existinganswers[$i]->feedback, $answers[$i]->feedback);
        }
    }
}

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

namespace mod_quiz\backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");

/**
 * Restore date tests.
 *
 * @package    mod_quiz
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class restore_date_test extends \restore_date_testcase {

    /**
     * Test restore dates.
     */
    public function test_restore_dates(): void {
        global $DB, $USER;

        // Create quiz data.
        $record = [
            'timeopen' => 100,
            'timeclose' => 100,
            'timemodified' => 100,
            'timecreated' => 100,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
            'precreateattempts' => 1,
        ];
        list($course, $quiz) = $this->create_course_and_module('quiz', $record);

        // Create questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        // Add to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);

        // Create an attempt.
        $timestamp = 100;
        $quizobj = \mod_quiz\quiz_settings::create($quiz->id);
        $attempt = quiz_create_attempt($quizobj, 1, false, $timestamp, false);
        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timestamp);
        quiz_attempt_save_started($quizobj, $quba, $attempt, $timestamp);

        // Quiz grade.
        $grade = new \stdClass();
        $grade->quiz = $quiz->id;
        $grade->userid = $USER->id;
        $grade->grade = 8.9;
        $grade->timemodified = $timestamp;
        $grade->id = $DB->insert_record('quiz_grades', $grade);

        // User override.
        $override = (object)[
            'quiz' => $quiz->id,
            'groupid' => 0,
            'userid' => $USER->id,
            'sortorder' => 1,
            'timeopen' => 100,
            'timeclose' => 200
        ];
        $DB->insert_record('quiz_overrides', $override);

        // Set time fields to a constant for easy validation.
        $DB->set_field('quiz_attempts', 'timefinish', $timestamp);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newquiz = $DB->get_record('quiz', ['course' => $newcourseid]);

        $this->assertFieldsNotRolledForward($quiz, $newquiz, ['timecreated', 'timemodified']);
        $props = ['timeclose', 'timeopen'];
        $this->assertFieldsRolledForward($quiz, $newquiz, $props);
        $this->assertEquals($quiz->precreateattempts, $newquiz->precreateattempts);

        $newattempt = $DB->get_record('quiz_attempts', ['quiz' => $newquiz->id]);
        $newoverride = $DB->get_record('quiz_overrides', ['quiz' => $newquiz->id]);
        $newgrade = $DB->get_record('quiz_grades', ['quiz' => $newquiz->id]);

        // Attempt time checks.
        $diff = $this->get_diff();
        $this->assertEquals($timestamp, $newattempt->timemodified);
        $this->assertEquals($timestamp, $newattempt->timefinish);
        $this->assertEquals($timestamp, $newattempt->timestart);
        $this->assertEquals($timestamp + $diff, $newattempt->timecheckstate); // Should this be rolled?

        // Quiz override time checks.
        $diff = $this->get_diff();
        $this->assertEquals($override->timeopen + $diff, $newoverride->timeopen);
        $this->assertEquals($override->timeclose + $diff, $newoverride->timeclose);

        // Quiz grade time checks.
        $this->assertEquals($grade->timemodified, $newgrade->timemodified);
    }
}

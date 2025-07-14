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

namespace mod_quiz\external;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../webservice/tests/helpers.php');

use coding_exception;
use core_question_generator;
use externallib_advanced_testcase;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use required_capability_exception;
use stdClass;

/**
 * Test for the reopen_attempt and get_reopen_attempt_confirmation services.
 *
 * @package   mod_quiz
 * @category  external
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\external\reopen_attempt
 * @covers \mod_quiz\external\get_reopen_attempt_confirmation
 */
final class reopen_attempt_test extends externallib_advanced_testcase {
    /** @var stdClass|null if we make a quiz attempt, we store the student object here. */
    protected $student;

    public function test_reopen_attempt_service_works(): void {
        [$attemptid] = $this->create_attempt_at_quiz_with_one_shortanswer_question();

        reopen_attempt::execute($attemptid);

        $attemptobj = quiz_attempt::create($attemptid);
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $attemptobj->get_state());
    }

    public function test_reopen_attempt_service_checks_permissions(): void {
        [$attemptid] = $this->create_attempt_at_quiz_with_one_shortanswer_question();

        $unprivilegeduser = $this->getDataGenerator()->create_user();
        $this->setUser($unprivilegeduser);

        $this->expectException(required_capability_exception::class);
        reopen_attempt::execute($attemptid);
    }

    public function test_reopen_attempt_service_checks_attempt_state(): void {
        [$attemptid] = $this->create_attempt_at_quiz_with_one_shortanswer_question(quiz_attempt::IN_PROGRESS);

        $this->expectExceptionMessage("Attempt $attemptid is in the wrong state (In progress) to be reopened.");
        reopen_attempt::execute($attemptid);
    }

    public function test_get_reopen_attempt_confirmation_staying_open(): void {
        global $DB;
        [$attemptid, $quizid] = $this->create_attempt_at_quiz_with_one_shortanswer_question();
        $DB->set_field('quiz', 'timeclose', 0, ['id' => $quizid]);

        $message = get_reopen_attempt_confirmation::execute($attemptid);

        $this->assertEquals('<p>This will reopen attempt 1 by ' . fullname($this->student) .
                '.</p><p>The attempt will remain open and can be continued.</p>',
                $message);
    }

    public function test_get_reopen_attempt_confirmation_staying_open_until(): void {
        global $DB;
        [$attemptid, $quizid] = $this->create_attempt_at_quiz_with_one_shortanswer_question();
        $timeclose = time() + HOURSECS;
        $DB->set_field('quiz', 'timeclose', $timeclose, ['id' => $quizid]);

        $message = get_reopen_attempt_confirmation::execute($attemptid);

        $this->assertEquals('<p>This will reopen attempt 1 by ' . fullname($this->student) .
                '.</p><p>The attempt will remain open and can be continued until the quiz closes on ' .
                userdate($timeclose) . '.</p>',
                $message);
    }

    public function test_get_reopen_attempt_confirmation_submitting(): void {
        global $DB;
        [$attemptid, $quizid] = $this->create_attempt_at_quiz_with_one_shortanswer_question();
        $timeclose = time() - HOURSECS;
        $DB->set_field('quiz', 'timeclose', $timeclose, ['id' => $quizid]);

        $message = get_reopen_attempt_confirmation::execute($attemptid);

        $this->assertEquals('<p>This will reopen attempt 1 by ' . fullname($this->student) .
                '.</p><p>The attempt will be immediately submitted for grading.</p>',
                $message);
    }

    public function test_get_reopen_attempt_confirmation_service_checks_permissions(): void {
        [$attemptid] = $this->create_attempt_at_quiz_with_one_shortanswer_question();

        $unprivilegeduser = $this->getDataGenerator()->create_user();
        $this->setUser($unprivilegeduser);

        $this->expectException(required_capability_exception::class);
        get_reopen_attempt_confirmation::execute($attemptid);
    }

    public function test_get_reopen_attempt_confirmation_service_checks_attempt_state(): void {
        [$attemptid] = $this->create_attempt_at_quiz_with_one_shortanswer_question(quiz_attempt::IN_PROGRESS);

        $this->expectExceptionMessage("Attempt $attemptid is in the wrong state (In progress) to be reopened.");
        get_reopen_attempt_confirmation::execute($attemptid);
    }

    /**
     * Create a quiz of one shortanswer question and an attempt in a given state.
     *
     * @param string $attemptstate the desired attempt state. quiz_attempt::ABANDONED or ::IN_PROGRESS.
     * @return array with two elements, the attempt id and the quiz id.
     */
    protected function create_attempt_at_quiz_with_one_shortanswer_question(
        string $attemptstate = quiz_attempt::ABANDONED
    ): array {
        global $SITE;
        $this->resetAfterTest();

        // Make a quiz.
        $timeclose = time() + HOURSECS;
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance([
            'course' => $SITE->id,
            'timeclose' => $timeclose,
            'overduehandling' => 'autoabandon'
        ]);

        // Create a question.
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        // Add them to the quiz.
        $quizobj = quiz_settings::create($quiz->id);
        quiz_add_quiz_question($saq->id, $quiz, 0, 1);
        $quizobj->get_grade_calculator()->recompute_quiz_sumgrades();

        // Make a user to do the quiz.
        $this->student = $this->getDataGenerator()->create_user();
        $this->setUser($this->student);
        $quizobj = quiz_settings::create($quiz->id, $this->student->id);

        // Start the attempt.
        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 1, null);
        $attemptobj = quiz_attempt::create($attempt->id);

        if ($attemptstate === quiz_attempt::ABANDONED) {
            // Attempt goes overdue (e.g. if cron ran).
            $attemptobj->process_abandon($timeclose + 2 * get_config('quiz', 'graceperiodmin'), false);
        } else if ($attemptstate !== quiz_attempt::IN_PROGRESS) {
            throw new coding_exception('Status ' . $attemptstate . ' not currently supported.');
        }

        // Set current user to admin before we return.
        $this->setAdminUser();

        return [$attemptobj->get_attemptid(), $attemptobj->get_quizid()];
    }
}

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

namespace mod_quiz\task;

use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Unit tests for precreate_attempts
 *
 * @package   mod_quiz
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\task\precreate_attempts
 */
final class precreate_attempts_test extends \advanced_testcase {
    use \quiz_question_helper_test_trait;

    /**
     * Generate the various possible combinations precreation settings and the corresponding task output.
     *
     * @return array
     */
    public static function precreate_settings_provider(): array {
        return [
            [
                'period' => 0,
                'output' => 'Pre-creation of quiz attempts is disabled. Nothing to do.',
            ],
            [
                'period' => 1,
                'output' => 'Found 0 quizzes to create attempts for.',
            ],
        ];
    }

    /**
     * The scheduled task only looks for quizzes to generate if pre-creation is configured.
     *
     * Only look for quizzes to generate attempts for if precreateperiod is not 0, and precreateattempts is 1 or is unlocked.
     *
     * @param int $period
     * @param string $output
     * @dataProvider precreate_settings_provider
     */
    public function test_execute_disabled(int $period, string $output): void {
        $this->resetAfterTest();
        set_config('precreateperiod', $period, 'quiz');

        $task = new precreate_attempts();
        ob_start();
        $task->execute();
        $log = ob_get_clean();
        $this->assertMatchesRegularExpression("/{$output}/", $log);
    }

    /**
     * Test precreate attempts task.
     *
     * Generate quizzes with a variety of timeopen and precreateperiod settings, and ensure those that match the criteria
     * for attempt pre-generation are picked up by the scheduled task.
     */
    public function test_execute(): void {
        $this->resetAfterTest();

        // Generate a course.
        $course = $this->getDataGenerator()->create_course();
        // Generate 3 users.
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        // Enrol users on the course with the appropriate roles.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');

        set_config('precreateperiod', 12 * HOURSECS, 'quiz');
        set_config('precreateattempts', 1, 'quiz');
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        // Generate a quiz with timeopen 1 day in the future.
        $quizinfuture = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => time() + 86400,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        // Generate a quiz with timeopen 0.
        $quiznotimeopen = $quizgenerator->create_instance([
            'course' => $course->id,
            'precreateperiod' => 43200,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        // Generate a quiz with timeopen in the past.
        $quizinpast = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => time() - 86400,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        // Generate a quiz with timeopen 11 hours in the future.
        $quizwithattempts = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => time() + 39600,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        // Generate second quiz with timeopen 11 hours in the future.
        $quizinprecreateperiod = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => time() + 39600,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        // Generate second quiz with timeopen 11 hours in the future,
        // but do not give it any questions.
        $quizwithoutquestions = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => time() + 39600,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        // Add questions to the quizzes.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $this->add_two_regular_questions($questiongenerator, $quizinfuture);
        $this->add_two_regular_questions($questiongenerator, $quiznotimeopen);
        $this->add_two_regular_questions($questiongenerator, $quizinpast);
        $this->add_two_regular_questions($questiongenerator, $quizwithattempts);
        $this->add_two_regular_questions($questiongenerator, $quizinprecreateperiod);

        // Create attempts for one student on quiz 5.
        $quiz5settings = quiz_settings::create($quizwithattempts->id);
        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quiz5settings->get_context());
        $quba->set_preferred_behaviour($quiz5settings->get_quiz()->preferredbehaviour);
        $attempt = quiz_create_attempt($quiz5settings, 1, false, time(), false, $student1->id);
        quiz_start_new_attempt($quiz5settings, $quba, $attempt, 1, time());
        quiz_attempt_save_started($quiz5settings, $quba, $attempt);

        $this->assertEmpty(
            quiz_get_user_attempts(
                [
                    $quizinfuture->id,
                    $quiznotimeopen->id,
                    $quizinpast->id,
                    $quizwithoutquestions->id,
                ],
                $student1->id,
                'all'
            )
        );
        $this->assertEmpty(
            quiz_get_user_attempts(
                [
                    $quizinfuture->id,
                    $quiznotimeopen->id,
                    $quizinpast->id,
                    $quizwithattempts->id,
                    $quizwithoutquestions->id,
                ],
                $student2->id,
                'all',
            ),
        );
        $this->assertEmpty(
            quiz_get_user_attempts(
                [
                    $quizinfuture->id,
                    $quiznotimeopen->id,
                    $quizinpast->id,
                    $quizwithattempts->id,
                    $quizinprecreateperiod->id,
                    $quizwithoutquestions->id,
                ],
                $teacher->id,
                'all',
            ),
        );

        $student1existingattempts = quiz_get_user_attempts($quizwithattempts->id, $student1->id, 'all');
        $this->assertCount(1, $student1existingattempts);
        $this->assertEquals(reset($student1existingattempts)->state, quiz_attempt::IN_PROGRESS);
        $student1precreatedattempts = quiz_get_user_attempts($quizinprecreateperiod->id, $student1->id, 'all');
        $this->assertEmpty($student1precreatedattempts);
        $student2precreatedattempts = quiz_get_user_attempts($quizinprecreateperiod->id, $student2->id, 'all');
        $this->assertEmpty($student2precreatedattempts);

        $task = new precreate_attempts();
        ob_start();
        $task->execute();
        $log = ob_get_clean();

        $this->assertMatchesRegularExpression('/Found 1 quizzes to create attempts for/', $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quizinfuture->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quiznotimeopen->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quizinpast->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quizwithattempts->name}/", $log);
        $this->assertMatchesRegularExpression("/Creating attempts for {$quizinprecreateperiod->name}/", $log);
        $this->assertMatchesRegularExpression("/Created 2 attempts for {$quizinprecreateperiod->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quizwithoutquestions->name}/", $log);
        $this->assertMatchesRegularExpression('/Created attempts for 1 quizzes./', $log);

        // Students should have no attempts on quizzes that didn't meet criteria for pre-creation.
        $this->assertEmpty(
            quiz_get_user_attempts(
                [
                    $quizinfuture->id,
                    $quiznotimeopen->id,
                    $quizinpast->id,
                    $quizwithoutquestions->id,
                ],
                $student1->id,
                'all'
            )
        );
        $this->assertEmpty(
            quiz_get_user_attempts(
                [
                    $quizinfuture->id,
                    $quiznotimeopen->id,
                    $quizinpast->id,
                    $quizwithattempts->id,
                    $quizwithoutquestions->id,
                ],
                $student2->id,
                'all',
            ),
        );
        // Teacher should not have any attempts on any quizzes.
        $this->assertEmpty(
            quiz_get_user_attempts(
                [
                    $quizinfuture->id,
                    $quiznotimeopen->id,
                    $quizinpast->id,
                    $quizwithattempts->id,
                    $quizinprecreateperiod->id,
                    $quizwithoutquestions->id,
                ],
                $teacher->id,
                'all',
            ),
        );

        // Students existing attempts should remain.
        $student1existingattempts = quiz_get_user_attempts($quizwithattempts->id, $student1->id, 'all');
        $this->assertCount(1, $student1existingattempts);
        $this->assertEquals(reset($student1existingattempts)->state, quiz_attempt::IN_PROGRESS);
        // They should have NOT_STARTED attempts on quizzes that meet the criteria for pre-creation.
        $student1precreatedattempts = quiz_get_user_attempts($quizinprecreateperiod->id, $student1->id, 'all');
        $this->assertCount(1, $student1precreatedattempts);
        $this->assertEquals(reset($student1precreatedattempts)->state, quiz_attempt::NOT_STARTED);
        $student2precreatedattempts = quiz_get_user_attempts($quizinprecreateperiod->id, $student2->id, 'all');
        $this->assertCount(1, $student2precreatedattempts);
        $this->assertEquals(reset($student1precreatedattempts)->state, quiz_attempt::NOT_STARTED);
    }

    /**
     * Processing should stop at the end of a quiz once maxruntime has been reached.
     *
     * @return void
     */
    public function test_execute_maxruntime(): void {
        $this->resetAfterTest();

        // Generate a course.
        $course = $this->getDataGenerator()->create_course();
        // Generate 3 users.
        $student1 = $this->getDataGenerator()->create_user();
        // Enrol users on the course with the appropriate roles.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');

        set_config('precreateperiod', 12 * HOURSECS, 'quiz');
        set_config('precreateattempts', 1, 'quiz');
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        // Generate 3 quizzes within the pre-creation window.
        $timenow = time();
        $quiz1 = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => $timenow + 39600,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        // This quiz opens first, so should be processed first.
        $quiz2 = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => $timenow + 39599,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        $quiz3 = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => $timenow + 39600,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        // Add questions to the quizzes.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $this->add_two_regular_questions($questiongenerator, $quiz1);
        $this->add_two_regular_questions($questiongenerator, $quiz2);
        $this->add_two_regular_questions($questiongenerator, $quiz3);

        // Run the task with a maxruntime of 0, so that it should stop after processing the first quiz.
        $task = new precreate_attempts(0);
        ob_start();
        $task->execute();
        $log = ob_get_clean();

        // Verify that the task stopped after the quiz opening soonest.
        $this->assertMatchesRegularExpression('/Found 3 quizzes to create attempts for/', $log);
        $this->assertMatchesRegularExpression("/Creating attempts for {$quiz2->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quiz1->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quiz3->name}/", $log);
        $this->assertMatchesRegularExpression("/Created 1 attempts for {$quiz2->name}/", $log);
        $this->assertMatchesRegularExpression('/Time limit reached./', $log);
        $this->assertMatchesRegularExpression('/Created attempts for 1 quizzes./', $log);

        // Run the task again with the default maxruntime.
        ob_start();
        $task = new precreate_attempts();
        $task->execute();
        $log = ob_get_clean();

        // Verify that it picks up the remaining quiz for processing.
        $this->assertMatchesRegularExpression('/Found 2 quizzes to create attempts for/', $log);
        $this->assertMatchesRegularExpression("/Creating attempts for {$quiz1->name}/", $log);
        $this->assertMatchesRegularExpression("/Creating attempts for {$quiz3->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quiz2->name}/", $log);
        $this->assertMatchesRegularExpression("/Created 1 attempts for {$quiz1->name}/", $log);
        $this->assertMatchesRegularExpression("/Created 1 attempts for {$quiz3->name}/", $log);
        $this->assertDoesNotMatchRegularExpression('/Time limit reached./', $log);
        $this->assertMatchesRegularExpression('/Created attempts for 2 quizzes./', $log);
    }

    /**
     * Pre-creation is opt in based on quiz setting.
     *
     * @return void
     */
    public function test_execute_optin(): void {
        $this->resetAfterTest();

        // Generate a course.
        $course = $this->getDataGenerator()->create_course();
        // Generate 3 users.
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        // Enrol users on the course with the appropriate roles.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');

        // Precreation is disabled by default.
        set_config('precreateperiod', 12 * HOURSECS, 'quiz');
        set_config('precreateattempts', 0, 'quiz');
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        // Generate a quiz with timeopen 11 hours in the future, and precreateattempts set to 1.
        $quizprecreate = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => time() + 39600,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
            'precreateattempts' => 1,
        ]);
        // Generate a quiz with timeopen 11 hours in the future, and precreateattempts set to 0.
        $quiznoprecreate = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => time() + 39600,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
            'precreateattempts' => 0,
        ]);
        // Generate a quiz with timeopen 11 hours in the future, and precreateattempts set to null.
        $quizprecreatenull = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => time() + 39600,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $this->add_two_regular_questions($questiongenerator, $quizprecreate);
        $this->add_two_regular_questions($questiongenerator, $quiznoprecreate);
        $this->add_two_regular_questions($questiongenerator, $quizprecreatenull);

        // Run the task.
        ob_start();
        $task = new precreate_attempts();
        $task->execute();
        $log = ob_get_clean();

        // Attempts were now created for the opted-in quiz.
        $this->assertMatchesRegularExpression('/Found 1 quizzes to create attempts for/', $log);
        $this->assertMatchesRegularExpression("/Creating attempts for {$quizprecreate->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quiznoprecreate->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quizprecreatenull->name}/", $log);
        $this->assertMatchesRegularExpression('/Created attempts for 1 quizzes./', $log);

        // Now enabled by default.
        set_config('precreateattempts', 1, 'quiz');

        // Run the task again.
        ob_start();
        $task = new precreate_attempts();
        $task->execute();
        $log = ob_get_clean();

        // The quiz with null now has attempts generated.
        $this->assertMatchesRegularExpression('/Found 1 quizzes to create attempts for/', $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quizprecreate->name}/", $log);
        $this->assertDoesNotMatchRegularExpression("/Creating attempts for {$quiznoprecreate->name}/", $log);
        $this->assertMatchesRegularExpression("/Creating attempts for {$quizprecreatenull->name}/", $log);
        $this->assertMatchesRegularExpression('/Created attempts for 1 quizzes./', $log);
    }
}

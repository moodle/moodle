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
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;

/**
 * Helper trait for quiz question unit tests.
 *
 * This trait helps to execute different tests for quiz, for example if it needs to create a quiz, add question
 * to the question, add random quetion to the quiz, do a backup or restore.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait quiz_question_helper_test_trait {

    /** @var \stdClass $course Test course to contain quiz. */
    protected $course;

    /** @var \stdClass $quiz A test quiz. */
    protected $quiz;

    /** @var \stdClass $user A test logged-in user. */
    protected $user;

    /**
     * Create a test quiz for the specified course.
     *
     * @param \stdClass $course
     * @return  \stdClass
     */
    protected function create_test_quiz(\stdClass $course): \stdClass {

        /** @var mod_quiz_generator $quizgenerator */
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        return $quizgenerator->create_instance([
            'course' => $course->id,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
    }

    /**
     * Helper method to add regular questions in quiz.
     *
     * @param component_generator_base $questiongenerator
     * @param \stdClass $quiz
     * @param array $override
     */
    protected function add_two_regular_questions($questiongenerator, \stdClass $quiz, $override = null): void {
        // Create a couple of questions.
        $cat = $questiongenerator->create_question_category($override);

        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        // Create another version.
        $questiongenerator->update_question($saq);
        quiz_add_quiz_question($saq->id, $quiz);
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        // Create two version.
        $questiongenerator->update_question($numq);
        $questiongenerator->update_question($numq);
        quiz_add_quiz_question($numq->id, $quiz);
    }

    /**
     * Helper method to add random question to quiz.
     *
     * @param component_generator_base $questiongenerator
     * @param \stdClass $quiz
     * @param array $override
     */
    protected function add_one_random_question($questiongenerator, \stdClass $quiz, $override = []): void {
        // Create a random question.
        $cat = $questiongenerator->create_question_category($override);
        $questiongenerator->create_question('truefalse', null, ['category' => $cat->id]);
        $questiongenerator->create_question('essay', null, ['category' => $cat->id]);
        $this->add_random_questions($quiz->id, 0, $cat->id, 1);
    }

    /**
     * Attempt questions for a quiz and user.
     *
     * @param \stdClass $quiz Quiz to attempt.
     * @param \stdClass $user A user to attempt the quiz.
     * @param int $attemptnumber
     * @return array
     */
    protected function attempt_quiz(\stdClass $quiz, \stdClass $user, $attemptnumber = 1): array {
        $this->setUser($user);

        $starttime = time();
        $quizobj = quiz_settings::create($quiz->id, $user->id);

        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        // Start the attempt.
        $attempt = quiz_create_attempt($quizobj, $attemptnumber, null, $starttime, false, $user->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, $attemptnumber, $starttime);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_finish($starttime, false);

        $this->setUser();
        return [$quizobj, $quba, $attemptobj];
    }

    /**
     * A helper method to backup test quiz.
     *
     * @param \stdClass $quiz Quiz to attempt.
     * @param \stdClass $user A user to attempt the quiz.
     * @return string A backup ID ready to be restored.
     */
    protected function backup_quiz(\stdClass $quiz, \stdClass $user): string {
        global $CFG;

        // Get the necessary files to perform backup and restore.
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $backupid = 'test-question-backup-restore';

        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $quiz->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $user->id);
        $bc->execute_plan();

        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/' . $backupid;
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        return $backupid;
    }

    /**
     * A helper method to restore provided backup.
     *
     * @param string $backupid Backup ID to restore.
     * @param stdClass $course
     * @param stdClass $user
     */
    protected function restore_quiz(string $backupid, stdClass $course, stdClass $user): void {
        $rc = new restore_controller($backupid, $course->id,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $user->id, backup::TARGET_CURRENT_ADDING);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();
    }

    /**
     * A helper method to emulate duplication of the quiz.
     *
     * @param stdClass $course
     * @param stdClass $quiz
     * @return \cm_info|null
     */
    protected function duplicate_quiz($course, $quiz): ?\cm_info {
        return duplicate_module($course, get_fast_modinfo($course)->get_cm($quiz->cmid));
    }

    /**
     * Add random questions to a quiz, with a filter condition based on a category ID.
     *
     * @param int $quizid The quiz to add the questions to.
     * @param int $page The page number to add the questions to.
     * @param int $categoryid The category ID to use for the filter condition.
     * @param int $number The number of questions to add.
     * @return void
     */
    protected function add_random_questions(int $quizid, int $page, int $categoryid, int $number): void {
        $settings = quiz_settings::create($quizid);
        $structure = \mod_quiz\structure::create_for_quiz($settings);
        $filtercondition = [
            'filter' => [
                'category' => [
                    'jointype' => \qbank_managecategories\category_condition::JOINTYPE_DEFAULT,
                    'values' => [$categoryid],
                    'filteroptions' => ['includesubcategories' => false],
                ],
            ],
        ];
        $structure->add_random_questions($page, $number, $filtercondition);
    }
}

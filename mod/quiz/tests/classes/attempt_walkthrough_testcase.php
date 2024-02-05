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

namespace mod_quiz\tests;

use question_engine;
use mod_quiz\quiz_settings;
use mod_quiz\quiz_attempt;
use stdClass;

/**
 * Quiz attempt walk through using data from csv file.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class attempt_walkthrough_testcase extends \advanced_testcase {
    use question_helper_test_trait;

    /**
     * @var stdClass the quiz record we create.
     */
    protected $quiz;

    /**
     * @var array with slot no => question name => questionid. Question ids of questions created in the same category as random q.
     */
    protected $randqids;

    /**
     * Get the list of files which contain test data.
     *
     * @return array
     */
    protected static function get_test_files(): array {
        return [];
    }

    /**
     * Get the component name.
     *
     * @return string
     */
    protected static function get_component(): string {
        // If the late-static class name is namespaced, use the first part of the namespace.
        if (str_contains(static::class, '\\')) {
            return explode('\\', static::class)[0];
        }

        // Otherwise we have to assume that the test name is correctly frankenstyle named.
        return implode(
            '_',
            array_slice(
                explode('_', static::class, 3),
                0,
                2,
            )
        );
    }

    /**
     * Get the full path of the csv file.
     *
     * @param string $setname
     * @param string $test
     * @return string
     */
    protected static function get_full_path_of_csv_file(string $setname, string $test): string {
        return static::get_fixture_path(static::get_component(), "{$setname}{$test}.csv");
    }

    /**
     * The only test in this class. This is run multiple times depending on how many sets of files there are in fixtures/
     * directory.
     *
     * @param array $quizsettings of settings read from csv file quizzes.csv
     * @param array $csvdata of data read from csv file "questionsXX.csv", "stepsXX.csv" and "resultsXX.csv".
     * // phpcs:ignore moodle.Commenting.ValidTags.Invalid
     * @dataProvider get_data_for_walkthrough
     */
    public function test_walkthrough_from_csv($quizsettings, $csvdata): void {
        // CSV data files for these tests were generated using:
        // https://github.com/jamiepratt/moodle-quiz-tools/tree/master/responsegenerator.

        $this->create_quiz_simulate_attempts_and_check_results($quizsettings, $csvdata);
    }

    /**
     * Create a quiz, add questions to it, and simulate attempts on it.
     *
     * @param array $quizsettings Quiz overrides for this quiz.
     * @param array $csvdata Data loaded from csv files for this test.
     */
    public function create_quiz($quizsettings, $qs) {
        global $SITE, $DB;
        $this->setAdminUser();

        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $slots = [];
        $qidsbycat = [];
        $sumofgrades = 0;
        foreach ($qs as $qsrow) {
            $q = $this->explode_dot_separated_keys_to_make_subindexs($qsrow);

            $catname = ['name' => $q['cat']];
            if (!$cat = $DB->get_record('question_categories', ['name' => $q['cat']])) {
                $cat = $questiongenerator->create_question_category($catname);
            }
            $q['catid'] = $cat->id;
            foreach (['which' => null, 'overrides' => []] as $key => $default) {
                if (empty($q[$key])) {
                    $q[$key] = $default;
                }
            }

            if ($q['type'] !== 'random') {
                // Don't actually create random questions here.
                $overrides = ['category' => $cat->id, 'defaultmark' => $q['mark']] + $q['overrides'];
                if ($q['type'] === 'truefalse') {
                    // True/false question can never have hints, but sometimes we need to put them
                    // in the CSV file, to keep it rectangular.
                    unset($overrides['hint']);
                }
                $question = $questiongenerator->create_question($q['type'], $q['which'], $overrides);
                $q['id'] = $question->id;

                if (!isset($qidsbycat[$q['cat']])) {
                    $qidsbycat[$q['cat']] = [];
                }
                if (!empty($q['which'])) {
                    $name = $q['type'] . '_' . $q['which'];
                } else {
                    $name = $q['type'];
                }
                $qidsbycat[$q['catid']][$name] = $q['id'];
            }
            if (!empty($q['slot'])) {
                $slots[$q['slot']] = $q;
                $sumofgrades += $q['mark'];
            }
        }

        ksort($slots);

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        // Settings from param override defaults.
        $aggregratedsettings = $quizsettings + ['course' => $SITE->id,
                                                     'questionsperpage' => 0,
                                                     'grade' => 100.0,
                                                     'sumgrades' => $sumofgrades];

        $this->quiz = $quizgenerator->create_instance($aggregratedsettings);

        $this->randqids = [];
        foreach ($slots as $slotno => $slotquestion) {
            if ($slotquestion['type'] !== 'random') {
                quiz_add_quiz_question($slotquestion['id'], $this->quiz, 0, $slotquestion['mark']);
            } else {
                $this->add_random_questions($this->quiz->id, 0, $slotquestion['catid'], 1);
                $this->randqids[$slotno] = $qidsbycat[$slotquestion['catid']];
            }
        }
    }

    /**
     * Create quiz, simulate attempts and check results (if resultsXX.csv exists).
     *
     * @param array $quizsettings Quiz overrides for this quiz.
     * @param array $csvdata Data loaded from csv files for this test.
     */
    protected function create_quiz_simulate_attempts_and_check_results(array $quizsettings, array $csvdata) {
        $this->resetAfterTest();

        $this->create_quiz($quizsettings, $csvdata['questions']);

        $attemptids = $this->walkthrough_attempts($csvdata['steps']);

        if (isset($csvdata['results'])) {
            $this->check_attempts_results($csvdata['results'], $attemptids);
        }
    }

    /**
     * Load dataset from CSV file "{$setname}{$test}.csv".
     *
     * @param string $setname
     * @param string $test
     * @return array
     */
    protected static function load_csv_data_file(string $setname, string $test = ''): array {
        $files = [$setname => static::get_full_path_of_csv_file($setname, $test)];
        return static::dataset_from_files($files)->get_rows([$setname]);
    }

    /**
     * Break down row of csv data into sub arrays, according to column names.
     *
     * @param array $row from csv file with field names with parts separate by '.'.
     * @return array the row with each part of the field name following a '.' being a separate sub array's index.
     */
    protected function explode_dot_separated_keys_to_make_subindexs(array $row): array {
        $parts = [];
        foreach ($row as $columnkey => $value) {
            $newkeys = explode('.', trim($columnkey));
            $placetoputvalue =& $parts;
            foreach ($newkeys as $newkeydepth => $newkey) {
                if ($newkeydepth + 1 === count($newkeys)) {
                    $placetoputvalue[$newkey] = $value;
                } else {
                    // Going deeper down.
                    if (!isset($placetoputvalue[$newkey])) {
                        $placetoputvalue[$newkey] = [];
                    }
                    $placetoputvalue =& $placetoputvalue[$newkey];
                }
            }
        }
        return $parts;
    }

    /**
     * Data provider method for test_walkthrough_from_csv. Called by PHPUnit.
     *
     * @return array One array element for each run of the test. Each element contains an array with the params for
     *                  test_walkthrough_from_csv.
     */
    public static function get_data_for_walkthrough(): array {
        $quizzes = self::load_csv_data_file('quizzes')['quizzes'];
        $datasets = [];
        foreach ($quizzes as $quizsettings) {
            $dataset = [];
            foreach (static::get_test_files() as $file) {
                if (file_exists(static::get_full_path_of_csv_file($file, $quizsettings['testnumber']))) {
                    $dataset[$file] = self::load_csv_data_file($file, $quizsettings['testnumber'])[$file];
                }
            }
            $datasets[] = [$quizsettings, $dataset];
        }
        return $datasets;
    }

    /**
     * Helper to walk through attempts.
     *
     * @param array $steps the step data from the csv file.
     * @return array attempt no as in csv file => the id of the quiz_attempt as stored in the db.
     */
    protected function walkthrough_attempts(array $steps): array {
        global $DB;
        $attemptids = [];
        foreach ($steps as $steprow) {
            $step = $this->explode_dot_separated_keys_to_make_subindexs($steprow);
            // Find existing user or make a new user to do the quiz.
            $username = ['firstname' => $step['firstname'],
                              'lastname'  => $step['lastname']];

            if (!$user = $DB->get_record('user', $username)) {
                $user = $this->getDataGenerator()->create_user($username);
            }

            if (!isset($attemptids[$step['quizattempt']])) {
                // Start the attempt.
                $quizobj = quiz_settings::create($this->quiz->id, $user->id);
                $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
                $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

                $prevattempts = quiz_get_user_attempts($this->quiz->id, $user->id, 'all', true);
                $attemptnumber = count($prevattempts) + 1;
                $timenow = time();
                $attempt = quiz_create_attempt($quizobj, $attemptnumber, null, $timenow, false, $user->id);
                // Select variant and / or random sub question.
                if (!isset($step['variants'])) {
                    $step['variants'] = [];
                }
                if (isset($step['randqs'])) {
                    // Replace 'names' with ids.
                    foreach ($step['randqs'] as $slotno => $randqname) {
                        $step['randqs'][$slotno] = $this->randqids[$slotno][$randqname];
                    }
                } else {
                    $step['randqs'] = [];
                }

                quiz_start_new_attempt($quizobj, $quba, $attempt, $attemptnumber, $timenow, $step['randqs'], $step['variants']);
                quiz_attempt_save_started($quizobj, $quba, $attempt);
                $attemptid = $attemptids[$step['quizattempt']] = $attempt->id;
            } else {
                $attemptid = $attemptids[$step['quizattempt']];
            }

            // Process some responses from the student.
            $attemptobj = quiz_attempt::create($attemptid);
            $attemptobj->process_submitted_actions($timenow, false, $step['responses']);

            // Finish the attempt.
            if (!isset($step['finished']) || ($step['finished'] == 1)) {
                $attemptobj = quiz_attempt::create($attemptid);
                $attemptobj->process_submit($timenow, false);
                $attemptobj->process_grade_submission($timenow);
            }
        }
        return $attemptids;
    }

    /**
     * Assertion helper to check attempt results.
     *
     * @param array $results the results data from the csv file.
     * @param array $attemptids attempt no as in csv file => the id of the quiz_attempt as stored in the db.
     */
    protected function check_attempts_results(array $results, array $attemptids) {
        foreach ($results as $resultrow) {
            $result = $this->explode_dot_separated_keys_to_make_subindexs($resultrow);
            // Re-load quiz attempt data.
            $attemptobj = quiz_attempt::create($attemptids[$result['quizattempt']]);
            $this->check_attempt_results($result, $attemptobj);
        }
    }

    /**
     * Check that attempt results are as specified in $result.
     *
     * @param array        $result             row of data read from csv file.
     * @param quiz_attempt $attemptobj         the attempt object loaded from db.
     */
    protected function check_attempt_results(array $result, quiz_attempt $attemptobj) {
        foreach ($result as $fieldname => $value) {
            if ($value === '!NULL!') {
                $value = null;
            }
            switch ($fieldname) {
                case 'quizattempt':
                    break;
                case 'attemptnumber':
                    $this->assertEquals($value, $attemptobj->get_attempt_number());
                    break;
                case 'slots':
                    foreach ($value as $slotno => $slottests) {
                        foreach ($slottests as $slotfieldname => $slotvalue) {
                            switch ($slotfieldname) {
                                case 'mark':
                                    $this->assertEquals(
                                        round($slotvalue, 2),
                                        $attemptobj->get_question_mark($slotno),
                                        "Mark for slot $slotno of attempt {$result['quizattempt']}."
                                    );
                                    break;
                                default:
                                    throw new \coding_exception('Unknown slots sub field column in csv file '
                                                               . s($slotfieldname));
                            }
                        }
                    }
                    break;
                case 'finished':
                    $this->assertEquals((bool)$value, $attemptobj->is_finished());
                    break;
                case 'summarks':
                    $this->assertEquals(
                        (float)$value,
                        $attemptobj->get_sum_marks(),
                        "Sum of marks of attempt {$result['quizattempt']}."
                    );
                    break;
                case 'quizgrade':
                    // Check quiz grades.
                    $grades = quiz_get_user_grades($attemptobj->get_quiz(), $attemptobj->get_userid());
                    $grade = array_shift($grades);
                    $this->assertEquals($value, $grade->rawgrade, "Quiz grade for attempt {$result['quizattempt']}.");
                    break;
                case 'gradebookgrade':
                    // Check grade book.
                    $gradebookgrades = grade_get_grades(
                        $attemptobj->get_courseid(),
                        'mod',
                        'quiz',
                        $attemptobj->get_quizid(),
                        $attemptobj->get_userid()
                    );
                    $gradebookitem = array_shift($gradebookgrades->items);
                    $gradebookgrade = array_shift($gradebookitem->grades);
                    $this->assertEquals($value, $gradebookgrade->grade, "Gradebook grade for attempt {$result['quizattempt']}.");
                    break;
                default:
                    throw new \coding_exception('Unknown column in csv file ' . s($fieldname));
            }
        }
    }
}

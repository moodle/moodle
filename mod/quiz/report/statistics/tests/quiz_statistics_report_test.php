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

namespace quiz_statistics;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/report/statistics/report.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/statisticslib.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Tests for statistics report
 *
 * @package   quiz_statistics
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \quiz_statistics_report
 */
class quiz_statistics_report_test extends \advanced_testcase {

    use \quiz_question_helper_test_trait;

    /**
     * Secondary database connection for creating locks.
     *
     * @var \moodle_database|null
     */
    protected static ?\moodle_database $lockdb;

    /**
     * Lock factory using the secondary database connection.
     *
     * @var \moodle_database|null
     */
    protected static ?\core\lock\lock_factory $lockfactory;

    /**
     * Create a lock factory with a second database session.
     *
     * This allows us to create a lock in our test code that will block a lock request
     * on the same key in code under test.
     */
    public function setUp(): void {
        global $CFG;
        self::$lockdb = \moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);
        self::$lockdb->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->prefix, $CFG->dboptions);

        $lockfactoryclass = \core\lock\lock_config::get_lock_factory_class();
        $lockfactory = new $lockfactoryclass('quiz_statistics_get_stats');

        // Iterate lock factory hierarchy to see if it contains a 'db' property we can use.
        $reflectionclass = new \ReflectionClass($lockfactory);
        while ($reflectionclass) {
            if ($reflectionhasdb = $reflectionclass->hasProperty('db')) {
                break;
            }
            $reflectionclass = $reflectionclass->getParentClass();
        }

        if (!$reflectionhasdb) {
            $this->markTestSkipped('Test lock factory should be a db type');
        }

        $reflectiondb = new \ReflectionProperty($lockfactory, 'db');
        $reflectiondb->setValue($lockfactory, self::$lockdb);
        self::$lockfactory = $lockfactory;
    }

    /**
     * Dispose of the extra DB connection and lock factory.
     */
    public function tearDown(): void {
        self::$lockdb->dispose();
        self::$lockdb = null;
        self::$lockfactory = null;
    }

    /**
     * Return a generated quiz
     *
     * @return \stdClass
     */
    protected function create_and_attempt_quiz(): \stdClass {
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $quiz = $this->create_test_quiz($course);
        $quizcontext = \context_module::instance($quiz->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $this->add_two_regular_questions($questiongenerator, $quiz, ['contextid' => $quizcontext->id]);
        $this->attempt_quiz($quiz, $user);

        return $quiz;
    }

    /**
     * Test locking the calculation process.
     *
     * When there is a lock on the hash code, test_get_all_stats_and_analysis() should wait until the lock timeout, then throw an
     * exception.
     *
     * When there is no lock (or the lock has been released), it should return a result.
     *
     * @return void
     */
    public function test_get_all_stats_and_analysis_locking(): void {
        $this->resetAfterTest(true);
        $quiz = $this->create_and_attempt_quiz();
        $whichattempts = QUIZ_GRADEAVERAGE; // All attempts.
        $whichtries = \question_attempt::ALL_TRIES;
        $groupstudentsjoins = new \core\dml\sql_join();
        $qubaids = quiz_statistics_qubaids_condition($quiz->id, $groupstudentsjoins, $whichattempts);

        $report = new \quiz_statistics_report();
        $questions = $report->load_and_initialise_questions_for_calculations($quiz);

        $timeoutseconds = 20;
        set_config('getstatslocktimeout', $timeoutseconds, 'quiz_statistics');
        $lock = self::$lockfactory->get_lock($qubaids->get_hash_code(), 0);

        $progress = new \core\progress\none();

        $this->resetDebugging();
        $timebefore = microtime(true);
        try {
            $result = $report->get_all_stats_and_analysis(
                $quiz,
                $whichattempts,
                $whichtries,
                $groupstudentsjoins,
                $questions,
                $progress
            );
            $timeafter = microtime(true);

            // Verify that we waited as long as the timeout.
            $this->assertEqualsWithDelta($timeoutseconds, $timeafter - $timebefore, 1);
            $this->assertDebuggingCalled('Could not get lock on ' .
                    $qubaids->get_hash_code() . ' (Quiz ID ' . $quiz->id . ') after ' .
                    $timeoutseconds . ' seconds');
            $this->assertEquals([null, null], $result);
        } finally {
            $lock->release();
        }

        $this->resetDebugging();
        $result = $report->get_all_stats_and_analysis(
            $quiz,
            $whichattempts,
            $whichtries,
            $groupstudentsjoins,
            $questions
        );
        $this->assertDebuggingNotCalled();
        $this->assertNotEquals([null, null], $result);
    }

    /**
     * Test locking when the current page does not require calculations.
     *
     * When there is a lock on the hash code, test_get_all_stats_and_analysis() should return a null result immediately,
     * with no exception thrown.
     *
     * @return void
     */
    public function test_get_all_stats_and_analysis_locking_no_calculation(): void {
        $this->resetAfterTest(true);
        $quiz = $this->create_and_attempt_quiz();

        $whichattempts = QUIZ_GRADEAVERAGE; // All attempts.
        $whichtries = \question_attempt::ALL_TRIES;
        $groupstudentsjoins = new \core\dml\sql_join();
        $qubaids = quiz_statistics_qubaids_condition($quiz->id, $groupstudentsjoins, $whichattempts);

        $report = new \quiz_statistics_report();
        $questions = $report->load_and_initialise_questions_for_calculations($quiz);

        $timeoutseconds = 20;
        set_config('getstatslocktimeout', $timeoutseconds, 'quiz_statistics');

        $lock = self::$lockfactory->get_lock($qubaids->get_hash_code(), 0);

        $this->resetDebugging();
        try {
            $progress = new \core\progress\none();

            $timebefore = microtime(true);
            $result = $report->get_all_stats_and_analysis(
                $quiz,
                $whichattempts,
                $whichtries,
                $groupstudentsjoins,
                $questions,
                $progress,
                false
            );
            $timeafter = microtime(true);

            // Verify that we did not wait for the timeout before returning.
            $this->assertLessThan($timeoutseconds, $timeafter - $timebefore);
            $this->assertEquals([null, null], $result);
            $this->assertDebuggingNotCalled();
        } finally {
            $lock->release();
        }
    }
}

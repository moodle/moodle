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

/**
 * This file contains test helper code for testing the upgrade to the new
 * question engine. The actual tests are organised by question type in files
 * like question/type/truefalse/tests/upgradelibnewqe_test.php.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../upgradelib.php');


/**
 * Subclass of question_engine_attempt_upgrader to help with testing.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_question_engine_attempt_upgrader extends question_engine_attempt_upgrader {
    public function prevent_timeout() {
    }

    public function __construct($loader, $logger) {
        $this->questionloader = $loader;
        $this->logger = $logger;
    }
}


/**
 * Subclass of question_engine_upgrade_question_loader for unit testing.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_question_engine_upgrade_question_loader extends question_engine_upgrade_question_loader {
    public function put_question_in_cache($question) {
        $this->cache[$question->id] = $question;
    }

    public function load_question($questionid, $quizid) {
        global $CFG;

        if (isset($this->cache[$questionid])) {
            return $this->cache[$questionid];
        }

        return null;
    }

    public function put_dataset_in_cache($questionid, $selecteditem, $dataset) {
        $this->datasetcache[$questionid][$selecteditem] = $dataset;
    }

    public function load_dataset($questionid, $selecteditem) {
        global $DB;

        if (isset($this->datasetcache[$questionid][$selecteditem])) {
            return $this->datasetcache[$questionid][$selecteditem];
        }

        throw new coding_exception('Test dataset not loaded.');
    }
}


/**
 * Base class for tests that thest the upgrade of one particular attempt and
 * one question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_attempt_upgrader_test_base extends advanced_testcase {
    protected $updater;
    protected $loader;

    protected function setUp(): void {
        parent::setUp();
        $logger = new dummy_question_engine_assumption_logger();
        $this->loader = new test_question_engine_upgrade_question_loader($logger);
        $this->updater = new test_question_engine_attempt_upgrader($this->loader, $logger);
    }

    protected function tearDown(): void {
        $this->updater = null;
        parent::tearDown();
    }

    /**
     * Clear text, bringing independence of html2text results
     *
     * Some tests performing text comparisons of converted text are too much
     * dependent of the behavior of the html2text library. This function is
     * aimed to reduce such dependencies that should not affect the results
     * of these question attempt upgrade tests.
     */
    protected function clear_html2text_dependencies($qa) {
        // Cleaning all whitespace should be enough to ignore any html2text dependency
        if (property_exists($qa, 'responsesummary')) {
            $qa->responsesummary = preg_replace('/\s/', '', $qa->responsesummary);
        }
        if (property_exists($qa, 'questionsummary')) {
            $qa->questionsummary = preg_replace('/\s/', '', $qa->questionsummary);
        }
    }

    /**
     * Compare two qas, ignoring inessential differences.
     * @param object $expectedqa the expected qa.
     * @param object $qa the actual qa.
     */
    protected function compare_qas($expectedqa, $qa) {
        $this->clear_html2text_dependencies($expectedqa);
        $this->clear_html2text_dependencies($qa);

        $this->assertEquals($expectedqa, $qa);
    }
}

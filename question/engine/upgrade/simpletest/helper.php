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
 * question engine. The acutal tests are organised by question type in files
 * like question/type/truefalse/db/simpletest/testupgradelibnewqe.php.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../upgradelib.php');


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
 * Subclass of question_engine_assumption_logger that does nothing, for testing.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dummy_question_engine_assumption_logger extends question_engine_assumption_logger {
    protected $attemptid;

    public function __construct() {
    }

    public function log_assumption($description, $quizattemptid = null) {
    }

    public function __destruct() {
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
}


/**
 * Base class for tests that thest the upgrade of one particular attempt and
 * one question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_upgrader_test_base extends UnitTestCase {
    protected $updater;
    protected $loader;

    public function setUp() {
        $logger = new dummy_question_engine_assumption_logger();
        $this->loader = new test_question_engine_upgrade_question_loader($logger);
        $this->updater = new test_question_engine_attempt_upgrader($this->loader, $logger);
    }

    public function tearDown() {
        $this->updater = null;
    }
}

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
 * Basic test case.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * The simplest PHPUnit test case customised for Moodle
 *
 * It is intended for isolated tests that do not modify database or any globals.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class basic_testcase extends base_testcase {

    /**
     * Constructs a test case with the given name.
     *
     * Note: use setUp() or setUpBeforeClass() in your test cases.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    final public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->setBackupGlobals(false);
        $this->setBackupStaticAttributes(false);
        $this->setRunTestInSeparateProcess(false);
    }

    /**
     * Runs the bare test sequence and log any changes in global state or database.
     * @return void
     */
    final public function runBare(): void {
        global $DB;

        try {
            parent::runBare();

        } catch (Exception $ex) {
            $e = $ex;
        } catch (Throwable $ex) {
            // Engine errors in PHP7 throw exceptions of type Throwable (this "catch" will be ignored in PHP5).
            $e = $ex;
        }

        if (isset($e)) {
            // cleanup after failed expectation
            phpunit_util::reset_all_data();
            throw $e;
        }

        if ($DB->is_transaction_started()) {
            phpunit_util::reset_all_data();
            throw new coding_exception('basic_testcase '.$this->getName().' is not supposed to use database transactions!');
        }

        phpunit_util::reset_all_data(true);
    }
}

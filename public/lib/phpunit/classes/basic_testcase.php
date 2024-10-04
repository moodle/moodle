<?php
use PHPUnit\Framework\Attributes\After;
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
     *
     * @param string $name
     */
    final public function __construct($name = null) {
        parent::__construct($name);

        $this->setBackupGlobals(false);
        $this->setRunTestInSeparateProcess(false);
    }

    #[After]
    final public function test_teardown(): void {
        global $DB;

        if ($DB->is_transaction_started()) {
            phpunit_util::reset_all_data();
            throw new coding_exception('basic_testcase ' . $this->getName() . ' is not supposed to use database transactions!');
        }

        phpunit_util::reset_all_data(true);
    }

    /**
     * Get the name of the test.
     *
     * Replaces the original PHPUnit method.
     * @return string
     */
    final public function getName(): string {
        return $this->name();
    }
}

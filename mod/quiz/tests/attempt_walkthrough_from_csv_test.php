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

namespace mod_quiz;

// phpcs:disable moodle.PHPUnit.TestCaseNames.Missing

/**
 * Quiz attempt walk through using data from csv file.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class attempt_walkthrough_from_csv_test extends \mod_quiz\tests\attempt_walkthrough_testcase {
    #[\Override]
    protected static function get_test_files(): array {
        return ['questions', 'steps', 'results'];
    }
}

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
 * Unit tests for the quiz_statistics_table class.
 *
 * @package    quiz_statistics
 * @category   test
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/report/statistics/statistics_table.php');

/**
 * Class quiz_statistics_statistics_table_testcase
 *
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_statistics_statistics_table_testcase extends advanced_testcase {

    public function test_format_percentage() {
        $table = new quiz_statistics_table();

        // The format_percentage method is protected. Use Reflection to call the method.
        $reflector = new ReflectionClass('quiz_statistics_table');
        $method = $reflector->getMethod('format_percentage');
        $method->setAccessible(true);

        $this->assertEquals(
                '84.758%',
                $method->invokeArgs($table, [0.847576, true, 3])
        );

        $this->assertEquals(
                '84.758%',
                $method->invokeArgs($table, [84.7576, false, 3])
        );
    }

    public function test_format_percentage_range() {
        $table = new quiz_statistics_table();

        // The format_percentage_range method is protected. Use Reflection to call the method.
        $reflector = new ReflectionClass('quiz_statistics_table');
        $method = $reflector->getMethod('format_percentage_range');
        $method->setAccessible(true);

        $this->assertEquals(
                '54.400% − 84.758%',
                $method->invokeArgs($table, [0.544, 0.847576, true, 3])
        );

        $this->assertEquals(
                '54.400% − 84.758%',
                $method->invokeArgs($table, [54.4, 84.7576, false, 3])
        );
    }

    public function test_format_range() {
        $table = new quiz_statistics_table();

        // The format_range method is protected. Use Reflection to call the method.
        $reflector = new ReflectionClass('quiz_statistics_table');
        $method = $reflector->getMethod('format_range');
        $method->setAccessible(true);

        $this->assertEquals(
                '5 − 10',
                $method->invokeArgs($table, [5, 10])
        );

        $this->assertEquals(
                'Some Text − 10',
                $method->invokeArgs($table, ['Some Text', 10])
        );
    }
}

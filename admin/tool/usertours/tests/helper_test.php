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
 * Tests for helper.
 *
 * @package    tool_usertours
 * @copyright  2022 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for helper.
 *
 * @package    tool_usertours
 * @copyright  2022 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper_test extends advanced_testcase {

    /**
     * Data Provider for get_string_from_input.
     *
     * @return array
     */
    public function get_string_from_input_provider(): array {
        return [
            'Text'  => [
                'example',
                'example',
            ],
            'Text which looks like a langstring' => [
                'example,fakecomponent',
                'example,fakecomponent',
            ],
            'Text which is a langstring' => [
                'administration,core',
                'Administration',
            ],
            'Text which is a langstring but uses "moodle" instead of "core"' => [
                'administration,moodle',
                'Administration',
            ],
            'Text which is a langstring, but with extra whitespace' => [
                '  administration,moodle  ',
                'Administration',
            ],
            'Looks like a langstring, but has incorrect space around comma' => [
                'administration , moodle',
                'administration , moodle',
            ],
        ];
    }

    /**
     * Ensure that the get_string_from_input function returns langstring strings correctly.
     *
     * @dataProvider get_string_from_input_provider
     * @param string $string The string to test
     * @param string $expected The expected result
     */
    public function test_get_string_from_input($string, $expected) {
        $this->assertEquals($expected, helper::get_string_from_input($string));
    }
}

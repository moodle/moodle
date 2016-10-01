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
 * Tests for step.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for step.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class step_output_testcase extends advanced_testcase {

    /**
     * Data Provider for get_string_from_inpu.
     *
     * @return array
     */
    public function get_string_from_input_provider() {
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
     * Ensure that the get_string_from_input function returns translated
     * strings correctly.
     *
     * @dataProvider get_string_from_input_provider
     * @param   string  $string     The string to test
     * @param   string  $expected   The expected result
     */
    public function test_get_string_from_input($string, $expected) {
        $rc = new ReflectionClass('\\tool_usertours\\output\\step');
        $rcm = $rc->getMethod('get_string_from_input');
        $rcm->setAccessible(true);
        $this->assertEquals($expected, $rcm->invoke(null, $string));
    }
}

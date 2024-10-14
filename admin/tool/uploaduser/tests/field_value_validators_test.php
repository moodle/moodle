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

namespace tool_uploaduser;

use tool_uploaduser\local\field_value_validators;

/**
 * Tests for field value validators of tool_uploaduser.
 *
 * @package    tool_uploaduser
 * @copyright  2019 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class field_value_validators_test extends \advanced_testcase {

    /**
     * Data provider for \field_value_validators_testcase::test_validate_theme().
     */
    public static function themes_provider(): array {
        return [
            'User themes disabled' => [
                false, 'boost', 'warning', get_string('userthemesnotallowed', 'tool_uploaduser')
            ],
            'User themes enabled, empty theme' => [
                true, '', 'warning', get_string('notheme', 'tool_uploaduser')
            ],
            'User themes enabled, invalid theme' => [
                true, 'badtheme', 'warning', get_string('invalidtheme', 'tool_uploaduser', 'badtheme')
            ],
            'User themes enabled, valid theme' => [
                true, 'boost', 'normal', ''
            ],
        ];
    }

    /**
     * Unit test for \tool_uploaduser\local\field_value_validators::validate_theme()
     *
     * @dataProvider themes_provider
     * @param boolean $userthemesallowed Whether to allow user themes.
     * @param string $themename The theme name to be tested.
     * @param string $expectedstatus The expected status.
     * @param string $expectedmessage The expected validation message.
     */
    public function test_validate_theme($userthemesallowed, $themename, $expectedstatus, $expectedmessage): void {
        $this->resetAfterTest();

        // Set value for $CFG->allowuserthemes.
        set_config('allowuserthemes', $userthemesallowed);

        // Validate the theme.
        list($status, $message) = field_value_validators::validate_theme($themename);

        // Check the status and validation message.
        $this->assertEquals($expectedstatus, $status);
        $this->assertEquals($expectedmessage, $message);
    }
}

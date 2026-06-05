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

namespace core_user;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/user/editlib.php');

/**
 * Unit tests for useredit_validate_description_length().
 *
 * @package    core_user
 * @category   test
 * @copyright  2026 Andi Permana <andi.permana@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers ::useredit_validate_description_length
 */
final class editlib_validate_description_test extends \advanced_testcase {

    /**
     * Data provider for {@see test_useredit_validate_description_length}.
     *
     * @return array[]
     */
    public static function useredit_validate_description_length_provider(): array {
        define('USER_DESCRIPTION_MAX_LENGTH', 100);
        return [
            'empty description passes' => [
                [],
                [],
            ],
            'short description passes' => [
                ['description_editor' => ['text' => 'Hello world']],
                [],
            ],
            'exactly at limit passes' => [
                ['description_editor' => ['text' => str_repeat('a', USER_DESCRIPTION_MAX_LENGTH)]],
                [],
            ],
            'one char over limit fails' => [
                ['description_editor' => ['text' => str_repeat('a', USER_DESCRIPTION_MAX_LENGTH + 1)]],
                ['description_editor' => get_string('maximumchars', '', USER_DESCRIPTION_MAX_LENGTH)],
            ],
            'well over limit fails' => [
                ['description_editor' => ['text' => str_repeat('a', USER_DESCRIPTION_MAX_LENGTH * 10)]],
                ['description_editor' => get_string('maximumchars', '', USER_DESCRIPTION_MAX_LENGTH)],
            ],
        ];
    }

    /**
     * Test that useredit_validate_description_length returns correct errors.
     *
     * @dataProvider useredit_validate_description_length_provider
     * @param array $data Form data to validate.
     * @param array $expected Expected errors array.
     */
    public function test_useredit_validate_description_length(array $data, array $expected): void {
        $this->assertSame($expected, useredit_validate_description_length($data));
    }
}

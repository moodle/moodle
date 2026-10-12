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

namespace tool_langimport;

/**
 * Tests for \tool_langimport\locale class.
 *
 * @package    tool_langimport
 * @category   test
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\tool_langimport\controller::class)]
final class controller_test extends \advanced_testcase {

    /**
     * Test uninstall of language with invalid values.
     *
     * @params string $lang
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('uninstall_lang_invalid_provider')]
    public function test_uninstall_lang_invalid(string $lang): void {
        global $CFG;

        $controller = new controller();
        $this->assertFalse($controller->uninstall_language($lang));
        $this->assertFileExists("{$CFG->dataroot}/lang");
        $this->assertFileExists("{$CFG->dirroot}/lang/en");
    }

    /**
     * Data provider for uninstall_lang tests with invalid values.
     *
     * @return array
     */
    public static function uninstall_lang_invalid_provider(): array {
        return [
            'Empty string' => [''],
            'Meaningless empty string' => [' '],
            'Default language' => ['en'],
            'Invalid language string' => ['swedish'],
        ];
    }
}

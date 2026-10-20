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

namespace tool_dataprivacy;

/**
 * Tests for the tool_dataprivacy hook callbacks.
 *
 * @package    tool_dataprivacy
 * @copyright  2026 Jayce Birrell <jayce.birrell@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(hook_callbacks::class)]
final class hook_callbacks_test extends \advanced_testcase {
    /**
     * Test the user deletion confirmation only warns about automatic deletion requests when they are enabled.
     *
     * @param int $enabled Value of the automaticdeletionrequests setting
     * @param bool $expectwarning Whether the warning is expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('user_deletion_confirmation_text_provider')]
    public function test_user_deletion_confirmation_text(int $enabled, bool $expectwarning): void {
        $this->resetAfterTest();
        set_config('automaticdeletionrequests', $enabled, 'tool_dataprivacy');

        $text = \core\user::get_deletion_confirmation_text();
        $warning = get_string('automaticdeletionrequestswarning', 'tool_dataprivacy');

        $this->assertSame($expectwarning, str_contains($text, $warning));
        $this->assertSame($expectwarning, str_contains($text, get_docs_url('Data_privacy')));
    }

    /**
     * Data provider for {@see test_user_deletion_confirmation_text}.
     *
     * @return array
     */
    public static function user_deletion_confirmation_text_provider(): array {
        return [
            'Enabled' => [1, true],
            'Disabled' => [0, false],
        ];
    }
}

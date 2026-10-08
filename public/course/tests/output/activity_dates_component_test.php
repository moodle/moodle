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

namespace core_course\output;

/**
 * Tests for the activity dates component.
 *
 * @package    core_course
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(activity_dates_component::class)]
final class activity_dates_component_test extends \advanced_testcase {
    /**
     * Test the dates are formatted, including the dates relative to another date.
     */
    public function test_get_formatted_dates(): void {
        $this->resetAfterTest();

        $component = new activity_dates_component([
            ['label' => 'Opens:', 'timestamp' => 1700000000],
            ['label' => 'Due:', 'timestamp' => 1700000000 + DAYSECS, 'relativeto' => 1700000000],
            ['label' => 'Closes:', 'timestamp' => 1700000000, 'relativeto' => 1700000000 + HOURSECS],
        ]);

        $this->assertTrue($component->has_dates());
        $this->assertEquals(
            [
                ['label' => 'Opens:', 'value' => userdate(1700000000, get_string('strftimedaydatetime', 'core_langconfig'))],
                ['label' => 'Due:', 'value' => '1d 0h 0m after course start'],
                ['label' => 'Closes:', 'value' => '0d 1h 0m before course start'],
            ],
            $component->get_formatted_dates(),
        );
    }
}

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
 * Tests for tool_customlang_utils checkout progress calculation.
 *
 * @package    tool_customlang
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_customlang;

use advanced_testcase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use tool_customlang_utils;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/customlang/locallib.php');

/**
 * Tests for tool_customlang_utils checkout progress calculation.
 *
 * @package    tool_customlang
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversMethod(tool_customlang_utils::class, 'calculate_checkout_percent')]
final class locallib_test extends advanced_testcase {
    /**
     * The in-progress checkout percentage must never reach 100, even once the
     * rough string estimate has been reached or exceeded, so that the caller
     * cannot mistake an in-progress checkout for a completed one.
     *
     * @param int $done number of strings processed so far
     * @param int $expected expected in-progress percentage
     */
    #[DataProvider('calculate_checkout_percent_provider')]
    public function test_calculate_checkout_percent(int $done, int $expected): void {
        $this->assertSame($expected, tool_customlang_utils::calculate_checkout_percent($done));
    }

    /**
     * Data provider for {@see self::test_calculate_checkout_percent()}.
     *
     * @return array
     */
    public static function calculate_checkout_percent_provider(): array {
        $rough = tool_customlang_utils::ROUGH_NUMBER_OF_STRINGS;

        return [
            'no strings processed yet' => [0, 0],
            'partway through the rough estimate' => [(int) ($rough / 2), 49],
            'one string below the rough estimate' => [$rough - 1, 98],
            // This is the exact scenario from MDL-89494: once the number of
            // processed strings reaches the rough estimate, the in-progress
            // percentage must stay below 100, because real checkouts on
            // larger sites can process far more strings than the estimate
            // before the loop actually finishes.
            'processed count equals the rough estimate' => [$rough, 99],
            'processed count exceeds the rough estimate' => [$rough + 5000, 99],
        ];
    }
}

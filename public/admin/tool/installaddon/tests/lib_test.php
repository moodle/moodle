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
 * Unit tests for tool_installaddon lib.
 *
 * @package    tool_installaddon
 * @copyright  2026 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_installaddon;

use core_course\local\entity\activity_chooser_footer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/installaddon/lib.php');

/**
 * Test installaddon lib functions.
 */
final class lib_test extends \advanced_testcase {
    /**
     * Tests chooser footer generation for marketplace link content.
     *
     * @covers ::tool_installaddon_custom_chooser_footer
     */
    public function test_tool_installaddon_custom_chooser_footer(): void {
        $this->resetAfterTest();

        $footer = \tool_installaddon_custom_chooser_footer(1, 1);

        $this->assertInstanceOf(activity_chooser_footer::class, $footer);
        $this->assertSame('tool_installaddon/footer', $footer->get_footer_js_file());
        $this->assertStringContainsString('https://marketplace.moodle.com/', $footer->get_footer_template());
        $this->assertStringContainsString('site=', $footer->get_footer_template());
    }
}

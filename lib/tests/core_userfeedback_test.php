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

namespace core;

/**
 * Tests for \core_userfeedback
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_userfeedback
 */
final class core_userfeedback_test extends \advanced_testcase {
    public function test_footer_not_added_if_disabled(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $page = new \moodle_page();
        $renderer = new \core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->standard_footer_html();
        $this->assertStringNotContainsString(
            get_string('calltofeedback_give', 'core'),
            $html,
        );
    }

    public function test_footer_added_if_enabled_loggedin(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('enableuserfeedback', 1);;

        $page = new \moodle_page();
        $renderer = new \core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->standard_footer_html();
        $this->assertStringContainsString(
            get_string('calltofeedback_give', 'core'),
            $html,
        );
    }

    public function test_footer_not_added_if_loggedout(): void {
        $this->resetAfterTest();

        set_config('enableuserfeedback', 1);;

        $page = new \moodle_page();
        $renderer = new \core_renderer($page, RENDERER_TARGET_GENERAL);

        $html = $renderer->standard_footer_html();
        $this->assertStringNotContainsString(
            get_string('calltofeedback_give', 'core'),
            $html,
        );
    }
}

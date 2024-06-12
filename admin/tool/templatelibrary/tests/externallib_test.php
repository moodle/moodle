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

namespace tool_templatelibrary;

use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External learning plans webservice API tests.
 *
 * @package tool_templatelibrary
 * @copyright 2015 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class externallib_test extends externallib_advanced_testcase {

    /**
     * Test list all.
     */
    public function test_list_templates(): void {
        $result = external::list_templates('', '');
        $count = count($result);
        // We have 3 templates in this tool - and there must be more else where.
        $this->assertGreaterThan(3, $count);
    }

    /**
     * Test we can filter by component.
     */
    public function test_list_templates_for_component(): void {
        $result = external::list_templates('tool_templatelibrary', '');
        $count = count($result);
        $this->assertEquals(3, $count);

        $this->assertContains("tool_templatelibrary/display_template", $result);
        $this->assertContains("tool_templatelibrary/search_results", $result);
        $this->assertContains("tool_templatelibrary/list_templates_page", $result);
    }

    /**
     * Test we can filter by a string.
     */
    public function test_list_templates_with_filter(): void {
        $result = external::list_templates('tool_templatelibrary', 'page');
        $count = count($result);
        // Should be only one matching template.
        $this->assertEquals(1, $count);
        $this->assertEquals($result[0], "tool_templatelibrary/list_templates_page");
    }

    public function test_load_canonical_template(): void {
        global $CFG;

        $originaltheme = $CFG->theme;
        // Change the theme to 'base' because it overrides these templates.
        $CFG->theme = 'boost';

        $template = external::load_canonical_template('core', 'notification_error');

        // Only the base template should contain the docs.
        $this->assertStringContainsString('@template core/notification_error', $template);

        // Restore the original theme.
        $CFG->theme = $originaltheme;
    }
}

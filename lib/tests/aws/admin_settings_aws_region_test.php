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
 * factor_sms unit tests.
 *
 * @package   core
 * @author    Mikhail Golenkov <mikhailgolenkov@catalyst-au.net>
 * @copyright 2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\aws;

/**
 * Testcase for the list of AWS regions admin setting.
 *
 * @package    core
 * @author     Mikhail Golenkov <mikhailgolenkov@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\aws\admin_settings_aws_region
 */
class admin_settings_aws_region_test extends \advanced_testcase {

    /**
     * Cleanup after all tests are executed.
     *
     * @return void
     */
    public function tearDown(): void {
        $admin = admin_get_root();
        $admin->purge_children(true);
    }
    /**
     * Test that output_html() method works and returns HTML string with expected content.
     */
    public function test_output_html(): void {
        $this->resetAfterTest();
        $setting = new admin_settings_aws_region('test_aws_region',
            'Test visible name', 'Test description', 'Test default setting');
        $html = $setting->output_html('');
        $this->assertTrue(str_contains($html, 'Test visible name'));
        $this->assertTrue(str_contains($html, 'Test description'));
        $this->assertTrue(str_contains($html, 'Default: Test default setting'));
        $this->assertTrue(str_contains($html,
            '<input type="text" list="s__test_aws_region" name="s__test_aws_region" value=""'));
        $this->assertTrue(str_contains($html, '<datalist id="s__test_aws_region">'));
        $this->assertTrue(str_contains($html, '<option value="'));
    }
}

<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace tool_capability;

// phpcs:disable moodle.PHPUnit.TestCaseProvider.dataProviderSyntaxMethodNotFound

/**
 * Detect common problems in capability definitions of plugins.
 *
 * @group     plugin_checks
 * @package   tool_capability
 * @copyright 2025 Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class plugin_checks_test extends \core\tests\plugin_checks_testcase {
    /**
     * Verify contents of plugin db/access.php file.
     *
     * @dataProvider all_plugins_provider
     * @coversNothing
     *
     * @param string $component
     * @param string $plugintype
     * @param string $pluginname
     * @param string $dir
     */
    public function test_db_access_file(string $component, string $plugintype, string $pluginname, string $dir): void {
        global $CFG;

        $stringmanager = get_string_manager();
        $corerolefile = "$CFG->dirroot/lang/en/role.php";
        $langfile = "$dir/lang/en/$component.php";

        $file = "$dir/db/access.php";
        $capabilities = $this->fetch_array_from_file($file, 'capabilities');
        if (!$capabilities) {
            $this->expectNotToPerformAssertions();
            return;
        }

        foreach ($capabilities as $capname => $capability) {
            if ($plugintype === 'qbank' && str_starts_with($capname, 'moodle/question:')) {
                // Question bank capabilities are irregular.
                $strname = explode('/', $capname, 2)[1];
                $this->assertTrue($stringmanager->string_exists($strname, 'core_role'),
                    "Missing capability name string '$strname' in $corerolefile");
                continue;
            }
            $this->assertMatchesRegularExpression("|^$plugintype/$pluginname:[a-z0-9_]+$|", $capname);
            $strname = substr($capname, strlen($plugintype) + 1);
            $this->assertTrue($stringmanager->string_exists($strname, $component),
                "Missing capability name string '$strname' in $langfile");
            $this->assertSame($capname, clean_param($capname, PARAM_CAPABILITY));
        }
    }
}

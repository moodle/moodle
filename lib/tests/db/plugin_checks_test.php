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

namespace core\db;

// phpcs:disable moodle.PHPUnit.TestCaseProvider.dataProviderSyntaxMethodNotFound

/**
 * Detect common problems in plugin database structures.
 *
 * @group     plugin_checks
 * @package   core
 * @copyright 2025 Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class plugin_checks_test extends \core\tests\plugin_checks_testcase {
    /**
     * Verify plugin db/install.xml file.
     *
     * @dataProvider all_plugins_provider
     * @coversNothing
     *
     * @param string $component
     * @param string $plugintype
     * @param string $pluginname
     * @param string $dir
     */
    public function test_db_install_file(string $component, string $plugintype, string $pluginname, string $dir): void {
        global $DB;
        $DB->get_manager(); // Preload XMLDB classes.

        $file = "$dir/db/install.xml";
        if (!file_exists($file)) {
            $this->expectNotToPerformAssertions();
            return;
        }

        $rawcontents = file_get_contents($file);
        $xmldb = new \xmldb_file($file);
        $xmldb->loadXMLStructure();
        $xmlcontents = $xmldb->getStructure()->xmlOutput();
        $this->assertXmlStringEqualsXmlString(
            $rawcontents,
            $xmlcontents,
            "XMLDB structure does not match the install.xml file in $file",
        );
    }
}

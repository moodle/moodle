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

namespace core\event;

// phpcs:disable moodle.PHPUnit.TestCaseProvider.dataProviderSyntaxMethodNotFound

/**
 * Detect common problems in plugin events.
 *
 * @group     plugin_checks
 * @package   core
 * @copyright 2025 Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class plugin_checks_test extends \core\tests\plugin_checks_testcase {
    /**
     * Verify all plugin events.
     *
     * @dataProvider all_plugins_provider
     * @coversNothing
     *
     * @param string $component
     * @param string $plugintype
     * @param string $pluginname
     * @param string $dir
     */
    public function test_event_classes(string $component, string $plugintype, string $pluginname, string $dir): void {
        $events = \core_component::get_component_classes_in_namespace($component, 'event');
        if (!$events) {
            $this->expectNotToPerformAssertions();
            return;
        }

        foreach ($events as $eventclassname => $unused) {
            $rc = new \ReflectionClass($eventclassname);
            if ($rc->isAbstract()) {
                continue;
            }
            if (!is_subclass_of($eventclassname, \core\event\base::class)) {
                // Most likely an observer in irregular location, ignore for now.
                continue;
            }
            $this->assertIsString($eventclassname::get_name());
        }
    }
}

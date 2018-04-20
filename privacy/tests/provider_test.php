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
 * Unit tests for all Privacy Providers.
 *
 * @package     core_privacy
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\manager;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\metadata\types\type;
use \core_privacy\local\metadata\types\database_table;
use \core_privacy\local\metadata\types\external_location;
use \core_privacy\local\metadata\types\plugin_type_link;
use \core_privacy\local\metadata\types\subsystem_link;
use \core_privacy\local\metadata\types\user_preference;

/**
 * Unit tests for all Privacy Providers.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_testcase extends advanced_testcase {
    /**
     * Returns a list of frankenstyle names of core components (plugins and subsystems).
     *
     * @return array the array of frankenstyle component names with the relevant class name.
     */
    public function get_component_list() {
        $components = [];
        // Get all plugins.
        $plugintypes = \core_component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $typedir) {
            $plugins = \core_component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $frankenstyle = $plugintype . '_' . $pluginname;
                $components[$frankenstyle] = [
                    'component' => $frankenstyle,
                    'classname' => manager::get_provider_classname_for_component($frankenstyle),
                ];

            }
        }
        // Get all subsystems.
        foreach (\core_component::get_core_subsystems() as $name => $path) {
            if (isset($path)) {
                $frankenstyle = 'core_' . $name;
                $components[$frankenstyle] = [
                    'component' => $frankenstyle,
                    'classname' => manager::get_provider_classname_for_component($frankenstyle),
                ];
            }
        }
        return $components;
    }

    /**
     * Test that the specified null_provider works as expected.
     *
     * @dataProvider null_provider_provider
     * @param   string  $component The name of the component.
     * @param   string  $classname The name of the class for privacy
     */
    public function test_null_provider($component, $classname) {
        $reason = $classname::get_reason();
        $this->assertInternalType('string', $reason);

        $this->assertInternalType('string', get_string($reason, $component));
        $this->assertDebuggingNotCalled();
    }

    /**
     * Data provider for the null_provider tests.
     *
     * @return array
     */
    public function null_provider_provider() {
        return array_filter($this->get_component_list(), function($component) {
                return static::component_implements(
                    $component['classname'],
                    \core_privacy\local\metadata\null_provider::class
                );
        });
    }

    /**
     * Test that the specified metadata_provider works as expected.
     *
     * @dataProvider metadata_provider_provider
     * @param   string  $component The name of the component.
     * @param   string  $classname The name of the class for privacy
     */
    public function test_metadata_provider($component, $classname) {
        global $DB;

        $collection = new collection($component);
        $metadata = $classname::get_metadata($collection);
        $this->assertInstanceOf(collection::class, $metadata);
        $this->assertSame($collection, $metadata);
        $this->assertContainsOnlyInstancesOf(type::class, $metadata->get_collection());

        foreach ($metadata->get_collection() as $item) {
            // All items must have a valid string name.
            // Note: This is not a string identifier.
            $this->assertInternalType('string', $item->get_name());

            if ($item instanceof database_table) {
                // Check that the table is valid.
                $this->assertTrue($DB->get_manager()->table_exists($item->get_name()));
            }

            if ($summary = $item->get_summary()) {
                // Summary is optional, but when provided must be a valid string identifier.
                $this->assertInternalType('string', $summary);

                // Check that the string is also correctly defined.
                $this->assertInternalType('string', get_string($summary, $component));
                $this->assertDebuggingNotCalled();
            }

            if ($fields = $item->get_privacy_fields()) {
                // Privacy fields are optional, but when provided must be a valid string identifier.
                foreach ($fields as $field => $identifier) {
                    $this->assertInternalType('string', $field);
                    $this->assertInternalType('string', $identifier);

                    // Check that the string is also correctly defined.
                    $this->assertInternalType('string', get_string($identifier, $component));
                    $this->assertDebuggingNotCalled();
                }
            }
        }
    }

    /**
     * Data provider for the metadata\provider tests.
     *
     * @return array
     */
    public function metadata_provider_provider() {
        return array_filter($this->get_component_list(), function($component) {
                return static::component_implements(
                    $component['classname'],
                    \core_privacy\local\metadata\provider::class
                );
        });
    }

    /**
     * Checks whether the component's provider class implements the specified interface, either directly or as a grandchild.
     *
     * @param   string  $providerclass The name of the class to test.
     * @param   string  $interface the name of the interface we want to check.
     * @return  bool    Whether the class implements the interface.
     */
    protected static function component_implements($providerclass, $interface) {
        if (class_exists($providerclass) && interface_exists($interface)) {
            return is_subclass_of($providerclass, $interface);
        }

        return false;
    }

}

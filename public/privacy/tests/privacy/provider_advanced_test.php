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

namespace core_privacy\privacy;

use core_privacy\manager;

/**
 * Slow unit tests for all Privacy Providers that require database modifications.
 *
 * @package     core_privacy
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @copyright   2025 Petr Skoda
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_advanced_test extends \advanced_testcase {
    /**
     * Returns a list of frankenstyle names of core components (plugins and subsystems).
     *
     * @return array the array of frankenstyle component names with the relevant class name.
     */
    public static function get_component_list(): array {
        $components = ['core' => [
            'component' => 'core',
            'classname' => manager::get_provider_classname_for_component('core'),
        ]];
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
     * Ensure that providers do not throw an error when processing a deleted user.
     *
     * @group           plugin_checks
     * @dataProvider    is_user_data_provider
     * @coversNothing
     * @param   string  $component
     */
    public function test_component_understands_deleted_users($component): void {
        $this->resetAfterTest();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();

        // Delete the user and their context.
        delete_user($user);
        $usercontext = \context_user::instance($user->id);
        $usercontext->delete();

        $contextlist = manager::component_class_callback($component, \core_privacy\local\request\core_user_data_provider::class,
            'get_contexts_for_userid', [$user->id]);

        $this->assertInstanceOf(\core_privacy\local\request\contextlist::class, $contextlist);
    }

    /**
     * List of providers which implement the core_user_data_provider.
     *
     * @return array
     */
    public static function is_user_data_provider(): array {
        return array_map(
            fn ($data) => ['component' => $data['component']],
            array_filter(self::get_component_list(), function($component): bool {
                return static::component_implements(
                    $component['classname'],
                    \core_privacy\local\request\core_user_data_provider::class
                );
            }),
        );
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

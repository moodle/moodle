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
 * Provides testable_core_plugin_manager class.
 *
 * @package     core
 * @category    test
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/testable_update_api.php');

/**
 * Testable variant of the core_plugin_manager
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_core_plugin_manager extends core_plugin_manager {

    /** @var testable_core_plugin_manager holds the singleton instance */
    protected static $singletoninstance;

    /**
     * Allows us to inject items directly into the plugins info tree.
     *
     * Do not forget to call our reset_caches() after using this method to force a new
     * singleton instance.
     *
     * @param string $type plugin type
     * @param string $name plugin name
     * @param \core\plugininfo\base $plugininfo plugin info class
     */
    public function inject_testable_plugininfo($type, $name, \core\plugininfo\base $plugininfo) {

        // Let the parent initialize the ->pluginsinfo tree.
        parent::get_plugins();

        // Inject the additional plugin info.
        $this->pluginsinfo[$type][$name] = $plugininfo;
    }

    /**
     * Returns testable subclass of the client.
     *
     * @return \core\update\testable_api
     */
    protected function get_update_api_client() {
        return \core\update\testable_api::client();
    }

    /**
     * Mockup implementation of loading available updates info.
     *
     * This testable implementation does not actually use
     * {@link \core\update\checker}. Instead, it provides hard-coded list of
     * fictional available updates for our foo_bar plugin.
     *
     * Note there is a difference in the behaviour as the actual update API
     * does not return info of lower version than requested. To mock up well,
     * make sure the injected foo_bar testable plugin info has version lower
     * than the lowest one returned here.
     *
     * @param string $component
     * @return array|null array of \core\update\info objects or null
     */
    public function load_available_updates_for_plugin($component) {

        if ($component === 'foo_bar') {
            $updates = array();

            $updates[] = new \core\update\info($component, array(
                'version' => '2015093000',
                'release' => 'Foo bar 15.09.30 beta',
                'maturity' => MATURITY_BETA,
            ));

            $updates[] = new \core\update\info($component, array(
                'version' => '2015100400',
                'release' => 'Foo bar 15.10.04',
                'maturity' => MATURITY_STABLE,
            ));

            $updates[] = new \core\update\info($component, array(
                'version' => '2015100500',
                'release' => 'Foo bar 15.10.05 beta',
                'maturity' => MATURITY_BETA,
            ));

            return $updates;
        }

        return null;
    }

    /**
     * Adds fake plugin information from record.
     *
     * @param testable_plugininfo_base $record
     * @return void
     */
    public function add_fake_plugin_info($record): void {
        $this->load_present_plugins();

        $this->presentplugins[$record->type][$record->name] = $record;
    }
}

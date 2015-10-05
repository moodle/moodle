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
     */
    public function inject_testable_plugininfo($type, $name, \core\plugininfo\base $plugininfo) {
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
     * fictional available updates for some standard plugin.
     *
     * @param string $component
     * @return array|null array of \core\update\info objects or null
     */
    public function load_available_updates_for_plugin($component) {

        if ($component === 'mod_forum') {
            $updates = array();

            $updates[] = new \core\update\info($component, array(
                'version' => '2002073008',
                'release' => 'Forum 0.1',
                'maturity' => MATURITY_ALPHA,
                'url' => 'https://en.wikipedia.org/wiki/Moodle',
                'download' => 'https://moodle.org/plugins/pluginversion.php?id=1',
                'downloadmd5' => md5('I can not think of anything funny to type here'),
            ));

            $updates[] = new \core\update\info($component, array(
                'version' => '2999122400',
                'release' => 'Forum NG',
                'maturity' => MATURITY_BETA,
            ));

            return $updates;
        }

        return null;
    }
}

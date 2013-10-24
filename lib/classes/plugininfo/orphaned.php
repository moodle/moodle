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
 * Defines class used for orphaned subplugins.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\plugininfo;

defined('MOODLE_INTERNAL') || die();


/**
 * Orphaned subplugins class.
 */
class orphaned extends base {
    public function is_uninstall_allowed() {
        return true;
    }

    /**
     * We do not know if orphaned subplugins are enabled.
     * @return bool
     */
    public function is_enabled() {
        return null;
    }

    /**
     * No lang strings are present.
     */
    public function init_display_name() {
        $this->displayname = $this->component;
    }

    /**
     * Oprhaned plugins can not be enabled.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        return null;
    }

    /**
     * Gathers and returns the information about all plugins of the given type,
     * either on disk or previously installed.
     *
     * @param string $type the name of the plugintype, eg. mod, auth or workshopform
     * @param string $typerootdir full path to the location of the plugin dir
     * @param string $typeclass the name of the actually called class
     * @return array of plugintype classes, indexed by the plugin name
     */
    public static function get_plugins($type, $typerootdir, $typeclass) {
        $return = array();
        $manager = \core_plugin_manager::instance();
        $plugins = $manager->get_installed_plugins($type);

        foreach ($plugins as $name => $version) {
            $plugin              = new $typeclass();
            $plugin->type        = $type;
            $plugin->typerootdir = $typerootdir;
            $plugin->name        = $name;
            $plugin->rootdir     = null;
            $plugin->displayname = $name;
            $plugin->versiondb   = $version;
            $plugin->init_is_standard();

            $return[$name] = $plugin;
        }

        return $return;
    }
}

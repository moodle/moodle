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
 * Addon.
 *
 * @package    block_xp
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\plugin;

/**
 * Addon class.
 *
 * You might be looking here wondering whether this is, in fact, the addon. No, it isn't.
 *
 * @package    block_xp
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addon {

    /**
     * Get the expected release version.
     *
     * @return string
     */
    final public function get_expected_release() {
        $pluginman = \core_plugin_manager::instance();
        $blockxp = $pluginman->get_plugin_info('block_xp');
        $release = $blockxp ? $blockxp->release : '';
        if (!preg_match('/^([0-9]+)\.([0-9]+)/', $release, $parts)) {
            return '?';
        }
        return ((int) $parts[1]) - 2 . '.' . $parts[2];
    }

    /**
     * Whether the plugin's release.
     *
     * @return string
     */
    final public function get_release() {
        $localxp = static::get_plugin_info();
        return $localxp ? $localxp->release : '-';
    }

    /**
     * Whether the plugin is activated.
     *
     * @return bool
     */
    public function is_activated() {
        // Consider activated if we detect a legacy version.
        return $this->is_legacy_version_present();
    }

    /**
     * Whether a legacy version is installed.
     *
     * The legacy version do not know about the concept of this class, which
     * can lead to issues. In order to avoid breaking existing installations
     * using currently outdated local_xp, we flag the addon as activated
     * when using a legacy version.
     *
     * @return bool
     */
    final public function is_legacy_version_present() {
        $localxp = static::get_plugin_info();
        return !empty($localxp) && $localxp->versiondb < 2022021115;
    }

    /**
     * Whether the plugin is installed and upgraded.
     *
     * @return bool
     */
    public function is_installed_and_upgraded() {
        return $this->is_legacy_version_present()
            && static::get_plugin_info()->is_installed_and_upgraded();
    }

    /**
     * Whether the addon is older than.
     *
     * @param int $version The version to test against.
     * @return bool
     */
    public function is_older_than($version) {
        $localxp = static::get_plugin_info();
        return !empty($localxp) && $localxp->versiondb < $version;
    }

    /**
     * Whether the plugin is out of sync.
     *
     * @return bool
     */
    public function is_out_of_sync() {
        // If we use a legacy version, we're certain to be out of sync.
        return $this->is_legacy_version_present();
    }

    /**
     * Whether the plugin is present.
     *
     * @return bool
     */
    final public function is_present() {
        $localxp = static::get_plugin_info();
        return !empty($localxp);
    }

    /**
     * Require the plugin to be activated.
     */
    public function require_activated() {
        if (!$this->is_activated()) {
            throw new \moodle_exception('addonnotactivated', 'block_xp');
        }
    }

    /**
     * Get the plugin info.
     *
     * @return \core\plugininfo\base|null
     */
    public static function get_plugin_info() {
        $pluginman = \core_plugin_manager::instance();
        return $pluginman->get_plugin_info('local_xp');
    }

    /**
     * Whether the plugin is automatically.
     *
     * @return bool
     */
    public static function is_automatically_activated() {
        global $CFG;
        return empty($CFG->local_xp_disable_automatic_activation);
    }

    /**
     * Whether the plugin is marked to activate.
     *
     * @return bool
     */
    public static function is_marked_to_activate() {
        global $CFG;
        return !empty($CFG->local_xp_activate);
    }

    /**
     * Simplest check to identify whether the plugin is present.
     *
     * @return bool
     */
    public static function is_container_present() {
        return class_exists('local_xp\local\container');
    }

    /**
     * Whether the plugin should be activated.
     *
     * @return bool
     */
    public static function should_activate() {
        return static::is_container_present()
            && (static::is_automatically_activated() || static::is_marked_to_activate());
    }
}

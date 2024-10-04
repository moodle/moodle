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
 * Defines classes used for plugin info.
 *
 * @package    core
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\plugininfo;

use admin_externalpage;
use moodle_url;
use part_of_admin_tree;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * Class for repositories
 */
class repository extends base {

    /** @var int Repository state, when it's enabled and visible. */
    public const REPOSITORY_ON = 1;

    /** @var int Repository state, when it's enabled but hidden. */
    public const REPOSITORY_OFF = 0;

    /** @var int Repository state, when it's disabled. */
    public const REPOSITORY_DISABLED = -1;

    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        global $DB;
        return $DB->get_records_menu('repository', null, 'type ASC', 'type, type AS val');
    }

    /**
     * Returns current status for a pluginname.
     *
     * Repositories needs to be calculated in a different way than the default method in the base class because they need to take
     * into account the value of the visible column too.
     *
     * @param string $pluginname The plugin name to check.
     * @return int The current status (enabled, disabled...) of $pluginname.
     */
    public static function get_enabled_plugin(string $pluginname): int {
        global $DB;

        $repository = $DB->get_record('repository', ['type' => $pluginname]);
        if ($repository) {
            switch ($repository->visible) {
                case 1:
                    $value = self::REPOSITORY_ON;
                    break;
                default:
                    $value = self::REPOSITORY_OFF;
            }
        } else {
            $value = self::REPOSITORY_DISABLED;
        }
        return $value;
    }

    /**
     * Enable or disable a plugin.
     * When possible, the change will be stored into the config_log table, to let admins check when/who has modified it.
     *
     * @param string $pluginname The plugin name to enable/disable.
     * @param int $enabled Whether the pluginname should be enabled and visible (1), enabled but hidden (0) or disabled (-1).
     *
     * @return bool Whether $pluginname has been updated or not.
     */
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        global $DB;

        $haschanged = false;

        // Enabled repositories exist in 'repository' table.
        // Visible = REPOSITORY_ON ==> Enabled and visible.
        // Visible = REPOSITORY_OFF ==> Enabled but hidden.
        // Disabled repositories don't exist in 'repository' table.
        if ($plugin = $DB->get_record('repository', ['type' => $pluginname])) {
            // The plugin is enabled.
            $oldvalue = $plugin->visible;
            $repositorytype = \repository::get_type_by_typename($pluginname);
            if ($enabled == self::REPOSITORY_DISABLED) {
                $haschanged = $repositorytype->delete();
                $enabled = '';
            } else if ($oldvalue != $enabled) {
                $haschanged = $repositorytype->update_visibility($enabled);
            }
        } else {
            // Not all repositories have their own 'repository' record created. Disabled repositories don't have one.
            // Make changes only when we want to enable repository.
            $oldvalue = '';
            if ($enabled == self::REPOSITORY_ON || $enabled == self::REPOSITORY_OFF) {
                $type = new \repository_type($pluginname, [], $enabled);
                if (!$haschanged = $type->create()) {
                    throw new \moodle_exception('invalidplugin', 'repository', '', $pluginname);
                }
            }
        }
        if ($haschanged) {
            // Include this information into config changes table.
            add_to_config_log('repository_visibility', $oldvalue, $enabled, $pluginname);
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    public function get_settings_section_name() {
        return 'repositorysettings'.$this->name;
    }

    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if ($hassiteconfig && $this->is_enabled()) {
            // Completely no access to repository setting when it is not enabled.
            $sectionname = $this->get_settings_section_name();
            $settings = new admin_externalpage($sectionname, $this->displayname,
                new moodle_url('/admin/repository.php', ['action' => 'edit', 'repos' => $this->name]), 'moodle/site:config', false);
            $adminroot->add($parentnodename, $settings);
        }
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return moodle_url
     */
    public static function get_manage_url() {
        return new moodle_url('/admin/repository.php');
    }

    /**
     * Defines if there should be a way to uninstall the plugin via the administration UI.
     * @return boolean
     */
    public function is_uninstall_allowed() {
        if ($this->name === 'upload' || $this->name === 'coursefiles' || $this->name === 'user' || $this->name === 'recent') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Pre-uninstall hook.
     * This is intended for disabling of plugin, some DB table purging, etc.
     * Converts all linked files to standard files when repository is removed
     * and cleans up all records in the DB for that repository.
     */
    public function uninstall_cleanup() {
        global $CFG;
        require_once($CFG->dirroot.'/repository/lib.php');

        $repo = \repository::get_type_by_typename($this->name);
        if ($repo) {
            $repo->delete(true);
        }

        parent::uninstall_cleanup();
    }
}

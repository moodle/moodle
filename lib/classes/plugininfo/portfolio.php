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

use moodle_url;

/**
 * Class for portfolios
 */
class portfolio extends base {

    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        global $DB;

        $enabled = array();
        $rs = $DB->get_recordset('portfolio_instance', array('visible'=>1), 'plugin ASC', 'plugin');
        foreach ($rs as $repository) {
            $enabled[$repository->plugin] = $repository->plugin;
        }
        $rs->close();

        return $enabled;
    }

    public static function enable_plugin(string $pluginname, int $enabled): bool {
        global $DB, $CFG;

        require_once($CFG->libdir . '/portfoliolib.php');

        $haschanged = false;
        $oldvalue = null;
        $data = ['visible' => $enabled];
        if ($plugin = $DB->get_record('portfolio_instance', ['plugin' => $pluginname])) {
            $instance = portfolio_instance($plugin->id);
            $oldvalue = $instance->get('visible');
            if (empty($oldvalue) && $instance->instance_sanity_check()) {
                throw new \moodle_exception('cannotsetvisible', 'portfolio');
            }

            // Only set visibility if it's different from the current value.
            if ($oldvalue != $enabled) {
                $haschanged = true;
                $instance->set('visible', $enabled);
                $instance->save();
            }
        } else {
            $haschanged = true;
            portfolio_static_function($pluginname, 'create_instance', $pluginname, $pluginname, $data);
        }

        if ($haschanged) {
            // Include this information into config changes table.
            add_to_config_log('portfolio_visibility', $oldvalue, $enabled, $pluginname);
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return moodle_url
     */
    public static function get_manage_url() {
        return new moodle_url('/admin/portfolio.php');
    }

    /**
     * Defines if there should be a way to uninstall the plugin via the administration UI.
     * @return boolean
     */
    public function is_uninstall_allowed() {
        return true;
    }

    /**
     * Pre-uninstall hook.
     * This is intended for disabling of plugin, some DB table purging, etc.
     */
    public function uninstall_cleanup() {
        global $DB;

        // Get all instances of this portfolio.
        $count = $DB->count_records('portfolio_instance', array('plugin' => $this->name));
        if ($count > 0) {
            // This portfolio is in use, get the it's ID.
            $rec = $DB->get_record('portfolio_instance', array('plugin' => $this->name));

            // Remove all records from portfolio_instance_config.
            $DB->delete_records('portfolio_instance_config', array('instance' => $rec->id));
            // Remove all records from portfolio_instance_user.
            $DB->delete_records('portfolio_instance_user', array('instance' => $rec->id));
            // Remove all records from portfolio_log.
            $DB->delete_records('portfolio_log', array('portfolio' => $rec->id));
            // Remove all records from portfolio_tempdata.
            $DB->delete_records('portfolio_tempdata', array('instance' => $rec->id));

            // Remove the record from the portfolio_instance table.
            $DB->delete_records('portfolio_instance', array('id' => $rec->id));
        }

        parent::uninstall_cleanup();
    }
}

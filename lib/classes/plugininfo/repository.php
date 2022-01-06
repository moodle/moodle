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

use moodle_url, part_of_admin_tree, admin_externalpage;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for repositories
 */
class repository extends base {
    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        global $DB;
        return $DB->get_records_menu('repository', null, 'type ASC', 'type, type AS val');
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
            $settingsurl = new moodle_url('/admin/repository.php',
                array('sesskey' => sesskey(), 'action' => 'edit', 'repos' => $this->name));
            $settings = new admin_externalpage($sectionname, $this->displayname,
                $settingsurl, 'moodle/site:config', false);
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

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

use moodle_url, part_of_admin_tree, admin_settingpage, admin_externalpage;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for text filters
 */
class filter extends base {

    public function init_display_name() {
        if (!get_string_manager()->string_exists('filtername', $this->component)) {
            $this->displayname = '[filtername,' . $this->component . ']';
        } else {
            $this->displayname = get_string('filtername', $this->component);
        }
    }

    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        global $DB, $CFG;
        require_once("$CFG->libdir/filterlib.php");

        $enabled = array();
        $filters = $DB->get_records_select('filter_active', "active <> :disabled", array('disabled'=>TEXTFILTER_DISABLED), 'filter ASC', 'id, filter');
        foreach ($filters as $filter) {
            $enabled[$filter->filter] = $filter->filter;
        }

        return $enabled;
    }

    public function get_settings_section_name() {
        return 'filtersetting' . $this->name;
    }

    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.
        $filter = $this;     // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig) {
            return;
        }
        if (file_exists($this->full_path('settings.php'))) {
            $fullpath = $this->full_path('settings.php');
        } else if (file_exists($this->full_path('filtersettings.php'))) {
            $fullpath = $this->full_path('filtersettings.php');
        } else {
            return;
        }

        $section = $this->get_settings_section_name();
        $settings = new admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
        include($fullpath); // This may also set $settings to null.

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    public function is_uninstall_allowed() {
        return true;
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return moodle_url
     */
    public static function get_manage_url() {
        return new moodle_url('/admin/filters.php');
    }

    /**
     * Pre-uninstall hook.
     *
     * This is intended for disabling of plugin, some DB table purging, etc.
     *
     * NOTE: to be called from uninstall_plugin() only.
     * @private
     */
    public function uninstall_cleanup() {
        global $DB, $CFG;

        $DB->delete_records('filter_active', array('filter' => $this->name));
        $DB->delete_records('filter_config', array('filter' => $this->name));

        if (empty($CFG->filterall)) {
            $stringfilters = array();
        } else if (!empty($CFG->stringfilters)) {
            $stringfilters = explode(',', $CFG->stringfilters);
            $stringfilters = array_combine($stringfilters, $stringfilters);
        } else {
            $stringfilters = array();
        }

        unset($stringfilters[$this->name]);

        set_config('stringfilters', implode(',', $stringfilters));
        set_config('filterall', !empty($stringfilters));

        parent::uninstall_cleanup();
    }
}

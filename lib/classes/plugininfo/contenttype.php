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
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\plugininfo;

/**
 * Class for contentbank plugins
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contenttype extends base {

    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    /**
     * Defines if there should be a way to uninstall the plugin via the administration UI.
     *
     * @return bool
     */
    public function is_uninstall_allowed() {
        return true;
    }

    /**
     * Get the name for the settings section.
     *
     * @return string
     */
    public function get_settings_section_name() {
        return 'contentbanksetting' . $this->name;
    }

    /**
     * Load the global settings for a particular contentbank plugin (if there are any)
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        /** @var \admin_root $ADMIN */
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php
        $contenttype = $this; // Also to be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = null;
        if (file_exists($this->full_path('settings.php'))) {
            $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
            include($this->full_path('settings.php')); // This may also set $settings to null.
        }
        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return \moodle_url
     */
    public static function get_manage_url() {
        return new \moodle_url('/admin/settings.php', array('section' => 'managecontentbanktypes'));
    }


    /**
     * Gathers and returns the information about all plugins of the given type
     *
     * @param string $type the name of the plugintype, eg. mod, auth or workshopform
     * @param string $typerootdir full path to the location of the plugin dir
     * @param string $typeclass the name of the actually called class
     * @param \core_plugin_manager $pluginman the plugin manager calling this method
     * @return array of plugintype classes, indexed by the plugin name
     */
    public static function get_plugins($type, $typerootdir, $typeclass, $pluginman) {
        global $CFG;

        $contents = parent::get_plugins($type, $typerootdir, $typeclass, $pluginman);
        if (!empty($CFG->contentbank_plugins_sortorder)) {
            $order = explode(',', $CFG->contentbank_plugins_sortorder);
            $order = array_merge(array_intersect($order, array_keys($contents)),
                array_diff(array_keys($contents), $order));
        } else {
            $order = array_keys($contents);
        }
        $sortedcontents = array();
        foreach ($order as $contentname) {
            $sortedcontents[$contentname] = $contents[$contentname];
        }
        return $sortedcontents;
    }

    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        global $CFG;

        $plugins = \core_plugin_manager::instance()->get_installed_plugins('contenttype');

        if (!$plugins) {
            return array();
        }

        $plugins = array_keys($plugins);
        // Order the plugins.
        if (!empty($CFG->contentbank_plugins_sortorder)) {
            $order = explode(',', $CFG->contentbank_plugins_sortorder);
            $order = array_merge(array_intersect($order, $plugins),
                array_diff($plugins, $order));
        } else {
            $order = $plugins;
        }

        // Filter to return only enabled plugins.
        $enabled = array();
        foreach ($order as $plugin) {
            $disabled = get_config('contentbank_' . $plugin, 'disabled');
            if (empty($disabled)) {
                $enabled[$plugin] = $plugin;
            }
        }
        return $enabled;
    }

    public static function enable_plugin(string $pluginname, int $enabled): bool {
        $haschanged = false;

        $plugin = 'contentbank_' . $pluginname;
        $oldvalue = get_config($plugin, 'disabled');
        $disabled = !$enabled;
        // Only set value if there is no config setting or if the value is different from the previous one.
        if ($oldvalue == false && $disabled) {
            set_config('disabled', $disabled, $plugin);
            $haschanged = true;
        } else if ($oldvalue != false && !$disabled) {
            unset_config('disabled', $plugin);
            $haschanged = true;
        }

        if ($haschanged) {
            add_to_config_log('disabled', $oldvalue, $disabled, $plugin);
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    /**
     * Optional extra warning before uninstallation adding number of existing contenttype contents.
     *
     * @return string
     */
    public function get_uninstall_extra_warning() {
        global $DB;

        $contentcount = $DB->count_records('contentbank_content', ['contenttype' => "contenttype_$this->name"]);
        if (!$contentcount) {
            return '';
        }

        $message = get_string('contenttypeuninstalling',
            'core_admin',
            (object)['count' => $contentcount, 'type' => $this->displayname]
        );

        return $message;
    }

    /**
     * Pre-uninstall hook.
     *
     * This is intended for disabling of plugin, some DB table purging, etc.
     *
     * NOTE: to be called from uninstall_plugin() only.
     */
    public function uninstall_cleanup() {
        global $DB;

        $records = $DB->get_records('contentbank_content', ['contenttype' => 'contenttype_'.$this->name]);
        $contenttypename = 'contenttype_'.$this->name;
        $contenttypeclass = "\\$contenttypename\\contenttype";
        foreach ($records as $record) {
            $context = \context::instance_by_id($record->contextid, MUST_EXIST);
            $contenttype = new $contenttypeclass($context);
            $contentclass = "\\$contenttypename\\content";
            $content = new $contentclass($record);
            $contenttype->delete_content($content);
        }

        parent::uninstall_cleanup();
    }
}

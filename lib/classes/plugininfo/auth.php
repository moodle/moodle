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
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\plugininfo;

use moodle_url, part_of_admin_tree, admin_settingpage, admin_externalpage;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for authentication plugins
 */
class auth extends base {
    public function is_uninstall_allowed() {
        global $DB;

        if (in_array($this->name, array('manual', 'nologin', 'webservice', 'mnet'))) {
            return false;
        }

        return !$DB->record_exists('user', array('auth'=>$this->name));
    }

    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        global $CFG;

        // These two are always enabled and can't be disabled.
        $enabled = array('nologin'=>'nologin', 'manual'=>'manual');
        foreach (explode(',', $CFG->auth) as $auth) {
            $enabled[$auth] = $auth;
        }

        return $enabled;
    }

    public function get_settings_section_name() {
        return 'authsetting' . $this->name;
    }

    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.
        $auth = $this;       // Also to be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = null;
        if (file_exists($this->full_path('settings.php'))) {
            // TODO: finish implementation of common settings - locking, etc.
            $settings = new admin_settingpage($section, $this->displayname,
                'moodle/site:config', $this->is_enabled() === false);
            include($this->full_path('settings.php')); // This may also set $settings to null.
        } else if (file_exists($this->full_path('config.html'))) {
            $settingsurl = new moodle_url('/admin/auth_config.php', array('auth' => $this->name));
            $settings = new admin_externalpage($section, $this->displayname,
                $settingsurl, 'moodle/site:config', $this->is_enabled() === false);
        }

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return moodle_url
     */
    public static function get_manage_url() {
        return new moodle_url('/admin/settings.php', array('section'=>'manageauths'));
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
        global $CFG;

        if (!empty($CFG->auth)) {
            $auths = explode(',', $CFG->auth);
            $auths = array_unique($auths);
        } else {
            $auths = array();
        }
        if (($key = array_search($this->name, $auths)) !== false) {
            unset($auths[$key]);
            $value = implode(',', $auths);
            add_to_config_log('auth', $CFG->auth, $value, 'core');
            set_config('auth', $value);
        }

        if (!empty($CFG->registerauth) and $CFG->registerauth === $this->name) {
            unset_config('registerauth');
        }

        parent::uninstall_cleanup();
    }
}

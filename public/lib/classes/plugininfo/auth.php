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

namespace core\plugininfo;

/**
 * Defines classes used for plugin info.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth extends base {
    #[\Override]
    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    #[\Override]
    public function is_uninstall_allowed() {
        global $DB;

        if (in_array($this->name, ['manual', 'nologin', 'webservice', 'mnet'])) {
            return false;
        }

        return !$DB->record_exists('user', ['auth' => $this->name]);
    }

    #[\Override]
    public static function get_enabled_plugins() {
        global $CFG;

        // These two are always enabled and can't be disabled.
        $enabled = ['nologin' => 'nologin', 'manual' => 'manual'];
        foreach (explode(',', $CFG->auth) as $auth) {
            $enabled[$auth] = $auth;
        }

        return $enabled;
    }

    #[\Override]
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        global $CFG;

        $haschanged = false;
        $plugins = [];
        if (!empty($CFG->auth)) {
            $plugins = array_flip(explode(',', $CFG->auth));
        }
        // Only set visibility if it's different from the current value.
        if ($enabled && !array_key_exists($pluginname, $plugins)) {
            $plugins[$pluginname] = $pluginname;
            $haschanged = true;
        } else if (!$enabled && array_key_exists($pluginname, $plugins)) {
            unset($plugins[$pluginname]);
            $haschanged = true;

            if ($pluginname == $CFG->registerauth) {
                set_config('registerauth', '');
            }
        }

        if ($haschanged) {
            $new = implode(',', array_flip($plugins));
            add_to_config_log('auth', $CFG->auth, $new, 'core');
            set_config('auth', $new);
            \core\session\manager::destroy_by_auth_plugin($pluginname);
            // Remove stale sessions.
            \core\session\manager::gc();
            // Reset caches.
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    #[\Override]
    public function get_settings_section_name() {
        return 'authsetting' . $this->name;
    }

    #[\Override]
    public function load_settings(
        \core_admin\setting\tree\part_of_admin_tree $adminroot,
        $parentnodename,
        $hassiteconfig,
    ) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        /** @var \admin_root $ADMIN */
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
            $settings = new \core_admin\setting\settingpage\settingpage(
                $section,
                $this->displayname,
                'moodle/site:config',
                $this->is_enabled() === false
            );
            include($this->full_path('settings.php')); // This may also set $settings to null.
        }

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    #[\Override]
    public static function get_manage_url() {
        return new \core\url('/admin/settings.php', ['section' => 'manageauths']);
    }

    #[\Override]
    public function uninstall_cleanup() {
        global $CFG;

        if (!empty($CFG->auth)) {
            $auths = explode(',', $CFG->auth);
            $auths = array_unique($auths);
        } else {
            $auths = [];
        }
        if (($key = array_search($this->name, $auths)) !== false) {
            unset($auths[$key]);
            $value = implode(',', $auths);
            add_to_config_log('auth', $CFG->auth, $value, 'core');
            set_config('auth', $value);
            \core\session\manager::destroy_by_auth_plugin($this->name);
        }

        if (!empty($CFG->registerauth) && $CFG->registerauth === $this->name) {
            unset_config('registerauth');
        }

        parent::uninstall_cleanup();
    }
}

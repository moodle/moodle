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
 * Class for webservice protocols
 *
 * @package    core
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice extends base {
    #[\Override]
    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    #[\Override]
    public static function get_enabled_plugins() {
        global $CFG;

        if (empty($CFG->enablewebservices) || empty($CFG->webserviceprotocols)) {
            return [];
        }

        $enabled = [];
        foreach (explode(',', $CFG->webserviceprotocols) as $protocol) {
            $enabled[$protocol] = $protocol;
        }

        return $enabled;
    }

    #[\Override]
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        global $CFG;

        $haschanged = false;
        $plugins = [];
        if (!empty($CFG->webserviceprotocols)) {
            $plugins = array_flip(explode(',', $CFG->webserviceprotocols));
        }

        // Remove plugins that are no longer available.
        $availablews = \core_component::get_plugin_list('webservice');
        foreach ($plugins as $key => $notused) {
            if (empty($availablews[$key])) {
                unset($plugins[$key]);
            }
        }

        // Only set visibility if it's different from the current value.
        if ($enabled && !array_key_exists($pluginname, $plugins)) {
            $plugins[$pluginname] = $pluginname;
            $haschanged = true;
        } else if (!$enabled && array_key_exists($pluginname, $plugins)) {
            unset($plugins[$pluginname]);
            $haschanged = true;
        }

        if ($haschanged) {
            $new = implode(',', array_flip($plugins));
            add_to_config_log('webserviceprotocols', $CFG->webserviceprotocols ?? '', $new, 'core');
            set_config('webserviceprotocols', $new);
            // Reset caches.
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    #[\Override]
    public function get_settings_section_name() {
        return 'webservicesetting' . $this->name;
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
        $webservice = $this; // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig || !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = new \core_admin\setting\settingpage\settingpage(
            $section,
            $this->displayname,
            'moodle/site:config',
            $this->is_enabled() === false,
        );
        include($this->full_path('settings.php')); // This may also set $settings to null.

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    #[\Override]
    public function is_uninstall_allowed() {
        return true;
    }
}

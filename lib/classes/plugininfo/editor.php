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

use admin_settingpage;
use moodle_url;
use part_of_admin_tree;

/**
 * Class for HTML editors
 */
class editor extends base {
    #[\Override]
    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    #[\Override]
    public static function get_enabled_plugins() {
        global $CFG;

        if (empty($CFG->texteditors)) {
            return [
                'tiny' => 'tiny',
                'textarea' => 'textarea',
            ];
        }

        $enabled = [];
        foreach (explode(',', $CFG->texteditors) as $editor) {
            $enabled[$editor] = $editor;
        }

        return $enabled;
    }

    #[\Override]
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        global $CFG;

        $haschanged = false;
        if (!empty($CFG->texteditors)) {
            $plugins = array_flip(explode(',', $CFG->texteditors));
        } else {
            $plugins = [];
        }

        // Only set visibility if it's different from the current value.
        if ($enabled && !array_key_exists($pluginname, $plugins)) {
            $plugins[$pluginname] = $pluginname;
            $haschanged = true;
        } else if (!$enabled && array_key_exists($pluginname, $plugins)) {
            unset($plugins[$pluginname]);
            $haschanged = true;
        }

        // At least one editor must be active.
        if (empty($plugins)) {
            $plugins['textarea'] = 'textarea';
            $haschanged = true;
        }

        if ($haschanged) {
            $new = implode(',', array_flip($plugins));
            add_to_config_log('editor_visibility', !$enabled, $enabled, $pluginname);
            set_config('texteditors', $new);
            // Reset caches.
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    #[\Override]
    public function get_settings_section_name() {
        return 'editorsettings' . $this->name;
    }

    #[\Override]
    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        /** @var \admin_root $ADMIN */
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.
        $editor = $this;     // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig or !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = new admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
        include($this->full_path('settings.php')); // This may also set $settings to null.

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    #[\Override]
    public function is_uninstall_allowed() {
        if ($this->name === 'textarea') {
            return false;
        } else {
            return true;
        }
    }

    #[\Override]
    public function uninstall_cleanup(): void {
        global $DB;

        self::enable_plugin($this->name, 0);
        $DB->delete_records_select(
            'user_preferences',
            "name = :name AND " . $DB->sql_compare_text('value') . " = " . $DB->sql_compare_text(':value'),
            [
                'name' => 'htmleditor',
                'value' => $this->name,
            ],
        );
    }

    #[\Override]
    public static function get_manage_url() {
        return new moodle_url('/admin/settings.php', array('section'=>'manageeditors'));
    }

    #[\Override]
    public static function plugintype_supports_ordering(): bool {
        return true;
    }

    #[\Override]
    public static function get_sorted_plugins(bool $enabledonly = false): ?array {
        global $CFG;

        $pluginmanager = \core_plugin_manager::instance();
        $plugins = $pluginmanager->get_plugins_of_type('editor');

        // The Editor list is stored in an ordered string.
        $activeeditors = explode(',', $CFG->texteditors);

        $sortedplugins = [];
        foreach ($activeeditors as $editor) {
            if (isset($plugins[$editor])) {
                $sortedplugins[$editor] = $plugins[$editor];
                unset($plugins[$editor]);
            }
        }

        if ($enabledonly) {
            return $sortedplugins;
        }

        // Sort the rest of the plugins lexically.
        uasort($plugins, function ($a, $b) {
            return strnatcasecmp($a->name, $b->name);
        });

        return array_merge(
            $sortedplugins,
            $plugins,
        );
    }

    #[\Override]
    public static function change_plugin_order(string $pluginname, int $direction): bool {
        $activeeditors = array_keys(self::get_sorted_plugins(true));
        $key = array_search($pluginname, $activeeditors);

        if ($key === false) {
            return false;
        }

        if ($direction === self::MOVE_DOWN) {
            // Move down the list.
            if ($key < (count($activeeditors) - 1)) {
                $fsave = $activeeditors[$key];
                $activeeditors[$key] = $activeeditors[$key + 1];
                $activeeditors[$key + 1] = $fsave;
                add_to_config_log('editor_position', $key, $key + 1, $pluginname);
                set_config('texteditors', implode(',', $activeeditors));
                \core_plugin_manager::reset_caches();

                return true;
            }
        } else if ($direction === self::MOVE_UP) {
            if ($key >= 1) {
                $fsave = $activeeditors[$key];
                $activeeditors[$key] = $activeeditors[$key - 1];
                $activeeditors[$key - 1] = $fsave;
                add_to_config_log('editor_position', $key, $key - 1, $pluginname);
                set_config('texteditors', implode(',', $activeeditors));
                \core_plugin_manager::reset_caches();

                return true;
            }
        }

        return false;
    }
}

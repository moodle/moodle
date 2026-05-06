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
class enrol extends base {
    #[\Override]
    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    #[\Override]
    public static function get_enabled_plugins() {
        global $CFG;

        $enabled = [];
        foreach (explode(',', $CFG->enrol_plugins_enabled) as $enrol) {
            $enabled[$enrol] = $enrol;
        }

        return $enabled;
    }

    #[\Override]
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        global $CFG;

        $haschanged = false;
        $plugins = [];
        if (!empty($CFG->enrol_plugins_enabled)) {
            $plugins = array_flip(explode(',', $CFG->enrol_plugins_enabled));
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
            add_to_config_log('enrol_plugins_enabled', !$enabled, $enabled, $pluginname);
            set_config('enrol_plugins_enabled', $new);
            // Reset caches.
            \core_plugin_manager::reset_caches();
            // Resets all enrol caches.
            $syscontext = \context_system::instance();
            $syscontext->mark_dirty();
        }

        return $haschanged;
    }

    #[\Override]
    public function get_settings_section_name() {
        if (file_exists($this->full_path('settings.php'))) {
            return 'enrolsettings' . $this->name;
        } else {
            return null;
        }
    }

    #[\Override]
    public function load_settings(
        \core_admin\setting\tree\part_of_admin_tree $adminroot,
        $parentnodename,
        $hassiteconfig,
    ) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        /** @var \core_admin\setting\tree\root $ADMIN */
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.
        $enrol = $this;      // Also can be used inside settings.php.

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

        include($this->full_path('settings.php')); // This may also set $settings to null!

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    #[\Override]
    public function is_uninstall_allowed() {
        if ($this->name === 'manual') {
            return false;
        }
        return true;
    }

    #[\Override]
    public static function get_manage_url() {
        return new \core\url('/admin/settings.php', ['section' => 'manageenrols']);
    }

    #[\Override]
    public function get_uninstall_extra_warning() {
        global $DB, $OUTPUT;

        $sql = "SELECT COUNT('x')
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                 WHERE e.enrol = :plugin";
        $count = $DB->count_records_sql($sql, ['plugin' => $this->name]);

        if (!$count) {
            return '';
        }

        $migrateurl = new \core\url('/admin/enrol.php', ['action' => 'migrate', 'enrol' => $this->name, 'sesskey' => sesskey()]);
        $migrate = new \single_button($migrateurl, get_string('migratetomanual', 'core_enrol'));
        $button = $OUTPUT->render($migrate);

        $result = '<p>' . get_string('uninstallextraconfirmenrol', 'core_plugin', ['enrolments' => $count]) . '</p>';
        $result .= $button;

        return $result;
    }

    #[\Override]
    public function uninstall_cleanup() {
        global $DB, $CFG;

        // NOTE: this is a bit brute force way - it will not trigger events and hooks properly.

        // Nuke all role assignments.
        role_unassign_all(['component' => 'enrol_' . $this->name]);

        // Purge participants.
        $DB->delete_records_select('user_enrolments', "enrolid IN (SELECT id FROM {enrol} WHERE enrol = ?)", [$this->name]);

        // Purge enrol instances.
        $DB->delete_records('enrol', ['enrol' => $this->name]);

        // Tweak enrol settings.
        if (!empty($CFG->enrol_plugins_enabled)) {
            $enabledenrols = explode(',', $CFG->enrol_plugins_enabled);
            $enabledenrols = array_unique($enabledenrols);
            $enabledenrols = array_flip($enabledenrols);
            unset($enabledenrols[$this->name]);
            $enabledenrols = array_flip($enabledenrols);
            if (is_array($enabledenrols)) {
                set_config('enrol_plugins_enabled', implode(',', $enabledenrols));
            }
        }

        parent::uninstall_cleanup();
    }
}

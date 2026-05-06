<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

use core_admin\admin_search;

/**
 * Authentication plugin administration.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manageauths extends \core_admin\setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('authsui', get_string('authsettings', 'admin'), '', '');
    }

    #[\Override]
    public function get_setting() {
        return true;
    }

    #[\Override]
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '' and doesn't write anything
     *
     * @param mixed $data Unused
     * @return string Always returns ''
     */
    #[\Override]
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Search to find if Query is related to auth plugin
     *
     * @param string $query The string to search for
     * @return bool true for related false for not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $authsavailable = \core_component::get_plugin_list('auth');
        foreach ($authsavailable as $auth => $dir) {
            if (strpos($auth, $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
            $authplugin = \core\di::get(\core\authentication::class)->get_plugin($auth);
            $authtitle = $authplugin->get_title();
            if (strpos(\core_text::strtolower($authtitle), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }
        return false;
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $CFG, $OUTPUT, $DB;

        // Display strings.
        $txt = get_strings(['authenticationplugins', 'users', 'administration',
            'settings', 'edit', 'name', 'enable', 'disable',
            'up', 'down', 'none', 'users']);
        $txt->updown = "$txt->up/$txt->down";
        $txt->uninstall = get_string('uninstallplugin', 'core_admin');
        $txt->testsettings = get_string('testsettings', 'core_auth');

        $authsavailable = \core_component::get_plugin_list('auth');
        // Fix the list of enabled auths.
        \core\di::get(\core\authentication::class)->get_enabled_plugins(true);
        if (empty($CFG->auth)) {
            $authsenabled = [];
        } else {
            $authsenabled = explode(',', $CFG->auth);
        }

        // Construct the display array, with enabled auth plugins at the top, in order.
        $displayauths = [];
        $registrationauths = [];
        $registrationauths[''] = $txt->disable;
        $authplugins = [];
        $authentication = \core\di::get(\core\authentication::class);
        foreach ($authsenabled as $auth) {
            $authplugin = $authentication->get_plugin($auth);
            $authplugins[$auth] = $authplugin;
            // Get the auth title (from core or own auth lang files).
            $authtitle = $authplugin->get_title();
            // Apply titles.
            $displayauths[$auth] = $authtitle;
            if ($authplugin->can_signup()) {
                $registrationauths[$auth] = $authtitle;
            }
        }

        foreach ($authsavailable as $auth => $dir) {
            if (array_key_exists($auth, $displayauths)) {
                continue; // Already in the list.
            }
            $authplugin = $authentication->get_plugin($auth);
            $authplugins[$auth] = $authplugin;
            // Get the auth title (from core or own auth lang files).
            $authtitle = $authplugin->get_title();
            // Apply titles.
            $displayauths[$auth] = $authtitle;
            if ($authplugin->can_signup()) {
                $registrationauths[$auth] = $authtitle;
            }
        }

        $return = $OUTPUT->heading(get_string('actauthhdr', 'auth'), 3, 'main');
        $return .= $OUTPUT->box_start('generalbox authsui');

        $table = new \html_table();
        $table->head  = [$txt->name, $txt->users, $txt->enable, $txt->updown, $txt->settings, $txt->testsettings, $txt->uninstall];
        $table->colclasses = [
            'leftalign',
            'centeralign',
            'centeralign',
            'centeralign',
            'centeralign',
            'centeralign',
            'centeralign',
        ];
        $table->data  = [];
        $table->attributes['class'] = 'admintable table generaltable table-hover';
        $table->id = 'manageauthtable';

        // Add always enabled plugins first.
        $displayname = $displayauths['manual'];
        $settings = "<a href=\"settings.php?section=authsettingmanual\">{$txt->settings}</a>";
        $usercount = $DB->count_records('user', ['auth' => 'manual', 'deleted' => 0]);
        $table->data[] = [$displayname, $usercount, '', '', $settings, '', ''];
        $displayname = $displayauths['nologin'];
        $usercount = $DB->count_records('user', ['auth' => 'nologin', 'deleted' => 0]);
        $table->data[] = [$displayname, $usercount, '', '', '', '', ''];

        // Iterate through auth plugins and add to the display table.
        $updowncount = 1;
        $authcount = count($authsenabled);
        $url = "auth.php?sesskey=" . sesskey();
        foreach ($displayauths as $auth => $name) {
            if ($auth == 'manual' || $auth == 'nologin') {
                continue;
            }
            $class = '';
            // Hide/show link.
            if (in_array($auth, $authsenabled)) {
                $hideshow = "<a href=\"$url&amp;action=disable&amp;auth=$auth\">";
                $hideshow .= $OUTPUT->pix_icon('t/hide', get_string('disable')) . '</a>';
                $enabled = true;
                $displayname = $name;
            } else {
                $hideshow = "<a href=\"$url&amp;action=enable&amp;auth=$auth\">";
                $hideshow .= $OUTPUT->pix_icon('t/show', get_string('enable')) . '</a>';
                $enabled = false;
                $displayname = $name;
                $class = 'dimmed_text';
            }

            $usercount = $DB->count_records('user', ['auth' => $auth, 'deleted' => 0]);

            // Up/down link (only if auth is enabled).
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $updown .= "<a href=\"$url&amp;action=up&amp;auth=$auth\">";
                    $updown .= $OUTPUT->pix_icon('t/up', get_string('moveup')) . '</a>&nbsp;';
                } else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                if ($updowncount < $authcount) {
                    $updown .= "<a href=\"$url&amp;action=down&amp;auth=$auth\">";
                    $updown .= $OUTPUT->pix_icon('t/down', get_string('movedown')) . '</a>&nbsp;';
                } else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                ++$updowncount;
            }

            // Settings link.
            if (file_exists($CFG->dirroot . '/auth/' . $auth . '/settings.php')) {
                $settings = "<a href=\"settings.php?section=authsetting$auth\">{$txt->settings}</a>";
            } else if (file_exists($CFG->dirroot . '/auth/' . $auth . '/config.html')) {
                throw new \coding_exception('config.html is no longer supported, please use settings.php instead.');
            } else {
                $settings = '';
            }

            // Uninstall link.
            $uninstall = '';
            if ($uninstallurl = \core_plugin_manager::instance()->get_uninstall_url('auth_' . $auth, 'manage')) {
                $uninstall = \html_writer::link($uninstallurl, $txt->uninstall);
            }

            $test = '';
            if (!empty($authplugins[$auth]) && method_exists($authplugins[$auth], 'test_settings')) {
                $testurl = new \moodle_url('/auth/test_settings.php', ['auth' => $auth]);
                $test = \html_writer::link($testurl, $txt->testsettings);
            }

            // Add a row to the table.
            $row = new \html_table_row([$displayname, $usercount, $hideshow, $updown, $settings, $test, $uninstall]);
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
        }
        $return .= \html_writer::table($table);
        $return .= get_string('configauthenticationplugins', 'admin') . '<br />' . get_string('tablenosave', 'filters');
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(manageauths::class, \admin_setting_manageauths::class);

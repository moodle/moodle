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
 * Store management setting.
 *
 * @package    tool_log
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/adminlib.php");

class tool_log_setting_managestores extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('tool_log_manageui', get_string('managelogging', 'tool_log'), '', '');
    }

    /**
     * Always returns true, does nothing.
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing.
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything.
     *
     * @param mixed $data ignored
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available log plugins.
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $query = core_text::strtolower($query);
        $plugins = \tool_log\log\manager::get_store_plugins();
        foreach ($plugins as $plugin => $fulldir) {
            if (strpos(core_text::strtolower($plugin), $query) !== false) {
                return true;
            }
            $localised = get_string('pluginname', $plugin);
            if (strpos(core_text::strtolower($localised), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control.
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        global $OUTPUT, $PAGE;

        // Display strings.
        $strup = get_string('up');
        $strdown = get_string('down');
        $strsettings = get_string('settings');
        $strenable = get_string('enable');
        $strdisable = get_string('disable');
        $struninstall = get_string('uninstallplugin', 'core_admin');
        $strversion = get_string('version');

        $pluginmanager = core_plugin_manager::instance();
        $logmanager = new \tool_log\log\manager();
        $available = $logmanager->get_store_plugins();
        $enabled = get_config('tool_log', 'enabled_stores');
        if (!$enabled) {
            $enabled = array();
        } else {
            $enabled = array_flip(explode(',', $enabled));
        }

        $allstores = array();
        foreach ($enabled as $key => $store) {
            $allstores[$key] = true;
            $enabled[$key] = true;
        }
        foreach ($available as $key => $store) {
            $allstores[$key] = true;
            $available[$key] = true;
        }

        $return = $OUTPUT->heading(get_string('actlogshdr', 'tool_log'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox loggingui');

        $table = new html_table();
        $table->head = array(get_string('name'), get_string('reportssupported', 'tool_log'), $strversion, $strenable,
                $strup . '/' . $strdown, $strsettings, $struninstall);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign',
                'centeralign');
        $table->id = 'logstoreplugins';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data = array();

        // Iterate through store plugins and add to the display table.
        $updowncount = 1;
        $storecount = count($enabled);
        $url = new moodle_url('/admin/tool/log/stores.php', array('sesskey' => sesskey()));
        $printed = array();
        foreach ($allstores as $store => $unused) {
            $plugininfo = $pluginmanager->get_plugin_info($store);
            $version = get_config($store, 'version');
            if ($version === false) {
                $version = '';
            }

            if (get_string_manager()->string_exists('pluginname', $store)) {
                $name = get_string('pluginname', $store);
            } else {
                $name = $store;
            }

            $reports = $logmanager->get_supported_reports($store);
            if (!empty($reports)) {
                $supportedreports = implode(', ', $reports);
            } else {
                $supportedreports = '-';
            }

            // Hide/show links.
            if (isset($enabled[$store])) {
                $aurl = new moodle_url($url, array('action' => 'disable', 'store' => $store));
                $hideshow = "<a href=\"$aurl\">";
                $hideshow .= $OUTPUT->pix_icon('t/hide', $strdisable) . '</a>';
                $isenabled = true;
                $displayname = "<span>$name</span>";
            } else {
                if (isset($available[$store])) {
                    $aurl = new moodle_url($url, array('action' => 'enable', 'store' => $store));
                    $hideshow = "<a href=\"$aurl\">";
                    $hideshow .= $OUTPUT->pix_icon('t/show', $strenable) . '</a>';
                    $isenabled = false;
                    $displayname = "<span class=\"dimmed_text\">$name</span>";
                } else {
                    $hideshow = '';
                    $isenabled = false;
                    $displayname = '<span class="notifyproblem">' . $name . '</span>';
                }
            }
            if ($PAGE->theme->resolve_image_location('icon', $store, false)) {
                $icon = $OUTPUT->pix_icon('icon', '', $store, array('class' => 'icon pluginicon'));
            } else {
                $icon = $OUTPUT->spacer();
            }

            // Up/down link (only if store is enabled).
            $updown = '';
            if ($isenabled) {
                if ($updowncount > 1) {
                    $aurl = new moodle_url($url, array('action' => 'up', 'store' => $store));
                    $updown .= "<a href=\"$aurl\">";
                    $updown .= $OUTPUT->pix_icon('t/up', $strup) . '</a>&nbsp;';
                } else {
                    $updown .= $OUTPUT->spacer();
                }
                if ($updowncount < $storecount) {
                    $aurl = new moodle_url($url, array('action' => 'down', 'store' => $store));
                    $updown .= "<a href=\"$aurl\">";
                    $updown .= $OUTPUT->pix_icon('t/down', $strdown) . '</a>&nbsp;';
                } else {
                    $updown .= $OUTPUT->spacer();
                }
                ++$updowncount;
            }

            // Add settings link.
            if (!$version) {
                $settings = '';
            } else {
                if ($surl = $plugininfo->get_settings_url()) {
                    $settings = html_writer::link($surl, $strsettings);
                } else {
                    $settings = '';
                }
            }

            // Add uninstall info.
            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url($store, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $struninstall);
            }

            // Add a row to the table.
            $table->data[] = array($icon . $displayname, $supportedreports, $version, $hideshow, $updown, $settings, $uninstall);

            $printed[$store] = true;
        }

        $return .= html_writer::table($table);
        $return .= get_string('configlogplugins', 'tool_log') . '<br />' . get_string('tablenosave', 'admin');
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

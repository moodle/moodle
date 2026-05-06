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

namespace core_admin\setting\setting;

use core_admin\admin_search;

/**
 * Data formats manager. Allow reorder and to enable/disable data formats and jump to settings
 *
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class managedataformats extends \admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('managedataformats', new \lang_string('managedataformats'), '', '');
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '' and doesn't write anything
     *
     * @param mixed $data string or array, must not be NULL
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Search to find if Query is related to format plugin
     *
     * @param string $query The string to search for
     * @return bool true for related false for not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }
        $formats = \core_plugin_manager::instance()->get_plugins_of_type('dataformat');
        foreach ($formats as $format) {
            if (strpos($format->component, $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
            if (strpos(\core_text::strtolower($format->displayname), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }
        return false;
    }

    /**
     * Return XHTML to display control
     *
     * @param mixed $data Unused
     * @param string $query
     * @return string highlight
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;
        $return = '';

        $formats = \core_plugin_manager::instance()->get_plugins_of_type('dataformat');

        $txt = get_strings(array('settings', 'name', 'enable', 'disable', 'up', 'down', 'default'));
        $txt->uninstall = get_string('uninstallplugin', 'core_admin');
        $txt->updown = "$txt->up/$txt->down";

        $table = new \html_table();
        $table->head  = array($txt->name, $txt->enable, $txt->updown, $txt->uninstall, $txt->settings);
        $table->align = array('left', 'center', 'center', 'center', 'center');
        $table->attributes['class'] = 'manageformattable table generaltable admintable table-striped table-hover';
        $table->data  = array();

        $cnt = 0;
        $spacer = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'iconsmall'));
        $totalenabled = 0;
        foreach ($formats as $format) {
            if ($format->is_enabled() && $format->is_installed_and_upgraded()) {
                $totalenabled++;
            }
        }
        foreach ($formats as $format) {
            $status = $format->get_status();
            $url = new \moodle_url('/admin/dataformats.php',
                    array('sesskey' => sesskey(), 'name' => $format->name));

            $class = '';
            if ($format->is_enabled()) {
                $strformatname = $format->displayname;
                if ($totalenabled == 1&& $format->is_enabled()) {
                    $hideshow = '';
                } else {
                    $hideshow = \html_writer::link($url->out(false, array('action' => 'disable')),
                        $OUTPUT->pix_icon('t/hide', $txt->disable, 'moodle', array('class' => 'iconsmall')));
                }
            } else {
                $class = 'dimmed_text';
                $strformatname = $format->displayname;
                $hideshow = \html_writer::link($url->out(false, array('action' => 'enable')),
                    $OUTPUT->pix_icon('t/show', $txt->enable, 'moodle', array('class' => 'iconsmall')));
            }

            $updown = '';
            if ($cnt) {
                $updown .= \html_writer::link($url->out(false, array('action' => 'up')),
                    $OUTPUT->pix_icon('t/up', $txt->up, 'moodle', array('class' => 'iconsmall'))). '';
            } else {
                $updown .= $spacer;
            }
            if ($cnt < count($formats) - 1) {
                $updown .= '&nbsp;'.\html_writer::link($url->out(false, array('action' => 'down')),
                    $OUTPUT->pix_icon('t/down', $txt->down, 'moodle', array('class' => 'iconsmall')));
            } else {
                $updown .= $spacer;
            }

            $uninstall = '';
            if ($status === \core_plugin_manager::PLUGIN_STATUS_MISSING) {
                $uninstall = get_string('status_missing', 'core_plugin');
            } else if ($status === \core_plugin_manager::PLUGIN_STATUS_NEW) {
                $uninstall = get_string('status_new', 'core_plugin');
            } else if ($uninstallurl = \core_plugin_manager::instance()->get_uninstall_url('dataformat_'.$format->name, 'manage')) {
                if ($totalenabled != 1 || !$format->is_enabled()) {
                    $uninstall = \html_writer::link($uninstallurl, $txt->uninstall);
                }
            }

            $settings = '';
            if ($format->get_settings_url()) {
                $settings = \html_writer::link($format->get_settings_url(), $txt->settings);
            }

            $row = new \html_table_row(array($strformatname, $hideshow, $updown, $uninstall, $settings));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
            $cnt++;
        }
        $return .= \html_writer::table($table);
        return highlight($query, $return);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(managedataformats::class, \admin_setting_managedataformats::class);

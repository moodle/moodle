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

use core_admin\admin_search;

/**
 * Content bank content types manager. Allow reorder and to enable/disable content bank content types and jump to settings
 *
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_managecontentbankcontenttypes extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('contentbank', new lang_string('managecontentbanktypes'), '', '');
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
     * Search to find if Query is related to content bank plugin
     *
     * @param string $query The string to search for
     * @return bool true for related false for not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }
        $types = core_plugin_manager::instance()->get_plugins_of_type('contenttype');
        foreach ($types as $type) {
            if (strpos($type->component, $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
            if (strpos(core_text::strtolower($type->displayname), $query) !== false) {
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

        $types = core_plugin_manager::instance()->get_plugins_of_type('contenttype');
        $txt = get_strings(array('settings', 'name', 'enable', 'disable', 'order', 'up', 'down', 'default'));
        $txt->uninstall = get_string('uninstallplugin', 'core_admin');

        $table = new html_table();
        $table->head  = array($txt->name, $txt->enable, $txt->order, $txt->settings, $txt->uninstall);
        $table->align = array('left', 'center', 'center', 'center', 'center');
        $table->attributes['class'] = 'managecontentbanktable table generaltable admintable table-hover';
        $table->data  = array();
        $spacer = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'iconsmall'));

        $totalenabled = 0;
        $count = 0;
        foreach ($types as $type) {
            if ($type->is_enabled() && $type->is_installed_and_upgraded()) {
                $totalenabled++;
            }
        }

        foreach ($types as $type) {
            $url = new moodle_url('/admin/contentbank.php',
                array('sesskey' => sesskey(), 'name' => $type->name));

            $class = '';
            $strtypename = $type->displayname;
            if ($type->is_enabled()) {
                $hideshow = html_writer::link($url->out(false, array('action' => 'disable')),
                    $OUTPUT->pix_icon('t/hide', $txt->disable, 'moodle', array('class' => 'iconsmall')));
            } else {
                $class = 'dimmed_text';
                $hideshow = html_writer::link($url->out(false, array('action' => 'enable')),
                    $OUTPUT->pix_icon('t/show', $txt->enable, 'moodle', array('class' => 'iconsmall')));
            }

            $updown = '';
            if ($count) {
                $updown .= html_writer::link($url->out(false, array('action' => 'up')),
                        $OUTPUT->pix_icon('t/up', $txt->up, 'moodle', array('class' => 'iconsmall'))). '';
            } else {
                $updown .= $spacer;
            }
            if ($count < count($types) - 1) {
                $updown .= '&nbsp;'.html_writer::link($url->out(false, array('action' => 'down')),
                        $OUTPUT->pix_icon('t/down', $txt->down, 'moodle', array('class' => 'iconsmall')));
            } else {
                $updown .= $spacer;
            }

            $settings = '';
            if ($type->get_settings_url()) {
                $settings = html_writer::link($type->get_settings_url(), $txt->settings);
            }

            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('contenttype_'.$type->name, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $txt->uninstall);
            }

            $row = new html_table_row(array($strtypename, $hideshow, $updown, $settings, $uninstall));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
            $count++;
        }
        $return .= html_writer::table($table);
        return highlight($query, $return);
    }
}

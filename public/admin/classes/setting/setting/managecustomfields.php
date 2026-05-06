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
 * Custom fields manager. Allows to enable/disable custom fields and jump to settings.
 *
 * @package    core
 * @copyright  2018 Toni Barbera
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class managecustomfields extends \admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('customfieldsui', new \lang_string('managecustomfields', 'core_admin'), '', '');
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
        $formats = \core_plugin_manager::instance()->get_plugins_of_type('customfield');
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
        $return = $OUTPUT->heading(new \lang_string('customfields', 'core_customfield'), 3, 'main');
        $return .= $OUTPUT->box_start('generalbox customfieldsui');

        $fields = \core_plugin_manager::instance()->get_plugins_of_type('customfield');

        $txt = get_strings(array('settings', 'name', 'enable', 'disable', 'up', 'down'));
        $txt->uninstall = get_string('uninstallplugin', 'core_admin');
        $txt->updown = "$txt->up/$txt->down";

        $table = new \html_table();
        $table->head  = array($txt->name, $txt->enable, $txt->uninstall, $txt->settings);
        $table->align = array('left', 'center', 'center', 'center');
        $table->attributes['class'] = 'managecustomfieldtable table generaltable admintable table-striped table-hover';
        $table->data  = array();

        $spacer = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'iconsmall'));
        foreach ($fields as $field) {
            $url = new \moodle_url('/admin/customfields.php',
                    array('sesskey' => sesskey(), 'field' => $field->name));

            if ($field->is_enabled()) {
                $strfieldname = $field->displayname;
                $class = '';
                $hideshow = \html_writer::link($url->out(false, array('action' => 'disable')),
                        $OUTPUT->pix_icon('t/hide', $txt->disable, 'moodle', array('class' => 'iconsmall')));
            } else {
                $strfieldname = $field->displayname;
                $class = 'dimmed_text';
                $hideshow = \html_writer::link($url->out(false, array('action' => 'enable')),
                    $OUTPUT->pix_icon('t/show', $txt->enable, 'moodle', array('class' => 'iconsmall')));
            }
            $settings = '';
            if ($field->get_settings_url()) {
                $settings = \html_writer::link($field->get_settings_url(), $txt->settings);
            }
            $uninstall = '';
            if ($uninstallurl = \core_plugin_manager::instance()->get_uninstall_url('customfield_'.$field->name, 'manage')) {
                $uninstall = \html_writer::link($uninstallurl, $txt->uninstall);
            }
            $row = new \html_table_row(array($strfieldname, $hideshow, $uninstall, $settings));
            $row->attributes['class'] = $class;
            $table->data[] = $row;
        }
        $return .= \html_writer::table($table);
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(managecustomfields::class, \admin_setting_managecustomfields::class);

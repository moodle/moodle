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
 * Special class for antiviruses administration.
 *
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manageantiviruses extends \admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('antivirusesui', get_string('antivirussettings', 'antivirus'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @param string $data Unused
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available editors
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $antivirusesavailable = \core\antivirus\manager::get_available();
        foreach ($antivirusesavailable as $antivirus => $antivirusstr) {
            if (strpos($antivirus, $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
            if (strpos(\core_text::strtolower($antivirusstr), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        // Display strings.
        $txt = get_strings(array('administration', 'settings', 'edit', 'name', 'enable', 'disable',
            'up', 'down', 'none'));
        $struninstall = get_string('uninstallplugin', 'core_admin');

        $txt->updown = "$txt->up/$txt->down";

        $antivirusesavailable = \core\antivirus\manager::get_available();
        $activeantiviruses = explode(',', $CFG->antiviruses);

        $activeantiviruses = array_reverse($activeantiviruses);
        foreach ($activeantiviruses as $key => $antivirus) {
            if (empty($antivirusesavailable[$antivirus])) {
                unset($activeantiviruses[$key]);
            } else {
                $name = $antivirusesavailable[$antivirus];
                unset($antivirusesavailable[$antivirus]);
                $antivirusesavailable[$antivirus] = $name;
            }
        }
        $antivirusesavailable = array_reverse($antivirusesavailable, true);
        $return = $OUTPUT->heading(get_string('actantivirushdr', 'antivirus'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox antivirusesui');

        $table = new \html_table();
        $table->head  = array($txt->name, $txt->enable, $txt->updown, $txt->settings, $struninstall);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->id = 'antivirusmanagement';
        $table->attributes['class'] = 'admintable table generaltable table-hover';
        $table->data  = array();

        // Iterate through auth plugins and add to the display table.
        $updowncount = 1;
        $antiviruscount = count($activeantiviruses);
        $baseurl = new \moodle_url('/admin/antiviruses.php', array('sesskey' => sesskey()));
        foreach ($antivirusesavailable as $antivirus => $name) {
            // Hide/show link.
            $class = '';
            if (in_array($antivirus, $activeantiviruses)) {
                $hideshowurl = $baseurl;
                $hideshowurl->params(array('action' => 'disable', 'antivirus' => $antivirus));
                $hideshowimg = $OUTPUT->pix_icon('t/hide', get_string('disable'));
                $hideshow = \html_writer::link($hideshowurl, $hideshowimg);
                $enabled = true;
                $displayname = $name;
            } else {
                $hideshowurl = $baseurl;
                $hideshowurl->params(array('action' => 'enable', 'antivirus' => $antivirus));
                $hideshowimg = $OUTPUT->pix_icon('t/show', get_string('enable'));
                $hideshow = \html_writer::link($hideshowurl, $hideshowimg);
                $enabled = false;
                $displayname = $name;
                $class = 'dimmed_text';
            }

            // Up/down link.
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $updownurl = $baseurl;
                    $updownurl->params(array('action' => 'up', 'antivirus' => $antivirus));
                    $updownimg = $OUTPUT->pix_icon('t/up', get_string('moveup'));
                    $updown = \html_writer::link($updownurl, $updownimg);
                } else {
                    $updownimg = $OUTPUT->spacer();
                }
                if ($updowncount < $antiviruscount) {
                    $updownurl = $baseurl;
                    $updownurl->params(array('action' => 'down', 'antivirus' => $antivirus));
                    $updownimg = $OUTPUT->pix_icon('t/down', get_string('movedown'));
                    $updown = \html_writer::link($updownurl, $updownimg);
                } else {
                    $updownimg = $OUTPUT->spacer();
                }
                ++ $updowncount;
            }

            // Settings link.
            if (file_exists($CFG->dirroot.'/lib/antivirus/'.$antivirus.'/settings.php')) {
                $eurl = new \moodle_url('/admin/settings.php', array('section' => 'antivirussettings'.$antivirus));
                $settings = \html_writer::link($eurl, $txt->settings);
            } else {
                $settings = '';
            }

            $uninstall = '';
            if ($uninstallurl = \core_plugin_manager::instance()->get_uninstall_url('antivirus_'.$antivirus, 'manage')) {
                $uninstall = \html_writer::link($uninstallurl, $struninstall);
            }

            // Add a row to the table.
            $row = new \html_table_row(array($displayname, $hideshow, $updown, $settings, $uninstall));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
        }
        $return .= \html_writer::table($table);
        $return .= get_string('configantivirusplugins', 'antivirus') . \html_writer::empty_tag('br') . get_string('tablenosave', 'admin');
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(manageantiviruses::class, \admin_setting_manageantiviruses::class);

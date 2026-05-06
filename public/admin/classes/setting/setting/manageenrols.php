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
 * Special class for enrol plugins management.
 *
 * @copyright 2010 Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manageenrols extends \core_admin\setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('enrolsui', get_string('manageenrols', 'enrol'), '', '');
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
     * @return string Always returns ''
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Checks if $query is one of the available enrol plugins
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $query = \core_text::strtolower($query);
        $enrols = enrol_get_plugins(false);
        foreach ($enrols as $name=>$enrol) {
            $localised = get_string('pluginname', 'enrol_'.$name);
            if (strpos(\core_text::strtolower($name), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
            if (strpos(\core_text::strtolower($localised), $query) !== false) {
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
        global $CFG, $OUTPUT, $DB, $PAGE;

        // Display strings.
        $strup        = get_string('up');
        $strdown      = get_string('down');
        $strsettings  = get_string('settings');
        $strenable    = get_string('enable');
        $strdisable   = get_string('disable');
        $struninstall = get_string('uninstallplugin', 'core_admin');
        $strusage     = get_string('enrolusage', 'enrol');
        $strversion   = get_string('version');
        $strtest      = get_string('testsettings', 'core_enrol');

        $pluginmanager = \core_plugin_manager::instance();

        $enrols_available = enrol_get_plugins(false);
        $active_enrols    = enrol_get_plugins(true);

        $allenrols = array();
        foreach ($active_enrols as $key=>$enrol) {
            $allenrols[$key] = true;
        }
        foreach ($enrols_available as $key=>$enrol) {
            $allenrols[$key] = true;
        }
        // Now find all borked plugins and at least allow then to uninstall.
        $condidates = $DB->get_fieldset_sql("SELECT DISTINCT enrol FROM {enrol}");
        foreach ($condidates as $candidate) {
            if (empty($allenrols[$candidate])) {
                $allenrols[$candidate] = true;
            }
        }

        $return = $OUTPUT->heading(get_string('actenrolshhdr', 'enrol'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox enrolsui');

        $table = new \html_table();
        $table->head  = array(get_string('name'), $strusage, $strversion, $strenable, $strup.'/'.$strdown, $strsettings, $strtest, $struninstall);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->id = 'courseenrolmentplugins';
        $table->attributes['class'] = 'admintable table generaltable table-hover';
        $table->data  = array();

        // Iterate through enrol plugins and add to the display table.
        $updowncount = 1;
        $enrolcount = count($active_enrols);
        $url = new \moodle_url('/admin/enrol.php', array('sesskey'=>sesskey()));
        $printed = array();
        foreach($allenrols as $enrol => $unused) {
            $plugininfo = $pluginmanager->get_plugin_info('enrol_'.$enrol);
            $version = get_config('enrol_'.$enrol, 'version');
            if ($version === false) {
                $version = '';
            }

            if (get_string_manager()->string_exists('pluginname', 'enrol_'.$enrol)) {
                $name = get_string('pluginname', 'enrol_'.$enrol);
            } else {
                $name = $enrol;
            }
            // Usage.
            $ci = $DB->count_records('enrol', array('enrol'=>$enrol));
            $cp = $DB->count_records_select('user_enrolments', "enrolid IN (SELECT id FROM {enrol} WHERE enrol = ?)", array($enrol));
            $usage = "$ci / $cp";

            // Hide/show links.
            $class = '';
            if (isset($active_enrols[$enrol])) {
                $aurl = new \moodle_url($url, array('action'=>'disable', 'enrol'=>$enrol));
                $hideshow = "<a href=\"$aurl\">";
                $hideshow .= $OUTPUT->pix_icon('t/hide', $strdisable) . '</a>';
                $enabled = true;
                $displayname = $name;
            } else if (isset($enrols_available[$enrol])) {
                $aurl = new \moodle_url($url, array('action'=>'enable', 'enrol'=>$enrol));
                $hideshow = "<a href=\"$aurl\">";
                $hideshow .= $OUTPUT->pix_icon('t/show', $strenable) . '</a>';
                $enabled = false;
                $displayname = $name;
                $class = 'dimmed_text';
            } else {
                $hideshow = '';
                $enabled = false;
                $displayname = '<span class="notifyproblem">'.$name.'</span>';
            }
            if ($PAGE->theme->resolve_image_location('icon', 'enrol_' . $name, false)) {
                $icon = $OUTPUT->pix_icon('icon', '', 'enrol_' . $name, array('class' => 'icon pluginicon'));
            } else {
                $icon = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'icon pluginicon noicon'));
            }

            // Up/down link (only if enrol is enabled).
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $aurl = new \moodle_url($url, array('action'=>'up', 'enrol'=>$enrol));
                    $updown .= "<a href=\"$aurl\">";
                    $updown .= $OUTPUT->pix_icon('t/up', $strup) . '</a>&nbsp;';
                } else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                if ($updowncount < $enrolcount) {
                    $aurl = new \moodle_url($url, array('action'=>'down', 'enrol'=>$enrol));
                    $updown .= "<a href=\"$aurl\">";
                    $updown .= $OUTPUT->pix_icon('t/down', $strdown) . '</a>&nbsp;';
                } else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                ++$updowncount;
            }

            // Add settings link.
            if (!$version) {
                $settings = '';
            } else if ($surl = $plugininfo->get_settings_url()) {
                $settings = \html_writer::link($surl, $strsettings);
            } else {
                $settings = '';
            }

            // Add uninstall info.
            $uninstall = '';
            if ($uninstallurl = \core_plugin_manager::instance()->get_uninstall_url('enrol_'.$enrol, 'manage')) {
                $uninstall = \html_writer::link($uninstallurl, $struninstall);
            }

            $test = '';
            if (!empty($enrols_available[$enrol]) and method_exists($enrols_available[$enrol], 'test_settings')) {
                $testsettingsurl = new \moodle_url('/enrol/test_settings.php', ['enrol' => $enrol]);
                $test = \html_writer::link($testsettingsurl, $strtest);
            }

            // Add a row to the table.
            $row = new \html_table_row(array($icon.$displayname, $usage, $version, $hideshow, $updown, $settings, $test, $uninstall));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;

            $printed[$enrol] = true;
        }

        $return .= \html_writer::table($table);
        $return .= get_string('configenrolplugins', 'enrol').'<br />'.get_string('tablenosave', 'admin');
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(manageenrols::class, \admin_setting_manageenrols::class);

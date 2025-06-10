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
 * Admin setting to detect and set required settings in Moodle.
 *
 * @package local_o365
 * @author Enovation
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2021 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\adminsetting;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/adminlib.php');

/**
 * Admin setting to detect and set required settings in Moodle.
 */
class moodlesetup extends \admin_setting {

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $heading
     * @param string $description
     */
    public function __construct($name, $heading, $description) {
        $this->nosave = true;
        parent::__construct($name, $heading, $description, '0');
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return true;
    }

    /**
     * Write the setting.
     *
     * We do this manually so just pretend here.
     *
     * @param mixed $data Incoming form data.
     * @return string Always empty string representing no issues.
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Return an XHTML string for the settings.
     *
     * @param mixed $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;

        $button = \html_writer::tag('button', get_string('settings_check_moodle_settings', 'local_o365'),
            ['class' => 'setupmoodle', 'style' => 'margin: 0 0 0.75rem']);
        $results = \html_writer::tag('div', '', ['class' => 'results']);
        $settinghtml = $button.$results;

        // Using a <script> tag here instead of $PAGE->requires->js() because using $PAGE object loads file too late.
        $scripturl = new \moodle_url('/local/o365/classes/adminsetting/moodlesetup.js');
        $settinghtml .= '<script src="'.$scripturl->out().'"></script>';

        $ajaxurl = new \moodle_url('/local/o365/ajax.php');
        $settinghtml .= '<script>
                            $(function() {
                                var opts = {
                                    url: "'.$ajaxurl->out().'",
                                    iconsuccess: "'.addslashes($OUTPUT->pix_icon('t/check', 'success', 'moodle')).'",
                                    iconinfo: "'.addslashes($OUTPUT->pix_icon('i/info', 'information', 'moodle')).'",
                                    iconerror: "'.addslashes($OUTPUT->pix_icon('t/delete', 'error', 'moodle')).'",

                                    strcheck: "'.addslashes(get_string('settings_check_moodle_settings', 'local_o365')).'",
                                    strchecking: "'.addslashes(get_string('settings_moodlesetup_checking', 'local_o365')).'",
                                };
                                $("#admin-'.$this->name.'").moodlesetup(opts);
                            });
                        </script>';

        return format_admin_setting($this, $this->visiblename, $settinghtml, $this->description, true, '', null, $query);
    }
}

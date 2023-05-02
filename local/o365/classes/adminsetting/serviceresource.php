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
 * Admin setting to configure an o365 service.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\adminsetting;

use admin_setting_configtext;
use html_writer;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/adminlib.php');
require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * Admin setting to configure an o365 service.
 */
class serviceresource extends admin_setting_configtext {
    /**
     * Return an XHTML string for the setting.
     *
     * @param mixed $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;
        $settinghtml = '';

        // Input + detect button.
        $inputattrs = [
            'type' => 'text',
            'class' => 'maininput',
            'size' => 30,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => s($data),
        ];
        $input = html_writer::empty_tag('input', $inputattrs);
        if (auth_oidc_is_setup_complete()) {
            $buttonattrs = ['class' => 'detect'];
            $strdetect = get_string('settings_serviceresourceabstract_detect', 'local_o365');
            $detectbutton = html_writer::tag('button', $strdetect, $buttonattrs);
            $settinghtml .= html_writer::div($input.$detectbutton);
            if (!empty($data)) {
                $icon = $OUTPUT->pix_icon('t/check', 'valid', 'moodle');
                $strvalid = get_string('settings_serviceresourceabstract_valid', 'local_o365', $this->visiblename);
                $statusmessage = html_writer::tag('span', $strvalid, ['class' => 'statusmessage']);
                $settinghtml .= html_writer::div($icon.$statusmessage, 'alert-success alert local_o365_statusmessage');
            } else {
                $icon = $OUTPUT->pix_icon('i/warning', 'valid', 'moodle');
                $strnocreds = get_string('settings_serviceresourceabstract_empty', 'local_o365');
                $statusmessage = html_writer::tag('span', $strnocreds, ['class' => 'statusmessage']);
                $settinghtml .= html_writer::div($icon.$statusmessage, 'alert-info alert local_o365_statusmessage');
            }

            // Using a <script> tag here instead of $PAGE->requires->js() because using $PAGE object loads file too late.
            $scripturl = new \moodle_url('/local/o365/classes/adminsetting/serviceresource.js');
            $settinghtml .= '<script src="'.$scripturl->out().'"></script>';

            $strvalid = get_string('settings_serviceresourceabstract_valid', 'local_o365', $this->visiblename);
            $strinvalid = get_string('settings_serviceresourceabstract_invalid', 'local_o365', $this->visiblename);
            $strerror = get_string('settings_serviceresourceabstract_error', 'local_o365', $this->visiblename);
            $strdetect = get_string('settings_serviceresourceabstract_detect', 'local_o365', $this->visiblename);
            $strdetecting = get_string('settings_serviceresourceabstract_detecting', 'local_o365', $this->visiblename);
            $iconvalid = addslashes($OUTPUT->pix_icon('t/check', 'valid', 'moodle'));
            $iconinvalid = addslashes($OUTPUT->pix_icon('t/delete', 'invalid', 'moodle'));
            $iconloading = addslashes($OUTPUT->pix_icon('i/ajaxloader', 'loading', 'moodle'));
            $ajaxurl = new \moodle_url('/local/o365/ajax.php');
            $settinghtml .= '<script>
                                $(function() {
                                    var opts = {
                                        url: "'.$ajaxurl->out().'",
                                        setting: "'.$this->name.'",
                                        strvalid: "'.$strvalid.'",
                                        strinvalid: "'.$strinvalid.'",
                                        iconvalid: "'.$iconvalid.'",
                                        iconinvalid: "'.$iconinvalid.'",
                                        iconloading: "'.$iconloading.'",
                                        strerror: "'.$strerror.'",
                                        strdetecting: "'.$strdetecting.'",
                                        strdetect: "'.$strdetect.'"
                                    };
                                    $("#admin-'.$this->name.'").serviceresource(opts);
                                });
                            </script>';
        } else {
            $settinghtml .= html_writer::div($input);
            $icon = $OUTPUT->pix_icon('i/warning', 'valid', 'moodle');
            $strnocreds = get_string('settings_serviceresourceabstract_nocreds', 'local_o365');
            $statusmessage = html_writer::tag('span', $strnocreds, ['class' => 'statusmessage']);
            $settinghtml .= html_writer::div($icon.$statusmessage, 'alert-info alert local_o365_statusmessage');
        }

        return format_admin_setting($this, $this->visiblename, $settinghtml, $this->description);
    }
}

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
 * Settings of Team actions on course reset.
 *
 * @package local_o365
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2021 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\adminsetting;

use admin_setting;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * Class courseresetteams.
 */
class courseresetteams extends admin_setting {
    /**
     * Return the setting.
     *
     * @return mixed|null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Store new setting.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function write_setting($data) {
        $this->config_write($this->name, $data);
        return '';
    }

    /**
     * Return HTML string for the setting.
     *
     * @param mixed $data
     * @param string $query
     *
     * @return string
     */
    public function output_html($data, $query = '') {
        $settinghtml = '';

        $options = [
            COURSE_SYNC_RESET_SITE_SETTING_DO_NOTHING => new \lang_string('settings_course_reset_teams_option_do_nothing',
                'local_o365'),
        ];

        $coursesyncsetting = get_config('local_o365', 'coursesync');
        if ($coursesyncsetting == 'oncustom') {
            $options[COURSE_SYNC_RESET_SITE_SETTING_DISCONNECT_ONLY] = new \lang_string(
                'settings_course_reset_teams_option_archive_only', 'local_o365');
        }

        $options[COURSE_SYNC_RESET_SITE_SETTING_DISCONNECT_AND_CREATE_NEW] = new \lang_string(
            'settings_course_reset_teams_option_force_archive', 'local_o365');
        $options[COURSE_SYNC_RESET_SITE_SETTING_PER_COURSE] = new \lang_string(
            'settings_course_reset_teams_option_per_course', 'local_o365');

        $currentvalue = (isset($options[$data])) ? $data : $this->get_defaultsetting();
        foreach ($options as $key => $desc) {
            $radioattributes = [
                'type' => 'radio',
                'id' => $this->get_id() . '_' . $key,
                'name' => $this->get_full_name(),
                'value' => $key,
                'class' => 'local_o365_acp_option',
            ];
            if ($currentvalue == $key) {
                $radioattributes['checked'] = 'checked';
            }
            $settinghtml .= \html_writer::empty_tag('input', $radioattributes);
            $settinghtml .= \html_writer::label($desc, $this->get_id().'_'.$key, false);
            $settinghtml .= \html_writer::empty_tag('br');
            $settinghtml .= \html_writer::empty_tag('br');
        }

        return format_admin_setting($this, $this->visiblename, $settinghtml, $this->description);
    }
}

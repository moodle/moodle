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
 * Path to directory
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configdirectory extends admin_setting_configfile {

    /**
     * Returns an XHTML field
     *
     * @param string $data This is the value for the field
     * @param string $query
     * @return string XHTML
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;
        $default = $this->get_defaultsetting();

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'size' => $this->size,
            'value' => $data,
            'showvalidity' => !empty($data),
            'valid' => $data && file_exists($data) && is_dir($data),
            'readonly' => !empty($CFG->preventexecpath),
            'forceltr' => $this->get_force_ltr()
        ];

        if (!empty($CFG->preventexecpath)) {
            $this->visiblename .= '<div class="alert alert-info">'.get_string('execpathnotallowed', 'admin').'</div>';
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configdirectory', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}

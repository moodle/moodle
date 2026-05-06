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
 * Empty setting used to allow flags (advanced) on settings that can have no sensible default.
 * Note: Only advanced makes sense right now - locked does not.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configempty extends admin_setting_configtext {

    /**
     * @param string $name
     * @param string $visiblename
     * @param string $description
     */
    public function __construct($name, $visiblename, $description) {
        parent::__construct($name, $visiblename, $description, '', PARAM_RAW);
    }

    /**
     * Returns an XHTML string for the hidden field
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name()
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configempty', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', get_string('none'), $query);
    }
}

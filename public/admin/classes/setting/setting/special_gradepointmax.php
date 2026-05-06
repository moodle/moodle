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
 * A setting for setting the maximum grade value. Must be an integer between 1 and 10000.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_gradepointmax extends admin_setting_configtext {

    /**
     * Config gradepointmax constructor
     *
     * @param string $name Overidden by "gradepointmax"
     * @param string $visiblename Overridden by "gradepointmax" language string.
     * @param string $description Overridden by "gradepointmax_help" language string.
     * @param string $defaultsetting Not used, overridden by 100.
     * @param mixed $paramtype Overridden by PARAM_INT.
     * @param int $size Overridden by 5.
     */
    public function __construct($name = '', $visiblename = '', $description = '', $defaultsetting = '', $paramtype = PARAM_INT, $size = 5) {
        $name = 'gradepointmax';
        $visiblename = get_string('gradepointmax', 'grades');
        $description = get_string('gradepointmax_help', 'grades');
        $defaultsetting = 100;
        $paramtype = PARAM_INT;
        $size = 5;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }

    /**
     * Save the selected setting
     *
     * @param string $data The selected site
     * @return string empty string or error message
     */
    public function write_setting($data) {
        if ($data === '') {
            $data = (int)$this->defaultsetting;
        } else {
            $data = $data;
        }
        return parent::write_setting($data);
    }

    /**
     * Validate data before storage
     * @param string $data The submitted data
     * @return bool|string true if ok, string if error found
     */
    public function validate($data) {
        if (((string)(int)$data === (string)$data && $data > 0 && $data <= 10000)) {
            return true;
        } else {
            return get_string('gradepointmax_validateerror', 'grades');
        }
    }

    /**
     * Return an XHTML string for the setting
     * @param array $data Associative array of value=>xx, forced=>xx, adv=>xx
     * @param string $query search query to be highlighted
     * @return string XHTML to display control
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'size' => $this->size,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'attributes' => [
                'maxlength' => 5
            ],
            'forceltr' => $this->get_force_ltr()
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configtext', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}

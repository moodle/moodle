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
 * Admin setting class for encrypted values using secure encryption.
 *
 * @copyright 2019 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_encryptedpassword extends admin_setting {

    /**
     * Constructor. Same as parent except that the default value is always an empty string.
     *
     * @param string $name Internal name used in config table
     * @param string $visiblename Name shown on form
     * @param string $description Description that appears below field
     */
    public function __construct(string $name, string $visiblename, string $description) {
        parent::__construct($name, $visiblename, $description, '');
    }

    public function get_setting() {
        return $this->config_read($this->name);
    }

    public function write_setting($data) {
        $data = trim($data);
        if ($data === '') {
            // Value can really be set to nothing.
            $savedata = '';
        } else {
            // Encrypt value before saving it.
            $savedata = \core\encryption::encrypt($data);
        }
        return ($this->config_write($this->name, $savedata) ? '' : get_string('errorsetting', 'admin'));
    }

    public function output_html($data, $query='') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'set' => $data !== '',
            'novalue' => $this->get_setting() === null
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_encryptedpassword', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description,
                true, '', $default, $query);
    }
}

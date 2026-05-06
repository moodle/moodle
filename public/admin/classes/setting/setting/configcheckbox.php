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
 * Checkbox
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configcheckbox extends admin_setting {
    /** @var string Value used when checked */
    public $yes;
    /** @var string Value used when not checked */
    public $no;

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param string $yes value used when checked
     * @param string $no value used when not checked
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $yes='1', $no='0') {
        parent::__construct($name, $visiblename, $description, $defaultsetting);
        $this->yes = (string)$yes;
        $this->no  = (string)$no;
    }

    /**
     * Retrieves the current setting using the objects name
     *
     * @return string
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Sets the value for the setting
     *
     * Sets the value for the setting to either the yes or no values
     * of the object by comparing $data to yes
     *
     * @param mixed $data Gets converted to str for comparison against yes value
     * @return string empty string or error
     */
    public function write_setting($data) {
        if ((string)$data === $this->yes) { // convert to strings before comparison
            $data = $this->yes;
        } else {
            $data = $this->no;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns an XHTML checkbox field
     *
     * @param string $data If $data matches yes then checkbox is checked
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'no' => $this->no,
            'value' => $this->yes,
            'checked' => (string) $data === $this->yes,
            'readonly' => $this->is_readonly(),
        ];

        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            if ((string)$default === $this->yes) {
                $defaultinfo = get_string('checkboxyes', 'admin');
            } else {
                $defaultinfo = get_string('checkboxno', 'admin');
            }
        } else {
            $defaultinfo = NULL;
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configcheckbox', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }
}

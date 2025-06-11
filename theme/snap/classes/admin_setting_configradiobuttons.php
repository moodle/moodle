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

namespace theme_snap;

/**
 * Class to render radio buttons in settings pages.
 * @package theme_snap
 * @author SL
 * @copyright Blackboard Ltd 2017
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class admin_setting_configradiobuttons extends \admin_setting {

    public $radios;

    /**
     * Constructor
     * @param string $name Unique ascii name, either 'mysetting' for settings that in config or
     *                     'myplugin/mysetting' for ones in config_plugins.
     * @param string $title Localised.
     * @param string $description Long localised info
     * @param string|int $defaultsetting
     * @param array $radios array of $value => $label for each selection.
     */
    public function __construct($name, $title, $description, $default, $radios) {
        $this->radios = $radios;
        parent::__construct($name, $title, $description, $default);
    }

    /**
     * Generates the HTML for the setting
     *
     * @param string $data
     * @param string $query
     */
    public function output_html($data, $query='') {
        $default = $this->get_defaultsetting();
        $current = $this->get_setting();
        $checkedvalue = $default;
        if (!empty($current)) {
            $checkedvalue = $current;
        }
        $inputs = '<div class="form-radio" ><div class="radio" id="'.$this->get_id().'">';
        foreach ($this->radios as $key => $value) {
            $checked = '';
            if ($key === $checkedvalue) {
                $checked = 'checked';
            }
            $inputs .= '<label id="' .s($this->get_full_name()).'_'.$key.'"><input type="radio" name="'.
                    s($this->get_full_name()). '" value="' .$key. '" ' .$checked. '>' .s($value). '</label><br>';
        }
        $inputs .= '</div></div>';
        return format_admin_setting($this, $this->visiblename, $inputs,
        $this->description, true, '', $default, $query);
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Saves the setting
     *
     * @param string $data
     * @return bool
     */
    public function write_setting($data) {
        if (!array_key_exists($data, $this->radios)) {
            return ''; // Data does not match valid radio value.
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

}

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
 * Text input with a maximum length constraint.
 *
 * @copyright 2015 onwards Ankit Agarwal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtext_with_maxlength extends admin_setting_configtext {

    /** @var int maximum number of chars allowed. */
    protected $maxlength;

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *                     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int $size default field size
     * @param mixed $maxlength int maxlength allowed, 0 for infinite.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW,
                                $size=null, $maxlength = 0) {
        $this->maxlength = $maxlength;
        $this->set_data_attribute('validation-max-length', $maxlength);
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }

    /**
     * Validate data before storage
     *
     * @param string $data data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        $parentvalidation = parent::validate($data);
        if ($parentvalidation === true) {
            if ($this->maxlength > 0) {
                // Max length check.
                $length = core_text::strlen($data);
                if ($length > $this->maxlength) {
                    return get_string('maximumchars', 'moodle',  $this->maxlength);
                }
                return true;
            } else {
                return true; // No max length check needed.
            }
        } else {
            return $parentvalidation;
        }
    }

    /**
     * Return an XHTML string for the setting.
     *
     * @param string $data data.
     * @param string $query query statement.
     * @return string Returns an XHTML string
     */
    public function output_html($data, $query = ''): string {
        global $PAGE;
        $PAGE->requires->js_call_amd('core_form/configtext_maxlength', 'init');

        return parent::output_html($data, $query);
    }
}

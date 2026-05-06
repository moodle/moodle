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
 * A setting for setting the default grade point value. Must be an integer between 1 and $CFG->gradepointmax.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class special_gradepointdefault extends \admin_setting_configtext {
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
        $name = 'gradepointdefault';
        $visiblename = get_string('gradepointdefault', 'grades');
        $description = get_string('gradepointdefault_help', 'grades');
        $defaultsetting = 100;
        $paramtype = PARAM_INT;
        $size = 5;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }

    /**
     * Validate data before storage
     * @param string $data The submitted data
     * @return bool|string true if ok, string if error found
     */
    public function validate($data) {
        global $CFG;
        if (((string)(int)$data === (string)$data && $data > 0 && $data <= $CFG->gradepointmax)) {
            return true;
        } else {
            return get_string('gradepointdefault_validateerror', 'grades');
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(special_gradepointdefault::class, \admin_setting_special_gradepointdefault::class);

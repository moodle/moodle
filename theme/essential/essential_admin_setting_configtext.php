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
 * Config text setting with validation.
 *
 * @package    theme
 * @subpackage essential
 * @copyright  &copy; 2016-onwards G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class essential_admin_setting_configtext extends admin_setting_configtext {

    /** @var string RegEx expression to test */
    public $regex;
    /** @var string Error message to show if validation fails */
    public $error;

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in
     * config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param int $regex RegEx expression to test.
     * @param int $error Error message to show if validation fails.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $regex, $error) {
        $this->regex = $regex;
        $this->error = $error;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        $validated = parent::validate($data); // Pass parent validation first.

        if ($validated == true) {
            $matches = preg_match($this->regex, $data);
            if ($matches === false) {
                $validated = 'preg_match() error.';
            } else if ($matches == 0) {
                $validated = '\''.$data.'\''.$this->error;
            }
        }

        return $validated;
    }
}

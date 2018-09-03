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
 * Put properties with validation setting.
 *
 * @package    theme
 * @subpackage essential
 * @copyright  &copy; 2017-onwards G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class essential_admin_setting_putprops extends admin_setting_configtextarea {

    /** @var string Name of the theme. */
    private $themename;
    /** @var string Name of the 'callable' function to call with the name of the theme and the properties as an array. */
    private $callme;
    /** @var string Report back from the parsing 'callable' to inform the user in the text area. */
    private $report = '';

    /**
     * Not a setting, just putting properties.
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in
     * config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param string $themename Name of the theme.
     * @param string $callme Name of the 'callable' function to call with the name of the theme and the properties as an array.
     */
    public function __construct($name, $visiblename, $description, $themename, $callme) {
        $this->themename = $themename;
        $this->callme = $callme;
        parent::__construct($name, $visiblename, $description, ''); // Last parameter is default.
    }

    public function get_defaultsetting() {
        return '';
    }

    public function write_setting($data) {
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }

        return ($this->config_write($this->name, $this->report) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Validate data before storage.
     * @param string data.
     * @return mixed true if alright, string if error found.
     */
    public function validate($data) {
        $validated = parent::validate($data); // Pass parent validation first.

        if ($validated == true) {
            if (!empty($data)) {
                // Only attempt decode if we have the start of a JSON string, otherwise will certainly be the saved report.
                if ($data[0] == '{') {
                    $props = json_decode($data, true);
                    if ($props === null) {
                        if (function_exists('json_last_error_msg')) {
                            $validated = json_last_error_msg();
                        } else {
                            // Fall back to numeric error for older PHP version.
                            $validated = json_last_error();
                        }
                    } else {
                        $this->report = call_user_func($this->callme, $this->themename, $props);
                    }
                } else {
                    // Keep what we have.
                    $this->report = $data;
                }
            }
        }

        return $validated;
    }

    /**
     * Returns an HTML string
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        $return = parent::output_html($data, $query);

        return $return;
    }
}

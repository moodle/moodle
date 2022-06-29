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
 * Special setting for auth_shibboleth convert_data.
 *
 * @package    auth_shibboleth
 * @copyright  2020 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Admin settings class for the convert_data option.
 *
 * @package    auth_shibboleth
 * @copyright  2020 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_shibboleth_admin_setting_convert_data extends admin_setting_configfile {

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultdirectory
     */
    public function __construct($name, $visiblename, $description, $defaultdirectory) {
        parent::__construct($name, $visiblename, $description, $defaultdirectory);
    }

    /**
     * Validate the file path (location).
     *
     * This method ensures that the file defined as a data modification API exists and is not located in the site
     * data directory ($CFG->dataroot). We should prohibit using files from the site data directory as this introduces
     * security vulnerabilities.
     *
     * @param string $filepath The path to the file.
     * @return mixed bool true for success or string:error on failure.
     */
    public function validate($filepath) {
        global $CFG;

        if (empty($filepath)) {
            return true;
        }

        // Fail if the file does not exist or it is not readable by the webserver process.
        if (!is_readable($filepath)) {
            return get_string('auth_shib_convert_data_warning', 'auth_shibboleth');
        }

        // Fail if the absolute file path matches the currently defined dataroot path.
        if (preg_match('/' . preg_quote($CFG->dataroot, '/') . '/', realpath($filepath))) {
            return get_string('auth_shib_convert_data_filepath_warning', 'auth_shibboleth');
        }

        return true;
    }
}

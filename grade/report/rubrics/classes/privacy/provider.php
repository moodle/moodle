<?php
// This file is part of the gradereport rubrics plugin
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
 * Defines rubrics report privacy\provider class.
 *
 * @package    gradereport_rubrics
 * @category   privacy
 * @copyright  2018 LTS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace gradereport_rubrics\privacy;

/**
 * Privacy API implementation for the gradereport rubrics plugin.
 *
 * @copyright  2018 Karen Holland <karen@lts.ie>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin does not store any personal user data.
    \core_privacy\local\metadata\null_provider {
    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:metadata';
    }
}

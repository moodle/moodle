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
 * Course module navigation block main controller
 *
 * @package         block_course_modulenavigation
 * @copyright       2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author          Sylvain Revneu | Jordan Kesraoui | Pimenko
 * @license         http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_course_modulenavigation\privacy;

use core_privacy\local\metadata\null_provider;

/**
 * Privacy Subsystem for block_course_modulenavigation implementing null_provider.
 *
 * @copyright  2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements // This plugin does not store any personal user data.
    null_provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason(): string {
        return 'privacy:null_reason';
    }
}

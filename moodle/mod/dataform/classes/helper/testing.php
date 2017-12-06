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
 * The mod_dataform testing helper.
 *
 * @package    mod_dataform
 * @copyright  2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * This class provides helper methods for testing.
 */
class testing {
    /**
     * Returns role short name override if exists. Overrides can be defined in config.php
     * in the following way:
     * define('PHPUNIT_ROLENAME_EDITINGTEACHER', 'instructor');
     *
     * @param string $shortname
     * @return string
     */
    public static function get_role_shortname($shortname) {
        $constantstr = strtoupper("PHPUNIT_ROLENAME_$shortname");
        if (defined($constantstr) and $newshortname = constant($constantstr)) {
            return $newshortname;
        }

        return $shortname;
    }
}

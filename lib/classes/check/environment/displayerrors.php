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
 * Verifies displaying of errors
 *
 * Problem for lib files and 3rd party code because we can not disable debugging
 * in these scripts (they do not include config.php)
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\check\environment;

defined('MOODLE_INTERNAL') || die();

use core\check\result;
use core\check\check;

/**
 * Verifies displaying of errors
 *
 * Problem for lib files and 3rd party code because we can not disable debugging
 * in these scripts (they do not include config.php)
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class displayerrors extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_displayerrors_name', 'report_security');
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        $details = get_string('check_displayerrors_details', 'report_security');

        if (defined('WARN_DISPLAY_ERRORS_ENABLED')) {
            $status = result::WARNING;
            $summary = get_string('check_displayerrors_error', 'report_security');
        } else {
            $status = result::OK;
            $summary = get_string('check_displayerrors_ok', 'report_security');
        }
        return new result($status, $summary, $details);
    }
}


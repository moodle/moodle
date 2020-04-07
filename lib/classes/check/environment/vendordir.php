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
 * Check the presence of the vendor directory.
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\check\environment;

defined('MOODLE_INTERNAL') || die();

use core\check\check;
use core\check\result;

/**
 * Check the presence of the vendor directory.
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class vendordir extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_vendordir_name', 'report_security');
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $CFG;
        $details = get_string('check_vendordir_details', 'report_security', ['path' => $CFG->dirroot.'/vendor']);
        $summary = get_string('check_vendordir_info', 'report_security');

        if (is_dir($CFG->dirroot.'/vendor')) {
            $status = result::WARNING;
        } else {
            $status = result::OK;
        }
        return new result($status, $summary, $details);
    }
}


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
 * Verifies if arbitrary SQL execution is enabled in the site
 *
 * @package    block_configurable_reports
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_configurable_reports\check;

use core\check\result;

/**
 * Verifies if arbitrary SQL execution is enabled in the site
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sql_execution extends \core\check\check {

    /**
     * Return result
     *
     * @return result
     */
    public function get_result(): result {
        global $CFG;

        if (!empty($CFG->block_configurable_reports_enable_sql_execution)) {
            $status = result::WARNING;
            $summary = get_string('checksql_execution_warning', 'block_configurable_reports');
        } else {
            $status = result::OK;
            $summary = get_string('checksql_execution_ok', 'block_configurable_reports');
        }
        $details = get_string('checksql_execution_details', 'block_configurable_reports');

        return new result($status, $summary, $details);
    }

}


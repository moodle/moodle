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

namespace mod_lti;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for LTI activity.
 *
 * @package    mod_lti
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Get SQL to query DB for LTI tool proxy records.
     *
     * @param bool $orphanedonly If true, return SQL to get orphaned proxies only.
     * @param bool $count If true, return SQL to get the count of the records instead of the records themselves.
     * @return string SQL.
     */
    public static function get_tool_proxy_sql(bool $orphanedonly = false, bool $count = false): string {
        if ($count) {
            $select = "SELECT count(*) as type_count";
            $sort = "";
        } else {
            // We only want the fields from lti_tool_proxies table. Must define every column to be compatible with mysqli.
            $select = "SELECT ltp.id, ltp.name, ltp.regurl, ltp.state, ltp.guid, ltp.secret, ltp.vendorcode,
                              ltp.capabilityoffered, ltp.serviceoffered, ltp.toolproxy, ltp.createdby,
                              ltp.timecreated, ltp.timemodified";
            $sort = " ORDER BY ltp.name ASC, ltp.state DESC, ltp.timemodified DESC";
        }
        $from = " FROM {lti_tool_proxies} ltp";
        if ($orphanedonly) {
            $join = " LEFT JOIN {lti_types} lt ON ltp.id = lt.toolproxyid";
            $where = " WHERE lt.toolproxyid IS null";
        } else {
            $join = "";
            $where = "";
        }

        return $select . $from . $join . $where . $sort;
    }
}

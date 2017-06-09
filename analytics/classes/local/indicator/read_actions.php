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
 * Read actions indicator.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Read actions indicator.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class read_actions extends linear {

    public static function get_name() {
        return get_string('indicator:readactions', 'analytics');
    }

    public static function required_sample_data() {
        // User is not required, calculate_sample can handle its absence.
        return array('context');
    }

    protected function calculate_sample($sampleid, $sampleorigin, $starttime = false, $endtime = false) {
        global $DB;

        $select = '';
        $params = array();

        if ($user = $this->retrieve('user', $sampleid)) {
            $select .= "userid = :userid AND ";
            $params = $params + array('userid' => $user->id);
        }

        // Filter by context to use the db table index.
        $context = $this->retrieve('context', $sampleid);
        $select .= "contextlevel = :contextlevel AND contextinstanceid = :contextinstanceid AND " .
            "crud = 'r' AND timecreated > :starttime AND timecreated <= :endtime";
        $params = $params + array('contextlevel' => $context->contextlevel,
            'contextinstanceid' => $context->instanceid, 'starttime' => $starttime, 'endtime' => $endtime);
        $nrecords = $DB->count_records_select('logstore_standard_log', $select, $params);

        // We define a list of ranges to fit $nrecords into it
        // # Done absolutely nothing
        // # Not much really, just accessing the course once a week
        // # More than just accessing the course, some interaction
        // # Significant contribution, will depend on the course anyway

        // We need to adapt the limits to the time range duration.
        $nweeks = $this->get_time_range_weeks_number($starttime, $endtime);

        // Careful with this, depends on the course.
        $limit = $nweeks * 3 * 10;
        $ranges = array(
            array('eq', 0),
            // 1 course access per week (3 records are easily generated).
            array('le', $nweeks * 3),
            // 3 course accesses per week doing some stuff.
            array('le', $limit),
            array('gt', $limit)
        );
        return $this->classify_value($nrecords, $ranges);
    }
}

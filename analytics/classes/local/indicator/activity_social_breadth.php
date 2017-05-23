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
 * Social breadth abstract indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Social breadth abstract indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class activity_social_breadth extends community_of_inquiry_activity {

    public function calculate_sample($sampleid, $tablename, $starttime = false, $endtime = false) {

        // May not be available.
        $user = $this->retrieve('user', $sampleid);

        if (!$useractivities = $this->get_student_activities($sampleid, $tablename, $starttime, $endtime)) {
            // Null if no activities.
            return null;
        }

        $scoreperactivity = (self::get_max_value() - self::get_min_value()) / count($useractivities);

        $score = self::get_min_value();

        foreach ($useractivities as $contextid => $cm) {
            // TODO Add support for other levels than 1.
            if ($this->any_log($contextid, $user)) {
                $score += $scoreperactivity;
            }
        }

        // To avoid decimal problems.
        if ($score > self::MAX_VALUE) {
            return self::MAX_VALUE;
        } else if ($score < self::MIN_VALUE) {
            return self::MIN_VALUE;
        }
        return $score;
    }
}

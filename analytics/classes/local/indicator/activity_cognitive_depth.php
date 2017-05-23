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
 * Cognitive depth abstract indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Cognitive depth abstract indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class activity_cognitive_depth extends community_of_inquiry_activity {

    public function calculate_sample($sampleid, $tablename, $starttime = false, $endtime = false) {

        // May not be available.
        $user = $this->retrieve('user', $sampleid);

        if (!$useractivities = $this->get_student_activities($sampleid, $tablename, $starttime, $endtime)) {
            // Null if no activities.
            return null;
        }

        $scoreperactivity = (self::get_max_value() - self::get_min_value()) / count($useractivities);

        $score = self::get_min_value();

        // Iterate through the module activities/resources which due date is part of this time range.
        foreach ($useractivities as $contextid => $cm) {

            $potentiallevel = $this->get_cognitive_depth_level($cm);
            if (!is_int($potentiallevel) || $potentiallevel > 5 || $potentiallevel < 1) {
                throw new \coding_exception('Activities\' potential level of engagement possible values go from 1 to 5.');
            }
            $scoreperlevel = $scoreperactivity / $potentiallevel;

            switch ($potentiallevel) {
                case 5:
                    // Cognitive level 4 is to comment on feedback.
                    if ($this->any_feedback('submitted', $cm, $contextid, $user)) {
                        $score += $scoreperlevel * 5;
                        break;
                    }
                    // The user didn't reach the activity max cognitive depth, continue with level 2.

                case 4:
                    // Cognitive level 4 is to comment on feedback.
                    if ($this->any_feedback('replied', $cm, $contextid, $user)) {
                        $score += $scoreperlevel * 4;
                        break;
                    }
                    // The user didn't reach the activity max cognitive depth, continue with level 2.

                case 3:
                    // Cognitive level 3 is to view feedback.

                    if ($this->any_feedback('viewed', $cm, $contextid, $user)) {
                        // Max score for level 3.
                        $score += $scoreperlevel * 3;
                        break;
                    }
                    // The user didn't reach the activity max cognitive depth, continue with level 2.

                case 2:
                    // Cognitive depth level 2 is to submit content.

                    if ($this->any_write_log($contextid, $user)) {
                        $score += $scoreperlevel * 2;
                        break;
                    }
                    // The user didn't reach the activity max cognitive depth, continue with level 1.

                case 1:
                    // Cognitive depth level 1 is just accessing the activity.

                    if ($this->any_log($contextid, $user)) {
                        $score += $scoreperlevel;
                    }

                default:
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

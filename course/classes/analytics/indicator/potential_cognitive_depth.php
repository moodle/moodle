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
 * Potential cognitive depth indicator.
 *
 * @package   core_course
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

use \core_analytics\local\indicator\community_of_inquiry_activity;

/**
 * Potential cognitive depth indicator.
 *
 * It extends linear instead of discrete as there is a linear relation between
 * the different cognitive levels activities can reach.
 *
 * @package   core_course
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class potential_cognitive_depth extends \core_analytics\local\indicator\linear {

    /**
     * get_name
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('indicator:potentialcognitive', 'moodle');
    }

    /**
     * Specify the required data to process this indicator.
     *
     * @return string[]
     */
    public static function required_sample_data() {
        // We require course because, although this indicator can also work with course_modules we can't
        // calculate anything without the course.
        return array('course');
    }

    /**
     * calculate_sample
     *
     * @throws \coding_exception
     * @param int $sampleid
     * @param string $sampleorigin
     * @param int|false $notusedstarttime
     * @param int|false $notusedendtime
     * @return float
     */
    public function calculate_sample($sampleid, $sampleorigin, $notusedstarttime = false, $notusedendtime = false) {

        if ($sampleorigin === 'course_modules') {
            $cm = $this->retrieve('course_modules', $sampleid);
            $cminfo = \cm_info::create($cm);

            $cognitivedepthindicator = $this->get_cognitive_indicator($cminfo->modname);
            $potentiallevel = $cognitivedepthindicator->get_cognitive_depth_level($cminfo);
            if ($potentiallevel > community_of_inquiry_activity::MAX_COGNITIVE_LEVEL) {
                throw new \coding_exception('Maximum cognitive depth level is ' .
                    community_of_inquiry_activity::MAX_COGNITIVE_LEVEL . ', ' . $potentiallevel . ' provided by ' .
                        get_class($this));
            }

        } else {
            $course = $this->retrieve('course', $sampleid);
            $modinfo = get_fast_modinfo($course);

            $cms = $modinfo->get_cms();
            if (!$cms) {
                return self::get_min_value();
            }

            $potentiallevel = 0;
            foreach ($cms as $cm) {
                if (!$cognitivedepthindicator = $this->get_cognitive_indicator($cm->modname)) {
                    continue;
                }
                $level = $cognitivedepthindicator->get_cognitive_depth_level($cm);
                if ($level > community_of_inquiry_activity::MAX_COGNITIVE_LEVEL) {
                    throw new \coding_exception('Maximum cognitive depth level is ' .
                        community_of_inquiry_activity::MAX_COGNITIVE_LEVEL . ', ' . $level . ' provided by ' . get_class($this));
                }
                if ($level > $potentiallevel) {
                    $potentiallevel = $level;
                }
            }
        }

        // Values from -1 to 1 range split in 5 parts (the max cognitive depth level).
        // Note that we divide by 4 because we start from -1.
        $levelscore = round((self::get_max_value() - self::get_min_value()) / 4, 2);
        // We substract $levelscore because we want to start from the lower score and there is no cognitive depth level 0.
        return self::get_min_value() + ($levelscore * $potentiallevel) - $levelscore;
    }

    /**
     * Returns the cognitive depth class of this indicator.
     *
     * @param string $modname
     * @return \core_analytics\local\indicator\base|false
     */
    protected function get_cognitive_indicator($modname) {
        $indicators = \core_analytics\manager::get_all_indicators();
        foreach ($indicators as $indicator) {
            if ($indicator instanceof community_of_inquiry_activity &&
                    $indicator->get_indicator_type() === community_of_inquiry_activity::INDICATOR_COGNITIVE &&
                    $indicator->get_activity_type() === $modname) {
                return $indicator;
            }
        }
        return false;
    }
}

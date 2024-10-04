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
 * Completion enabled set indicator.
 *
 * @package   core_course
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/completionlib.php');

/**
 * Completion enabled set indicator.
 *
 * @package   core_course
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_enabled extends \core_analytics\local\indicator\binary {

    /**
     * get_name
     *
     * @return new \lang_string
     */
    public static function get_name(): \lang_string {
        return new \lang_string('indicator:completionenabled', 'moodle');
    }

    /**
     * required_sample_data
     *
     * @return string[]
     */
    public static function required_sample_data() {
        // Minimum course although it also accepts course_modules.
        return array('course');
    }

    /**
     * Is completion enabled? Work both with courses and activities.
     *
     * @param int $sampleid
     * @param string $sampleorigin
     * @param int|false $notusedstarttime
     * @param int|false $notusedendtime
     * @return float
     */
    public function calculate_sample($sampleid, $sampleorigin, $notusedstarttime = false, $notusedendtime = false) {

        $course = $this->retrieve('course', $sampleid);

        // It may not be available, but if it is the indicator checks if completion is enabled for the cm.
        $cm = $this->retrieve('course_modules', $sampleid);

        $completion = new \completion_info($course);

        if (!$completion->is_enabled($cm)) {
            $value = self::get_min_value();
        } else if (!$cm && !$completion->has_criteria()) {
            // Course completion enabled with no criteria counts as nothing.
            $value = self::get_min_value();
        } else {
            $value = self::get_max_value();
        }
        return $value;
    }
}

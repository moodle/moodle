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
 * Test indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_indicator_fullname extends \core_analytics\local\indicator\linear {

    /**
     * Returns a lang_string object representing the name for the indicator.
     *
     * Used as column identificator.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        // Using a string that exists and contains a corresponding '_help' string.
        return new \lang_string('allowstealthmodules');
    }

    /**
     * include_averages
     *
     * @return bool
     */
    protected static function include_averages() {
        return false;
    }

    /**
     * required_sample_data
     *
     * @return string[]
     */
    public static function required_sample_data() {
        return array('course');
    }

    /**
     * calculate_sample
     *
     * @param int $sampleid
     * @param string $samplesorigin
     * @param int $starttime
     * @param int $endtime
     * @return float
     */
    protected function calculate_sample($sampleid, $samplesorigin, $starttime, $endtime) {

        $course = $this->retrieve('course', $sampleid);

        $firstchar = substr($course->fullname, 0, 1);
        if ($firstchar === 'a') {
            return self::MIN_VALUE;
        } else if ($firstchar === 'b') {
            return -0.2;
        } else if ($firstchar === 'c') {
            return 0.2;
        } else {
            return self::MAX_VALUE;
        }
    }
}

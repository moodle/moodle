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
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test indicator.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_indicator_discrete extends \core_analytics\local\indicator\discrete {

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name(): \lang_string {
        // Using a string that exists and contains a corresponding '_help' string.
        return new \lang_string('allowstealthmodules');
    }

    /**
     * The different classes this discrete indicator provides.
     * @return [type] [description]
     */
    protected static function get_classes() {
        return [0, 1, 2, 3, 4];
    }

    /**
     * Just for testing.
     *
     * @param  float $value
     * @param  string $subtype
     * @return string
     */
    public function get_calculation_outcome($value, $subtype = false) {
        return self::OUTCOME_OK;
    }

    /**
     * Custom indicator calculated value display as otherwise we would display meaningless numbers to users.
     *
     * @param  float  $value
     * @param  string $subtype
     * @return string
     */
    public function get_display_value($value, $subtype = false) {
        return $value;
    }

    /**
     * calculate_sample
     *
     * @param int $sampleid
     * @param string $sampleorigin
     * @param int $starttime
     * @param int $endtime
     * @return float
     */
    protected function calculate_sample($sampleid, $sampleorigin, $starttime = false, $endtime = false) {
        return 4;
    }
}

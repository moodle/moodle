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
 * Abstract base indicator.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract base indicator.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends \core_analytics\calculable {

    /**
     * Min value an indicator can return.
     */
    const MIN_VALUE = -1;

    /**
     * Max value an indicator can return.
     */
    const MAX_VALUE = 1;

    /**
     * Converts the calculated indicators to dataset feature/s.
     *
     * @param float|int[] $calculatedvalues
     * @return array
     */
    abstract protected function to_features($calculatedvalues);

    /**
     * Calculates the sample.
     *
     * Return a value from self::MIN_VALUE to self::MAX_VALUE or null if the indicator can not be calculated for this sample.
     *
     * @param int $sampleid
     * @param string $sampleorigin
     * @param integer $starttime Limit the calculation to this timestart
     * @param integer $endtime Limit the calculation to this timeend
     * @return float|null
     */
    abstract protected function calculate_sample($sampleid, $sampleorigin, $starttime, $endtime);

    /**
     * Should this value be displayed?
     *
     * Indicators providing multiple features can be used this method to discard some of them.
     *
     * @param float $value
     * @param string $subtype
     * @return bool
     */
    public function should_be_displayed($value, $subtype) {
        // We should everything by default.
        return true;
    }

    /**
     * Allows indicators to specify data they need.
     *
     * e.g. A model using courses as samples will not provide users data, but an indicator like
     * "user is hungry" needs user data.
     *
     * @return null|string[] Name of the required elements (use the database tablename)
     */
    public static function required_sample_data() {
        return null;
    }

    /**
     * Returns an instance of the indicator.
     *
     * Useful to reset cached data.
     *
     * @return \core_analytics\local\indicator\base
     */
    public static function instance() {
        return new static();
    }

    /**
     * Returns the maximum value an indicator calculation can return.
     *
     * @return float
     */
    public static function get_max_value() {
        return self::MAX_VALUE;
    }

    /**
     * Returns the minimum value an indicator calculation can return.
     *
     * @return float
     */
    public static function get_min_value() {
        return self::MIN_VALUE;
    }

    /**
     * Hook to allow indicators to pre-fill data that is shared accross time range calculations.
     *
     * Useful to fill analysable-dependant data that does not depend on the time ranges. Use
     * instance vars to cache data that can be re-used across samples calculations but changes
     * between time ranges (indicator instances are reset between time ranges to avoid unexpected
     * problems).
     *
     * You are also responsible of emptying previous analysable caches.
     *
     * @param \core_analytics\analysable $analysable
     * @return void
     */
    public function fill_per_analysable_caches(\core_analytics\analysable $analysable) {
    }

    /**
     * Calculates the indicator.
     *
     * Returns an array of values which size matches $sampleids size.
     *
     * @param int[] $sampleids
     * @param string $samplesorigin
     * @param integer $starttime Limit the calculation to this timestart
     * @param integer $endtime Limit the calculation to this timeend
     * @param array $existingcalculations Existing calculations of this indicator, indexed by sampleid.
     * @return array [0] = [$sampleid => int[]|float[]], [1] = [$sampleid => int|float], [2] = [$sampleid => $sampleid]
     */
    public function calculate($sampleids, $samplesorigin, $starttime = false, $endtime = false, $existingcalculations = array()) {

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            echo '.';
        }

        $calculations = array();
        $newcalculations = array();
        $notnulls = array();
        foreach ($sampleids as $sampleid => $unusedsampleid) {

            if (isset($existingcalculations[$sampleid])) {
                $calculatedvalue = $existingcalculations[$sampleid];
            } else {
                $calculatedvalue = $this->calculate_sample($sampleid, $samplesorigin, $starttime, $endtime);
                $newcalculations[$sampleid] = $calculatedvalue;
            }

            if (!is_null($calculatedvalue)) {
                $notnulls[$sampleid] = $sampleid;
                $this->validate_calculated_value($calculatedvalue);
            }

            $calculations[$sampleid] = $calculatedvalue;
        }

        $features = $this->to_features($calculations);

        return array($features, $newcalculations, $notnulls);
    }

    /**
     * Validates the calculated value.
     *
     * @throws \coding_exception
     * @param float $calculatedvalue
     * @return true
     */
    protected function validate_calculated_value($calculatedvalue) {
        if ($calculatedvalue > self::MAX_VALUE || $calculatedvalue < self::MIN_VALUE) {
            throw new \coding_exception('Calculated values should be higher than ' . self::MIN_VALUE .
                ' and lower than ' . self::MAX_VALUE . ' ' . $calculatedvalue . ' received');
        }
        return true;
    }
}

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

    const MIN_VALUE = -1;

    const MAX_VALUE = 1;

    /**
     * @var array[]
     */
    protected $sampledata = array();

    /**
     * Converts the calculated indicators to feature/s.
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

    public function should_be_displayed($value, $subtype) {
        // We should everything by default.
        return true;
    }

    /**
     * @return null|string
     */
    public static function required_sample_data() {
        return null;
    }

    public static function instance() {
        return new static();
    }

    public static function get_max_value() {
        return self::MAX_VALUE;
    }

    public static function get_min_value() {
        return self::MIN_VALUE;
    }

    /**
     * Calculates the indicator.
     *
     * Returns an array of values which size matches $sampleids size.
     *
     * @param array $sampleids
     * @param string $samplesorigin
     * @param integer $starttime Limit the calculation to this timestart
     * @param integer $endtime Limit the calculation to this timeend
     * @return array The format to follow is [userid] = int|float[]
     */
    public function calculate($sampleids, $samplesorigin, $starttime = false, $endtime = false) {

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            echo '.';
        }

        $calculations = array();
        foreach ($sampleids as $sampleid => $unusedsampleid) {

            $calculatedvalue = $this->calculate_sample($sampleid, $samplesorigin, $starttime, $endtime);

            if (!is_null($calculatedvalue) && ($calculatedvalue > self::MAX_VALUE || $calculatedvalue < self::MIN_VALUE)) {
                throw new \coding_exception('Calculated values should be higher than ' . self::MIN_VALUE .
                    ' and lower than ' . self::MAX_VALUE . ' ' . $calculatedvalue . ' received');
            }

            $calculations[$sampleid] = $calculatedvalue;
        }

        $calculations = $this->to_features($calculations);

        return $calculations;
    }

    public function set_sample_data($data) {
        $this->sampledata = $data;
    }

    protected function retrieve($tablename, $sampleid) {
        if (empty($this->sampledata[$sampleid]) || empty($this->sampledata[$sampleid][$tablename])) {
            // We don't throw an exception because indicators should be able to
            // try multiple tables until they find something they can use.
            return false;
        }
        return $this->sampledata[$sampleid][$tablename];
    }

    protected static function add_samples_averages() {
        return false;
    }

    protected static function get_average_columns() {
        return array('mean');
    }

    protected function calculate_averages($values) {
        $mean = array_sum($values) / count($values);
        return array($mean);
    }
}

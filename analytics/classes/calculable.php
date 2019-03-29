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
 * Calculable dataset items abstract class.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Calculable dataset items abstract class.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class calculable {

    /**
     * Neutral calculation outcome.
     */
    const OUTCOME_NEUTRAL = 0;

    /**
     * Very positive calculation outcome.
     */
    const OUTCOME_VERY_POSITIVE = 1;

    /**
     * Positive calculation outcome.
     */
    const OUTCOME_OK = 2;

    /**
     * Negative calculation outcome.
     */
    const OUTCOME_NEGATIVE = 3;

    /**
     * Very negative calculation outcome.
     */
    const OUTCOME_VERY_NEGATIVE = 4;

    /**
     * @var array[]
     */
    protected $sampledata = array();

    /**
     * Returns a lang_string object representing the name for the indicator or target.
     *
     * Used as column identificator.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static abstract function get_name() : \lang_string;

    /**
     * The class id is the calculable class full qualified class name.
     *
     * @return string
     */
    public function get_id() {
        // Using get_class as get_component_classes_in_namespace returns double escaped fully qualified class names.
        return '\\' . get_class($this);
    }

    /**
     * add_sample_data
     *
     * @param array $data
     * @return void
     */
    public function add_sample_data($data) {
        $this->sampledata = $this->array_merge_recursive_keep_keys($this->sampledata, $data);
    }

    /**
     * clear_sample_data
     *
     * @return void
     */
    public function clear_sample_data() {
        $this->sampledata = array();
    }

    /**
     * Returns the visible value of the calculated value.
     *
     * @param float $value
     * @param string|false $subtype
     * @return string
     */
    public function get_display_value($value, $subtype = false) {
        return $value;
    }

    /**
     * Returns how good the calculated value is.
     *
     * Use one of \core_analytics\calculable::OUTCOME_* values.
     *
     * @param float $value
     * @param string|false $subtype
     * @return int
     */
    abstract public function get_calculation_outcome($value, $subtype = false);

    /**
     * Retrieve the specified element associated to $sampleid.
     *
     * @param string $elementname
     * @param int $sampleid
     * @return \stdClass|false An \stdClass object or false if it can not be found.
     */
    protected function retrieve($elementname, $sampleid) {
        if (empty($this->sampledata[$sampleid]) || empty($this->sampledata[$sampleid][$elementname])) {
            // We don't throw an exception because indicators should be able to
            // try multiple tables until they find something they can use.
            return false;
        }
        return $this->sampledata[$sampleid][$elementname];
    }

    /**
     * Returns the number of weeks a time range contains.
     *
     * Useful for calculations that depend on the time range duration. Note that it returns
     * a float, rounding the float may lead to inaccurate results.
     *
     * @param int $starttime
     * @param int $endtime
     * @return float
     */
    protected function get_time_range_weeks_number($starttime, $endtime) {
        if ($endtime <= $starttime) {
            throw new \coding_exception('End time timestamp should be greater than start time.');
        }

        $starttimedt = new \DateTime();
        $starttimedt->setTimestamp($starttime);
        $starttimedt->setTimezone(new \DateTimeZone('UTC'));
        $endtimedt = new \DateTime();
        $endtimedt->setTimestamp($endtime);
        $endtimedt->setTimezone(new \DateTimeZone('UTC'));

        $diff = $endtimedt->getTimestamp() - $starttimedt->getTimestamp();
        return $diff / WEEKSECS;
    }

    /**
     * Limits the calculated value to the minimum and maximum values.
     *
     * @param float $calculatedvalue
     * @return float|null
     */
    protected function limit_value($calculatedvalue) {
        return max(min($calculatedvalue, static::get_max_value()), static::get_min_value());
    }

    /**
     * Classifies the provided value into the provided range according to the ranges predicates.
     *
     * Use:
     * - eq as 'equal'
     * - ne as 'not equal'
     * - lt as 'lower than'
     * - le as 'lower or equal than'
     * - gt as 'greater than'
     * - ge as 'greater or equal than'
     *
     * @throws \coding_exception
     * @param int|float $value
     * @param array $ranges e.g. [ ['lt', 20], ['ge', 20] ]
     * @return float
     */
    protected function classify_value($value, $ranges) {

        // To automatically return calculated values from min to max values.
        $rangeweight = (static::get_max_value() - static::get_min_value()) / (count($ranges) - 1);

        foreach ($ranges as $key => $range) {

            $match = false;

            if (count($range) != 2) {
                throw new \coding_exception('classify_value() $ranges array param should contain 2 items, the predicate ' .
                    'e.g. greater (gt), lower or equal (le)... and the value.');
            }

            list($predicate, $rangevalue) = $range;

            switch ($predicate) {
                case 'eq':
                    if ($value == $rangevalue) {
                        $match = true;
                    }
                    break;
                case 'ne':
                    if ($value != $rangevalue) {
                        $match = true;
                    }
                    break;
                case 'lt':
                    if ($value < $rangevalue) {
                        $match = true;
                    }
                    break;
                case 'le':
                    if ($value <= $rangevalue) {
                        $match = true;
                    }
                    break;
                case 'gt':
                    if ($value > $rangevalue) {
                        $match = true;
                    }
                    break;
                case 'ge':
                    if ($value >= $rangevalue) {
                        $match = true;
                    }
                    break;
                default:
                    throw new \coding_exception('Unrecognised predicate ' . $predicate . '. Please use eq, ne, lt, le, ge or gt.');
            }

            // Calculate and return a linear calculated value for the provided value.
            if ($match) {
                return round(static::get_min_value() + ($rangeweight * $key), 2);
            }
        }

        throw new \coding_exception('The provided value "' . $value . '" can not be fit into any of the provided ranges, you ' .
            'should provide ranges for all possible values.');
    }

    /**
     * Merges arrays recursively keeping the same keys the original arrays have.
     *
     * @link http://php.net/manual/es/function.array-merge-recursive.php#114818
     * @return array
     */
    private function array_merge_recursive_keep_keys() {
        $arrays = func_get_args();
        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            reset($base);
            foreach ($array as $key => $value) {
                if (is_array($value) && !empty($base[$key]) && is_array($base[$key])) {
                    $base[$key] = $this->array_merge_recursive_keep_keys($base[$key], $value);
                } else {
                    if (isset($base[$key]) && is_int($key)) {
                        $key++;
                    }
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }
}

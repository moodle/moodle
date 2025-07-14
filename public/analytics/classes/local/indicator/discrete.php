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
 * Abstract discrete indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract discrete indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class discrete extends base {

    /**
     * Classes need to be defined so they can be converted internally to individual dataset features.
     *
     * @return string[]
     */
    protected static function get_classes() {
        throw new \coding_exception('Please overwrite get_classes() specifying your discrete-values\' indicator classes');
    }

    /**
     * Returns 1 feature header for each of the classes.
     *
     * @return string[]
     */
    public static function get_feature_headers() {
        $fullclassname = '\\' . get_called_class();

        foreach (static::get_classes() as $class) {
            $headers[] = $fullclassname . '/' . $class;
        }

        return $headers;
    }

    /**
     * Whether the value should be displayed or not.
     *
     * @param float $value
     * @param string $subtype
     * @return bool
     */
    public function should_be_displayed($value, $subtype) {
        if ($value != static::get_max_value()) {
            // Discrete values indicators are converted internally to 1 feature per indicator, we are only interested
            // in showing the feature flagged with the max value.
            return false;
        }
        return true;
    }

    /**
     * Returns the value to display when the prediction is $value.
     *
     * @param float $value
     * @param string $subtype
     * @return string
     */
    public function get_display_value($value, $subtype = false) {

        $displayvalue = array_search($subtype, static::get_classes(), false);

        debugging('Please overwrite \core_analytics\local\indicator\discrete::get_display_value to show something ' .
            'different than the default "' . $displayvalue . '"', DEBUG_DEVELOPER);

        return $displayvalue;
    }

    /**
     * get_display_style
     *
     * @param float $ignoredvalue
     * @param string $ignoredsubtype
     * @return int
     */
    public function get_display_style($ignoredvalue, $ignoredsubtype) {
        // No style attached to indicators classes, they are what they are, a cat,
        // a horse or a sandwich, they are not good or bad.
        return \core_analytics\calculable::OUTCOME_NEUTRAL;
    }

    /**
     * From calculated values to dataset features.
     *
     * One column for each class.
     *
     * @param float[] $calculatedvalues
     * @return float[]
     */
    protected function to_features($calculatedvalues) {

        $classes = static::get_classes();

        foreach ($calculatedvalues as $sampleid => $calculatedvalue) {

            // Using intval as it may come as a float from the db.
            $classindex = array_search(intval($calculatedvalue), $classes, true);

            if ($classindex === false && !is_null($calculatedvalue)) {
                throw new \coding_exception(get_class($this) . ' calculated value "' . $calculatedvalue .
                    '" is not one of its defined classes (' . json_encode($classes) . ')');
            }

            // We transform the calculated value into multiple features, one for each of the possible classes.
            $features = array_fill(0, count($classes), 0);

            // 1 to the selected value.
            if (!is_null($calculatedvalue)) {
                $features[$classindex] = 1;
            }

            $calculatedvalues[$sampleid] = $features;
        }

        return $calculatedvalues;
    }

    /**
     * Validates the calculated value.
     *
     * @param float $calculatedvalue
     * @return true
     */
    protected function validate_calculated_value($calculatedvalue) {

        // Using intval as it may come as a float from the db.
        if (!in_array(intval($calculatedvalue), static::get_classes())) {
            throw new \coding_exception(get_class($this) . ' calculated value "' . $calculatedvalue .
                '" is not one of its defined classes (' . json_encode(static::get_classes()) . ')');
        }
        return true;
    }
}

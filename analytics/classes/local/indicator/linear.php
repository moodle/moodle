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
 * Abstract linear indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract linear indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class linear extends base {

    /**
     * Set to false to avoid context features to be added as dataset features.
     *
     * @return bool
     */
    protected static function include_averages() {
        return true;
    }

    /**
     * get_feature_headers
     *
     * @return array
     */
    public static function get_feature_headers() {

        $fullclassname = '\\' . get_called_class();

        if (static::include_averages()) {
            // The calculated value + context indicators.
            $headers = array($fullclassname, $fullclassname . '/mean');
        } else {
            $headers = array($fullclassname);
        }
        return $headers;
    }

    /**
     * Show only the main feature.
     *
     * @param float $value
     * @param string $subtype
     * @return bool
     */
    public function should_be_displayed($value, $subtype) {
        if ($subtype != false) {
            return false;
        }
        return true;
    }

    /**
     * get_display_value
     *
     * @param float $value
     * @param string $subtype
     * @return string
     */
    public function get_display_value($value, $subtype = false) {
        $diff = static::get_max_value() - static::get_min_value();
        return round(100 * ($value - static::get_min_value()) / $diff) . '%';
    }

    /**
     * get_calculation_outcome
     *
     * @param float $value
     * @param string $subtype
     * @return int
     */
    public function get_calculation_outcome($value, $subtype = false) {
        if ($value < 0) {
            return self::OUTCOME_NEGATIVE;
        } else {
            return self::OUTCOME_OK;
        }
    }

    /**
     * Converts the calculated values to a list of features for the dataset.
     *
     * @param array $calculatedvalues
     * @return array
     */
    protected function to_features($calculatedvalues) {

        // Null mean if all calculated values are null.
        $nullmean = true;
        foreach ($calculatedvalues as $value) {
            if (!is_null($value)) {
                // Early break, we don't want to spend a lot of time here.
                $nullmean = false;
                break;
            }
        }

        if ($nullmean) {
            $mean = null;
        } else {
            $mean = round(array_sum($calculatedvalues) / count($calculatedvalues), 2);
        }

        foreach ($calculatedvalues as $sampleid => $calculatedvalue) {

            if (!is_null($calculatedvalue)) {
                $calculatedvalue = round($calculatedvalue, 2);
            }

            if (static::include_averages()) {
                $calculatedvalues[$sampleid] = array($calculatedvalue, $mean);
            } else {
                // Basically just convert the scalar to an array of scalars with a single value.
                $calculatedvalues[$sampleid] = array($calculatedvalue);
            }
        }

        // Returns each sample as an array of values, appending the mean to the calculated value.
        return $calculatedvalues;
    }
}

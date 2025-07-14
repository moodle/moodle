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
 * Abstract binary indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract binary indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class binary extends discrete {

    /**
     * get_classes
     *
     * @return array
     */
    final public static function get_classes() {
        return [-1, 1];
    }

    /**
     * It should always be displayed.
     *
     * Binary values have no subtypes by default, please overwrite if
     * your indicator is adding extra features.
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

        // No subtypes for binary values by default.
        if ($value == -1) {
            return get_string('no');
        } else if ($value == 1) {
            return get_string('yes');
        } else {
            throw new \moodle_exception('errorpredictionformat', 'analytics');
        }
    }

    /**
     * get_calculation_outcome
     *
     * @param float $value
     * @param string $subtype
     * @return int
     */
    public function get_calculation_outcome($value, $subtype = false) {

        // No subtypes for binary values by default.
        if ($value == -1) {
            return self::OUTCOME_NEGATIVE;
        } else if ($value == 1) {
            return self::OUTCOME_OK;
        } else {
            throw new \moodle_exception('errorpredictionformat', 'analytics');
        }
    }

    /**
     * get_feature_headers
     *
     * @return array
     */
    public static function get_feature_headers() {
        // Just 1 single feature obtained from the calculated value.
        return array('\\' . get_called_class());
    }

    /**
     * to_features
     *
     * @param array $calculatedvalues
     * @return array
     */
    protected function to_features($calculatedvalues) {
        // Indicators with binary values have only 1 feature for indicator, here we do nothing else
        // than converting each sample scalar value to an array of scalars with 1 element.
        array_walk($calculatedvalues, function(&$calculatedvalue) {
            // Just return it as an array.
            $calculatedvalue = array($calculatedvalue);
        });

        return $calculatedvalues;
    }
}

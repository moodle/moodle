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
 * Binary classifier target.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\target;

defined('MOODLE_INTERNAL') || die();

/**
 * Binary classifier target.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class binary extends discrete {

    /**
     * is_linear
     *
     * @return bool
     */
    public function is_linear() {
        return false;
    }

    /**
     * Returns the target discrete values.
     *
     * Only useful for targets using discrete values, must be overwriten if it is the case.
     *
     * @return array
     */
    final public static function get_classes() {
        return array(0, 1);
    }

    /**
     * Returns the predicted classes that will be ignored.
     *
     * @return array
     */
    public function ignored_predicted_classes() {
        // Zero-value class is usually ignored in binary classifiers.
        return array(0);
    }

    /**
     * Is the calculated value a positive outcome of this target?
     *
     * @param string $value
     * @param string $ignoredsubtype
     * @return int
     */
    public function get_calculation_outcome($value, $ignoredsubtype = false) {

        if (!self::is_a_class($value)) {
            throw new \moodle_exception('errorpredictionformat', 'analytics');
        }

        if (in_array($value, $this->ignored_predicted_classes(), false)) {
            // Just in case, if it is ignored the prediction should not even be recorded but if it would, it is ignored now,
            // which should mean that is it nothing serious.
            return self::OUTCOME_VERY_POSITIVE;
        }

        // By default binaries are danger when prediction = 1.
        if ($value) {
            return self::OUTCOME_VERY_NEGATIVE;
        }
        return self::OUTCOME_VERY_POSITIVE;
    }

    /**
     * classes_description
     *
     * @return string[]
     */
    protected static function classes_description() {
        return array(
            get_string('yes'),
            get_string('no')
        );
    }

}

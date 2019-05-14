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
 * Discrete values target.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\target;

defined('MOODLE_INTERNAL') || die();

/**
 * Discrete values target.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class discrete extends base {

    /**
     * Are this target calculations linear values?
     *
     * @return bool
     */
    public function is_linear() {
        // Not supported yet.
        throw new \coding_exception('Sorry, this version\'s prediction processors only support targets with binary values.' .
            ' You can write your own and overwrite this method though.');
    }

    /**
     * Is the provided class one of this target valid classes?
     *
     * @param mixed $class
     * @return bool
     */
    protected static function is_a_class($class) {
        return (in_array($class, static::get_classes(), false));
    }

    /**
     * get_display_value
     *
     * @param float $value
     * @param string $ignoredsubtype
     * @return string
     */
    public function get_display_value($value, $ignoredsubtype = false) {

        if (!self::is_a_class($value)) {
            throw new \moodle_exception('errorpredictionformat', 'analytics');
        }

        // To discard any possible weird keys devs used.
        $classes = array_values(static::get_classes());
        $descriptions = array_values(static::classes_description());

        if (count($classes) !== count($descriptions)) {
            throw new \coding_exception('You need to describe all your classes (' . json_encode($classes) .
                ') in self::classes_description');
        }

        $key = array_search($value, $classes);
        if ($key === false) {
            throw new \coding_exception('You need to describe all your classes (' . json_encode($classes) .
                ') in self::classes_description');
        }

        return $descriptions[$key];
    }

    /**
     * get_calculation_outcome
     *
     * @param float $value
     * @param string $ignoredsubtype
     * @return int
     */
    public function get_calculation_outcome($value, $ignoredsubtype = false) {

        if (!self::is_a_class($value)) {
            throw new \moodle_exception('errorpredictionformat', 'analytics');
        }

        if (in_array($value, $this->ignored_predicted_classes(), false)) {
            // Just in case, if it is ignored the prediction should not even be recorded.
            return self::OUTCOME_OK;
        }

        debugging('Please overwrite \core_analytics\local\target\discrete::get_calculation_outcome, all your target ' .
            'classes are styled the same way otherwise', DEBUG_DEVELOPER);
        return self::OUTCOME_OK;
    }

    /**
     * Returns all the possible values the target calculation can return.
     *
     * Only useful for targets using discrete values, must be overwriten if it is the case.
     *
     * @return array
     */
    public static function get_classes() {
        // Coding exception as this will only be called if this target have non-linear values.
        throw new \coding_exception('Overwrite get_classes() and return an array with the different values the ' .
            'target calculation can return');
    }

    /**
     * Returns descriptions for each of the values the target calculation can return.
     *
     * The array indexes should match self::get_classes indexes.
     *
     * @return array
     */
    protected static function classes_description() {
        throw new \coding_exception('Overwrite classes_description() and return an array with a description for each of the ' .
            'different values the target calculation can return. Indexes should match self::get_classes indexes');
    }

    /**
     * Returns the predicted classes that will be ignored.
     *
     * Better be keen to add more than less classes here, the callback is always able to discard some classes. As an example
     * a target with classes 'grade 0-3', 'grade 3-6', 'grade 6-8' and 'grade 8-10' is interested in flagging both 'grade 6-8'
     * and 'grade 8-10' as ignored. On the other hand, a target like dropout risk with classes 'yes', 'no' may just be
     * interested in 'yes'.
     *
     * @return array List of values that will be ignored (array keys are ignored).
     */
    public function ignored_predicted_classes() {
        // Coding exception as this will only be called if this target have non-linear values.
        throw new \coding_exception('Overwrite ignored_predicted_classes() and return an array with the classes that should not ' .
            'trigger the callback');
    }

    /**
     * This method determines if a prediction is interesing for the model or not.
     *
     * This method internally calls ignored_predicted_classes to skip classes
     * flagged by the target as not important for users.
     *
     * @param mixed $predictedvalue
     * @param float $predictionscore
     * @return bool
     */
    public function triggers_callback($predictedvalue, $predictionscore) {

        if (!parent::triggers_callback($predictedvalue, $predictionscore)) {
            return false;
        }

        if (in_array($predictedvalue, $this->ignored_predicted_classes())) {
            return false;
        }

        return true;
    }
}

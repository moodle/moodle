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

    public function is_linear() {
        // Not supported yet.
        throw new \coding_exception('Sorry, this version\'s prediction processors only support targets with binary values.');
    }

    protected static function is_a_class($class) {
        return (in_array($class, static::get_classes()));
    }

    public function get_display_value($value) {

        if (!self::is_a_class($value)) {
            throw new \moodle_exception('errorpredictionformat', 'analytics');
        }

        // array_values to discard any possible weird keys devs used.
        $classes = array_values(static::get_classes());
        $descriptions = array_values(static::classes_description());

        if (count($classes) !== count($descriptions)) {
            throw new \coding_exception('You need to describe all your classes (' . json_encode($classes) . ') in self::classes_description');
        }

        $key = array_search($value, $classes);
        if ($key === false) {
            throw new \coding_exception('You need to describe all your classes (' . json_encode($classes) . ') in self::classes_description');
        }

        return $descriptions[$key];
    }

    public function get_value_style($value) {

        if (!self::is_a_class($value)) {
            throw new \moodle_exception('errorpredictionformat', 'analytics');
        }

        if (in_array($value, $this->ignored_predicted_classes())) {
            // Just in case, if it is ignored the prediction should not even be recorded.
            return '';
        }

        debugging('Please overwrite \core_analytics\local\target\discrete::get_value_style, all your target classes are styled ' .
            'the same way otherwise', DEBUG_DEVELOPER);
        return 'alert alert-danger';
    }

    /**
     * Returns the target discrete values.
     *
     * Only useful for targets using discrete values, must be overwriten if it is the case.
     *
     * @return array
     */
    public static function get_classes() {
        // Coding exception as this will only be called if this target have non-linear values.
        throw new \coding_exception('Overwrite get_classes() and return an array with the different target classes');
    }

    protected static function classes_description() {
        throw new \coding_exception('Overwrite classes_description() and return an array with the target classes description and ' .
            'indexes matching self::get_classes');
    }

    /**
     * Returns the predicted classes that will be ignored.
     *
     * Better be keen to add more than less classes here, the callback is always able to discard some classes. As an example
     * a target with classes 'grade 0-3', 'grade 3-6', 'grade 6-8' and 'grade 8-10' is interested in flagging both 'grade 0-3'
     * and 'grade 3-6'. On the other hand, a target like dropout risk with classes 'yes', 'no' may just be interested in 'yes'.
     *
     * @return array
     */
    protected function ignored_predicted_classes() {
        // Coding exception as this will only be called if this target have non-linear values.
        throw new \coding_exception('Overwrite ignored_predicted_classes() and return an array with the classes that triggers ' .
            'the callback');
    }
}

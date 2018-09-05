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
 * Target base.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours\local\target;

defined('MOODLE_INTERNAL') || die();

use tool_usertours\step;

/**
 * Target base.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {
    /**
     * @var     step        $step           The step being targetted.
     */
    protected $step;

    /**
     * @var     array       $forcedsettings The settings forced by this type.
     */
    protected static $forcedsettings = [];

    /**
     * Create the target type.
     *
     * @param   step        $step       The step being targetted.
     */
    public function __construct(step $step) {
        $this->step = $step;
    }

    /**
     * Convert the target value to a valid CSS selector for use in the
     * output configuration.
     *
     * @return string
     */
    abstract public function convert_to_css();

    /**
     * Convert the step target to a friendly name for use in the UI.
     *
     * @return string
     */
    abstract public function get_displayname();

    /**
     * Add the target type configuration to the form.
     *
     * @param   MoodleQuickForm $mform      The form to add configuration to.
     */
    public static function add_config_to_form(\MoodleQuickForm $mform) {
    }

    /**
     * Add the disabledIf values.
     *
     * @param   MoodleQuickForm $mform      The form to add configuration to.
     */
    public static function add_disabled_constraints_to_form(\MoodleQuickForm $mform) {
    }

    /**
     * Prepare data to submit to the form.
     *
     * @param   object          $data       The data being passed to the form
     */
    abstract public function prepare_data_for_form($data);

    /**
     * Whether the specified step setting is forced by this target type.
     *
     * @param   string          $key        The name of the key to check.
     * @return  boolean
     */
    public function is_setting_forced($key) {
        return isset(static::$forcedsettings[$key]);
    }

    /**
     * The value of the forced setting.
     *
     * @param   string          $key        The name of the key to check.
     * @return  mixed
     */
    public function get_forced_setting_value($key) {
        if ($this->is_setting_forced($key)) {
            return static::$forcedsettings[$key];
        }

        return null;
    }

    /**
     * Fetch the targetvalue from the form for this target type.
     *
     * @param   stdClass        $data       The data submitted in the form
     * @return  string
     */
    abstract public function get_value_from_form($data);
}

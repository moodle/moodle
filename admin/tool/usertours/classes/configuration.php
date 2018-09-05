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
 * Step configuration detail class.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours;

defined('MOODLE_INTERNAL') || die();

/**
 * Step configuration detail class.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configuration {

    /**
     * @var TOURDEFAULT
     */
    const TOURDEFAULT = 'usetourdefault';

    /**
     * Get the list of keys which can be defaulted in the tour.
     *
     * @return  array
     */
    public static function get_defaultable_keys() {
        return [
            'placement',
            'orphan',
            'backdrop',
            'reflex',
        ];
    }

    /**
     * Get the default value for the specified key.
     *
     * @param   string          $key        The key for the specified value
     * @return  mixed
     */
    public static function get_default_value($key) {
        switch($key) {
            case 'placement':
                return 'bottom';
            case 'orphan':
            case 'backdrop':
            case 'reflex':
                return false;
        }
    }

    /**
     * Get the default value for the specified key for the step form.
     *
     * @param   string          $key        The key for the specified value
     * @return  mixed
     */
    public static function get_step_default_value($key) {
        switch($key) {
            case 'placement':
            case 'orphan':
            case 'backdrop':
            case 'reflex':
                return self::TOURDEFAULT;
        }
    }

    /**
     * Get the list of possible placement options.
     *
     * @param   string          $default    The default option.
     * @return  array
     */
    public static function get_placement_options($default = null) {
        $values = [
            'top'    => get_string('above',   'tool_usertours'),
            'bottom' => get_string('below',   'tool_usertours'),
            'left'   => get_string('left',    'tool_usertours'),
            'right'  => get_string('right',   'tool_usertours'),
        ];

        if ($default === null) {
            return $values;
        }

        if (!isset($values[$default])) {
            $default = self::get_default_value('placement');
        }

        $values = array_reverse($values, true);
        $values[self::TOURDEFAULT] = get_string('defaultvalue', 'tool_usertours', $values[$default]);
        $values = array_reverse($values, true);

        return $values;
    }

}

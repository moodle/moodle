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
 * Event handlers for ues_meta_viewer events.
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/cps/events/lib.php');

abstract class blocks_cps_ues_meta_viewer_handler {
    public static function ues_user_data_ui_keys($fields) {
        // Remove unecessary sport codes.
        $sports = array();

        foreach (range(2, 4) as $codenum) {
            $sports[] = "user_sport$codenum";
        }

        $notsports = function ($key) use ($sports) {
            return !in_array($key, $sports);
        };

        $fields->keys = array_filter($fields->keys, $notsports);

        return true;
    }

    public static function ues_user_data_ui_element($handler) {

        // Play nice, only handle what I need.
        $handled = array(
            'username',
            'user_ferpa',
            'user_reg_status',
            'user_degree',
            'user_year',
            'user_major',
            'user_college',
            'user_keypadid',
            'user_sport1',
            'user_anonymous_number'
        );

        if (in_array($handler->ui_element->key(), $handled)) {
            $field = $handler->ui_element->key();

            $name = get_string($field, 'block_cps');

            $handler->ui_element = new cps_meta_ui_element($field, $name);
        }

        return true;
    }
}
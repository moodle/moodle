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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/cps/events/ues_people_lib.php');

abstract class blocks_cps_ues_people_handler {
    public static function ues_people_outputs($data) {
        // Contains the requested order.
        $interfere = array(
            'sec_number', 'credit_hours', 'user_degree', 'user_ferpa',
            'user_keypadid', 'user_college', 'user_major', 'user_year',
            'user_reg_status'
        );

        $s = ues::gen_str('block_cps');

        foreach ($interfere as $meta) {
            if (!isset($data->outputs[$meta])) {
                $data->outputs[$meta] = new cps_people_element($meta, $s($meta));
            }
            unset($data->outputs[$meta]);
            $data->outputs[$meta] = new cps_people_element($meta, $s($meta));
        }

        return $data;
    }
}
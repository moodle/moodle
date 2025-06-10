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

require_once($CFG->dirroot . '/blocks/ues_people/lib.php');

class cps_people_element extends ues_people_element_output {
    private function span_yes() {
        $values = explode('_', $this->field);
        $class = end($values);
        return html_writer::tag('span', 'Y', array('class' => "$class yes"));
    }

    public function format($user) {
        switch ($this->field) {
            case 'user_ferpa':
            case 'user_degree':
                return !empty($user->{$this->field}) ? $this->span_yes() : 'N';
            case 'user_reg_status':
                return isset($user->{$this->field}) ?
                    date('m-d-Y', $user->{$this->field}) : '';
            default:
                return parent::format($user);
        }
    }
}